<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Data artikel berita dan wawasan riset, metodologi & data analytics Aether Data Lab.
     *
     * @return array
     */
    public static function getArticles(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'panduan-memilih-metode-sem-pls-vs-cb-sem',
                'title' => 'Panduan Metodologi: Memilih Antara SEM-PLS (SmartPLS) vs CB-SEM (AMOS) untuk Uji Hipotesis Skripsi & Tesis',
                'category' => 'Kuantitatif & SEM',
                'date' => '14 September 2026',
                'author' => 'Tim Riset & Metodologi Aether',
                'read_time' => '5 menit baca',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
                'summary' => 'Bingung menentukan software untuk model persamaan struktural? Pelajari kapan harus memakai SmartPLS dengan pendekatan prediktif atau AMOS/LISREL untuk konfirmasi teori dan uji goodness of fit.',
                'content' => '
                    <p class="lead text-lg text-slate-700 font-medium mb-6">
                        Structural Equation Modeling (SEM) menjadi standar emas dalam riset manajemen, psikologi, pendidikan, dan ilmu sosial. Namun banyak mahasiswa dan peneliti terjebak dalam perdebatan antara Partial Least Squares SEM (PLS-SEM) dan Covariance-Based SEM (CB-SEM).
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">1. Karakteristik Data & Ukuran Sampel</h3>
                    <p class="text-slate-600 mb-4">
                        Jika sampel Anda terbatas (di bawah 100 responden) atau data tidak berdistribusi normal multivariat, <strong>PLS-SEM (SmartPLS)</strong> adalah solusi ideal karena bersifat non-parametrik dan berbasis varians. Sebaliknya, bila sampel Anda besar (>200) dan tujuan riset adalah konfirmasi teori ketat, <strong>CB-SEM (AMOS/LISREL)</strong> lebih direkomendasikan.
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">2. Outer Model vs Inner Model</h3>
                    <p class="text-slate-600 mb-4">
                        Pada evaluasi outer model, perhatikan uji validitas konvergen (nilai outer loading &gt; 0.70 dan AVE &gt; 0.50), validitas diskriminan (kriteria Fornell-Larcker atau HTMT rasio &lt; 0.90), serta reliabilitas konstruk menggunakan Cronbach Alpha dan Composite Reliability.
                    </p>
                    <blockquote class="p-4 my-6 border-l-4 border-[#2B8BD1] bg-sky-50 rounded-r-xl italic text-slate-800 font-medium">
                        "Jangan memaksakan software sebelum memahami hakikat model penelitian Anda. Dosen penguji sidang tidak hanya menguji signifikansi p-value, tetapi pemahaman Anda terhadap justifikasi metodologi yang dipilih."
                        <span class="block mt-2 text-xs font-bold uppercase not-italic text-[#1768B5]">— Nur Atika Rochmah, S.Stat., M.Si. (Lead Quantitative Consultant Aether Data Lab)</span>
                    </blockquote>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">3. Tips Menghadapi Sidang Ujian</h3>
                    <p class="text-slate-600 mb-4">
                        Di Aether Data Lab, pendampingan tidak berhenti pada keluaran tabel output software. Kami membimbing Anda membaca matriks koefisien jalur, f-squared effect size, Q-squared predictive relevance, hingga simulasi tanya-jawab bab 4 bersama mentor.
                    </p>
                '
            ],
            [
                'id' => 2,
                'slug' => 'strategi-lolos-uji-asumsi-klasik-regresi-linier-berganda',
                'title' => 'Strategi Mengatasi Data Tidak Normal & Heteroskedastisitas pada Uji Asumsi Klasik Regresi',
                'category' => 'Ekonometrika & Regresi',
                'date' => '08 September 2026',
                'author' => 'Divisi Analisis Statistik',
                'read_time' => '4 menit baca',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                'summary' => 'Data Anda tidak lolos uji Kolmogorov-Smirnov atau uji Glejser? Temukan teknik transformasi data (Log, Ln, Sqrt), eliminasi outlier berbasis Mahalanobis, serta pendekatan robust standard errors yang sah secara ilmiah.',
                'content' => '
                    <p class="lead text-lg text-slate-700 font-medium mb-6">
                        Salah satu momok terbesar saat menyusun bab 4 skripsi kuantitatif adalah hasil uji asumsi klasik yang gagal memenuhi syarat: residual tidak berdistribusi normal, timbul multikolinearitas (VIF &gt; 10), atau pola scatterplot memperlihatkan gejala heteroskedastisitas.
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">Mengapa Manipulasi Data Adalah Kesalahan Fatal?</h3>
                    <p class="text-slate-600 mb-4">
                        Mengubah angka secara manual tanpa dasar metodologis sangat mudah terdeteksi penguji dan melanggar integritas akademik. Terdapat metode ilmiah yang diakui secara luas oleh literatur statistik:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-slate-600 mb-4 ml-2">
                        <li><strong>Deteksi & Penanganan Outlier:</strong> Identifikasi data ekstrem menggunakan Boxplot, Z-Score (&gt; 3.0), atau Cook’s Distance untuk menjustifikasi pemangkasan observasi pencilan.</li>
                        <li><strong>Transformasi Variabel:</strong> Terapkan transformasi matematis seperti Natural Logarithm (Ln), Square Root, atau Box-Cox transformation untuk meratakan sebaran skewness.</li>
                        <li><strong>Estimasi Alternatif:</strong> Jika heteroskedastisitas tetap ada, gunakan teknik Generalized Least Squares (GLS) atau White-Huber Robust Standard Errors di SPSS/EViews/Stata.</li>
                    </ul>
                '
            ],
            [
                'id' => 3,
                'slug' => 'analisis-tematik-kualitatif-menggunakan-nvivo-atlas-ti',
                'title' => 'Membangun Coding Tree Riset Kualitatif: Panduan Analisis Tematik Menggunakan NVivo & ATLAS.ti',
                'category' => 'Kualitatif & Tematik',
                'date' => '29 Agustus 2026',
                'author' => 'Tim Konsultan Kualitatif',
                'read_time' => '5 menit baca',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80',
                'summary' => 'Mengolah data wawancara mendalam, FGD, dan dokumen studi kasus secara sistematis. Pelajari tahapan open coding, axial coding, hingga penyusunan model konseptual tematik.',
                'content' => '
                    <p class="lead text-lg text-slate-700 font-medium mb-6">
                        Penelitian kualitatif bukan sekadar merangkum kutipan informan. Analisis data kualitatif membutuhkan ketelitian interpretasi melalui pengodean terstruktur yang dapat dipertanggungjawabkan derajat keabsahannya (triangulasi & audit trail).
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">Tahapan Pengodean Tematik Braun & Clarke (2006)</h3>
                    <p class="text-slate-600 mb-4">
                        Dengan bantuan Computer-Assisted Qualitative Data Analysis Software (CAQDAS) seperti NVivo atau ATLAS.ti, transkrip rekaman dianalisis melalui tahapan:
                    </p>
                    <ol class="list-decimal list-inside space-y-3 text-slate-600 mb-4 ml-2">
                        <li><strong>Familiarisasi Data:</strong> Membaca berulang transkrip dan membuat catatan reflektif (analytic memos).</li>
                        <li><strong>Initial Coding (Open Coding):</strong> Memberikan label kode pada unit makna penting dari jawaban narasumber.</li>
                        <li><strong>Mencari Tema (Axial Coding):</strong> Mengelompokkan kode-kode yang saling beririsan menjadi kategori tema induk.</li>
                        <li><strong>Visualisasi Konseptual:</strong> Menyajikan matriks perbandingan lintas informan, diagram lingkaran tema, dan hierarki kata.</li>
                    </ol>
                '
            ],
            [
                'id' => 4,
                'slug' => 'uji-kelayakan-instrumen-validitas-reliabilitas-kuesioner',
                'title' => 'Uji Validitas & Reliabilitas Kuesioner: Menghindari Butir Gugur Sebelum Pengumpulan Data Penuh',
                'category' => 'Instrumen & Skala',
                'date' => '18 Agustus 2026',
                'author' => 'Divisi Pengukuran & Psikometri',
                'read_time' => '4 menit baca',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80',
                'summary' => 'Panduan pilot test kuesioner kepada 30 responden. Analisis Corrected Item-Total Correlation (r-hitung vs r-tabel), Cronbach Alpha di atas 0.70, dan validitas isi (Content Validity Index / Aiken V).',
                'content' => '
                    <p class="lead text-lg text-slate-700 font-medium mb-6">
                        Sebelum menyebarkan ratusan instrumen angket atau survei online kepada target responden sesungguhnya, uji coba awal (pilot study) terhadap 30 responden wajib dilakukan untuk memastikan alat ukur valid dan reliabel.
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 mt-8 mb-4">Kriteria Penilaian Kuesioner Riset</h3>
                    <ul class="list-disc list-inside space-y-2 text-slate-600 mb-4 ml-2">
                        <li><strong>Validitas Butir (Product Moment Pearson):</strong> Bandingkan nilai r-hitung dengan r-tabel pada signifikansi 5%. Butir dinyatakan valid bila r-hitung &gt; r-tabel.</li>
                        <li><strong>Uji Reliabilitas (Cronbach’s Alpha):</strong> Pastikan koefisien reliabilitas &gt; 0.70. Jika terdapat butir yang menurunkan alpha (Alpha if Item Deleted lebih tinggi), pertimbangkan untuk merevisi atau menggugurkan indikator tersebut.</li>
                        <li><strong>Validitas Isi Expert Judgment:</strong> Libatkan pakar di bidangnya untuk menghitung koefisien Aiken’s V guna memastikan setiap butir mengukur indikator teori yang tepat.</li>
                    </ul>
                '
            ]
        ];
    }

    /**
     * Data paket layanan bimbingan & olah data riset Aether Data Lab.
     *
     * @return array
     */
    public static function getServices(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'kuantitatif',
                'title' => 'Olah Data Kuantitatif & Ekonometrika',
                'category' => 'Kuantitatif',
                'badge_meta' => 'Garansi Re-run',
                'coverage' => 'SPSS, EViews, Stata, R',
                'highlight' => 'Regresi, Data Panel, Time Series',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&h=500&q=80',
                'summary' => 'Analisis data statistik mulai dari uji asumsi klasik, regresi berganda, data panel FEM/REM/CEM, analisis jalur (Path Analysis), hingga regresi logistik lengkap dengan interpretasi output.',
                'link' => url('/layanan#kuantitatif'),
            ],
            [
                'id' => 2,
                'slug' => 'sem-pls',
                'title' => 'Pemodelan SEM-PLS & CB-SEM',
                'category' => 'Structural Modeling',
                'badge_meta' => 'Model Kompleks',
                'coverage' => 'SmartPLS, AMOS, LISREL',
                'highlight' => 'Mediasi, Moderasi & Second-Order',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&h=500&q=80',
                'summary' => 'Spesialisasi pemodelan persamaan struktural dengan mediasi, moderasi, model hierarkis (higher-order), uji multigroup (MGA), dan evaluasi kecocokan model (Goodness of Fit).',
                'link' => url('/layanan#sem-pls'),
            ],
            [
                'id' => 3,
                'slug' => 'kualitatif',
                'title' => 'Analisis Kualitatif & Mixed Methods',
                'category' => 'Kualitatif',
                'badge_meta' => 'Studi Kasus & Fenomenologi',
                'coverage' => 'NVivo, ATLAS.ti, MAXQDA',
                'highlight' => 'Thematic Coding & Triangulasi',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&h=500&q=80',
                'summary' => 'Reduksi data wawancara, focus group discussion (FGD), studi dokumen, pembuatan coding tree, peta tematik, serta triangulasi sumber dan metode riset gabungan.',
                'link' => url('/layanan#kualitatif'),
            ],
            [
                'id' => 4,
                'slug' => 'bimbingan-sidang',
                'title' => 'Bimbingan 1-on-1 & Simulasi Sidang',
                'category' => 'Mentorship',
                'badge_meta' => 'Sampai Paham',
                'coverage' => 'Private Online / Offline',
                'highlight' => 'Bedah Bab 4 & Tanya Jawab',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&h=500&q=80',
                'summary' => 'Sesi privat intensif bersama konsultan metodologi untuk membedah cara membaca tabel output, menyusun narasi pembahasan bab 4, dan simulasi menjawab pertanyaan penguji.',
                'link' => url('/layanan#bimbingan-sidang'),
            ],
        ];
    }

    /**
     * Tampilkan halaman utama resmi Aether Data Lab.
     */
    public function index()
    {
        $featuredArticles = array_slice(self::getArticles(), 0, 3);
        $featuredServices = self::getServices();

        return view('landing', compact('featuredArticles', 'featuredServices'));
    }

    /**
     * Halaman khusus Layanan Riset & Olah Data.
     */
    public function layanan()
    {
        $services = self::getServices();
        return view('pages.layanan', compact('services'));
    }

    /**
     * Halaman khusus Keunggulan & Metodologi Aether.
     */
    public function keunggulan()
    {
        return view('pages.keunggulan');
    }

    /**
     * Halaman indeks Berita & Wawasan Riset Ilmiah.
     */
    public function berita(Request $request)
    {
        $allArticles = self::getArticles();
        $categories = array_unique(array_column($allArticles, 'category'));
        $selectedCategory = $request->query('kategori');
        $searchQuery = $request->query('q');

        $articles = array_filter($allArticles, function ($item) use ($selectedCategory, $searchQuery) {
            $matchCategory = true;
            $matchSearch = true;

            if ($selectedCategory && strtolower($selectedCategory) !== 'semua') {
                $matchCategory = (strtolower($item['category']) === strtolower($selectedCategory));
            }

            if ($searchQuery) {
                $q = strtolower($searchQuery);
                $matchSearch = (str_contains(strtolower($item['title']), $q) || str_contains(strtolower($item['summary']), $q));
            }

            return $matchCategory && $matchSearch;
        });

        return view('pages.berita', compact('articles', 'categories', 'selectedCategory', 'searchQuery'));
    }

    /**
     * Halaman detail artikel berita dan wawasan riset.
     */
    public function beritaDetail($slug)
    {
        $allArticles = self::getArticles();
        $article = null;

        foreach ($allArticles as $item) {
            if ($item['slug'] === $slug) {
                $article = $item;
                break;
            }
        }

        if (!$article) {
            abort(404);
        }

        $relatedArticles = array_filter($allArticles, function ($item) use ($slug) {
            return $item['slug'] !== $slug;
        });
        $relatedArticles = array_slice($relatedArticles, 0, 3);

        return view('pages.berita-detail', compact('article', 'relatedArticles'));
    }
}
