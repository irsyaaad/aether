@extends('layouts.public')

@section('title', 'Wawasan & Artikel Metodologi Riset, Olah Data | Aether Data Lab')
@section('meta_description', 'Kumpulan artikel panduan statistik, tutorial SmartPLS, SPSS, AMOS, NVivo, tips bab 4 skripsi, serta strategi menjawab pertanyaan penguji sidang tugas akhir.')
@section('meta_keywords', 'artikel statistik, tutorial smartpls, panduan spss skripsi, tips uji hipotesis, metodologi penelitian, aether data lab')
@section('og_image', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&h=630&q=80')

@push('schema_json')
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        [
          '@type' => 'ListItem',
          'position' => 1,
          'name' => 'Beranda',
          'item' => url('/'),
        ],
        [
          '@type' => 'ListItem',
          'position' => 2,
          'name' => 'Wawasan & Artikel Riset',
          'item' => url('/berita'),
        ],
      ],
    ],
    [
      '@type' => 'CollectionPage',
      '@id' => url('/berita') . '#collection',
      'name' => 'Wawasan Riset, Statistik & Panduan Olah Data Aether Data Lab',
      'url' => url('/berita'),
      'description' => 'Publikasi panduan metodologi riset ilmiah, tutorial statistik terapan, dan tips sukses ujian sidang skripsi dan tesis.',
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<!-- Hero Section Berita -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="absolute right-0 top-0 w-96 h-96 rounded-full bg-[#58B7EA]/20 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Wawasan &amp; Artikel</span>
        </nav>

        <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
            Wawasan Riset &amp; <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F6C75A] via-[#58B7EA] to-white">Panduan Olah Data</span>
        </h1>
        <p class="mt-4 text-base sm:text-lg text-sky-100 max-w-2xl mx-auto">
            Pelajari teknik pengujian hipotesis, tutorial software riset (SPSS, SmartPLS, NVivo), dan strategi menghadapi pertanyaan penguji sidang tugas akhir.
        </p>

        <!-- Search Bar -->
        <div class="mt-8 max-w-xl mx-auto">
            <form action="{{ route('landing.berita') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="q"
                    value="{{ $searchQuery ?? '' }}"
                    placeholder="Cari topik: SEM-PLS, Uji Asumsi Klasik, Kualitatif, NVivo..."
                    class="w-full pl-5 pr-28 py-3.5 rounded-2xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-300 shadow-xl text-sm"
                />
                <button
                    type="submit"
                    class="absolute right-2 top-2 bottom-2 px-5 rounded-xl bg-[#102654] hover:bg-[#1768B5] text-white font-bold text-xs transition"
                >
                    Cari Topik
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Kategori Filter -->
<section class="py-6 bg-[#F8FBFE] border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a
                href="{{ route('landing.berita') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ !$selectedCategory || strtolower($selectedCategory) === 'semua' ? 'bg-[#1768B5] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
            >
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a
                    href="{{ route('landing.berita', ['kategori' => $cat]) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ strtolower($selectedCategory ?? '') === strtolower($cat) ? 'bg-[#1768B5] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                >
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- List Artikel -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(count($articles) > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $art)
                    <article class="flex flex-col bg-[#F8FBFE] rounded-3xl overflow-hidden border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-200 group">
                        <a href="{{ route('landing.berita.detail', $art['slug']) }}" class="relative block overflow-hidden aspect-16/10">
                            <img
                                src="{{ $art['image'] }}"
                                alt="{{ $art['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                loading="lazy"
                            />
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#102654]/90 text-[#F6C75A] backdrop-blur-sm border border-white/20">
                                {{ $art['category'] }}
                            </span>
                        </a>

                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
                                <span>{{ $art['date'] }}</span>
                                <span>•</span>
                                <span>{{ $art['read_time'] }}</span>
                            </div>

                            <h2 class="text-lg font-bold text-[#102654] group-hover:text-[#2B8BD1] transition line-clamp-2 leading-snug">
                                <a href="{{ route('landing.berita.detail', $art['slug']) }}">
                                    {{ $art['title'] }}
                                </a>
                            </h2>

                            <p class="text-xs sm:text-sm text-slate-600 mt-2 line-clamp-3 leading-relaxed flex-1">
                                {{ $art['summary'] }}
                            </p>

                            <div class="pt-4 mt-4 border-t border-slate-200 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">{{ $art['author'] }}</span>
                                <a href="{{ route('landing.berita.detail', $art['slug']) }}" class="text-xs font-bold text-[#1768B5] group-hover:translate-x-1 transition flex items-center gap-1">
                                    <span>Baca Selengkapnya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-[#F8FBFE] rounded-3xl border border-dashed border-slate-300 max-w-xl mx-auto">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="text-lg font-bold text-[#102654]">Topik Tidak Ditemukan</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Tidak ada artikel yang cocok dengan kata kunci pencarian Anda. Coba kata kunci lain atau reset filter kategori.
                </p>
                <div class="mt-4">
                    <a href="{{ route('landing.berita') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-[#1768B5] text-white hover:bg-[#102654] transition">
                        Reset Pencarian
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
