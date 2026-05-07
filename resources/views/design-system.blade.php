<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design System - AIAGEN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f1f5f9; font-family: 'Figtree', system-ui, sans-serif; }
        .ds-section { background: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .ds-section h2 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: .25rem; }
        .ds-section p.sub { font-size: .875rem; color: #64748b; margin-bottom: 1.5rem; }
        .ds-row { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; margin-bottom: 1rem; }
        .ds-row-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; width: 100%; margin-bottom: .25rem; }
        .swatch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: .5rem; }
        .swatch { border-radius: .5rem; height: 60px; display: flex; align-items: flex-end; padding: .375rem; font-size: .625rem; font-weight: 600; color: white; text-shadow: 0 1px 2px rgba(0,0,0,.3); }
        .form-preview { max-width: 480px; }
    </style>
</head>
<body class="p-4 md:p-8 max-w-5xl mx-auto">

    <header class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-primary-900">🎨 Design System</h1>
        <p class="text-secondary-600 mt-1">Warna Global, Button & Input Templates — AIAGEN</p>
    </header>

    {{-- ═══ COLOR PALETTE ═══ --}}
    <section class="ds-section">
        <h2>🔵 Warna Primer (Primary — Blue)</h2>
        <p class="sub">Digunakan untuk elemen utama: tombol, link, header aktif.</p>
        <div class="swatch-grid">
            <div class="swatch" style="background:#eff6ff;color:#1e3a8a">50</div>
            <div class="swatch" style="background:#dbeafe;color:#1e3a8a">100</div>
            <div class="swatch" style="background:#bfdbfe;color:#1e3a8a">200</div>
            <div class="swatch" style="background:#93c5fd;color:#172554">300</div>
            <div class="swatch" style="background:#60a5fa">400</div>
            <div class="swatch" style="background:#3b82f6">500</div>
            <div class="swatch" style="background:#2563eb">600 ★</div>
            <div class="swatch" style="background:#1d4ed8">700</div>
            <div class="swatch" style="background:#1e40af">800</div>
            <div class="swatch" style="background:#1e3a8a">900</div>
            <div class="swatch" style="background:#172554">950</div>
        </div>
    </section>

    <section class="ds-section">
        <h2>⬜ Warna Sekunder (Secondary — White / Light)</h2>
        <p class="sub">Digunakan untuk background, border, teks sekunder.</p>
        <div class="swatch-grid">
            <div class="swatch" style="background:#ffffff;border:1px solid #e2e8f0;color:#334155">50</div>
            <div class="swatch" style="background:#f8fafc;border:1px solid #e2e8f0;color:#334155">100</div>
            <div class="swatch" style="background:#f1f5f9;color:#334155">200</div>
            <div class="swatch" style="background:#e2e8f0;color:#334155">300</div>
            <div class="swatch" style="background:#cbd5e1;color:#1e293b">400</div>
            <div class="swatch" style="background:#94a3b8">500</div>
            <div class="swatch" style="background:#64748b">600</div>
            <div class="swatch" style="background:#475569">700</div>
            <div class="swatch" style="background:#334155">800</div>
            <div class="swatch" style="background:#1e293b">900</div>
            <div class="swatch" style="background:#0f172a">950</div>
        </div>
    </section>

    {{-- ═══ BUTTONS ═══ --}}
    <section class="ds-section">
        <h2>🔘 Template Button — Variasi Warna</h2>
        <p class="sub">6 varian warna: primary, secondary, outline, ghost, danger, success.</p>

        <div class="ds-row-label">Primary (Biru Solid)</div>
        <div class="ds-row">
            <button class="btn btn-primary btn-xs">XS Primary</button>
            <button class="btn btn-primary btn-sm">SM Primary</button>
            <button class="btn btn-primary btn-md">MD Primary</button>
            <button class="btn btn-primary btn-lg">LG Primary</button>
            <button class="btn btn-primary btn-md" disabled>Disabled</button>
        </div>

        <div class="ds-row-label">Secondary (Putih Solid)</div>
        <div class="ds-row">
            <button class="btn btn-secondary btn-xs">XS Secondary</button>
            <button class="btn btn-secondary btn-sm">SM Secondary</button>
            <button class="btn btn-secondary btn-md">MD Secondary</button>
            <button class="btn btn-secondary btn-lg">LG Secondary</button>
            <button class="btn btn-secondary btn-md" disabled>Disabled</button>
        </div>

        <div class="ds-row-label">Outline (Biru Border)</div>
        <div class="ds-row">
            <button class="btn btn-outline btn-xs">XS Outline</button>
            <button class="btn btn-outline btn-sm">SM Outline</button>
            <button class="btn btn-outline btn-md">MD Outline</button>
            <button class="btn btn-outline btn-lg">LG Outline</button>
            <button class="btn btn-outline btn-md" disabled>Disabled</button>
        </div>

        <div class="ds-row-label">Ghost (Transparan)</div>
        <div class="ds-row">
            <button class="btn btn-ghost btn-xs">XS Ghost</button>
            <button class="btn btn-ghost btn-sm">SM Ghost</button>
            <button class="btn btn-ghost btn-md">MD Ghost</button>
            <button class="btn btn-ghost btn-lg">LG Ghost</button>
        </div>

        <div class="ds-row-label">Danger (Merah)</div>
        <div class="ds-row">
            <button class="btn btn-danger btn-xs">XS Danger</button>
            <button class="btn btn-danger btn-sm">SM Danger</button>
            <button class="btn btn-danger btn-md">MD Danger</button>
            <button class="btn btn-danger btn-lg">LG Danger</button>
        </div>

        <div class="ds-row-label">Success (Hijau)</div>
        <div class="ds-row">
            <button class="btn btn-success btn-xs">XS Success</button>
            <button class="btn btn-success btn-sm">SM Success</button>
            <button class="btn btn-success btn-md">MD Success</button>
            <button class="btn btn-success btn-lg">LG Success</button>
        </div>

        <div class="ds-row-label">Button Group</div>
        <div class="ds-row">
            <div class="btn-group">
                <button class="btn btn-primary btn-md">Kiri</button>
                <button class="btn btn-primary btn-md">Tengah</button>
                <button class="btn btn-primary btn-md">Kanan</button>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline btn-sm">A</button>
                <button class="btn btn-outline btn-sm">B</button>
                <button class="btn btn-outline btn-sm">C</button>
            </div>
        </div>
    </section>

    {{-- ═══ FORM INPUTS ═══ --}}
    <section class="ds-section">
        <h2>📝 Template Kolom Isian (Input Fields)</h2>
        <p class="sub">3 ukuran (sm, md, lg), state: default, error, success, disabled.</p>

        <div class="form-preview">

            {{-- Sizes --}}
            <div class="ds-row-label">Ukuran Input</div>

            <div class="form-group">
                <label class="form-label">Small Input</label>
                <input type="text" class="form-input form-input-sm" placeholder="Input ukuran kecil...">
            </div>

            <div class="form-group">
                <label class="form-label">Medium Input (Default)</label>
                <input type="text" class="form-input form-input-md" placeholder="Input ukuran sedang...">
            </div>

            <div class="form-group">
                <label class="form-label">Large Input</label>
                <input type="text" class="form-input form-input-lg" placeholder="Input ukuran besar...">
            </div>

            {{-- States --}}
            <div class="ds-row-label mt-4">State Validasi</div>

            <div class="form-group">
                <label class="form-label form-label-required">Error State</label>
                <input type="text" class="form-input form-input-md form-input-error" value="Email tidak valid">
                <p class="form-error-text">Format email harus contoh@domain.com</p>
            </div>

            <div class="form-group">
                <label class="form-label">Success State</label>
                <input type="text" class="form-input form-input-md form-input-success" value="john@example.com">
                <p class="form-success-text">Email tersedia!</p>
            </div>

            <div class="form-group">
                <label class="form-label">Disabled State</label>
                <input type="text" class="form-input form-input-md" value="Tidak bisa diubah" disabled>
            </div>

            {{-- Select --}}
            <div class="ds-row-label mt-4">Select / Dropdown</div>

            <div class="form-group">
                <label class="form-label">Small Select</label>
                <select class="form-select form-select-sm">
                    <option>Pilih opsi...</option>
                    <option>Opsi A</option>
                    <option>Opsi B</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label form-label-required">Medium Select</label>
                <select class="form-select form-select-md">
                    <option>Pilih kategori...</option>
                    <option>Teknik Informatika</option>
                    <option>Sistem Informasi</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Large Select</label>
                <select class="form-select form-select-lg">
                    <option>Pilih program...</option>
                    <option>S1 Reguler</option>
                    <option>S1 Karyawan</option>
                </select>
            </div>

            {{-- Textarea --}}
            <div class="ds-row-label mt-4">Textarea</div>

            <div class="form-group">
                <label class="form-label">Small Textarea</label>
                <textarea class="form-textarea form-textarea-sm" placeholder="Catatan singkat..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label form-label-required">Medium Textarea</label>
                <textarea class="form-textarea form-textarea-md" placeholder="Deskripsi lengkap..."></textarea>
                <p class="form-helper">Maksimal 500 karakter.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Large Textarea</label>
                <textarea class="form-textarea form-textarea-lg" placeholder="Tulis konten panjang di sini..."></textarea>
            </div>

        </div>
    </section>

    {{-- ═══ USAGE GUIDE ═══ --}}
    <section class="ds-section">
        <h2>📖 Cara Penggunaan</h2>
        <p class="sub">Salin kelas CSS berikut ke Blade template Anda.</p>

        <div style="background:#0f172a;color:#e2e8f0;border-radius:.75rem;padding:1.5rem;font-family:monospace;font-size:.8rem;line-height:1.8;overflow-x:auto">
            <span style="color:#94a3b8">{{-- ★ Buttons --}}</span><br>
            &lt;button class="<span style="color:#60a5fa">btn btn-primary btn-md</span>"&gt;Simpan&lt;/button&gt;<br>
            &lt;button class="<span style="color:#60a5fa">btn btn-secondary btn-sm</span>"&gt;Batal&lt;/button&gt;<br>
            &lt;button class="<span style="color:#60a5fa">btn btn-danger btn-md</span>"&gt;Hapus&lt;/button&gt;<br>
            &lt;button class="<span style="color:#60a5fa">btn btn-outline btn-lg</span>"&gt;Detail&lt;/button&gt;<br><br>

            <span style="color:#94a3b8">{{-- ★ Inputs --}}</span><br>
            &lt;input class="<span style="color:#60a5fa">form-input form-input-md</span>" /&gt;<br>
            &lt;input class="<span style="color:#60a5fa">form-input form-input-md form-input-error</span>" /&gt;<br>
            &lt;select class="<span style="color:#60a5fa">form-select form-select-md</span>"&gt;...&lt;/select&gt;<br>
            &lt;textarea class="<span style="color:#60a5fa">form-textarea form-textarea-md</span>"&gt;&lt;/textarea&gt;<br><br>

            <span style="color:#94a3b8">{{-- ★ Labels & Helpers --}}</span><br>
            &lt;label class="<span style="color:#60a5fa">form-label form-label-required</span>"&gt;Nama&lt;/label&gt;<br>
            &lt;p class="<span style="color:#60a5fa">form-error-text</span>"&gt;Wajib diisi&lt;/p&gt;<br>
            &lt;p class="<span style="color:#60a5fa">form-helper</span>"&gt;Petunjuk isian&lt;/p&gt;<br><br>

            <span style="color:#94a3b8">{{-- ★ Tailwind color classes --}}</span><br>
            &lt;div class="<span style="color:#60a5fa">bg-primary-600 text-white</span>"&gt;...&lt;/div&gt;<br>
            &lt;div class="<span style="color:#60a5fa">bg-secondary-100 text-secondary-700</span>"&gt;...&lt;/div&gt;
        </div>
    </section>

</body>
</html>
