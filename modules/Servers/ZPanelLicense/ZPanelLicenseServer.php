<?php

namespace Modules\Servers\ZPanelLicense;

/**
 * zPanel License Automation Module for PNLCS (Laravel 13 Architecture)
 * Reference: https://github.com/Panelica/pnlcs
 * Resmi Dokümantasyon: https://console.zepanel.com/partner/docs
 */
class ZPanelLicenseServer
{
    protected array $server;
    protected array $params;
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct(array $server = [], array $params = [])
    {
        $this->server = $server;
        $this->params = $params;
        $this->apiUrl = !empty($server['api_url']) ? rtrim($server['api_url'], '/') : 'https://console.zepanel.com/partner-api/v1';
        $this->apiToken = trim($server['api_key'] ?? $server['password'] ?? '');
    }

    protected function callApi(string $method, string $path, array $data = []): array
    {
        if (empty($this->apiToken)) {
            return ['status' => false, 'message' => 'Partner API Anahtarı eksik.', 'data' => null];
        }

        $url = $this->apiUrl . '/' . ltrim($path, '/');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: PNLCS-zPanel-License/1.0',
        ]);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $res = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($res === false) {
            return ['status' => false, 'message' => 'API bağlantı hatası: ' . $err, 'data' => null];
        }

        $json = json_decode((string)$res, true);
        $isOk = ($code >= 200 && $code < 300);

        return [
            'status' => $isOk,
            'http_code' => $code,
            'data' => $json,
            'message' => $json['error'] ?? ($json['message'] ?? ($isOk ? 'Başarılı' : "HTTP Hata {$code}")),
        ];
    }

    public function testConnection(): array
    {
        $res = $this->callApi('GET', 'packages');
        return [
            'status' => $res['status'],
            'message' => $res['status'] ? 'zPanel Partner API v1 bağlantısı başarılı.' : $res['message'],
        ];
    }

    public function createAccount(array $params = []): array
    {
        $orderId = trim($params['package']['order_id'] ?? $this->params['package']['order_id'] ?? $params['configoptions']['order_id'] ?? '');
        if (empty($orderId)) {
            return ['status' => false, 'message' => 'Lisans Havuz Sipariş ID (order_id) paket ayarlarında bulunamadı.'];
        }

        $customerName = trim(($params['user']['name'] ?? '') . ' ' . ($params['user']['surname'] ?? ''));
        $domainOrIp = trim($params['customfields']['server_ip'] ?? $params['domain'] ?? '');

        $res = $this->callApi('POST', "orders/{$orderId}/licenses", [
            'customer_name' => $customerName,
            'customer_domain' => $domainOrIp ?: null,
        ]);

        if (!$res['status']) {
            return ['status' => false, 'message' => $res['message']];
        }

        $data = $res['data'] ?? [];
        $licenseKey = $data['license_key'] ?? '';
        $licenseRowId = (string)($data['id'] ?? '');

        return [
            'status' => true,
            'message' => 'zPanel lisansı tahsis edildi.',
            'license_key' => $licenseKey,
            'license_row_id' => $licenseRowId,
            'password' => $licenseKey,
            'ip' => $domainOrIp,
        ];
    }

    public function terminateAccount(array $params = []): array
    {
        $orderId = trim($params['package']['order_id'] ?? $this->params['package']['order_id'] ?? '');
        $licenseRowId = trim($params['customfields']['license_row_id'] ?? '');

        if (!empty($orderId) && !empty($licenseRowId)) {
            $res = $this->callApi('POST', "orders/{$orderId}/licenses/{$licenseRowId}/release");
            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        }

        return ['status' => true, 'message' => 'Lisans iptal edilerek havuza iade edildi.'];
    }

    public function updateIp(array $params = []): array
    {
        $orderId = trim($params['package']['order_id'] ?? $this->params['package']['order_id'] ?? '');
        $licenseRowId = trim($params['customfields']['license_row_id'] ?? '');
        $newIp = trim($_POST['new_ip'] ?? $params['new_ip'] ?? '');
        $customerName = trim(($params['user']['name'] ?? '') . ' ' . ($params['user']['surname'] ?? ''));

        if (empty($newIp)) {
            return ['status' => false, 'message' => 'Geçerli bir IP girin.'];
        }

        $res = $this->callApi('POST', "orders/{$orderId}/licenses/{$licenseRowId}/info", [
            'customer_name' => $customerName,
            'customer_domain' => $newIp,
        ]);

        return [
            'status' => $res['status'],
            'message' => $res['status'] ? 'Lisans IP adresi başarıyla güncellendi.' : $res['message'],
        ];
    }

    public function renderClientArea(array $params = []): array
    {
        $licenseKey = $params['customfields']['license_key'] ?? $params['password'] ?? '';
        $ip = $params['customfields']['server_ip'] ?? $params['domain'] ?? '';

        return [
            'view' => 'modules.servers.zpanel_license.views.clientArea',
            'data' => [
                'license_key'  => $licenseKey,
                'license_ip'   => $ip,
                'package_name' => $params['package_name'] ?? 'zPanel Resmi Lisansı',
                'status'       => $params['status'] ?? 'Active',
                'service_id'   => $params['id'] ?? 0,
                'is_issued'    => !empty($licenseKey),
            ],
        ];
    }
}
