<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>JEBER – Jeunesse de l'Église Baptiste de l'Étoile Rouge</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bleu:   #1E3A8A;
      --violet: #6D28D9;
      --orange: #F97316;
      --blanc:  #FFFFFF;
      --texte2: #E2E8F0;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }

    /* ── FOND ── */
    body { background: #0f172a; min-height: 100vh; }

    /* ── SLIDESHOW ── */
    #bg-slider { position: fixed; inset: 0; z-index: 0; }

    .bg-slide {
      position: absolute; inset: 0;
      background-size: cover;
      background-position: center;
      opacity: 0;
      transition: opacity 1.8s ease-in-out;
    }
    .bg-slide.active { opacity: 1; }
    .bg-slide::after {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(
        135deg,
        rgba(15,23,60,0.72) 0%,
        rgba(55,15,120,0.55) 50%,
        rgba(100,30,10,0.50) 100%
      );
    }

    #slide-1 { background-image: url('{{ asset("assets/acceuil/Photo1.jpg") }}'); }
    #slide-2 { background-image: url('{{ asset("assets/acceuil/Photo2.jpg") }}'); }
    #slide-3 { background-image: url('{{ asset("assets/acceuil/Photo3.jpg") }}'); }
    #slide-4 { background-image: url('{{ asset("assets/acceuil/Photo4.jpg") }}'); }
    #slide-5 { background-image: url('{{ asset("assets/acceuil/Photo5.jpg") }}'); }

    /* ── INDICATEURS ── */
    #slide-dots {
      position: fixed; bottom: 1.4rem; left: 50%;
      transform: translateX(-50%);
      z-index: 20; display: flex; gap: 8px;
    }
    .dot-ind {
      width: 28px; height: 3px; border-radius: 2px;
      background: rgba(255,255,255,0.25);
      transition: background 0.4s ease;
      overflow: hidden; cursor: pointer;
    }
    .dot-ind.active { background: rgba(255,255,255,0.55); }
    .dot-ind .dot-progress {
      height: 100%; width: 0%;
      background: var(--orange); border-radius: 2px;
    }
    .dot-ind.active .dot-progress { animation: dotFill 5s linear forwards; }
    @keyframes dotFill { from { width: 0%; } to { width: 100%; } }

    /* ── HEADER ── */
    header {
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 100; padding: 0.8rem 2rem;
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    /* ── LOGOS ── */
    .logo-img {
      height: 150px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
      transition: transform 0.3s ease;
    }

    .logo-image {
      height: 300px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
      transition: transform 0.3s ease;
    }
    .logo-img:hover { transform: scale(1.05); }

    /* ── HERO ── */
    .hero {
      position: relative; z-index: 10;
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      text-align: center;
      padding: 8rem 1.5rem 4rem;
    }

    .eyebrow {
      display: inline-flex; align-items: center; gap: 10px;
      margin-bottom: 1.4rem;
      opacity: 0; animation: fadeUp 0.7s ease forwards 0.2s;
    }
    .eyebrow span {
      font-size: 11px; letter-spacing: 2.5px;
      text-transform: uppercase; color: var(--orange); font-weight: 600;
    }
    .eyebrow::before, .eyebrow::after {
      content: ''; display: block;
      width: 36px; height: 1px;
      background: var(--orange); opacity: 0.6;
    }

    h1.brand {
      font-family: 'Montserrat', sans-serif;
      font-size: clamp(4rem, 12vw, 7.5rem);
      font-weight: 800; color: var(--bleu);
      letter-spacing: -2px; line-height: 1;
      margin-bottom: 0.5rem;
      opacity: 0; animation: fadeUp 0.7s ease forwards 0.4s;
    }

    h2.fullname {
      font-size: clamp(0.85rem, 2vw, 1.05rem);
      font-weight: 400; color: var(--texte2);
      letter-spacing: 1px; margin-bottom: 1.6rem;
      opacity: 0; animation: fadeUp 0.7s ease forwards 0.55s;
    }

    .tagline {
      display: flex; flex-wrap: wrap;
      justify-content: center; gap: 0.4rem 1rem;
      margin-bottom: 2.4rem;
      opacity: 0; animation: fadeUp 0.7s ease forwards 0.7s;
    }
    .tagline span {
      font-size: 0.78rem; letter-spacing: 1.2px;
      text-transform: uppercase;
      color: rgba(255,255,255,0.55); font-weight: 600;
    }
    .tagline .dot { color: var(--orange); opacity: 0.7; font-size: 1rem; }

    blockquote {
      max-width: 560px; margin: 0 auto 2.8rem;
      border-left: 3px solid var(--orange);
      padding: 0.8rem 1.2rem;
      background: rgba(255,255,255,0.04);
      border-radius: 0 8px 8px 0; text-align: left;
      opacity: 0; animation: fadeUp 0.7s ease forwards 0.85s;
    }
    blockquote p {
      font-style: italic; font-size: 0.88rem;
      color: var(--texte2); line-height: 1.65; margin-bottom: 0.4rem;
    }
    blockquote cite {
      font-size: 0.75rem; color: var(--orange);
      font-weight: 600; letter-spacing: 0.8px;
      text-transform: uppercase; font-style: normal;
    }

    /* ── BOUTON ── */
    .btn-connexion {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 0.9rem 2.6rem;
      background: var(--orange); color: var(--blanc);
      font-family: 'Poppins', sans-serif;
      font-weight: 600; font-size: 1rem;
      letter-spacing: 1.5px; text-transform: uppercase;
      text-decoration: none; border-radius: 50px;
      border: 2px solid transparent;
      position: relative; overflow: hidden;
      transition: all 0.35s ease;
      box-shadow: 0 8px 32px rgba(249,115,22,0.35);
      opacity: 0; animation: fadeUp 0.7s ease forwards 1s;
    }
    .btn-connexion::before {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(135deg, var(--orange), var(--violet));
      opacity: 0; transition: opacity 0.35s ease; border-radius: inherit;
    }
    .btn-connexion:hover::before { opacity: 1; }
    .btn-connexion:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 40px rgba(109,40,217,0.4);
      color: var(--blanc);
    }
    .btn-connexion:active { transform: translateY(0); }
    .btn-connexion svg, .btn-connexion span { position: relative; z-index: 1; }

    .btn-wrapper { position: relative; display: inline-block; }
    .btn-wrapper::after {
      content: ''; position: absolute;
      width: 120%; height: 120%; top: -10%; left: -10%;
      background: radial-gradient(ellipse at center, rgba(249,115,22,0.2) 0%, transparent 70%);
      border-radius: 50px; pointer-events: none;
      animation: pulse 2.8s ease-in-out infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 0.7; }
      50% { transform: scale(1.12); opacity: 0.4; }
    }

    /* ── IDENTITÉ ── */
    .identity {
      position: relative; z-index: 10;
      background: rgba(255,255,255,0.04);
      border-top: 1px solid rgba(162, 32, 32, 0.07);
      padding: 3.5rem 1.5rem; text-align: center;
      opacity: 0; animation: fadeUp 0.8s ease forwards 1.2s;
    }
    .identity h3 {
      font-family: 'Montserrat', sans-serif; font-weight: 800;
      font-size: 0.72rem; letter-spacing: 3px;
      text-transform: uppercase; color: var(--orange); margin-bottom: 1rem;
    }
    .identity p {
      max-width: 520px; margin: 0 auto;
      font-size: 0.95rem; color: rgba(255,255,255,0.55); line-height: 1.8;
    }

    /* ── FOOTER ── */
    footer {
      position: relative; z-index: 10;
      padding: 1.2rem; text-align: center;
      border-top: 1px solid rgba(255,255,255,0.05);
    }
    footer p { font-size: 0.72rem; color: rgba(255, 255, 255, 0.28); letter-spacing: 0.5px; }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 576px) {
      header { padding: 0.6rem 1rem; }
      .logo-img { height: 64px; }
      h1.brand { letter-spacing: -1px; }
      blockquote { text-align: center; border-left: none; border-top: 3px solid var(--orange); border-radius: 8px; }
    }
  </style>
