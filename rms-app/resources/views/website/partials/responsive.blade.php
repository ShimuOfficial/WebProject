<style>
    /* Full-site fit: no extra bars, scale down with the screen */
    html {
        overflow-x: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar,
    *::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
        display: none !important;
    }

    *,
    *::before,
    *::after {
        scrollbar-width: none;
        -ms-overflow-style: none;
        box-sizing: border-box;
    }

    html,
    body {
        max-width: 100%;
        overflow-x: hidden;
    }

    img,
    video,
    canvas,
    svg,
    iframe,
    table {
        max-width: 100%;
    }

    .container,
    .orders-wrap,
    .account-wrap {
        width: min(1200px, calc(100% - 24px));
        max-width: 100%;
    }

    .nav,
    .nav-inner,
    .hero-grid,
    .cta-inner,
    .footer-grid,
    .order-cart,
    .orders-card,
    .account-card,
    .dish,
    .feature-card {
        max-width: 100%;
        min-width: 0;
    }

    .category-dropdown-menu,
    .cart-drawer,
    .img-lightbox,
    #dishPreviewModal .dish-preview-panel {
        overflow: hidden;
        scrollbar-width: none;
    }

    .category-dropdown-menu {
        max-height: none;
        overflow-y: hidden;
    }

    .cart-drawer {
        overflow-y: hidden;
    }

    @media (max-width: 1100px) {
        .hero-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stat-divider {
            display: none;
        }
    }

    @media (max-width: 980px) {
        .container,
        .orders-wrap,
        .account-wrap {
            width: min(1200px, calc(100% - 20px));
        }

        .nav-inner {
            padding: 10px 0;
            gap: 10px;
        }

        .brand {
            font-size: 18px;
            min-width: 0;
            max-width: calc(100% - 88px);
        }

        .brand img {
            height: 32px;
            max-width: 110px;
            object-fit: contain;
        }

        .hero {
            padding: 18px 0 12px;
        }

        .hero-grid {
            min-height: 0;
            padding: 22px 18px;
            border-radius: 22px;
            gap: 18px;
        }

        .hero h1 {
            font-size: clamp(28px, 8vw, 42px);
            margin: 10px 0 12px;
        }

        .hero-sub {
            font-size: 14px;
        }

        .hero-actions {
            gap: 8px;
            margin: 16px 0 18px;
        }

        .hero-actions .btn {
            padding: 9px 14px;
            font-size: 13px;
        }

        .features,
        .process,
        .home-menu,
        .menu-section,
        .about,
        .location,
        .orders-page,
        .account-page,
        .cta-section {
            padding-top: 28px;
            padding-bottom: 36px;
        }

        .feature-card {
            min-height: 0;
            padding: 16px;
        }

        .feature-card h3 {
            font-size: 18px;
        }

        .process-grid,
        .features .grid,
        .home-menu .grid,
        #menuGrid,
        .grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px;
        }

        .dish-img {
            height: 140px;
        }

        .cta-inner {
            padding: 22px 18px;
            border-radius: 20px;
            gap: 14px;
        }

        .cta-inner h2 {
            font-size: clamp(20px, 5vw, 28px);
        }

        .orders-title,
        .account-title {
            font-size: clamp(24px, 7vw, 32px);
        }

        .order-cart-row,
        .order-cart-controls,
        .reserve-form,
        .account-grid,
        .about-layout,
        .footer-grid {
            grid-template-columns: 1fr !important;
        }

        .orders-track,
        .status-stepper {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 0;
        }

        .status-step:not(:last-child)::after {
            display: block !important;
        }

        .footer {
            padding: 32px 0 20px;
        }
    }

    @media (max-width: 720px) {
        .process-grid,
        .features .grid,
        .home-menu .grid,
        #menuGrid,
        .grid {
            grid-template-columns: 1fr !important;
        }

        .hero-actions,
        .location-actions,
        .account-head-actions,
        .order-cart-actions,
        .cta-inner .hero-actions {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .hero-actions .btn,
        .location-actions .btn,
        .cta-inner .btn,
        .order-cart-actions .btn-rect {
            width: 100%;
        }

        .order-cart-actions .btn-checkout {
            width: auto;
            align-self: flex-start;
        }

        .cart-item-title {
            grid-template-columns: 72px 1fr;
            gap: 10px;
        }

        .cart-item-media {
            width: 72px;
            height: 56px;
        }

        .dish-body {
            padding: 12px;
        }

        .dish-name {
            font-size: 13px;
        }

        .dish-desc {
            font-size: 11px;
            min-height: 0;
        }

        .section-title {
            font-size: clamp(20px, 6vw, 26px);
        }

        .about-detail,
        .reserve-upcoming-row,
        .orders-line,
        .account-head,
        .orders-head {
            grid-template-columns: 1fr;
            display: grid;
            gap: 8px;
        }

        .orders-card,
        .account-card,
        .order-cart,
        .reserve-panel,
        .location-card {
            padding: 14px;
            border-radius: 16px;
        }

        #dishPreviewImage {
            height: 10.5rem;
        }

        .dish-preview-panel {
            max-height: 88vh;
        }
    }

    @media (max-width: 480px) {
        .container,
        .orders-wrap,
        .account-wrap {
            width: calc(100% - 16px);
        }

        .brand {
            font-size: 15px;
            gap: 6px;
        }

        .brand-badge {
            width: 28px;
            height: 28px;
            border-radius: 8px;
        }

        .nav-toggle {
            padding: 6px 10px;
            font-size: 12px;
        }

        .hero-grid {
            padding: 16px 14px;
            border-radius: 16px;
        }

        .hero h1 {
            font-size: clamp(24px, 9vw, 30px);
        }

        .hero-stats strong {
            font-size: 16px;
        }

        .hero-stats {
            font-size: 11px;
            gap: 8px;
        }

        .btn {
            padding: 8px 12px;
            font-size: 12px;
        }

        .menu-search,
        .order-cart select,
        .order-cart textarea,
        .reserve-form input,
        .reserve-form select,
        .reserve-form textarea,
        .account-input {
            min-height: 40px;
            font-size: 14px;
            padding: 8px 10px;
        }

        .qty-btn {
            width: 26px;
            height: 26px;
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
