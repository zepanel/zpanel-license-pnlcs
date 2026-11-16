{{-- zPanel License PNLCS Blade View --}}
{{-- Dokümantasyon: https://console.zepanel.com/partner/docs --}}

<div class="zp-pnlcs-license" style="font-family: inherit;">
    <!-- Üst Başlık Kartı -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-slate-900">{{ $package_name }}</h2>
                    <span class="bg-sky-100 text-sky-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">zPanel v1.0</span>
                </div>
                <div class="text-sm text-slate-500">
                    Durum: 
                    @if(strtolower($status) === 'active')
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Aktif</span>
                    @else
                        <span class="bg-slate-100 text-slate-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $status }}</span>
                    @endif
                </div>
            </div>
            @if(!empty($is_issued))
            <div>
                <button type="button" class="bg-blue-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-blue-700 transition" onclick="navigator.clipboard.writeText('{{ $license_key }}'); this.innerText = 'Kopyalandı!'; setTimeout(() => this.innerText = 'Lisans Anahtarını Kopyala', 2500);">
                    Lisans Anahtarını Kopyala
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Lisans Detayı -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Resmi Lisans Bilgileri</h3>
        
        @if(!empty($is_issued))
            <div class="bg-slate-900 rounded-lg p-4 font-mono font-bold text-sky-400 text-base mb-5 flex justify-between items-center">
                <span>{{ $license_key }}</span>
                <span class="text-xs text-slate-400 font-sans font-normal bg-slate-800 px-2 py-1 rounded">Partner API Onaylı</span>
            </div>

            <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 flex justify-between items-center flex-wrap gap-4">
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase mb-1">Lisansa Bağlı Sunucu IP / Host</div>
                    <div class="font-mono font-bold text-slate-900">
                        {{ $license_ip ?: 'Tanımlanmadı (Kurulumda Otomatik Kilitlenir)' }}
                    </div>
                </div>
                <form method="POST" action="" class="flex gap-2 items-center">
                    @csrf
                    <input type="hidden" name="action" value="updateIp">
                    <input type="text" name="new_ip" value="{{ $license_ip }}" placeholder="Yeni IP (Örn: 185.120.45.67)" required class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-mono w-56">
                    <button type="submit" class="bg-slate-900 text-white text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-slate-800 transition">
                        IP Güncelle
                    </button>
                </form>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-4 text-sm">
                Lisans anahtarınız henüz oluşturulmadı veya provizyon aşamasındadır. Lütfen birkaç dakika sonra sayfayı yenileyiniz.
            </div>
        @endif
    </div>

    @if(!empty($is_issued))
    <!-- Hızlı Kurulum -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-base font-bold text-slate-900 mb-2">Hızlı Sunucu Kurulum Komutu</h3>
        <p class="text-sm text-slate-500 mb-3">Sunucunuzun root SSH terminaline bağlanarak aşağıdaki tek satırlık komutu çalıştırabilirsiniz:</p>
        <div class="bg-slate-900 text-sky-400 font-mono text-xs sm:text-sm p-4 rounded-lg overflow-x-auto">
            curl -fsSL https://get.zepanel.com/install.sh | bash -s -- --license={{ $license_key }}
        </div>
        <div class="mt-3 flex gap-4 text-xs text-slate-500 flex-wrap">
            <span>&check; <strong>Web Sunucusu:</strong> zMaxServer OpenLiteSpeed (LSAPI)</span>
            <span>&check; <strong>Müşteri Portu:</strong> 8026</span>
            <span>&check; <strong>Webmail:</strong> 8046</span>
        </div>
    </div>
    @endif
</div>
