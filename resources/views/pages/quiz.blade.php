@extends('layouts.public')

@section('title', 'Quiz Aether: Cek Kesiapan Riset & Rekomendasi Solusi Data | Aether Data Lab')
@section('meta_description', 'Ikuti Quiz Interaktif Aether Data Lab untuk mengetahui sejauh mana kesiapan skripsi/tesis kamu, kendala metodologi yang dihadapi, dan rekomendasi solusi olah data yang tepat.')
@section('meta_keywords', 'quiz riset, tes kesiapan skripsi, diagnosis olah data, evaluasi tesis, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Quiz Aether</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-[#F6C75A] text-xs font-bold border border-amber-400/30 mb-4">
            ⏱️ 2 Menit Asesmen Mandiri
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Quiz Diagnosis Kesiapan Riset</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Jawab 5 pertanyaan singkat di bawah ini untuk mendeteksi titik macet penelitianmu dan temukan solusi bimbingan yang paling presisi.
        </p>
    </div>
</section>

<!-- Interactive Quiz Container -->
<section class="py-16 bg-[#F8FBFE]" id="quizSection">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xl relative overflow-hidden">
            <!-- Progress Bar -->
            <div id="quizStage">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                    <span>Pertanyaan <span id="quizCurrent" class="text-[#2B8BD1]">1</span> dari 5</span>
                    <span>Skor: <span id="quizScore" class="text-[#F29B3A]">0</span></span>
                </div>
                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-6">
                    <div id="quizProgress" class="bg-gradient-to-r from-[#2B8BD1] to-[#F29B3A] h-full transition-all duration-300" style="width: 20%;"></div>
                </div>

                <h2 id="quizQuestion" class="text-xl sm:text-2xl font-bold text-[#102654] min-h-[60px] mb-6">
                    Loading pertanyaan...
                </h2>

                <div id="answerGrid" class="space-y-3">
                    <!-- Option buttons populated by JS -->
                </div>
            </div>

            <!-- Quiz Result (Hidden Initially) -->
            <div id="quizResult" class="hidden text-center py-6">
                <div class="w-20 h-20 rounded-full bg-sky-50 border-2 border-[#2B8BD1] flex items-center justify-center text-4xl mx-auto mb-4" id="resultEmoji">
                    🎯
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#2B8BD1]">Hasil Diagnosis Riset Kamu</span>
                <h3 id="resultTitle" class="text-2xl sm:text-3xl font-extrabold text-[#102654] mt-2">
                    Research Companion
                </h3>
                <div class="inline-block px-4 py-1.5 rounded-full bg-amber-100 text-[#B36A12] text-xs font-bold mt-2">
                    Total Skor Kesiapan: <span id="resultScore">0</span> / 50
                </div>
                <p id="resultDesc" class="text-slate-600 text-sm mt-4 max-w-lg mx-auto leading-relaxed">
                    Hasil analisis menunjukkan kamu sudah memiliki pondasi riset, namun membutuhkan partner bimbingan intensif untuk eksekusi olah data, interpretasi bab 4, dan persiapan ujian sidang.
                </p>

                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a id="waResultBtn" href="#" target="_blank" class="w-full sm:w-auto px-8 py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow-md hover:opacity-95">
                        Konsultasi Hasil Quiz via WhatsApp
                    </a>
                    <button onclick="restartQuiz()" class="w-full sm:w-auto px-6 py-3 rounded-2xl font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition text-sm">
                        Ulangi Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const aetherQuiz = [
  {
    q: "Kamu sekarang paling sering stuck di bagian mana?",
    a: [
      ["Topik & rumusan masalah", "Masih bingung arah riset dan novelty.", 10, "starter"],
      ["Metodologi & instrumen", "Sudah ada topik, tapi desain riset & kuesioner belum mantap.", 10, "starter"],
      ["Olah data & interpretasi bab 4", "Data ada, tapi hasil output bikin pusing.", 10, "companion"],
      ["Artikel & publikasi jurnal", "Naskah sudah ada, tinggal mau dimatangkan dan submit.", 10, "publish"]
    ]
  },
  {
    q: "Seberapa jelas target penelitianmu saat ini?",
    a: [
      ["Masih samar banget", "Butuh banyak arahan dari awal.", 6, "starter"],
      ["Lumayan jelas", "Tapi masih sering berubah setelah revisi dosen.", 8, "starter"],
      ["Sudah jelas", "Butuh pendampingan intensif untuk eksekusi olah data.", 10, "companion"],
      ["Sudah sampai tahap artikel", "Fokus ke kualitas naskah & LoA jurnal.", 10, "publish"]
    ]
  },
  {
    q: "Kalau ketemu output statistik atau hasil coding software, reaksimu?",
    a: [
      ["Blank 😵", "Butuh penjelasan pelan-pelan sampai paham logikanya.", 7, "companion"],
      ["Paham sedikit", "Tapi belum yakin cara menafsirkannya ke narasi bab 4.", 9, "companion"],
      ["Lumayan paham", "Butuh second opinion & simulasi uji hipotesis.", 10, "companion"],
      ["Data bukan masalahku", "Aku lebih fokus ke penulisin jurnal & proofreading.", 8, "publish"]
    ]
  },
  {
    q: "Gaya pendampingan yang paling kamu butuhkan?",
    a: [
      ["Checklist & panduan terstruktur", "Aku suka tahu step-by-step yang jelas.", 9, "starter"],
      ["Diskusi 1:1 langsung", "Aku pengin bisa banyak tanya tanpa dihakimi.", 10, "companion"],
      ["Review hasil & bedah bab 4", "Aku butuh evaluasi kritis sebelum maju sidang.", 10, "companion"],
      ["Proofreading & perbaikan naskah", "Aku butuh naskah rapi standar editor jurnal.", 10, "publish"]
    ]
  },
  {
    q: "Output target yang paling ingin kamu capai dalam waktu dekat?",
    a: [
      ["Proposal Bab 1-3 di-ACC", "Biar bisa segera ambil data penelitian.", 8, "starter"],
      ["Analisis data & Bab 4 tuntas", "Biar bisa lanjut ke kesimpulan dan daftar sidang.", 10, "companion"],
      ["Lulus sidang dengan nilai A", "Biar siap menjawab semua pertanyaan penguji.", 10, "companion"],
      ["Artikel lolos SINTA / Scopus", "Biar syarat yudisium/wisuda terpenuhi.", 10, "publish"]
    ]
  }
];

let qi = 0, qscore = 0;
const buckets = {starter: 0, companion: 0, publish: 0};
const qEl = document.getElementById("quizQuestion");
const aEl = document.getElementById("answerGrid");
const curEl = document.getElementById("quizCurrent");
const progEl = document.getElementById("quizProgress");
const scoreEl = document.getElementById("quizScore");
const stageEl = document.getElementById("quizStage");
const resultEl = document.getElementById("quizResult");

function renderQuiz() {
  const item = aetherQuiz[qi];
  qEl.textContent = item.q;
  curEl.textContent = qi + 1;
  progEl.style.width = ((qi + 1) / aetherQuiz.length * 100) + "%";
  aEl.innerHTML = "";
  
  const symbols = ["▲", "◆", "●", "■"];
  item.a.forEach((opt, idx) => {
    const b = document.createElement("button");
    b.className = "w-full text-left p-4 rounded-2xl border border-slate-200 hover:border-[#2B8BD1] hover:bg-sky-50 transition group flex items-start gap-3";
    b.innerHTML = `
      <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-[#2B8BD1] group-hover:text-white font-bold text-xs shrink-0 transition">
        ${symbols[idx]}
      </span>
      <div>
        <div class="font-bold text-sm text-[#102654] group-hover:text-[#1768B5]">${opt[0]}</div>
        <div class="text-xs text-slate-500 mt-0.5">${opt[1]}</div>
      </div>
    `;
    b.onclick = () => {
      qscore += opt[2];
      buckets[opt[3]]++;
      scoreEl.textContent = qscore;
      qi++;
      if (qi < aetherQuiz.length) {
        renderQuiz();
      } else {
        finishQuiz();
      }
    };
    aEl.appendChild(b);
  });
}

function finishQuiz() {
  stageEl.classList.add("hidden");
  resultEl.classList.remove("hidden");
  document.getElementById("resultScore").textContent = qscore;
  const winner = Object.entries(buckets).sort((a, b) => b[1] - a[1])[0][0];
  const map = {
    starter: {
      emoji: "🧭",
      title: "Research Starter",
      desc: "Kamu paling butuh fondasi riset yang jelas dulu: topik, research gap, rumusan masalah, metodologi, dan desain instrumen. Mulai bimbingan dari struktur awal proposal."
    },
    companion: {
      emoji: "🚀",
      title: "Research Companion",
      desc: "Kamu sudah memiliki konsep, namun butuh partner bimbingan intensif untuk eksekusi olah data, interpretasi output bab 4, dan simulasi tanya-jawab persiapan ujian sidang."
    },
    publish: {
      emoji: "📚",
      title: "Publication Ready",
      desc: "Riset kamu sudah berjalan jauh. Fokus terbaikmu sekarang adalah merapikan naskah, academic proofreading, formatting author guidelines, dan submission ke jurnal SINTA/Scopus."
    }
  };

  const res = map[winner] || map.companion;
  document.getElementById("resultEmoji").textContent = res.emoji;
  document.getElementById("resultTitle").textContent = res.title;
  document.getElementById("resultDesc").textContent = res.desc;

  const waMsg = encodeURIComponent(`Halo Aether Data Lab, saya sudah menyelesaikan Quiz Kesiapan Riset dengan skor ${qscore}/50 (Kategori: ${res.title}). Saya ingin konsultasi lanjutan.`);
  document.getElementById("waResultBtn").href = `https://wa.me/6285745143123?text=${waMsg}`;
}

function restartQuiz() {
  qi = 0;
  qscore = 0;
  buckets.starter = 0;
  buckets.companion = 0;
  buckets.publish = 0;
  scoreEl.textContent = "0";
  resultEl.classList.add("hidden");
  stageEl.classList.remove("hidden");
  renderQuiz();
}

document.addEventListener("DOMContentLoaded", renderQuiz);
</script>
@endsection
