{{-- zPanel License PNLCS Blade View --}}
<div class="zp-pnlcs-license">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 mb-1">{{ $package_name }}</h2>
                <div class="text-sm text-slate-500">Durum: <span class="text-emerald-700 font-semibold">{{ $status }}</span></div>
            </div>
            <div>
                <button type="button" class="bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-full hover:bg-blue-700" onclick="alert('Lisans: {{ $license_key }}');">
                    Anahtarı Kopyala &rarr;
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Lisans Anahtarı &amp; Tanımlı IP</h3>
        <div class="bg-slate-100 rounded-lg p-4 font-mono font-bold text-slate-900 text-lg mb-4 flex justify-between items-center">
            <span>{{ $license_key }}</span>
            <span class="text-xs text-slate-500 font-normal">Orijinal zPanel v1.0</span>
        </div>
        <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 flex justify-between items-center flex-wrap gap-4">
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase">Tanımlı Sunucu IP</div>
                <div class="font-mono font-bold text-slate-900">{{ $license_ip ?: 'Tanımlı Değil (İlk kurulumda kilitlenir)' }}</div>
            </div>
            <form class="flex gap-2">
                <input type="text" placeholder="Yeni IP Girin" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-mono">
                <button type="button" class="bg-slate-900 text-white text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-slate-800">Güncelle</button>
            </form>
        </div>
    </div>
</div>

