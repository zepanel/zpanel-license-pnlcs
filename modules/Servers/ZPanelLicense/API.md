# zPanel Lisans Otomasyon Modülü API Dokümantasyonu (PNLCS)

zPanel Lisans modülü, PNLCS (Laravel 13) ortamında resmi zPanel Partner REST API (`https://console.zepanel.com/partner-api/v1`) altyapısına bağlanarak lisans havuzu işlemlerini otomatikleştirir.

---

## 1. Kimlik Doğrulama

```http
Authorization: Bearer zp_partner_...
Content-Type: application/json
Accept: application/json
```

---

## 2. Desteklenen PNLCS Metotları ve Partner API Uç Noktaları

| PNLCS Metodu | HTTP Yöntemi | Partner API Uç Noktası | Açıklama |
|---|---|---|---|
| `testConnection()` | `GET` | `/orders/:orderId/licenses` | Sipariş havuzuna ve token'a erişim testi |
| `createAccount()` | `POST` | `/orders/:orderId/licenses` | Havuzdan yeni lisans tahsis etme |
| `terminateAccount()` | `POST` | `/orders/:orderId/licenses/:licenseId/release` | Lisansı havuza iade etme / serbest bırakma |
| `updateLicenseInfo()` | `POST` | `/orders/:orderId/licenses/:licenseId/info` | Lisans IP / Domain bilgisini güncelleme |
| `clientArea()` | Blade | `views/clientArea.blade.php` | Lisans anahtarı ve tek tık kurulum kılavuzu |
