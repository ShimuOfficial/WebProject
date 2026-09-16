<style>
    :root {
        --brand: #c45c26;
        --brand2: #1f3d34;
        --accent: #e8b86d;
        --bg: #f4efe6;
        --surface: #fffdf8;
        --card: #fffdf8;
        --border: #e4d8c8;
        --text: #1a1510;
        --muted: #74685c;
        --radius: 14px;
        --radius-lg: 22px;
        --ink: #14110e;
        --copper: #c45c26;
        --forest: #1f3d34;
    }

    html {
        overflow-x: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    html::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;
    }

    body {
        font-family: "Outfit", sans-serif;
        background:
            radial-gradient(1200px 400px at 10% -10%, rgba(196, 92, 38, .08), transparent 50%),
            var(--bg);
        overflow-x: hidden;
        color: var(--text);
    }

    h1, h2, .section-title, .hero h1, .account-title, .orders-title {
        font-family: "Fraunces", serif;
        letter-spacing: -.03em;
    }

    .container {
        width: min(1200px, 92%);
    }

    .btn {
        border-radius: 12px;
        padding: 11px 18px;
        font-weight: 650;
        letter-spacing: .01em;
        box-shadow: none;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(20, 17, 14, .12);
    }

    .btn-primary {
        background: var(--copper);
        color: #fff8f1;
        border-color: var(--copper);
    }

    .btn-outline {
        background: transparent;
        border-color: rgba(31, 61, 52, .28);
        color: var(--forest);
    }

    .btn-rect {
        border-radius: 12px;
        min-width: 150px;
        padding: 13px 22px;
    }

    .btn-checkout {
        min-width: 0;
        width: auto;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        border-radius: 8px;
    }

    .order-cart-actions .btn-checkout {
        flex: 0 0 auto;
        width: auto;
    }

    .nav {
        background: rgba(244, 239, 230, .86);
        border-bottom: 1px solid rgba(228, 216, 200, .9);
        backdrop-filter: blur(16px);
    }

    .nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 14px 0;
    }

    .brand {
        font-family: "Fraunces", serif;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.03em;
    }

    .brand-mark {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 10px;
        background: #1a1510;
        flex: 0 0 auto;
    }

    .footer-brand .brand-mark {
        width: 36px;
        height: 36px;
    }

    .footer-social {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        font-weight: 700;
    }

    .contact-photo {
        margin: 8px 0 28px;
        border-radius: 18px;
        overflow: hidden;
        aspect-ratio: 16 / 6;
        background: var(--ink);
    }

    .contact-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    @media (max-width: 720px) {
        .contact-photo {
            aspect-ratio: 16 / 9;
        }
    }

    .brand-badge {
        background: var(--ink);
        border-radius: 14px;
        width: 40px;
        height: 40px;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .nav-link {
        padding: 8px 12px;
        color: var(--muted);
        font-size: 14px;
        font-weight: 600;
        border-radius: 10px;
    }

    .nav-link:hover,
    .nav-link.is-active {
        color: var(--ink);
        background: rgba(255, 253, 248, .7);
    }

    .nav-links .btn {
        padding: 9px 14px;
        font-size: 13px;
    }

    .cart-count {
        min-width: 20px;
        height: 20px;
        border-radius: 999px;
        display: inline-grid;
        place-items: center;
        background: var(--ink);
        color: #fff;
        font-size: 11px;
        margin-left: 4px;
        padding: 0 6px;
    }

    .hero {
        padding: 36px 0 20px;
        background: transparent;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 36px;
        align-items: center;
        background: var(--ink);
        color: #f6efe4;
        border-radius: 32px;
        padding: 48px;
        overflow: hidden;
        position: relative;
        min-height: 520px;
        background-size: cover;
        background-position: center;
    }

    .hero-grid::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg, rgba(20, 17, 14, .92) 0%, rgba(20, 17, 14, .55) 48%, rgba(196, 92, 38, .28) 100%);
    }

    .hero-content,
    .hero-panel {
        position: relative;
        z-index: 1;
    }

    .hero-badge {
        background: rgba(232, 184, 109, .12);
        color: #f3d7a5;
        border-color: rgba(232, 184, 109, .28);
    }

    .hero h1 {
        font-size: clamp(42px, 6vw, 72px);
        line-height: .95;
        margin: 16px 0 18px;
        color: #fffaf3;
    }

    .hero h1 span {
        color: #e8b86d;
        font-style: italic;
    }

    .hero-grid .btn-primary {
        background: #e8b86d;
        color: #1a1510;
        border-color: #e8b86d;
    }

    .hero-grid .btn-outline {
        border-color: rgba(255, 255, 255, .32);
        color: #fffaf3;
    }

    .hero-stats {
        margin-top: 8px;
        color: rgba(246, 239, 228, .7);
        gap: 18px;
    }

    .hero-stats strong {
        color: #fff;
        font-family: "Fraunces", serif;
        font-size: 22px;
    }

    .stat-divider {
        background: rgba(255, 255, 255, .16);
    }

    .hero-panel {
        display: grid;
        gap: 14px;
    }

    .hero-tile {
        background: rgba(255, 253, 248, .1);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 20px;
        padding: 18px 20px;
        backdrop-filter: blur(10px);
    }

    .hero-tile strong {
        display: block;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .hero-tile span {
        color: rgba(246, 239, 228, .72);
        font-size: 13px;
        line-height: 1.5;
    }

    .section-label {
        color: var(--copper);
        letter-spacing: .18em;
    }

    .features {
        padding: 72px 0 20px;
        background: transparent;
    }

    .feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: 0 18px 40px rgba(20, 17, 14, .05);
        padding: 24px;
        min-height: 210px;
    }

    .feature-icon {
        background: #1f3d34;
        color: #f4efe6;
        width: 46px;
        height: 46px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .feature-card h3 {
        font-family: "Fraunces", serif;
        font-size: 22px;
        margin: 16px 0 8px;
    }

    .process {
        padding: 24px 0 72px;
    }

    .process-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-top: 28px;
    }

    .process-step {
        padding: 8px 8px 8px 0;
    }

    .process-step b {
        display: block;
        font-family: "Fraunces", serif;
        font-size: 28px;
        color: var(--copper);
        margin-bottom: 8px;
    }

    .home-menu {
        padding: 10px 0 80px;
    }

    .home-menu-head {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .dish {
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: 0 16px 36px rgba(20, 17, 14, .06);
    }

    .dish:hover {
        transform: none;
        box-shadow: 0 24px 44px rgba(20, 17, 14, .12);
        border-color: rgba(196, 92, 38, .35);
        z-index: 2;
    }

    .dish-img {
        height: 190px;
        background: #2a2118;
    }

    .dish-order {
        background: var(--ink);
        border-color: var(--ink);
        color: #fff8f1;
        border-radius: 11px;
        padding: 8px 13px;
    }

    .dish-order:hover {
        background: var(--copper);
        border-color: var(--copper);
    }

    .dish-price {
        color: var(--copper);
        font-size: 16px;
    }

    .menu-section,
    .about,
    .location,
    .orders-page,
    .account-page {
        background: transparent;
        padding: 48px 0 80px;
    }

    .menu-search,
    .order-cart select,
    .order-cart textarea,
    .reserve-form input,
    .reserve-form select,
    .reserve-form textarea,
    .account-input {
        background: #fffdf8;
        border-color: var(--border);
        border-radius: 12px;
    }

    .orders-card,
    .account-card,
    .location-card,
    .reserve-panel {
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: 0 18px 40px rgba(20, 17, 14, .05);
        border-radius: 24px;
    }

    .status-step.is-complete .status-step-dot,
    .status-step.is-complete:not(:last-child)::after {
        background: #2f7d57;
        border-color: #2f7d57;
    }

    .cta-section {
        background: transparent;
        border: 0;
        padding: 0 0 80px;
    }

    .cta-inner {
        background: var(--forest);
        color: #f6efe4;
        border: 0;
        border-radius: 28px;
        padding: 42px;
    }

    .cta-inner h2 {
        color: #fffaf3;
    }

    .cta-inner p {
        color: rgba(246, 239, 228, .74);
    }

    .cta-inner .btn-outline {
        border-color: rgba(255, 255, 255, .3);
        color: #fff;
    }

    .cta-inner .btn-primary {
        background: #e8b86d;
        color: #1a1510;
        border-color: #e8b86d;
    }

    .footer {
        background: var(--ink);
        color: #f6efe4;
        border-top: 0;
        padding: 56px 0 28px;
    }

    .footer-desc,
    .footer-links,
    .footer-links a,
    .footer-bottom {
        color: rgba(246, 239, 228, .72);
    }

    .footer-bottom {
        border-top-color: rgba(255, 255, 255, .08);
    }

    .policy-box {
        background: #faf6ef;
        border-color: var(--border);
        border-radius: 16px;
    }

    .img-lightbox {
        background: rgba(20, 17, 14, .88);
    }

    @media (max-width: 980px) {
        .hero-grid,
        .process-grid,
        .features .grid,
        .home-menu .grid {
            grid-template-columns: 1fr 1fr;
        }

        .hero-grid {
            padding: 28px 22px;
            min-height: 0;
            grid-template-columns: 1fr;
        }

        .hero-panel {
            display: none;
        }

        .hero-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .stat-divider {
            display: none;
        }

        .nav-toggle {
            display: inline-flex;
        }

        .nav-links {
            display: none;
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            padding-bottom: 10px;
        }

        .nav-inner.is-open .nav-links {
            display: flex;
        }

        .reserve-form,
        .status-stepper,
        .order-cart-controls {
            grid-template-columns: 1fr;
        }

        .status-step:not(:last-child)::after {
            display: none;
        }

        .cta-inner,
        .footer-grid,
        .orders-line,
        .admin-head,
        .account-head {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 620px) {
        .hero-grid,
        .process-grid,
        .features .grid,
        .home-menu .grid,
        #menuGrid {
            grid-template-columns: 1fr !important;
        }

        .hero h1 {
            font-size: 36px;
        }

        .container {
            width: min(1200px, 94%);
        }

        .btn-rect {
            min-width: 0;
            width: 100%;
        }

        .order-cart-actions {
            display: grid;
        }

        .cta-inner {
            padding: 28px 18px;
        }

        .nav-links .btn,
        .nav-links .btn-primary,
        .nav-links .btn-outline {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
    }

    .nav-toggle {
        display: none;
        border: 1px solid var(--border);
        background: #fffdf8;
        border-radius: 10px;
        padding: 8px 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .nav-inner {
        flex-wrap: wrap;
    }
</style>
