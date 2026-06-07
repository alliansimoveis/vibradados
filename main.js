/* ============================================================
   VIBRA MARKETING — Interactions
   GSAP · ScrollTrigger · Lenis · vanilla JS
   ============================================================ */
(function () {
  "use strict";

  const root = document.documentElement;
  root.classList.add("js");

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isTouch = window.matchMedia("(hover: none), (pointer: coarse)").matches;

  document.getElementById("year").textContent = new Date().getFullYear();

  /* ---------- LOADER ---------- */
  function dismissLoader() {
    const loader = document.getElementById("loader");
    if (!loader) return;
    loader.classList.add("is-done");
    setTimeout(() => loader.remove(), 1200);
    document.body.dispatchEvent(new Event("vibra:ready"));
  }

  /* ---------- BOOT (after libs load) ---------- */
  function boot() {
    const hasGSAP = window.gsap && window.ScrollTrigger;
    if (hasGSAP) gsap.registerPlugin(ScrollTrigger);

    /* ----- Lenis smooth scroll ----- */
    let lenis = null;
    if (window.Lenis && !reduceMotion) {
      lenis = new Lenis({ duration: 1.15, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true, touchMultiplier: 1.6 });
      if (hasGSAP) {
        lenis.on("scroll", ScrollTrigger.update);
        gsap.ticker.add((time) => lenis.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);
      } else {
        const raf = (t) => { lenis.raf(t); requestAnimationFrame(raf); };
        requestAnimationFrame(raf);
      }
    }

    /* ----- Anchor links via Lenis ----- */
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
      a.addEventListener("click", (e) => {
        const id = a.getAttribute("href");
        if (id.length < 2) return;
        const target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        if (lenis) lenis.scrollTo(target, { offset: -10, duration: 1.4 });
        else target.scrollIntoView({ behavior: "smooth" });
      });
    });

    /* ----- Nav scrolled state ----- */
    const nav = document.getElementById("nav");
    const onScroll = () => nav.classList.toggle("is-scrolled", window.scrollY > 40);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    /* ----- Hero title intro ----- */
    if (hasGSAP && !reduceMotion) {
      const heroLines = document.querySelectorAll(".hero__title .line__in");
      gsap.fromTo(heroLines, { yPercent: 110, y: 0 }, { yPercent: 0, y: 0, duration: 1.15, ease: "power4.out", stagger: 0.1, delay: 0.15 });

      gsap.utils.toArray(".hero [data-reveal]").forEach((el, i) => {
        gsap.to(el, { opacity: 1, y: 0, duration: 1, ease: "power3.out", delay: 0.5 + i * 0.08 });
      });
    } else {
      document.querySelectorAll(".hero__title .line__in").forEach((l) => (l.style.transform = "none"));
      document.querySelectorAll(".hero [data-reveal]").forEach((l) => { l.style.opacity = 1; l.style.transform = "none"; });
    }

    /* ----- Scroll reveals ----- */
    if (hasGSAP && !reduceMotion) {
      gsap.utils.toArray("[data-reveal]").forEach((el) => {
        if (el.closest(".hero")) return;
        gsap.to(el, {
          opacity: 1, y: 0, duration: 1, ease: "power3.out",
          scrollTrigger: { trigger: el, start: "top 86%", once: true },
        });
      });

      gsap.utils.toArray("[data-reveal-lines]").forEach((el) => {
        gsap.fromTo(el, { opacity: 0, y: 30 }, {
          opacity: 1, y: 0, duration: 1.1, ease: "power3.out",
          scrollTrigger: { trigger: el, start: "top 85%", once: true },
        });
      });
    } else {
      document.querySelectorAll("[data-reveal], [data-reveal-lines]").forEach((el) => {
        el.style.opacity = 1; el.style.transform = "none";
      });
    }

    /* ----- Strike line ----- */
    document.querySelectorAll(".strike").forEach((el) => {
      if (hasGSAP) {
        ScrollTrigger.create({ trigger: el, start: "top 80%", once: true,
          onEnter: () => el.classList.add("is-on") });
      } else el.classList.add("is-on");
    });

    /* ----- Journey progress bar ----- */
    const jProg = document.querySelector(".journey__progress i");
    if (jProg && hasGSAP && !reduceMotion) {
      gsap.to(jProg, { width: "100%", ease: "none",
        scrollTrigger: { trigger: ".journey", start: "top 70%", end: "bottom 70%", scrub: true } });
      gsap.utils.toArray(".journey__step").forEach((step, i) => {
        gsap.fromTo(step, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: .7, ease: "power3.out",
          scrollTrigger: { trigger: step, start: "top 88%", once: true }, delay: i * 0.04 });
      });
    } else if (jProg) { jProg.style.width = "100%"; }

    /* ----- Counters ----- */
    const counters = document.querySelectorAll("[data-count]");
    counters.forEach((el) => {
      const target = parseFloat(el.getAttribute("data-count"));
      const suffix = el.getAttribute("data-suffix") || "";
      const run = () => {
        const obj = { v: 0 };
        if (hasGSAP && !reduceMotion) {
          gsap.to(obj, { v: target, duration: 1.8, ease: "power2.out",
            onUpdate: () => { el.textContent = Math.round(obj.v) + suffix; } });
        } else { el.textContent = target + suffix; }
      };
      if (hasGSAP) ScrollTrigger.create({ trigger: el, start: "top 90%", once: true, onEnter: run });
      else run();
    });

    /* ----- Parallax auras ----- */
    if (hasGSAP && !reduceMotion) {
      gsap.utils.toArray("[data-parallax]").forEach((el) => {
        const speed = parseFloat(el.getAttribute("data-parallax"));
        gsap.to(el, { yPercent: speed * 100, ease: "none",
          scrollTrigger: { trigger: document.body, start: "top top", end: "bottom bottom", scrub: true } });
      });
    }

    /* ----- Flow viz draw ----- */
    if (hasGSAP && !reduceMotion) {
      gsap.utils.toArray(".flow__line").forEach((line) => {
        const len = line.getTotalLength();
        gsap.fromTo(line, { strokeDasharray: len, strokeDashoffset: len },
          { strokeDashoffset: 0, duration: 1.6, ease: "power2.out",
            scrollTrigger: { trigger: line, start: "top 80%", once: true },
            onComplete: () => { line.style.strokeDasharray = "6 8"; } });
      });
    }
  }

  /* ---------- CURSOR (independent of libs) ---------- */
  function initCursor() {
    if (isTouch) return;
    const cursor = document.getElementById("cursor");
    const dot = cursor.querySelector(".cursor__dot");
    const ring = cursor.querySelector(".cursor__ring");
    let mx = window.innerWidth / 2, my = window.innerHeight / 2;
    let rx = mx, ry = my;

    window.addEventListener("mousemove", (e) => {
      mx = e.clientX; my = e.clientY;
      dot.style.left = mx + "px"; dot.style.top = my + "px";
    });
    const loop = () => {
      rx += (mx - rx) * 0.18; ry += (my - ry) * 0.18;
      ring.style.left = rx + "px"; ring.style.top = ry + "px";
      requestAnimationFrame(loop);
    };
    loop();

    document.querySelectorAll('a, button, [data-magnetic], [data-tilt]').forEach((el) => {
      el.addEventListener("mouseenter", () => cursor.classList.add("is-hover"));
      el.addEventListener("mouseleave", () => cursor.classList.remove("is-hover"));
    });
    window.addEventListener("mousedown", () => cursor.classList.add("is-down"));
    window.addEventListener("mouseup", () => cursor.classList.remove("is-down"));
  }

  /* ---------- MAGNETIC BUTTONS ---------- */
  function initMagnetic() {
    if (isTouch || reduceMotion) return;
    document.querySelectorAll("[data-magnetic]").forEach((el) => {
      const strength = 0.35;
      el.addEventListener("mousemove", (e) => {
        const r = el.getBoundingClientRect();
        const x = e.clientX - (r.left + r.width / 2);
        const y = e.clientY - (r.top + r.height / 2);
        el.style.transform = `translate(${x * strength}px, ${y * strength}px)`;
      });
      el.addEventListener("mouseleave", () => { el.style.transform = "translate(0,0)"; });
    });
  }

  /* ---------- 3D TILT CARDS + glow tracking ---------- */
  function initTilt() {
    if (isTouch || reduceMotion) return;
    document.querySelectorAll("[data-tilt]").forEach((card) => {
      const max = 8;
      card.addEventListener("mousemove", (e) => {
        const r = card.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width;
        const py = (e.clientY - r.top) / r.height;
        const rx = (0.5 - py) * max;
        const ry = (px - 0.5) * max;
        card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-4px)`;
        card.style.setProperty("--mx", px * 100 + "%");
        card.style.setProperty("--my", py * 100 + "%");
      });
      card.addEventListener("mouseleave", () => {
        card.style.transform = "perspective(900px) rotateX(0) rotateY(0)";
      });
    });
  }

  /* ---------- INIT ---------- */
  initCursor();
  initMagnetic();
  initTilt();

  // Loader: dismiss on load (max 2s safety cap)
  let dismissed = false;
  const safeDismiss = () => { if (!dismissed) { dismissed = true; dismissLoader(); } };
  window.addEventListener("load", () => setTimeout(safeDismiss, 1500));
  setTimeout(safeDismiss, 2000); // hard cap

  // Boot interactions once GSAP/Lenis are present (they load with defer)
  if (window.gsap) boot();
  else window.addEventListener("load", boot);
})();
