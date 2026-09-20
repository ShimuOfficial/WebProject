<style>
    /* Stacking scale so overlays never sit under transformed cards */
    .nav { z-index: 200; }
    .category-dropdown-menu { z-index: 260; }
    .cart-backdrop { z-index: 9000; }
    .cart-drawer { z-index: 9010; }
    .img-lightbox { z-index: 9500; }
    #dishPreviewModal {
        z-index: 10000 !important;
        isolation: isolate;
    }
    #dishPreviewModal .dish-preview-panel {
        z-index: 1;
        isolation: isolate;
    }

    .dish,
    .feature-card,
    .process-step,
    .orders-card,
    .account-card,
    .hero-grid,
    .cta-inner {
        position: relative;
        z-index: 1;
        transform: none;
    }

    @keyframes fadeUp {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .dish {
        animation: fadeUp .45s ease both;
        overflow: hidden;
    }

    .dish:hover {
        z-index: 2;
        transform: none;
        box-shadow: 0 16px 32px rgba(20, 17, 14, .14);
    }

    .grid,
    #menuGrid,
    .home-menu .grid,
    .features .grid,
    .process-grid {
        align-items: stretch;
        isolation: auto;
        overflow: visible;
    }

    body.dish-modal-open .dish,
    body.dish-modal-open .dish:hover,
    body.dish-modal-open .feature-card,
    body.dish-modal-open .hero-grid,
    body.lightbox-open .dish,
    body.lightbox-open .dish:hover {
        transform: none !important;
        animation: none !important;
        z-index: 0 !important;
        filter: none !important;
        pointer-events: none;
    }

    body.dish-modal-open .nav,
    body.lightbox-open .nav {
        z-index: 200;
    }

    #dishPreviewModal .dish-preview-image {
        height: 180px;
        flex: 0 0 180px;
        overflow: hidden;
        background: #111;
    }

    #dishPreviewImage {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #dishPreviewName {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    #dishPreviewPrice {
        white-space: nowrap;
    }

    #dishPreviewIngredients {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .orders-track,
    .status-stepper {
        position: relative;
        z-index: 0;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }

    .status-step {
        z-index: 1;
        min-width: 0;
    }

    .status-step-label {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .about-media-title,
    .dish-stock-badge {
        z-index: 2;
        max-width: calc(100% - 16px);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hero-content,
    .hero-panel {
        z-index: 1;
    }

    @media (max-width: 720px) {
        #dishPreviewModal .dish-preview-image {
            height: 140px;
            flex-basis: 140px;
        }
    }

    #dishPreviewModal .dish-preview-close,
    #dishPreviewClose {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        background: rgba(255,255,255,.95);
        box-shadow: 0 6px 16px rgba(0,0,0,.18);
    }

    #dishPreviewModal .dish-preview-panel > .dish-preview-image + div,
    #dishPreviewModal .tw-px-5 {
        position: relative;
        z-index: 2;
        background: #fff;
    }

    #dishPreviewModal .tw-mb-2 {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dish-preview-chip,
    #dishPreviewIngredients span {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #f5f5f4;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #44403c;
    }

    .btn:hover {
        z-index: 2;
    }

    .section-head,
    .home-menu-head,
    .account-head,
    .orders-head,
    .admin-toolbar {
        margin-bottom: 24px;
    }

    .menu-filters {
        display: grid;
        gap: 16px;
        margin: 8px 0 32px;
    }

    .category-dropdown {
        margin: 0;
    }

    #menuGrid,
    .home-menu .grid,
    .features .grid,
    .process-grid {
        margin-top: 4px;
    }

    .section-sub + .grid,
    .section-sub + .process-grid,
    .section-title + .process-grid,
    .section-title + .grid,
    .section-sub + .location .grid {
        margin-top: 24px;
    }

    .features-grid,
    .process {
        margin-top: 8px;
    }

    .reserve-panel .section-head {
        margin-bottom: 22px;
    }

    .location .grid,
    .about-layout {
        margin-top: 20px;
    }
</style>
