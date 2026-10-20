<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<title>Aether Data Lab — Data &amp; Research Analytics</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo_aether.png') }}">
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<style>
:root{
  --navy:#102654; --navy2:#1b3591; --blue:#2f6fd6; --purple:#6a4cff;
  --yellow:#ffd24c; --mint:#A7F3D0; --pink:#ff83b7; --bg:#f7f8fc;
  --line:#e8ebf4; --text:#1f2a44; --muted:#6b748b; --white:#fff;
  --radius:24px; --shadow:0 18px 45px rgba(31,42,68,.10);
  --aether-blue:#2B8BD1;
  --aether-blue2:#58B7EA;
  --aether-deep:#1768B5;
  --aether-gold:#F6C75A;
  --aether-orange:#F29B3A;
  --aether-ivory:#FFFDF8;
  --aether-cream:#FBF3E5;
}
*{box-sizing:border-box}
body{
  margin:0;
  font-family:"Plus Jakarta Sans",sans-serif;
  background:#F8FBFE !important;
  color:#17324D !important;
}
a{color:inherit;text-decoration:none}
img{display:block;max-width:100%}
.wrap{max-width:1180px;margin:auto;padding:0 24px}

.top-testi{
  background:linear-gradient(90deg,#2B8BD1,#58B7EA 55%,#F2A23A) !important;
  color:#fff !important;
  font-size:13px;
  overflow:hidden;
}
.top-testi .wrap{display:flex;align-items:center;gap:16px;min-height:38px;white-space:nowrap}
.top-testi .tag{
  background:#FFF5D8 !important;
  color:#B36A12 !important;
  padding:5px 16px;
  border-radius:999px;
  font-weight:800;
}
.top-testi .scroll{overflow:hidden;flex:1}
.top-testi .move{display:inline-block;padding-left:100%;animation:mar 28s linear infinite}
@keyframes mar{0%{transform:translateX(0)}100%{transform:translateX(-100%)}}

header{
  background:rgba(255,255,255,.94) !important;
  position:sticky;
  top:0;
  z-index:50;
  border-bottom:1px solid #E5EDF5 !important;
  backdrop-filter:blur(16px);
}
.nav1{display:flex;align-items:center;gap:18px;padding:14px 0 10px}
.brand-link{
  display:flex;
  align-items:center;
  gap:10px;
  text-decoration:none;
}
.brand-logo-img{
  height:48px;
  width:auto;
  object-fit:contain;
  display:block;
  transition:transform 0.2s ease;
}
.brand-link:hover .brand-logo-img{
  transform:scale(1.02);
}
.search{flex:1;display:flex;gap:10px;max-width:540px;margin:0 auto}
.search input{
  flex:1;
  border:1.5px solid #CBD5E1;
  border-radius:999px;
  height:42px;
  padding:0 18px;
  font-size:14px;
  outline:none;
  background:#F8FAFC;
  font-family:inherit;
}
.search input:focus{
  border-color:#2B8BD1;
  background:#fff;
  box-shadow:0 0 0 3px rgba(43,139,209,0.15);
}
.search button{
  height:42px;
  padding:0 22px;
  border:none;
  border-radius:999px;
  background:#102654;
  color:#fff;
  font-weight:800;
  cursor:pointer;
  transition:background 0.2s ease;
}
.search button:hover{
  background:#1b3591;
}
.auth-actions{margin-left:auto;display:flex;align-items:center;gap:10px}
.nav2{
  display:flex;
  justify-content:space-between;
  align-items:center;
  width:100%;
  padding:12px 0 14px;
  font-size:14px;
  border-top:1px solid #F1F5F9;
  position:relative;
}
.nav2 > a{
  font-weight:600;
  color:#334155;
  transition:color 0.2s ease;
  text-decoration:none;
  padding:6px 2px;
  white-space:nowrap;
}
.nav2 > a:hover{
  color:var(--aether-blue);
}

.nav2-dropdown{
  position:relative;
  display:inline-flex;
  align-items:center;
  padding:4px 0;
}
.nav2-dropdown-toggle{
  display:inline-flex;
  align-items:center;
  gap:5px;
  font-weight:600;
  color:#334155;
  background:transparent;
  border:none;
  padding:6px 6px;
  border-radius:8px;
  font-size:14px;
  font-family:inherit;
  cursor:pointer;
  transition:all 0.2s ease;
  white-space:nowrap;
}
.nav2-dropdown-toggle:hover,
.nav2-dropdown.is-open .nav2-dropdown-toggle{
  color:var(--aether-blue);
  background:rgba(43,139,209,0.08);
}
.nav2-dropdown-toggle svg{
  transition:transform 0.2s ease;
}
.nav2-dropdown:hover .nav2-dropdown-toggle svg,
.nav2-dropdown.is-open .nav2-dropdown-toggle svg{
  transform:rotate(180deg);
}

/* Dropdown Menu with zero-gap hitbox bridge */
.nav2-dropdown-menu{
  position:absolute;
  top:100%;
  left:0;
  margin-top:2px;
  min-width:275px;
  background:#ffffff;
  border:1px solid #E2E8F0;
  border-radius:14px;
  box-shadow:0 18px 40px rgba(15,23,42,0.14);
  padding:8px 0;
  opacity:0;
  visibility:hidden;
  transform:translateY(6px);
  transition:opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
  pointer-events:none;
  z-index:1000;
}
/* Invisible hover bridge between toggle and menu */
.nav2-dropdown-menu::before{
  content:"";
  position:absolute;
  top:-14px;
  left:0;
  right:0;
  height:16px;
  background:transparent;
}
.nav2-dropdown:hover .nav2-dropdown-menu,
.nav2-dropdown.is-open .nav2-dropdown-menu{
  opacity:1;
  visibility:visible;
  transform:translateY(0);
  pointer-events:auto;
}
.nav2-dropdown-menu a{
  display:block;
  padding:10px 18px;
  font-size:13.5px;
  color:#334155;
  font-weight:500;
  transition:all 0.15s ease;
  white-space:nowrap;
  text-decoration:none;
}
.nav2-dropdown-menu a:hover{
  background:#F1F5F9;
  color:var(--aether-blue);
  padding-left:22px;
}

/* HERO */
.hero{
  background:
    linear-gradient(90deg,rgba(255,255,255,.10),rgba(255,255,255,.02)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDkwMCI+CjxkZWZzPgogIDxsaW5lYXJHcmFkaWVudCBpZD0iYmciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI0ZERjlGMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIuMzgiIHN0b3AtY29sb3I9IiNGOEU1QjEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjYyIiBzdG9wLWNvbG9yPSIjRjI5QjNBIi8+CiAgICA8c3RvcCBvZmZzZXQ9Ii43OCIgc3RvcC1jb2xvcj0iIzU4QjdFQSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMTc2OEI1Ii8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8ZmlsdGVyIGlkPSJibHVyIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSIzNCIvPjwvZmlsdGVyPgogIDxsaW5lYXJHcmFkaWVudCBpZD0id2F2ZTEiIHgxPSIwIiB4Mj0iMSI+CiAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiNGNkM3NUEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjUiIHN0b3AtY29sb3I9IiNGMzlBMzUiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iI0ZGRDg3NiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJ3YXZlMiIgeDE9IjAiIHgyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzQ2QjdFQiIvPgogICAgPHN0b3Agb2Zmc2V0PSIuNTUiIHN0b3AtY29sb3I9IiMyMjc5QzYiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzE5NEU5QiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPHJlY3Qgd2lkdGg9IjE2MDAiIGhlaWdodD0iOTAwIiBmaWxsPSJ1cmwoI2JnKSIvPgo8Y2lyY2xlIGN4PSIyNTAiIGN5PSIxNzAiIHI9IjIyMCIgZmlsbD0iI0ZGRTE3RiIgb3BhY2l0eT0iLjU1IiBmaWx0ZXI9InVybCgjYmx1cikiLz4KPGNpcmNsZSBjeD0iMTI1MCIgY3k9IjIwMCIgcj0iMjYwIiBmaWxsPSIjRkY5RjU3IiBvcGFjaXR5PSIuNDUiIGZpbHRlcj0idXJsKCNibHVyKSIvPgo8Y2lyY2xlIGN4PSIxMTIwIiBjeT0iNzYwIiByPSIzNDAiIGZpbGw9IiMxOTkwRDQiIG9wYWNpdHk9Ii41NSIgZmlsdGVyPSJ1cmwoI2JsdXIpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjgwIDUyMCA0MTAgNjYwIDY1MCA1NzUgQzkwNSA0ODUgMTA1MCA1NDAgMTI4NSA0MjUgQzE0MTAgMzY1IDE1MDUgMjkwIDE2MDAgMjI1IEwxNjAwIDkwMCBMMCA5MDBaIiBmaWxsPSJ1cmwoI3dhdmUyKSIgb3BhY2l0eT0iLjk0Ii8+CjxwYXRoIGQ9Ik0wIDU1NSBDMjcwIDQ3MCA0NTAgNTYwIDY3NSA1MjAgQzkyNSA0NzUgMTEzNSA0MDUgMTYwMCAxNjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0idXJsKCN3YXZlMSkiIHN0cm9rZS13aWR0aD0iOTYiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgb3BhY2l0eT0iLjk1Ii8+CjxwYXRoIGQ9Ik0wIDUyMCBDMjkwIDQ0NSA0NzAgNTIwIDcxMCA0NzUgQzk3MCA0MjUgMTE5NSAzNjAgMTYwMCAxMzUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0ZGRjVEOCIgc3Ryb2tlLXdpZHRoPSIyNCIgb3BhY2l0eT0iLjc4Ii8+CjxwYXRoIGQ9Ik0xOTAgMCBDMzkwIDIyMCA1NzAgMjE1IDc0MCAxMzggQzk5MCAyNSAxMTMwIDEyMCAxMzQwIDQwIiBmaWxsPSJub25lIiBzdHJva2U9IiNGRkZGRkYiIHN0cm9rZS13aWR0aD0iMjIiIG9wYWNpdHk9Ii40NSIvPgo8L3N2Zz4=") center/cover no-repeat !important;
  color:#fff !important;
  overflow:hidden;
  position:relative;
}
.hero::before{
  content:"" !important;
  display:block !important;
  position:absolute !important;
  inset:0 !important;
  background:linear-gradient(
    90deg,
    rgba(20,83,138,.78) 0%,
    rgba(28,110,178,.62) 38%,
    rgba(28,110,178,.20) 64%,
    rgba(255,255,255,.02) 100%
  ) !important;
  z-index:0 !important;
}
.hero .wrap{display:grid;grid-template-columns:1.02fr .98fr;gap:20px;align-items:stretch;min-height:580px;position:relative;z-index:2 !important}
.left-hero{padding:42px 0 36px;position:relative;z-index:3}
.kicker{
  font-size:13px;
  font-weight:800;
  letter-spacing:.12em;
  text-transform:uppercase;
  margin-bottom:12px;
  color:#FFF3C4 !important;
  text-shadow:0 2px 8px rgba(0,45,84,.28) !important;
}
.left-hero h1{
  font-size:clamp(40px,4.5vw,56px);
  line-height:1.08;
  margin:0 0 18px;
  letter-spacing:-.04em;
  max-width:560px;
  color:#FFFFFF !important;
  text-shadow:0 3px 16px rgba(0,45,84,.34) !important;
}
.left-hero h1 span{
  color:#FFF0A8 !important;
  text-shadow:0 3px 14px rgba(0,45,84,.28) !important;
}
.benefits{display:grid;gap:20px;margin:30px 0}
.benefit{display:grid;grid-template-columns:44px 1fr;gap:12px;align-items:start}
.benefit .icon{
  width:44px;
  height:44px;
  border-radius:14px;
  background:rgba(255,255,255,.96) !important;
  color:#2B8BD1 !important;
  border:1px solid rgba(255,255,255,.65);
  display:grid;
  place-items:center;
  font-size:21px;
}
.benefit b{
  display:block;
  font-size:17px;
  margin-bottom:4px;
  color:#FFFFFF !important;
  text-shadow:0 2px 8px rgba(0,45,84,.26);
}
.benefit span{
  color:#F2F8FD !important;
  line-height:1.5;
  font-size:14.5px;
  text-shadow:0 1px 6px rgba(0,45,84,.20);
}
.hero-cta{display:flex;gap:12px;flex-wrap:wrap}
.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  padding:14px 22px;
  border-radius:18px;
  font-weight:800 !important;
  border:none;
  cursor:pointer;
  transition:all 0.2s ease;
}
.btn.yellow{
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#26384A !important;
  box-shadow:0 6px 18px rgba(242,155,58,0.28);
}
.btn.yellow:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(242,155,58,0.36)}
.btn.white{
  background:#fff !important;
  color:#1F6FAF !important;
  border:1px solid rgba(31,111,175,.12) !important;
}
.btn.white:hover{background:#F8FAFC !important}

.right-hero{
  position:relative;
  align-self:stretch;
  height:100%;
  min-height:580px;
  display:flex;
  flex-direction:column;
  justify-content:flex-end;
}
.person-wrap{
  position:absolute;
  inset:0;
  display:flex;
  justify-content:center;
  align-items:flex-end;
  pointer-events:none;
  z-index:1;
}
.person-wrap img{
  height:575px;
  max-height:100%;
  width:auto;
  object-fit:contain;
  object-position:bottom center;
  filter:drop-shadow(0 20px 40px rgba(11,28,92,.26));
  transform:translateY(0);
}
.float-card{
  position:absolute;
  background:rgba(255,255,255,.97) !important;
  color:#111f41;
  border-radius:22px;
  box-shadow:var(--shadow);
  z-index:2;
}
.card-target{left:14px;top:150px;padding:15px 18px;min-width:180px}
.card-target .mini{font-size:13px;color:var(--muted)}
.card-target b{display:block;font-size:30px;color:#F29B3A !important;margin:4px 0 0}
.bar{height:6px;background:#d8e0f7;border-radius:999px;margin-top:8px;overflow:hidden}
.card-target .bar span{
  display:block;
  height:100%;
  width:99%;
  background:linear-gradient(90deg,#2B8BD1,#58B7EA,#F29B3A) !important;
}
.card-join{right:0;bottom:16px;padding:16px 18px 12px;min-width:310px}
.card-join .top{font-weight:800;font-size:13.5px;margin-bottom:8px}
.card-join .row{display:grid;grid-template-columns:74px 1fr;gap:12px;align-items:center}
.card-join .badge{
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  border-radius:18px;
  padding:10px 8px;
  text-align:center;
}
.card-join .badge small{display:block;font-weight:700;font-size:11px}
.card-join .badge b{font-size:30px;line-height:1}
.chat-bubble{
  position:absolute;
  right:24px;
  top:64px;
  background:linear-gradient(135deg,#58B7EA,#8ED7F6) !important;
  color:#174E76 !important;
  padding:16px 18px;
  border-radius:20px 20px 20px 6px;
  font-weight:700;
  max-width:235px;
  box-shadow:0 14px 32px rgba(8,27,93,.18);
  font-size:13.5px;
  line-height:1.45;
  z-index:2;
}

section{padding:78px 0}
.sec-head{max-width:740px;margin:0 auto 34px;text-align:center;position:relative}
.sec-head h2{
  font-size:clamp(32px,3.8vw,46px);
  line-height:1.12;
  letter-spacing:-.03em;
  margin:0 0 12px;
  color:#17324D !important;
}
.sec-head p{margin:0;color:#5A6A7E !important;line-height:1.65;font-size:15px}

.data-eyebrow{
  display:inline-flex;
  padding:7px 14px;
  border-radius:999px;
  background:linear-gradient(135deg,#FFF1B8,#FFD474);
  color:#8A5B10;
  font-size:11px;
  font-weight:900;
  letter-spacing:.10em;
  margin-bottom:12px;
}
.usp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.usp-card{
  background:rgba(255,255,255,.97) !important;
  border:1px solid #E5EDF7;
  border-radius:26px;
  padding:26px;
  box-shadow:0 12px 30px rgba(38,94,151,.07);
  position:relative;
  overflow:hidden;
  transition:.25s ease;
}
.usp-card:hover{transform:translateY(-6px);box-shadow:0 18px 38px rgba(38,94,151,.12)}
.usp-card:after{content:"";position:absolute;width:120px;height:120px;border-radius:50%;right:-44px;bottom:-46px;opacity:.18}
.accent-yellow:after{background:#F7DF27}
.accent-blue:after{background:#1592F3}
.accent-orange:after{background:#F1971B}
.accent-cyan:after{background:#23D6E6}
.usp-icon{width:52px;height:52px;border-radius:16px;display:grid;place-items:center;background:#F4F9FF;font-size:24px;margin-bottom:14px}
.usp-card h3{font-size:22px;margin:0 0 8px;color:#17324D !important;letter-spacing:-.02em}
.usp-card p{font-size:14px;line-height:1.65;color:#5A6A7E !important;margin:0}
.pill{
  display:inline-block;
  padding:6px 12px;
  border-radius:999px;
  font-size:11px;
  font-weight:800;
  background:#E8F5FD !important;
  color:#277FC2 !important;
  margin-bottom:10px;
  letter-spacing:.04em;
}
.chips{display:flex;gap:7px;flex-wrap:wrap;margin-top:16px}
.chips span{
  padding:6px 10px;
  border-radius:999px;
  background:#F5F8FC;
  border:1px solid #E7EDF5;
  font-size:11px;
  font-weight:800;
  color:#3D668A;
}

/* QUIZ */
.quiz-zone{
  background:
    linear-gradient(rgba(255,255,255,.82),rgba(255,255,255,.82)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDcwMCI+CjxkZWZzPgo8ZmlsdGVyIGlkPSJiIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSI0MCIvPjwvZmlsdGVyPgo8L2RlZnM+CjxyZWN0IHdpZHRoPSIxNjAwIiBoZWlnaHQ9IjcwMCIgZmlsbD0iI0ZGRkRGOCIvPgo8ZWxsaXBzZSBjeD0iODAiIGN5PSI5MCIgcng9IjI2MCIgcnk9IjE3MCIgZmlsbD0iI0ZGQjQ1RCIgb3BhY2l0eT0iLjMwIiBmaWx0ZXI9InVybCgjYikiLz4KPGVsbGlwc2UgY3g9IjE1MTAiIGN5PSI2MTAiIHJ4PSIzMzAiIHJ5PSIxOTAiIGZpbGw9IiMyQjhCRDEiIG9wYWNpdHk9Ii4yNiIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxlbGxpcHNlIGN4PSIxMjkwIiBjeT0iNjAiIHJ4PSIxOTAiIHJ5PSIxMzAiIGZpbGw9IiNGRkQ2NkYiIG9wYWNpdHk9Ii4yOCIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjcwIDQ5NSA0ODAgNjUwIDcyMCA1NTUgQzk3MCA0NTUgMTIwMCA0NzAgMTYwMCAzMjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YyQTIzQSIgc3Ryb2tlLXdpZHRoPSI4IiBvcGFjaXR5PSIuNTUiLz4KPHBhdGggZD0iTTAgNjUwIEMyNjAgNTYwIDUyMCA2NzAgNzcwIDU5MCBDMTA2MCA1MDAgMTI0MCA1NjAgMTYwMCA0NTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzJDOEFDQiIgc3Ryb2tlLXdpZHRoPSIxNCIgb3BhY2l0eT0iLjM4Ii8+Cjwvc3ZnPg==") center/cover no-repeat !important;
  padding:76px 0;
}
.quiz-shell{display:grid;grid-template-columns:.82fr 1.18fr;gap:28px;align-items:stretch}
.quiz-intro{
  background:
    linear-gradient(135deg,rgba(43,139,209,.94),rgba(88,183,234,.88)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDkwMCI+CjxkZWZzPgogIDxsaW5lYXJHcmFkaWVudCBpZD0iYmciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI0ZERjlGMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIuMzgiIHN0b3AtY29sb3I9IiNGOEU1QjEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjYyIiBzdG9wLWNvbG9yPSIjRjI5QjNBIi8+CiAgICA8c3RvcCBvZmZzZXQ9Ii43OCIgc3RvcC1jb2xvcj0iIzU4QjdFQSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMTc2OEI1Ii8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8ZmlsdGVyIGlkPSJibHVyIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSIzNCIvPjwvZmlsdGVyPgogIDxsaW5lYXJHcmFkaWVudCBpZD0id2F2ZTEiIHgxPSIwIiB4Mj0iMSI+CiAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiNGNkM3NUEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjUiIHN0b3AtY29sb3I9IiNGMzlBMzUiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iI0ZGRDg3NiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJ3YXZlMiIgeDE9IjAiIHgyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzQ2QjdFQiIvPgogICAgPHN0b3Agb2Zmc2V0PSIuNTUiIHN0b3AtY29sb3I9IiMyMjc5QzYiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzE5NEU5QiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPHJlY3Qgd2lkdGg9IjE2MDAiIGhlaWdodD0iOTAwIiBmaWxsPSJ1cmwoI2JnKSIvPgo8Y2lyY2xlIGN4PSIyNTAiIGN5PSIxNzAiIHI9IjIyMCIgZmlsbD0iI0ZGRTE3RiIgb3BhY2l0eT0iLjU1IiBmaWx0ZXI9InVybCgjYmx1cikiLz4KPGNpcmNsZSBjeD0iMTI1MCIgY3k9IjIwMCIgcj0iMjYwIiBmaWxsPSIjRkY5RjU3IiBvcGFjaXR5PSIuNDUiIGZpbHRlcj0idXJsKCNibHVyKSIvPgo8Y2lyY2xlIGN4PSIxMTIwIiBjeT0iNzYwIiByPSIzNDAiIGZpbGw9IiMxOTkwRDQiIG9wYWNpdHk9Ii41NSIgZmlsdGVyPSJ1cmwoI2JsdXIpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjgwIDUyMCA0MTAgNjYwIDY1MCA1NzUgQzkwNSA0ODUgMTA1MCA1NDAgMTI4NSA0MjUgQzE0MTAgMzY1IDE1MDUgMjkwIDE2MDAgMjI1IEwxNjAwIDkwMCBMMCA5MDBaIiBmaWxsPSJ1cmwoI3dhdmUyKSIgb3BhY2l0eT0iLjk0Ii8+CjxwYXRoIGQ9Ik0wIDU1NSBDMjcwIDQ3MCA0NTAgNTYwIDY3NSA1MjAgQzkyNSA0NzUgMTEzNSA0MDUgMTYwMCAxNjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0idXJsKCN3YXZlMSkiIHN0cm9rZS13aWR0aD0iOTYiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgb3BhY2l0eT0iLjk1Ii8+CjxwYXRoIGQ9Ik0wIDUyMCBDMjkwIDQ0NSA0NzAgNTIwIDcxMCA0NzUgQzk3MCA0MjUgMTE5NSAzNjAgMTYwMCAxMzUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0ZGRjVEOCIgc3Ryb2tlLXdpZHRoPSIyNCIgb3BhY2l0eT0iLjc4Ii8+CjxwYXRoIGQ9Ik0xOTAgMCBDMzkwIDIyMCA1NzAgMjE1IDc0MCAxMzggQzk5MCAyNSAxMTMwIDEyMCAxMzQwIDQwIiBmaWxsPSJub25lIiBzdHJva2U9IiNGRkZGRkYiIHN0cm9rZS13aWR0aD0iMjIiIG9wYWNpdHk9Ii40NSIvPgo8L3N2Zz4=") center/cover no-repeat !important;
  color:#fff !important;
  border-radius:32px;
  padding:38px;
  position:relative;
  overflow:hidden;
  isolation:isolate;
}
.quiz-intro::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:32px;
  background:linear-gradient(135deg,rgba(28,105,171,.78),rgba(58,148,205,.54));
  z-index:-1;
}
.quiz-badge{
  display:inline-flex;
  padding:8px 14px;
  border-radius:999px;
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  font-weight:900;
  font-size:12px;
  letter-spacing:.04em;
}
.quiz-intro h2{
  font-size:40px;
  line-height:1.08;
  letter-spacing:-.04em;
  margin:20px 0 14px;
  color:#FFFFFF !important;
  text-shadow:0 2px 10px rgba(0,49,86,.30);
}
.quiz-intro p{color:#EEF8FF !important;line-height:1.65;font-size:15px;max-width:520px}
.quiz-mini{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px;position:relative;z-index:2}
.quiz-mini span{
  background:rgba(18,82,132,.32) !important;
  border:1px solid rgba(255,255,255,.26) !important;
  color:#FFFFFF !important;
  padding:9px 14px;
  border-radius:14px;
  font-size:12px;
  font-weight:700;
}
.kahoot-card{
  background:rgba(255,255,255,.97) !important;
  border-radius:32px;
  padding:30px;
  box-shadow:0 20px 50px rgba(31,42,68,.12);
  border:1px solid rgba(43,139,209,.13) !important;
  min-height:440px;
  display:flex;
  flex-direction:column;
  backdrop-filter:blur(8px);
}
.quiz-top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:26px}
.quiz-step-label{font-size:13px;font-weight:800;color:#61708f}
.quiz-progress{width:260px;max-width:44vw;height:9px;background:#edf0f7;border-radius:999px;overflow:hidden;margin-top:8px}
.quiz-progress span{
  display:block;
  height:100%;
  width:20%;
  background:linear-gradient(90deg,#2B8BD1,#58B7EA,#F29B3A) !important;
  border-radius:999px;
  transition:.25s;
}
.quiz-score{background:#fff4bf;border:1px solid #ffe17c;color:#27345e;padding:9px 13px;border-radius:14px;font-weight:900}
#quizStage h3{font-size:26px;line-height:1.25;margin:6px 0 22px;color:#17324D !important}
.answer-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.answer-btn{
  border:1px solid rgba(24,79,121,.08) !important;
  border-radius:20px;
  padding:20px 18px;
  min-height:110px;
  text-align:left;
  cursor:pointer;
  font-family:inherit;
  font-weight:800;
  font-size:15px;
  transition:.18s;
  position:relative;
  overflow:hidden;
}
.answer-btn:hover{transform:translateY(-3px);box-shadow:0 14px 25px rgba(35,48,90,.14)}
.answer-btn small{display:block;font-weight:600;opacity:.78;margin-top:6px;line-height:1.4}
.answer-btn:nth-child(1){background:#FFF0BE !important;color:#6F4B14 !important}
.answer-btn:nth-child(2){background:#DEF2FD !important;color:#1D5C89 !important}
.answer-btn:nth-child(3){background:#E9F6FD !important;color:#2C6B97 !important}
.answer-btn:nth-child(4){background:#FFE6D2 !important;color:#8C4B15 !important}

.quiz-result{text-align:center;margin:auto 0;padding:10px 12px}
.result-emoji{font-size:56px}
.result-tag{
  display:inline-block;
  margin-top:8px;
  padding:6px 12px;
  border-radius:999px;
  background:#edf1ff;
  color:#3157e5;
  font-weight:900;
  font-size:11px;
}
.quiz-result h3{font-size:32px;margin:12px 0 10px;color:#17324D}
.quiz-result p{color:#5A6A7E;line-height:1.7;max-width:570px;margin:0 auto 16px}
.result-score{
  display:inline-block;
  background:#fff7d7;
  border:1px solid #ffe59a;
  border-radius:14px;
  padding:10px 14px;
  margin-bottom:18px;
  font-weight:800;
  color:#1F3A56;
}
.result-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.quiz-retry{background:#fff;color:#3157e5;border:1.5px solid #dbe3f8}

/* PACKAGES */
.packs{background:#F8FBFE !important}
.pack-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.pack{
  background:rgba(255,255,255,.97) !important;
  border:1px solid rgba(43,139,209,.13) !important;
  border-radius:28px;
  overflow:hidden;
  position:relative;
  box-shadow:0 14px 34px rgba(49,107,154,.08) !important;
  transition:.25s ease;
  display:flex;
  flex-direction:column;
}
.pack:hover{transform:translateY(-6px)}
.pack.pop{
  border-color:#F2A23A !important;
  box-shadow:0 18px 40px rgba(242,162,58,.15) !important;
  transform:translateY(-8px);
}
.data-pack-head{
  height:120px;
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  padding:22px;
  color:#fff;
}
.data-pack-head span{font-size:42px;font-weight:900;opacity:.35;line-height:1}
.data-pack-head b{letter-spacing:.10em;font-size:13px}
.data-pack-head.starter{background:linear-gradient(135deg,#63C2F2,#2C8ED8)}
.data-pack-head.core{background:linear-gradient(135deg,#1592F3,#0B57BE)}
.data-pack-head.premium{background:linear-gradient(135deg,#F4B12C,#F1971B);color:#17324D}
.pack .body{padding:24px;flex:1;display:flex;flex-direction:column}
.ribbon{
  position:absolute;
  top:18px;
  right:16px;
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  padding:7px 14px;
  border-radius:999px;
  font-size:11px;
  font-weight:900;
  letter-spacing:.04em;
  z-index:2;
}
.pack h3{font-size:26px;margin:0 0 6px;color:#17324D !important}
.pack .sub{font-size:14px;color:#5A6A7E !important;line-height:1.5}
.price{font-size:30px;font-weight:900;color:#2B8BD1 !important;margin:16px 0 2px}
.price small{font-size:12px;color:var(--muted);font-weight:600}
.pack ul{list-style:none;padding:0;margin:18px 0 22px;flex:1}
.pack li{padding:10px 0;border-top:1px solid #F1F5F9;font-size:14px;color:#334155}
.pack li:before{content:"✓";color:#10b981;font-weight:900;margin-right:8px}
.pack .cta{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:14px 20px;
  border-radius:16px;
  background:linear-gradient(135deg,#2B8BD1,#1768B5) !important;
  color:#fff;
  font-weight:800 !important;
  text-align:center;
  transition:all 0.2s ease;
}
.pack .cta:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(43,139,209,0.3)}

/* HELP SECTION */
.help-section{
  background:
    linear-gradient(rgba(255,255,255,.82),rgba(255,255,255,.82)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDcwMCI+CjxkZWZzPgo8ZmlsdGVyIGlkPSJiIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSI0MCIvPjwvZmlsdGVyPgo8L2RlZnM+CjxyZWN0IHdpZHRoPSIxNjAwIiBoZWlnaHQ9IjcwMCIgZmlsbD0iI0ZGRkRGOCIvPgo8ZWxsaXBzZSBjeD0iODAiIGN5PSI5MCIgcng9IjI2MCIgcnk9IjE3MCIgZmlsbD0iI0ZGQjQ1RCIgb3BhY2l0eT0iLjMwIiBmaWx0ZXI9InVybCgjYikiLz4KPGVsbGlwc2UgY3g9IjE1MTAiIGN5PSI2MTAiIHJ4PSIzMzAiIHJ5PSIxOTAiIGZpbGw9IiMyQjhCRDEiIG9wYWNpdHk9Ii4yNiIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxlbGxpcHNlIGN4PSIxMjkwIiBjeT0iNjAiIHJ4PSIxOTAiIHJ5PSIxMzAiIGZpbGw9IiNGRkQ2NkYiIG9wYWNpdHk9Ii4yOCIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjcwIDQ5NSA0ODAgNjUwIDcyMCA1NTUgQzk3MCA0NTUgMTIwMCA0NzAgMTYwMCAzMjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YyQTIzQSIgc3Ryb2tlLXdpZHRoPSI4IiBvcGFjaXR5PSIuNTUiLz4KPHBhdGggZD0iTTAgNjUwIEMyNjAgNTYwIDUyMCA2NzAgNzcwIDU5MCBDMTA2MCA1MDAgMTI0MCA1NjAgMTYwMCA0NTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzJDOEFDQiIgc3Ryb2tlLXdpZHRoPSIxNCIgb3BhY2l0eT0iLjM4Ii8+Cjwvc3ZnPg==") center/cover no-repeat !important;
}
.help-box{
  display:grid;
  grid-template-columns:1.1fr .9fr;
  gap:32px;
  align-items:center;
  background:rgba(255,255,255,.97) !important;
  border-radius:34px;
  padding:38px;
  box-shadow:var(--shadow) !important;
  border:1px solid rgba(43,139,209,.13) !important;
}
.help-copy h2{
  font-size:clamp(30px,3.5vw,42px);
  line-height:1.1;
  letter-spacing:-.04em;
  margin:0 0 12px;
  color:#17324D !important;
}
.help-copy p{color:#5A6A7E !important;line-height:1.65;margin:0 0 16px;font-size:15px}
.help-list{display:grid;gap:10px;margin:18px 0 24px}
.help-list div{background:#f7f8fc;border:1px solid var(--line);padding:13px 16px;border-radius:16px;font-size:14px;color:#1F3A56}
.help-actions{display:flex;gap:12px;flex-wrap:wrap}
.help-visual{position:relative;display:flex;justify-content:center}
.help-card{
  position:absolute;
  right:10px;
  top:8px;
  background:linear-gradient(135deg,#2B8BD1,#58B7EA) !important;
  color:#fff !important;
  padding:14px 18px;
  border-radius:22px 22px 8px 22px;
  font-weight:700;
  max-width:230px;
  box-shadow:0 12px 28px rgba(43,139,209,0.25);
  font-size:13.5px;
  line-height:1.5;
}
.penguin-wrap{width:300px}

/* TOOLS SECTION */
.tools-section{background:#FFFDF8}
.tool-cloud{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;max-width:960px;margin:auto}
.tool-cloud span{
  background:#fff;
  border:1px solid #E4EDF7;
  box-shadow:0 8px 18px rgba(38,94,151,.05);
  border-radius:999px;
  padding:11px 18px;
  font-size:13.5px;
  font-weight:800;
  color:#285C86;
  transition:transform 0.2s ease;
}
.tool-cloud span:hover{transform:translateY(-2px);border-color:#2B8BD1;color:#1768B5}

/* MENTOR */
.mentor-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.mentor{
  background:rgba(255,255,255,.97) !important;
  border:1px solid rgba(43,139,209,.13) !important;
  border-radius:24px;
  overflow:hidden;
  box-shadow:0 12px 28px rgba(31,42,68,.06) !important;
  transition:.22s ease;
}
.mentor:hover{transform:translateY(-4px)}
.mentor img{height:230px;width:100%;object-fit:cover;object-position:center top}
.mentor .body{padding:16px}
.mentor b{display:block;font-size:16px;color:#17324D}
.mentor span{font-size:12.5px;color:var(--muted);margin-top:2px;display:block}

/* TESTIMONIALS */
.testi{
  background:
    linear-gradient(rgba(255,255,255,.82),rgba(255,255,255,.82)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDcwMCI+CjxkZWZzPgo8ZmlsdGVyIGlkPSJiIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSI0MCIvPjwvZmlsdGVyPgo8L2RlZnM+CjxyZWN0IHdpZHRoPSIxNjAwIiBoZWlnaHQ9IjcwMCIgZmlsbD0iI0ZGRkRGOCIvPgo8ZWxsaXBzZSBjeD0iODAiIGN5PSI5MCIgcng9IjI2MCIgcnk9IjE3MCIgZmlsbD0iI0ZGQjQ1RCIgb3BhY2l0eT0iLjMwIiBmaWx0ZXI9InVybCgjYikiLz4KPGVsbGlwc2UgY3g9IjE1MTAiIGN5PSI2MTAiIHJ4PSIzMzAiIHJ5PSIxOTAiIGZpbGw9IiMyQjhCRDEiIG9wYWNpdHk9Ii4yNiIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxlbGxpcHNlIGN4PSIxMjkwIiBjeT0iNjAiIHJ4PSIxOTAiIHJ5PSIxMzAiIGZpbGw9IiNGRkQ2NkYiIG9wYWNpdHk9Ii4yOCIgZmlsdGVyPSJ1cmwoI2IpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjcwIDQ5NSA0ODAgNjUwIDcyMCA1NTUgQzk3MCA0NTUgMTIwMCA0NzAgMTYwMCAzMjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YyQTIzQSIgc3Ryb2tlLXdpZHRoPSI4IiBvcGFjaXR5PSIuNTUiLz4KPHBhdGggZD0iTTAgNjUwIEMyNjAgNTYwIDUyMCA2NzAgNzcwIDU5MCBDMTA2MCA1MDAgMTI0MCA1NjAgMTYwMCA0NTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzJDOEFDQiIgc3Ryb2tlLXdpZHRoPSIxNCIgb3BhY2l0eT0iLjM4Ii8+Cjwvc3ZnPg==") center/cover no-repeat !important;
}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.quote{
  background:rgba(255,255,255,.97) !important;
  border:1px solid rgba(43,139,209,.13) !important;
  border-radius:24px;
  padding:26px;
  position:relative;
  box-shadow:0 12px 28px rgba(31,42,68,.06);
}
.quote:before{content:"“";position:absolute;right:18px;top:-8px;font-size:68px;color:#dfe6fb;line-height:1}
.quote p{line-height:1.7;color:#5A6A7E !important;font-size:14.5px;margin:0 0 16px;position:relative;z-index:2}
.stars{color:#ffb000;letter-spacing:2px;font-size:14px;margin-bottom:8px}
.person{display:flex;gap:12px;align-items:center}
.ava{
  width:44px;
  height:44px;
  border-radius:50%;
  background:linear-gradient(135deg,#ff83b7,#6a4cff);
  display:grid;
  place-items:center;
  color:#fff;
  font-weight:900;
  font-size:14px;
  flex:none;
}
.person b{display:block;font-size:15px;color:#17324D}
.person small{color:var(--muted);font-size:12px}

/* FLOW */
#workflow{background:#fff}
.flow-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.flow{text-align:center;position:relative}
.flow .n{
  width:60px;
  height:60px;
  border-radius:18px;
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  display:grid;
  place-items:center;
  margin:0 auto 16px;
  font-weight:900;
  font-size:22px;
  box-shadow:0 8px 20px rgba(242,155,58,0.22);
}
.flow h3{font-size:20px;margin:0 0 6px;color:#17324D !important}
.flow p{font-size:14px;color:#5A6A7E !important;line-height:1.6}
.flow:not(:last-child):after{
  content:"→";
  position:absolute;
  right:-14px;
  top:16px;
  color:#2B8BD1 !important;
  font-size:26px;
  font-weight:900;
}

/* FAQ */
.faq{background:#FFFDF8 !important}
.faq-list{max-width:920px;margin:auto;display:grid;gap:12px}
details{
  background:#fff;
  border:1px solid var(--line);
  border-radius:20px;
  padding:0 20px;
  transition:border-color 0.2s ease;
}
details[open]{border-color:#2B8BD1}
summary{
  list-style:none;
  cursor:pointer;
  padding:20px 0;
  font-weight:800;
  display:flex;
  justify-content:space-between;
  align-items:center;
  color:#17324D !important;
  font-size:16px;
}
summary::-webkit-details-marker{display:none}
summary::after{content:"+";font-size:24px;color:#F29B3A !important;font-weight:900}
details[open] summary::after{content:"−";color:#2B8BD1 !important}
details p{margin:0;padding:0 0 20px;color:#5A6A7E !important;line-height:1.7;font-size:14.5px}

/* CTA BAND */
.cta-band{
  background:
    linear-gradient(90deg,rgba(23,104,181,.88),rgba(43,139,209,.78)),
    url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNjAwIDkwMCI+CjxkZWZzPgogIDxsaW5lYXJHcmFkaWVudCBpZD0iYmciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI0ZERjlGMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIuMzgiIHN0b3AtY29sb3I9IiNGOEU1QjEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjYyIiBzdG9wLWNvbG9yPSIjRjI5QjNBIi8+CiAgICA8c3RvcCBvZmZzZXQ9Ii43OCIgc3RvcC1jb2xvcj0iIzU4QjdFQSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMTc2OEI1Ii8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8ZmlsdGVyIGlkPSJibHVyIj48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSIzNCIvPjwvZmlsdGVyPgogIDxsaW5lYXJHcmFkaWVudCBpZD0id2F2ZTEiIHgxPSIwIiB4Mj0iMSI+CiAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiNGNkM3NUEiLz4KICAgIDxzdG9wIG9mZnNldD0iLjUiIHN0b3AtY29sb3I9IiNGMzlBMzUiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iI0ZGRDg3NiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJ3YXZlMiIgeDE9IjAiIHgyPSIxIj4KICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iIzQ2QjdFQiIvPgogICAgPHN0b3Agb2Zmc2V0PSIuNTUiIHN0b3AtY29sb3I9IiMyMjc5QzYiLz4KICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzE5NEU5QiIvPgogIDwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPHJlY3Qgd2lkdGg9IjE2MDAiIGhlaWdodD0iOTAwIiBmaWxsPSJ1cmwoI2JnKSIvPgo8Y2lyY2xlIGN4PSIyNTAiIGN5PSIxNzAiIHI9IjIyMCIgZmlsbD0iI0ZGRTE3RiIgb3BhY2l0eT0iLjU1IiBmaWx0ZXI9InVybCgjYmx1cikiLz4KPGNpcmNsZSBjeD0iMTI1MCIgY3k9IjIwMCIgcj0iMjYwIiBmaWxsPSIjRkY5RjU3IiBvcGFjaXR5PSIuNDUiIGZpbHRlcj0idXJsKCNibHVyKSIvPgo8Y2lyY2xlIGN4PSIxMTIwIiBjeT0iNzYwIiByPSIzNDAiIGZpbGw9IiMxOTkwRDQiIG9wYWNpdHk9Ii41NSIgZmlsdGVyPSJ1cmwoI2JsdXIpIi8+CjxwYXRoIGQ9Ik0wIDYxMCBDMjgwIDUyMCA0MTAgNjYwIDY1MCA1NzUgQzkwNSA0ODUgMTA1MCA1NDAgMTI4NSA0MjUgQzE0MTAgMzY1IDE1MDUgMjkwIDE2MDAgMjI1IEwxNjAwIDkwMCBMMCA5MDBaIiBmaWxsPSJ1cmwoI3dhdmUyKSIgb3BhY2l0eT0iLjk0Ii8+CjxwYXRoIGQ9Ik0wIDU1NSBDMjcwIDQ3MCA0NTAgNTYwIDY3NSA1MjAgQzkyNSA0NzUgMTEzNSA0MDUgMTYwMCAxNjUiIGZpbGw9Im5vbmUiIHN0cm9rZT0idXJsKCN3YXZlMSkiIHN0cm9rZS13aWR0aD0iOTYiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgb3BhY2l0eT0iLjk1Ii8+CjxwYXRoIGQ9Ik0wIDUyMCBDMjkwIDQ0NSA0NzAgNTIwIDcxMCA0NzUgQzk3MCA0MjUgMTE5NSAzNjAgMTYwMCAxMzUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0ZGRjVEOCIgc3Ryb2tlLXdpZHRoPSIyNCIgb3BhY2l0eT0iLjc4Ii8+CjxwYXRoIGQ9Ik0xOTAgMCBDMzkwIDIyMCA1NzAgMjE1IDc0MCAxMzggQzk5MCAyNSAxMTMwIDEyMCAxMzQwIDQwIiBmaWxsPSJub25lIiBzdHJva2U9IiNGRkZGRkYiIHN0cm9rZS13aWR0aD0iMjIiIG9wYWNpdHk9Ii40NSIvPgo8L3N2Zz4=") center/cover no-repeat !important;
  color:#fff !important;
  position:relative;
  isolation:isolate;
}
.cta-band::before{
  content:"";
  position:absolute;
  inset:0;
  background:linear-gradient(90deg,rgba(21,91,151,.72),rgba(30,120,188,.48));
  z-index:-1;
}
.cta-box{
  display:grid;
  grid-template-columns:1fr auto;
  gap:28px;
  align-items:center;
  background:transparent;
  padding:8px 0;
}
.cta-box h2{
  font-size:clamp(32px,4vw,46px);
  line-height:1.1;
  letter-spacing:-.04em;
  margin:0 0 10px;
  color:#FFFFFF !important;
  text-shadow:0 2px 10px rgba(0,43,78,.28);
}
.cta-box p{margin:0;color:#F1F7FC !important;font-size:16px;line-height:1.6}
.cta-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:16px 26px;
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  border-radius:18px;
  font-weight:900 !important;
  white-space:nowrap;
  box-shadow:0 8px 24px rgba(242,155,58,0.35);
  transition:all 0.2s ease;
}
.cta-btn:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(242,155,58,0.45)}

/* FOOTER */
footer{
  background:linear-gradient(180deg,#2B8BD1,#1768B5) !important;
  color:#cdd7f0;
  padding:48px 0 24px;
}
.footer-grid{display:grid;grid-template-columns:1fr .8fr .8fr;gap:30px}
footer h4{margin:0 0 14px;color:#fff;font-size:16px;font-weight:800}
footer p,footer a{font-size:13.5px;color:#F0F7FF;line-height:1.7}
footer a:hover{color:#FFD978}
.copy{
  border-top:1px solid rgba(255,255,255,.2);
  margin-top:28px;
  padding-top:18px;
  font-size:12.5px;
  color:#E0F2FE;
  text-align:center;
}

/* FLOATING HELP */
.float-help{
  position:fixed;
  left:22px;
  bottom:22px;
  z-index:90;
  background:linear-gradient(135deg,#FFD978,#F29B3A) !important;
  color:#23405D !important;
  width:68px;
  height:68px;
  border-radius:50%;
  display:grid;
  place-items:center;
  font-weight:900;
  font-size:13px;
  box-shadow:0 14px 30px rgba(242,155,58,.35);
  border:3px solid #fff !important;
  transition:transform 0.2s ease;
}
.float-help:hover{transform:scale(1.08)}
.float-help small{font-size:9px;display:block;line-height:1.1;text-align:center}

/* CONFETTI */
.confetti-piece{
  position:fixed;
  width:9px;
  height:14px;
  top:-20px;
  z-index:9999;
  pointer-events:none;
  animation:confettiFall 1.9s linear forwards;
}
@keyframes confettiFall{to{transform:translateY(110vh) rotate(720deg);opacity:.95}}

/* RESPONSIVE */
@media(max-width:980px){
  .hero .wrap,.help-box,.cta-box{grid-template-columns:1fr}
  .card-grid,.pack-grid,.testi-grid{grid-template-columns:1fr 1fr}
  .mentor-grid{grid-template-columns:repeat(2,1fr)}
  .flow-grid{grid-template-columns:1fr 1fr}
  .search{max-width:none}
  .nav1{flex-wrap:wrap}
  .auth-actions{margin-left:0}
  .nav2{flex-wrap:wrap;justify-content:center;gap:12px 18px}
  .flow:not(:last-child):after{display:none}
  .quiz-shell{grid-template-columns:1fr}
  .quiz-intro h2{font-size:36px}
  .usp-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:640px){
  .brand-logo-img{height:38px}
  .search{order:3;width:100%}
  .nav2{overflow:visible;flex-wrap:wrap;gap:8px 12px;justify-content:flex-start;padding-bottom:12px}
  .hero .wrap,.card-grid,.pack-grid,.help-box,.mentor-grid,.testi-grid,.flow-grid,.footer-grid,.usp-grid{grid-template-columns:1fr}
  .right-hero{min-height:480px}
  .card-join{min-width:unset;left:16px;right:16px}
  .chat-bubble{right:20px;top:80px}
  .card-target{left:16px;top:190px}
  .help-copy h2,.cta-box h2{font-size:32px}
  .person-wrap img{height:440px}
  .answer-grid{grid-template-columns:1fr}
  .quiz-progress{max-width:62vw}
  .kahoot-card{padding:20px}
  .quiz-intro{padding:26px}
  .quiz-intro h2{font-size:30px}
}
</style>
</head>
<body>

@include('partials.public-header')

<main>
  <!-- HERO SECTION -->
  <section class="hero" id="hero">
    <div class="wrap">
      <div class="left-hero">
        <div class="kicker">AETHER DATA LAB • RESEARCH ANALYTICS</div>
        <h1>Data kamu ada. <br/>Insight-nya belum? <br/><span>Aether bantu olah sampai paham.</span></h1>
        
        <div class="benefits">
          <div class="benefit">
            <div class="icon">📥</div>
            <div>
              <b>Data Sourcing &amp; Preparation</b>
              <span>Cari dan siapkan sumber data riset yang legal, relevan, dan siap dianalisis.</span>
            </div>
          </div>
          <div class="benefit">
            <div class="icon">📊</div>
            <div>
              <b>Olah Data Multi-Method</b>
              <span>SPSS, SmartPLS, AMOS, regresi, SEM, panel data, time series, hingga analisis kualitatif.</span>
            </div>
          </div>
          <div class="benefit">
            <div class="icon">🧠</div>
            <div>
              <b>Interpretasi, Bukan Cuma Output</b>
              <span>Hasil dijelaskan agar siap dipakai untuk pembahasan, presentasi, dan publikasi.</span>
            </div>
          </div>
        </div>

        <div class="hero-cta">
          <a class="btn yellow" href="#data-packages">Konsultasi Olah Data →</a>
          <a class="btn white" href="#data-services">Lihat Data Services</a>
        </div>
      </div>

      <div class="right-hero">
        <div class="chat-bubble">Kirim jenis data + tujuan risetmu. Aether bantu tentukan metode yang paling pas 📊</div>

        <div class="float-card card-target">
          <div class="mini">Tercapai</div>
          <div style="font-weight:800;font-size:16px">Target Progress</div>
          <b>99%</b>
          <div class="bar"><span></span></div>
        </div>

        <div class="person-wrap">
          <img alt="Visual utama Aether" src="{{ asset('images/foto-tasya.png') }}"/>
        </div>

        <div class="float-card card-join">
          <div class="top">Selamat Sobat Aether Telah Mendaftar</div>
          <div class="row">
            <div class="badge">
              <small>{{ date('M') }}</small>
              <b>{{ date('j') }}</b>
            </div>
            <div>
              <div style="color:var(--muted);font-size:13px">Sobat Aether</div>
              <div style="font-weight:900;font-size:27px;line-height:1.1">Rina</div>
              <div style="color:var(--muted);font-size:13px">Mendaftar di Paket Research Companion</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- DATA SERVICES (USP SECTION) -->
  <section id="data-services">
    <div class="wrap">
      <div class="sec-head">
        <div class="data-eyebrow">UNIQUE SELLING POINT AETHER</div>
        <h2>Semua kebutuhan data riset, dari sumber sampai insight.</h2>
        <p>Aether diposisikan sebagai <b>Data &amp; Research Analytics Partner</b>: membantu menyiapkan data, memilih metode, mengolah, menginterpretasi, dan menyajikan hasil secara akademik.</p>
      </div>

      <div class="usp-grid">
        <article class="usp-card accent-yellow">
          <div class="usp-icon">🗂️</div>
          <span class="pill">DATA SOURCING</span>
          <h3>Data Riset Siap Pakai</h3>
          <p>Kurasi data publik, open data, data berlisensi, atau dataset sesuai topik penelitian. Fokus pada sumber yang legal dan dapat dipertanggungjawabkan.</p>
          <div class="chips">
            <span>Public Data</span>
            <span>Panel</span>
            <span>Time Series</span>
          </div>
        </article>

        <article class="usp-card accent-blue">
          <div class="usp-icon">🧹</div>
          <span class="pill">DATA PREPARATION</span>
          <h3>Cleaning &amp; Coding</h3>
          <p>Missing value, coding variabel, outlier, transformasi, tabulasi, merging, hingga data siap dianalisis.</p>
          <div class="chips">
            <span>Cleaning</span>
            <span>Coding</span>
            <span>Recoding</span>
          </div>
        </article>

        <article class="usp-card accent-orange">
          <div class="usp-icon">📈</div>
          <span class="pill">DATA ANALYSIS</span>
          <h3>Analisis Kuantitatif</h3>
          <p>Deskriptif, regresi, mediasi-moderasi, SEM-PLS, CB-SEM, panel data, dan metode kuantitatif lainnya.</p>
          <div class="chips">
            <span>SPSS</span>
            <span>SmartPLS</span>
            <span>AMOS</span>
          </div>
        </article>

        <article class="usp-card accent-cyan">
          <div class="usp-icon">🧩</div>
          <span class="pill">QUALITATIVE</span>
          <h3>Analisis Kualitatif</h3>
          <p>Coding, thematic analysis, content analysis, matriks temuan, dan visualisasi tema.</p>
          <div class="chips">
            <span>NVivo</span>
            <span>ATLAS.ti</span>
            <span>Thematic</span>
          </div>
        </article>

        <article class="usp-card accent-blue">
          <div class="usp-icon">🧠</div>
          <span class="pill">INTERPRETATION</span>
          <h3>Interpretasi Akademik</h3>
          <p>Menjelaskan arti output dan bagaimana hasilnya masuk ke pembahasan, implikasi, dan kesimpulan.</p>
          <div class="chips">
            <span>Bab 4</span>
            <span>Discussion</span>
            <span>Insight</span>
          </div>
        </article>

        <article class="usp-card accent-yellow">
          <div class="usp-icon">✨</div>
          <span class="pill">VISUALIZATION</span>
          <h3>Visualisasi &amp; Reporting</h3>
          <p>Grafik, tabel, dashboard sederhana, dan output untuk tesis, artikel, presentasi, dan institusi.</p>
          <div class="chips">
            <span>Charts</span>
            <span>Dashboard</span>
            <span>Report</span>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- QUIZ ZONE -->
  <section class="quiz-zone" id="quiz">
    <div class="wrap">
      <div class="quiz-shell">
        <div class="quiz-intro">
          <span class="quiz-badge">🎮 AETHER DATA QUIZ</span>
          <h2>Metode olah data apa yang paling cocok buat risetmu?</h2>
          <p>Jawab 5 pertanyaan singkat tentang bentuk data dan tujuan analisismu. Di akhir, Aether kasih arah metode yang paling masuk akal.</p>
          <div class="quiz-mini">
            <span>⚡ 2 menit</span>
            <span>🎯 Personalized result</span>
            <span>🏆 Ada skor</span>
          </div>
        </div>

        <div class="kahoot-card">
          <div class="quiz-top">
            <div>
              <span class="quiz-step-label">Pertanyaan <b id="quizCurrent">1</b>/5</span>
              <div class="quiz-progress"><span id="quizProgress"></span></div>
            </div>
            <div class="quiz-score">⭐ <span id="quizScore">0</span></div>
          </div>

          <div id="quizStage">
            <h3 id="quizQuestion">Kamu sekarang paling sering stuck di bagian mana?</h3>
            <div class="answer-grid" id="answerGrid"></div>
          </div>

          <div class="quiz-result" hidden="" id="quizResult">
            <div class="result-emoji" id="resultEmoji">🎓</div>
            <span class="result-tag">HASIL QUIZ KAMU</span>
            <h3 id="resultTitle">Research Companion</h3>
            <p id="resultDesc"></p>
            <div class="result-score">Skor kamu: <b id="resultScore">0</b>/50</div>
            <div class="result-actions">
              <a class="btn yellow" href="#data-packages">Lihat Program Cocok →</a>
              <button class="btn quiz-retry" id="quizRetry">Ulangi Quiz</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- DATA PACKAGES -->
  <section class="packs" id="data-packages">
    <div class="wrap">
      <div class="sec-head">
        <div class="data-eyebrow">PAKET DATA AETHER</div>
        <h2>Pilih berdasarkan level kebutuhan data kamu.</h2>
        <p>Harga mengikuti kompleksitas data dan metode. Fokusnya bukan paket generik, tapi kebutuhan analisis yang nyata.</p>
      </div>

      <div class="pack-grid">
        <article class="pack">
          <div class="data-pack-head starter">
            <span>01</span>
            <b>DATA STARTER</b>
          </div>
          <div class="body">
            <h3>Prepare My Data</h3>
            <div class="sub">Untuk data yang masih berantakan atau belum siap running.</div>
            <div class="price">Custom <small>/ sesuai scope</small></div>
            <ul>
              <li>Data checking &amp; screening</li>
              <li>Cleaning &amp; coding variabel</li>
              <li>Missing value / outlier check</li>
              <li>Data siap analisis</li>
            </ul>
            <a class="cta" href="#cta">Cek Data Saya</a>
          </div>
        </article>

        <article class="pack pop">
          <div class="ribbon">CORE SERVICE</div>
          <div class="data-pack-head core">
            <span>02</span>
            <b>DATA ANALYTICS</b>
          </div>
          <div class="body">
            <h3>Analyze My Data</h3>
            <div class="sub">USP utama Aether: running, analisis, interpretasi, sampai output akademik.</div>
            <div class="price">Custom <small>/ metode &amp; model</small></div>
            <ul>
              <li>Pemilihan metode yang tepat</li>
              <li>Running software (SPSS / PLS / AMOS)</li>
              <li>Uji model &amp; robustness</li>
              <li>Interpretasi hasil &amp; bab 4</li>
              <li>Tabel &amp; grafik siap pakai</li>
            </ul>
            <a class="cta" href="#cta">Konsultasi Analisis →</a>
          </div>
        </article>

        <article class="pack">
          <div class="data-pack-head premium">
            <span>03</span>
            <b>DATA TO PAPER</b>
          </div>
          <div class="body">
            <h3>From Data to Publication</h3>
            <div class="sub">Untuk hasil analisis yang ingin dibawa sampai pembahasan dan artikel.</div>
            <div class="price">Custom <small>/ sesuai deliverable</small></div>
            <ul>
              <li>Analisis &amp; interpretasi mendalam</li>
              <li>Draft hasil penelitian &amp; pembahasan</li>
              <li>Visualisasi akademik &amp; reporting</li>
              <li>Discussion support</li>
              <li>Publication preparation (Sinta / Scopus)</li>
            </ul>
            <a class="cta" href="#cta">Bawa Data ke Publikasi</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- HELP & MASCOT SECTION -->
  <section class="help-section" id="help">
    <div class="wrap">
      <div class="help-box">
        <div class="help-copy">
          <h2>Bingung data kamu harus diapakan?</h2>
          <p>Kirim contoh struktur data atau jelaskan tujuan penelitianmu. Pinguin Aether bantu mengarahkan kamu ke layanan data yang tepat.</p>
          <div class="help-list">
            <div>📥 Belum punya data? Tanyakan sumber data yang legal dan relevan.</div>
            <div>🧹 Data sudah ada tapi berantakan? Mulai dari cleaning &amp; coding.</div>
            <div>📊 Bingung pilih regresi, SEM, panel, atau metode lain? Mulai dari diagnosis.</div>
            <div>🧠 Output sudah ada tapi tidak paham artinya? Ambil interpretasi &amp; discussion support.</div>
          </div>
          <div class="help-actions">
            <a class="btn yellow" href="#cta">Tanya Aether Sekarang</a>
            <a class="btn white" href="#data-packages">Lihat Program</a>
          </div>
        </div>

        <div class="help-visual">
          <div class="help-card">Halo! Aku Pinguin Aether. Kalau kamu bingung mulai dari mana, kita cari jalurnya bareng ya ✨</div>
          <div class="penguin-wrap">
            <svg aria-hidden="true" class="penguin-svg" viewBox="0 0 220 220">
              <defs>
                <linearGradient id="pg1" x1="0" x2="1">
                  <stop offset="0%" stop-color="#3157E5"></stop>
                  <stop offset="100%" stop-color="#6D4AFF"></stop>
                </linearGradient>
              </defs>
              <circle cx="110" cy="110" fill="#E9F1FF" r="108"></circle>
              <ellipse cx="110" cy="122" fill="#132757" rx="58" ry="70"></ellipse>
              <ellipse cx="110" cy="128" fill="#FFFFFF" rx="40" ry="50"></ellipse>
              <circle cx="87" cy="88" fill="#132757" r="8"></circle>
              <circle cx="133" cy="88" fill="#132757" r="8"></circle>
              <circle cx="84" cy="85" fill="#fff" r="2.5"></circle>
              <circle cx="130" cy="85" fill="#fff" r="2.5"></circle>
              <path d="M110 96 L96 112 L124 112 Z" fill="#FFB84A"></path>
              <ellipse cx="69" cy="126" fill="url(#pg1)" rx="16" ry="34" transform="rotate(-20 69 126)"></ellipse>
              <ellipse cx="151" cy="126" fill="url(#pg1)" rx="16" ry="34" transform="rotate(20 151 126)"></ellipse>
              <ellipse cx="88" cy="184" fill="#FFB84A" rx="18" ry="9"></ellipse>
              <ellipse cx="132" cy="184" fill="#FFB84A" rx="18" ry="9"></ellipse>
              <path d="M159 72c15 6 23 15 27 31" fill="none" stroke="#FFD54A" stroke-linecap="round" stroke-width="8"></path>
              <path d="M46 74c-9 8-15 17-17 27" fill="none" stroke="#A8F0D6" stroke-linecap="round" stroke-width="8"></path>
              <circle cx="175" cy="150" fill="#FFD54A" r="11"></circle>
              <text fill="#132757" font-size="12" font-weight="900" text-anchor="middle" x="174" y="154">?</text>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- METHODS & TOOLS -->
  <section class="tools-section" id="tools">
    <div class="wrap">
      <div class="sec-head">
        <div class="data-eyebrow">METHODS &amp; TOOLS</div>
        <h2>Bukan terpaku satu software.</h2>
        <p>Metode dipilih berdasarkan pertanyaan penelitian dan karakter data.</p>
      </div>
      <div class="tool-cloud">
        <span>SPSS</span>
        <span>SmartPLS</span>
        <span>AMOS</span>
        <span>R</span>
        <span>Python</span>
        <span>NVivo</span>
        <span>ATLAS.ti</span>
        <span>Excel</span>
        <span>SEM</span>
        <span>Regression</span>
        <span>Panel Data</span>
        <span>Time Series</span>
        <span>Mediation</span>
        <span>Moderation</span>
        <span>Thematic Analysis</span>
      </div>
    </div>
  </section>

  <!-- MENTORS / EXPERTS -->
  <section id="mentor">
    <div class="wrap">
      <div class="sec-head">
        <h2>Data &amp; Research Experts Behind Aether</h2>
        <p>Profil expert dapat ditampilkan berdasarkan kompetensi analisis: kuantitatif, kualitatif, SEM, econometrics, visualization, dan publishing.</p>
      </div>

      <div class="mentor-grid">
        <article class="mentor">
          <img alt="Nur Atika Rochmah" src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=500&q=80"/>
          <div class="body">
            <b>Nur Atika Rochmah</b>
            <span>Mentor • Manajemen Bisnis & Development</span>
          </div>
        </article>

        <article class="mentor">
          <img alt="Deny Aditya Pratama" src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=500&q=80"/>
          <div class="body">
            <b>Deny Aditya Pratama</b>
            <span>Mentor • Manajemen Bisnis & Teknik</span>
          </div>
        </article>

        <article class="mentor">
          <img alt="Alessandra Natasya Panie" src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=500&q=80"/>
          <div class="body">
            <b>Alessandra Natasya Panie</b>
            <span>Mentor • Manajemen Pemasaran</span>
          </div>
        </article>

        <article class="mentor">
          <img alt="Nasya Ummi Syarifah" src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=80"/>
          <div class="body">
            <b>Nasywa Ummi Syarifah</b>
            <span>Mentor • Manajemen Keuangan</span>
          </div>
        </article>

        <article class="mentor">
          <img alt="Arrum Rochmani Hidayah" src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=80"/>
          <div class="body">
            <b>Arrum Rochmani Hidayah</b>
            <span>Mentor • Manajemen Keuangan</span>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testi" id="testi">
    <div class="wrap">
      <div class="sec-head">
        <h2>Apa kata mereka?</h2>
        <p>Format testimonial dibuat lebih dekat dengan model website referensi: langsung singkat, hangat, dan membantu membangun trust.</p>
      </div>

      <div class="testi-grid">
        <article class="quote">
          <div class="stars">★★★★★</div>
          <p>“Pendampingannya membantu banget untuk memetakan apa yang perlu saya kerjakan dulu. Jadi lebih terarah.”</p>
          <div class="person">
            <div class="ava">S1</div>
            <div>
              <b>Mahasiswa S1</b>
              <small>Contoh testimonial</small>
            </div>
          </div>
        </article>

        <article class="quote">
          <div class="stars">★★★★★</div>
          <p>“Analisis data dijelaskan dengan bahasa yang mudah dipahami. Saya jadi lebih percaya diri saat menjelaskan hasil penelitian.”</p>
          <div class="person">
            <div class="ava">S2</div>
            <div>
              <b>Mahasiswa S2</b>
              <small>Contoh testimonial</small>
            </div>
          </div>
        </article>

        <article class="quote">
          <div class="stars">★★★★★</div>
          <p>“Bagian terbaiknya adalah diskusi dua arah. Tidak terasa kaku, tapi tetap profesional dan rapi.”</p>
          <div class="person">
            <div class="ava">R</div>
            <div>
              <b>Peneliti</b>
              <small>Contoh testimonial</small>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- WORKFLOW -->
  <section id="workflow">
    <div class="wrap">
      <div class="sec-head">
        <div class="data-eyebrow">AETHER DATA FLOW</div>
        <h2>Dari file mentah sampai insight yang bisa dipertanggungjawabkan.</h2>
      </div>

      <div class="flow-grid">
        <div class="flow">
          <div class="n">1</div>
          <h3>Send Your Data</h3>
          <p>Kirim data, variabel, tujuan riset, atau kebutuhan dataset.</p>
        </div>
        <div class="flow">
          <div class="n">2</div>
          <h3>Data Diagnosis</h3>
          <p>Aether cek struktur, kualitas data, dan metode yang relevan.</p>
        </div>
        <div class="flow">
          <div class="n">3</div>
          <h3>Analyze</h3>
          <p>Cleaning, running, testing, dan visualisasi sesuai metode.</p>
        </div>
        <div class="flow">
          <div class="n">4</div>
          <h3>Explain the Insight</h3>
          <p>Hasil dijelaskan sampai kamu paham maknanya untuk penelitianmu.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="faq" id="faq">
    <div class="wrap">
      <div class="sec-head">
        <h2>FAQ</h2>
        <p>Pertanyaan yang paling sering muncul sebelum calon pengguna memutuskan untuk menghubungi Aether.</p>
      </div>

      <div class="faq-list">
        <details>
          <summary>Apakah Aether menjual data pribadi?</summary>
          <p>Tidak. Aether tidak menjual data pribadi. Layanan data difokuskan pada data publik, open data, data berlisensi, dataset yang sah, atau data milik klien sendiri.</p>
        </details>
        <details>
          <summary>Kalau saya belum tahu metode analisisnya, apakah tetap bisa konsultasi?</summary>
          <p>Bisa. Data diagnosis menjadi tahap awal untuk melihat tujuan penelitian, tipe variabel, ukuran sampel, struktur data, dan asumsi metode.</p>
        </details>
        <details>
          <summary>Apakah Aether hanya mengolah data kuantitatif?</summary>
          <p>Tidak. Aether juga dapat memosisikan layanan pada analisis kualitatif seperti coding, thematic analysis, content analysis, dan visualisasi temuan.</p>
        </details>
        <details>
          <summary>Apakah saya hanya menerima output software?</summary>
          <p>Tidak seharusnya. USP Aether adalah output + interpretasi agar pengguna memahami arti hasil dan cara menggunakannya dalam penulisan akademik.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- CTA BAND -->
  <section class="cta-band" id="cta">
    <div class="wrap cta-box">
      <div>
        <h2>Punya data? Jangan berhenti di Excel.</h2>
        <p>Kirim data dan tujuan risetmu. Aether bantu mengubah data mentah menjadi analisis, insight, dan output akademik.</p>
      </div>
      <a class="cta-btn" href="mailto:aetherpersadavidya@gmail.com">Konsultasi Data Sekarang →</a>
    </div>
  </section>
</main>

<!-- FLOATING WA BUTTON -->
<a class="float-help" href="https://wa.me/6281234567890?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20analisis%20data" target="_blank" rel="noopener">
  WA
  <small>Need Help?</small>
</a>

<!-- FOOTER -->
<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <a class="brand-link" href="{{ url('/') }}" style="margin-bottom:14px;display:inline-block" aria-label="Aether Science Publisher">
          <div style="background:#fff;padding:6px 14px;border-radius:12px;display:inline-flex;align-items:center;box-shadow:0 4px 14px rgba(0,0,0,0.12)">
            <img src="{{ asset('images/logo_aether.png') }}" alt="Aether Science Publisher" style="height:40px;width:auto;object-fit:contain">
          </div>
        </a>
        <p>Data • Research Analytics • Interpretation • Publishing</p>
      </div>

      <div>
        <h4>Explore</h4>
        <p>
          <a href="#hero">Home</a><br/>
          <a href="#data-services">Data Services</a><br/>
          <a href="#data-packages">Paket Olah Data</a><br/>
          <a href="#mentor">Mentor</a><br/>
          <a href="#faq">FAQ</a><br/>
          <a href="{{ route('landing.berita') }}">Artikel &amp; Wawasan</a>
        </p>
      </div>

      <div>
        <h4>Contact</h4>
        <p>
          Email: <a href="mailto:aetherpersadavidya@gmail.com">aetherpersadavidya@gmail.com</a><br/>
          Instagram: <a href="https://instagram.com/aetherpersadavidya" target="_blank" rel="noopener">@aetherpersadavidya</a><br/>
          WhatsApp: +62 812-3456-7890<br/>
          Indonesia
        </p>
      </div>
    </div>

    <div class="copy">
      PT Aether Persada Vidya · &copy; {{ date('Y') }} All Rights Reserved. Founded on Wisdom. Advancing Knowledge.
    </div>
  </div>
</footer>

<!-- SCRIPT: QUIZ ENGINE & CONFETTI -->
<script>
const aetherQuiz = [
  {
    q: "Kamu sekarang paling sering stuck di bagian mana?",
    a: [
      ["Topik & rumusan masalah", "Masih bingung arah risetnya.", 10, "starter"],
      ["Metodologi & instrumen", "Sudah ada topik, tapi desain riset belum mantap.", 10, "starter"],
      ["Olah data & interpretasi", "Data ada, tapi hasilnya bikin pusing.", 10, "companion"],
      ["Artikel & publikasi", "Naskah sudah ada, tinggal mau dimatangkan.", 10, "publish"]
    ]
  },
  {
    q: "Seberapa jelas target penelitianmu saat ini?",
    a: [
      ["Masih samar banget", "Butuh banyak arahan dari awal.", 6, "starter"],
      ["Lumayan jelas", "Tapi masih sering berubah.", 8, "starter"],
      ["Sudah jelas", "Butuh pendampingan untuk eksekusi.", 10, "companion"],
      ["Sudah sampai tahap artikel", "Fokus ke kualitas naskah & submission.", 10, "publish"]
    ]
  },
  {
    q: "Kalau ketemu output statistik atau hasil coding, reaksimu?",
    a: [
      ["Blank 😵", "Butuh penjelasan pelan-pelan.", 7, "companion"],
      ["Paham sedikit", "Tapi belum yakin menafsirkannya.", 9, "companion"],
      ["Lumayan paham", "Butuh second opinion.", 10, "companion"],
      ["Data bukan masalahku", "Aku lebih fokus ke penulisan/publikasi.", 8, "publish"]
    ]
  },
  {
    q: "Gaya pendampingan yang paling kamu suka?",
    a: [
      ["Checklist jelas", "Aku suka tahu step-by-step.", 9, "starter"],
      ["Diskusi 1:1", "Aku pengin bisa banyak tanya.", 10, "companion"],
      ["Review hasil", "Aku butuh evaluasi dan feedback.", 10, "companion"],
      ["Review naskah", "Aku butuh polish sebelum submit.", 10, "publish"]
    ]
  },
  {
    q: "Output yang paling kamu pengin tercapai dulu?",
    a: [
      ["Proposal lebih rapi", "Biar bisa lanjut penelitian.", 8, "starter"],
      ["Analisis data beres", "Biar bisa masuk pembahasan.", 10, "companion"],
      ["Tesis/skripsi makin solid", "Biar lebih siap diuji.", 10, "companion"],
      ["Artikel siap submit", "Biar masuk tahap publikasi.", 10, "publish"]
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
    b.className = "answer-btn";
    b.innerHTML = `<span>${symbols[idx]} &nbsp;${opt[0]}</span><small>${opt[1]}</small>`;
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
  stageEl.hidden = true;
  resultEl.hidden = false;
  document.getElementById("resultScore").textContent = qscore;
  const winner = Object.entries(buckets).sort((a, b) => b[1] - a[1])[0][0];
  const map = {
    starter: {
      emoji: "🧭",
      title: "Research Starter",
      desc: "Kamu paling butuh fondasi yang jelas dulu: topik, research gap, rumusan masalah, metodologi, dan arah penelitian. Mulai dari struktur, baru lanjut ke tahap berikutnya."
    },
    companion: {
      emoji: "🚀",
      title: "Research Companion",
      desc: "Kamu sudah punya pondasi, tapi butuh partner diskusi yang bisa menemani dari desain riset, data, analisis, sampai interpretasi. Ini jalur paling cocok untuk pendampingan intensif."
    },
    publish: {
      emoji: "📚",
      title: "Publication Ready",
      desc: "Kamu sudah cukup jauh. Fokus terbaikmu sekarang adalah merapikan naskah, memperkuat argumentasi, melakukan academic editing, dan menyiapkan submission."
    }
  };
  document.getElementById("resultEmoji").textContent = map[winner].emoji;
  document.getElementById("resultTitle").textContent = map[winner].title;
  document.getElementById("resultDesc").textContent = map[winner].desc;
  burstConfetti();
}

function burstConfetti() {
  const colors = ["#2B8BD1", "#58B7EA", "#1768B5", "#F6C75A", "#F29B3A", "#FFD978", "#FFFFFF"];
  for (let i = 0; i < 42; i++) {
    const p = document.createElement("i");
    p.className = "confetti-piece";
    p.style.left = Math.random() * 100 + "vw";
    p.style.background = colors[i % colors.length];
    p.style.animationDelay = (Math.random() * 0.45) + "s";
    p.style.transform = `rotate(${Math.random() * 360}deg)`;
    document.body.appendChild(p);
    setTimeout(() => p.remove(), 2500);
  }
}

document.getElementById("quizRetry").onclick = () => {
  qi = 0;
  qscore = 0;
  buckets.starter = buckets.companion = buckets.publish = 0;
  scoreEl.textContent = "0";
  resultEl.hidden = true;
  stageEl.hidden = false;
  renderQuiz();
};

renderQuiz();
</script>
</body>
</html>
