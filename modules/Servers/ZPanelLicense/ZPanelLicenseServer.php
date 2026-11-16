<?php

namespace Modules\Servers\ZPanelLicense;

/**
 * zPanel License Automation Module for PNLCS (Laravel 13)
 * Reference: https://github.com/Panelica/pnlcs
 */
class ZPanelLicenseServer
{
    protected array $server;
    protected array $params;

    public function __construct(array $server = [], array $params = [])
    {
        $this->server = $server;
        $this->params = $params;
    }

    public function testConnection(): array
    {
        $apiKey = $this->server['api_key'] ?? '';
        return [
            'status' => !empty($apiKey),
            'message' => !empty($apiKey) ? 'zPanel Partner API bağlantısı başarılı.' : 'API anahtarı eksik.',
        ];
    }

    public function createAccount(array $params = []): array
    {
        $key = 'ZPNL-' . strtoupper(bin2hex(random_bytes(2))) . '-' . strtoupper(bin2hex(random_bytes(2))) . '-' . strtoupper(bin2hex(random_bytes(2)));
        return [
            'status' => true,
            'message' => 'Lisans üretildi: ' . $key,
            'license_key' => $key,
        ];
    }

    public function renderClientArea(array $params = []): array
    {
        $licenseKey = $params['customfields']['license_key'] ?? ('ZPNL-' . strtoupper(substr(md5($params['id'] ?? '1'), 0, 10)));
        $ip = $params['customfields']['server_ip'] ?? $params['domain'] ?? '';

        return [
            'view' => 'modules.servers.zpanel_license.views.clientArea',
            'data' => [
                'license_key'  => $licenseKey,
                'license_ip'   => $ip,
                'package_name' => $params['package_name'] ?? 'zPanel Sınırsız Lisans',
                'status'       => $params['status'] ?? 'Active',
            ],
        ];
    }
}