</head>
<body>

  <!-- ══ FOND DIAPORAMA ══ -->
  <div id="bg-slider" aria-hidden="true">
    <div class="bg-slide active" id="slide-1"></div>
    <div class="bg-slide" id="slide-2"></div>
    <div class="bg-slide" id="slide-3"></div>
    <div class="bg-slide" id="slide-4"></div>
    <div class="bg-slide" id="slide-5"></div>
  </div>

  <!-- Indicateurs de progression -->
  <div id="slide-dots">
    <div class="dot-ind active"><div class="dot-progress"></div></div>
    <div class="dot-ind"><div class="dot-progress"></div></div>
    <div class="dot-ind"><div class="dot-progress"></div></div>
    <div class="dot-ind"><div class="dot-progress"></div></div>
    <div class="dot-ind"><div class="dot-progress"></div></div>
  </div>

  <!-- ══ EN-TÊTE ══ -->
  <header>
    <div class="d-flex align-items-center justify-content-between">

      <!-- Logo EBER (gauche) -->
      <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('assets/acceuil/Logo EBER.png') }}" alt="Logo EBER" class="logo-img" />
      </div>

      <!-- Logo JEBER (droite) -->
      <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('assets/acceuil/Logo JEBER.png') }}" alt="Logo JEBER" class="logo-image" />
      </div>

    </div>
  </header>

  <!-- ══ SECTION HÉRO ══ -->
  <main>
    <section class="hero">

      <div class="eyebrow">
        <span>Espace privé · Membres</span>
      </div>

      <h1 class="brand">JEBER</h1>
      <h2 class="fullname">Jeunesse de l'Église Baptiste de l'Étoile Rouge</h2>

      <div class="tagline">
        <span>Grandir dans la foi</span>
        <span class="dot">•</span>
        <span>Servir avec excellence</span>
        <span class="dot">•</span>
        <span>Impacter notre génération</span>
      </div>

      <blockquote>
        <p>« Que personne ne méprise ta jeunesse ; mais sois un modèle pour les fidèles, en parole, en conduite, en charité, en foi, en pureté. »</p>
        <cite>1 Timothée 4&nbsp;: 12</cite>
      </blockquote>

      <div class="btn-wrapper">
        <a href="{{ route('login') }}" class="btn-connexion">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
          </svg>
          <span>Se connecter</span>
        </a>
      </div>

    </section>

    <!-- ══ NOTRE IDENTITÉ ══ -->
    <section class="identity">
      <h3>Notre identité</h3>
      <p>
        Nous sommes une génération appelée à refléter Christ, à grandir dans Sa Parole
        et à servir avec passion au sein de l'Église Baptiste de l'Étoile Rouge.
      </p>
    </section>
  </main>

  <!-- ══ PIED DE PAGE ══ -->
  <footer>
    <p>JEBER – Jeunesse de l'Église Baptiste de l'Étoile Rouge &nbsp;·&nbsp; © 2026 – Tous droits réservés</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function() {
      const DURATION = 5000;
      const slides = document.querySelectorAll('.bg-slide');
      const dots   = document.querySelectorAll('.dot-ind');
      let current  = 0;
      let timer    = null;

      function goTo(index) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        const oldBar = dots[current].querySelector('.dot-progress');
        oldBar.style.animation = 'none';
        oldBar.offsetHeight;
        oldBar.style.animation = '';

        current = index;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
        const newBar = dots[current].querySelector('.dot-progress');
        newBar.style.animation = 'none';
        newBar.offsetHeight;
        newBar.style.animation = 'dotFill 5s linear forwards';
      }

      function next() { goTo((current + 1) % slides.length); }

      function startTimer() {
        clearInterval(timer);
        timer = setInterval(next, DURATION);
      }

      dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { goTo(i); startTimer(); });
      });

      startTimer();
    })();
  </script>
</body>
</html>