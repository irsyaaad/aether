@extends('layouts.public')

@section('title', $article['title'] . ' | Aether Data Lab')
@section('meta_description', Str::limit(strip_tags($article['summary']), 155))
@section('meta_keywords', $article['category'] . ', metodologi riset, tutorial statistik, olah data skripsi tesis, aether data lab')
@section('og_type', 'article')
@section('og_image', $article['image'])

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
          'name' => 'Wawasan Riset',
          'item' => url('/berita'),
        ],
        [
          '@type' => 'ListItem',
          'position' => 3,
          'name' => $article['title'],
          'item' => route('landing.berita.detail', $article['slug']),
        ],
      ],
    ],
    [
      '@type' => 'NewsArticle',
      '@id' => route('landing.berita.detail', $article['slug']) . '#article',
      'headline' => $article['title'],
      'description' => $article['summary'],
      'image' => [
        $article['image']
      ],
      'datePublished' => '2026-08-15T08:00:00+07:00',
      'dateModified' => '2026-09-14T10:00:00+07:00',
      'author' => [
        '@type' => 'Person',
        'name' => $article['author'],
      ],
      'publisher' => [
        '@type' => 'Organization',
        'name' => 'Aether Data Lab',
        'logo' => [
          '@type' => 'ImageObject',
          'url' => asset('images/logo_aether.jpeg'),
        ],
      ],
      'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('landing.berita.detail', $article['slug']),
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<article class="py-12 lg:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb Nav -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-[#1768B5] transition">Beranda</a>
            <span>/</span>
            <a href="{{ url('/berita') }}" class="hover:text-[#1768B5] transition">Wawasan Riset</a>
            <span>/</span>
            <span class="text-slate-900 truncate max-w-[200px] sm:max-w-md">{{ $article['category'] }}</span>
        </nav>

        <!-- Category Tag -->
        <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-[#1768B5] mb-4">
            {{ $article['category'] }}
        </span>

        <!-- Title -->
        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-[#102654] leading-tight tracking-tight">
            {{ $article['title'] }}
        </h1>

        <!-- Meta Bar -->
        <div class="flex items-center gap-4 text-xs text-slate-500 py-6 my-6 border-y border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#1768B5] to-[#58B7EA] flex items-center justify-center text-white font-bold text-xs">
                    {{ substr($article['author'], 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-slate-800">{{ $article['author'] }}</div>
                    <div class="text-[11px] text-slate-400">Konsultan Riset Aether</div>
                </div>
            </div>
            <span>•</span>
            <div>{{ $article['date'] }}</div>
            <span>•</span>
            <div>{{ $article['read_time'] }}</div>
        </div>

        <!-- Featured Image -->
        <div class="rounded-3xl overflow-hidden aspect-16/9 shadow-2xl mb-10 border border-slate-100">
            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
        </div>

        <!-- Article Content -->
        <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-4">
            {!! $article['content'] !!}
        </div>

        <!-- Share & WhatsApp Consultation Box -->
        <div class="my-12 p-8 rounded-3xl bg-[#F8FBFE] border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1 text-center sm:text-left">
                <div class="text-sm font-bold text-[#102654]">Mengalami Kendala Serupa pada Data Riset Kamu?</div>
                <div class="text-xs text-slate-500">Diskusikan bersama tim mentor metodologi Aether Data Lab tanpa dipungut biaya diagnosis awal.</div>
            </div>
            <a
                href="https://wa.me/6285745143123?text={{ urlencode('Halo Aether Data Lab, saya membaca artikel "' . $article['title'] . '" dan ingin konsultasi data riset saya.') }}"
                target="_blank"
                class="px-6 py-3 rounded-xl bg-[#2B8BD1] hover:bg-[#1768B5] text-white font-bold text-xs shadow-lg shadow-sky-500/20 whitespace-nowrap transition"
            >
                Konsultasi Topik Ini di WA
            </a>
        </div>

        <!-- Related Articles -->
        @if(count($relatedArticles) > 0)
            <div class="pt-12 border-t border-slate-200">
                <h3 class="text-xl font-bold text-[#102654] mb-6">Artikel Panduan Riset Lainnya</h3>
                <div class="grid sm:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $rel)
                        <div class="group">
                            <a href="{{ route('landing.berita.detail', $rel['slug']) }}" class="block aspect-16/10 rounded-2xl overflow-hidden mb-3 border border-slate-100">
                                <img src="{{ $rel['image'] }}" alt="{{ $rel['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            </a>
                            <div class="text-[11px] font-bold text-[#2B8BD1] mb-1">{{ $rel['category'] }}</div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#1768B5] line-clamp-2 leading-snug">
                                <a href="{{ route('landing.berita.detail', $rel['slug']) }}">{{ $rel['title'] }}</a>
                            </h4>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</article>
@endsection
