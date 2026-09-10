<style>
    :root {
        --sidebar-w: 268px;
        --primary: #c45c26;
        --primary-dark: #9d4318;
        --primary-light: #e07a3d;
        --secondary: #1f3d34;
        --accent: #e8b86d;
        --success: #2f7d57;
        --danger: #c2413b;
        --warning: #d97706;
        --info: #3d6b7a;
        --dark: #14110e;
        --content-bg: #f3eee5;
        --card-bg: #fffdf8;
        --text-primary: #1a1510;
        --text-secondary: #6f6458;
        --text-muted: #94887b;
        --border: #e4d8c8;
        --radius: 14px;
        --radius-sm: 10px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Outfit', system-ui, sans-serif;
        background: var(--content-bg);
        color: var(--text-primary);
        overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--sidebar-w);
        height: 100vh;
        background: #14110e;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        transition: transform .15s ease;
    }

    .sidebar-brand {
        padding: 24px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
    }

    .brand-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        font-size: 20px;
        color: #fff;
        box-shadow: none;
        flex-shrink: 0;
    }

    .brand-text {
        color: #fff;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -.5px;
    }

    .brand-sub {
        color: rgba(255, 255, 255, .45);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sidebar-menu {
        padding: 16px 12px;
        flex: 1;
    }

    .menu-label {
        color: rgba(255, 255, 255, .3);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 16px 16px 8px;
    }

    .menu-category-title {
        color: #000;
        font-size: 13px;
        letter-spacing: 1px;
    }

    .menu-select-card {
        transition: all .2s;
    }

    .menu-select-card.is-clickable {
        cursor: pointer;
    }

    .menu-select-card.is-disabled {
        cursor: not-allowed;
        opacity: .55;
        background: #f8fafc;
    }

    .menu-item-name {
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .menu-item-description {
        color: var(--text-muted);
        font-size: 12px;
    }

    .menu-item-meta {
        font-size: 11px;
        color: var(--text-muted);
    }

    .menu-selected-indicator {
        font-size: 11px;
        color: #16a34a;
        font-weight: 600;
    }

    .menu-recipe-missing {
        font-size: 11px;
        color: #b45309;
    }

    .qty-input-sm {
        max-width: 60px;
    }

    .text-11 {
        font-size: 11px;
    }

    .text-13 {
        font-size: 13px;
    }

    .order-summary-sticky {
        position: sticky;
        top: 100px;
    }

    .menu-empty-note {
        font-size: 13px;
    }

    .stock-after-order-title {
        font-size: 13px;
    }

    .filter-control-auto {
        width: auto;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 11px 16px;
        color: rgba(255, 255, 255, .6);
        text-decoration: none;
        border-radius: var(--radius-sm);
        margin-bottom: 2px;
        transition: color .12s;
        font-size: 14px;
        font-weight: 500;
        position: relative;
    }

    .menu-item:hover {
        background: transparent;
        color: #fff;
        transform: none;
    }

    .menu-item.active {
        background: rgba(255, 255, 255, 0.04);
        color: #fff;
        box-shadow: none;
    }

    .menu-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 22px;
        background: #c45c26;
        border-radius: 0 4px 4px 0;
    }

    .menu-item i {
        width: 22px;
        text-align: center;
        font-size: 15px;
    }

    .menu-item .badge {
        margin-left: auto;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .sidebar-footer {
        padding: 16px 20px;
        border-top: 1px solid rgba(255, 255, 255, .08);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-footer .avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .sidebar-footer .user-name {
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    .sidebar-footer .user-role {
        color: rgba(255, 255, 255, .45);
        font-size: 11px;
        text-transform: capitalize;
    }

    /* Main */
    .main-content {
        margin-left: var(--sidebar-w);
        width: calc(100% - var(--sidebar-w));
        min-height: 100vh;
    }

    @media (min-width: 992px) {
        .main-content {
            margin-left: var(--sidebar-w) !important;
            width: calc(100% - var(--sidebar-w)) !important;
        }
    }

    .top-navbar {
        background: rgba(255, 253, 248, .9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border);
        padding: 16px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.5px;
    }

    .page-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all .2s;
        position: relative;
    }

    .nav-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .nav-btn .notif-dot {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: var(--danger);
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .content-area {
        padding: 28px 32px;
    }

    /* Cards */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: 0 14px 32px rgba(20, 17, 14, .04);
        min-width: 0;
        max-width: 100%;
    }

    .card-header {
        padding: 18px 24px;
        background: transparent;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
    }

    .card-header.d-flex,
    .menu-items-header {
        gap: 10px;
        flex-wrap: wrap;
    }

    .card-body {
        padding: 24px;
    }

    /* Small admin utility classes to reduce inline styles */
    .text-xs {
        font-size: 12px;
    }

    .text-sm {
        font-size: 13px;
    }

    .text-base {
        font-size: 14px;
    }

    .text-lg {
        font-size: 18px;
    }

    .muted {
        color: var(--text-muted) !important;
    }

    .muted-weak {
        color: rgba(0, 0, 0, 0.45) !important;
    }

    .dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .dot-success {
        background: #10b981;
    }

    .dot-danger {
        background: #ef4444;
    }

    .dot-warning {
        background: #f59e0b;
    }

    .card-no-shadow {
        box-shadow: none !important;
    }

    .modal-rounded {
        border-radius: 16px !important;
        border: none !important;
    }

    .no-transform {
        transform: none !important;
    }

    .no-opacity {
        opacity: 0.3 !important;
    }

    .icon-42 {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sticky-top-100 {
        position: sticky;
        top: 100px;
    }

    /* Buttons */
    .btn-primary {
        background: var(--primary) !important;
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 650;
        font-size: 14px;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(196, 92, 38, .22);
    }

    .btn-primary:hover {
        background: var(--primary-dark) !important;
        color: #fff !important;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 13px;
        border-radius: var(--radius-sm);
    }

    .main-content .btn {
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: 650;
        box-shadow: none;
    }

    .main-content .btn-primary {
        background: var(--primary) !important;
        color: #fff !important;
        border-color: var(--primary) !important;
    }

    .main-content .btn-outline-primary,
    .main-content .btn-outline-secondary,
    .main-content .btn-outline-danger,
    .main-content .btn-outline-dark {
        background: #fff !important;
    }

    .main-content .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 12px 0;
    }

    .main-content .pagination .page-link {
        display: inline-block;
        padding: 6px 10px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text-primary);
        border-radius: 8px;
        text-decoration: none;
    }

    .main-content .pagination .page-item.active .page-link {
        background: var(--dark);
        color: #fff;
        border-color: var(--dark);
    }

    .form-control,
    .form-select {
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 14px;
        transition: all .2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
    }

    .alert-dismissible {
        border-radius: var(--radius-sm);
        border: none;
        font-size: 14px;
        font-weight: 500;
    }

    .ops-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding: 22px 24px;
        background: #14110e;
        color: #f6efe4;
        border-radius: 20px;
    }

    .ops-banner .muted {
        color: rgba(246, 239, 228, .62) !important;
    }

    .ops-banner-title {
        font-family: Fraunces, serif;
        font-size: clamp(22px, 4vw, 28px);
        letter-spacing: -.03em;
        overflow-wrap: anywhere;
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 22px;
        position: relative;
        overflow: hidden;
        min-width: 0;
        max-width: 100%;
        box-shadow: 0 14px 32px rgba(20, 17, 14, .04);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--primary);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgb(0 0 0/.08);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 16px;
    }

    .stat-card.primary .stat-icon {
        background: rgba(196, 92, 38, .12);
        color: var(--primary);
    }

    .stat-card.success .stat-icon {
        background: rgba(16, 185, 129, .1);
        color: var(--success);
    }

    .stat-card.warning .stat-icon {
        background: rgba(245, 158, 11, .1);
        color: var(--warning);
    }

    .stat-card.danger .stat-icon {
        background: rgba(239, 68, 68, .1);
        color: var(--danger);
    }

    .stat-card.info .stat-icon {
        background: rgba(6, 182, 212, .1);
        color: var(--info);
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 6px;
        font-weight: 500;
    }

    /* Tables */
    .table thead th {
        background: #f7f1e8;
        border-bottom: 2px solid var(--border);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 14px 16px;
    }

    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #3f2a16;
        background: #f4e7d6;
    }

    .status-badge.pending { background: #fff4d6; color: #92400e; }
    .status-badge.approved { background: #e7f4ee; color: #166534; }
    .status-badge.preparing { background: #e8eef5; color: #1e3a5f; }
    .status-badge.ready { background: #f3ead6; color: #7c4a12; }
    .status-badge.served,
    .status-badge.completed { background: #dcfce7; color: #166534; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .status-badge.available { background: #dcfce7; color: #166534; }
    .status-badge.occupied { background: #fee2e2; color: #991b1b; }
    .status-badge.reserved { background: #f3ead6; color: #7c4a12; }
    .status-badge.confirmed { background: #dcfce7; color: #166534; }

    .status-badge::before {
        content: '';
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.pending::before {
        background: #f59e0b;
    }

    .status-badge.approved::before {
        background: #3b82f6;
    }

    .status-badge.preparing::before {
        background: #3b82f6;
    }

    .status-badge.ready::before {
        background: #6366f1;
    }

    .status-badge.served::before,
    .status-badge.completed::before {
        background: #10b981;
    }

    .status-badge.cancelled::before {
        background: #ef4444;
    }

    .status-badge.available::before {
        background: #10b981;
    }

    .status-badge.occupied::before {
        background: #ef4444;
    }

    .status-badge.reserved::before {
        background: #3b82f6;
    }

    .status-badge.maintenance::before {
        background: #6b7280;
    }

    .status-badge.cancelled {
        border: 1px solid rgba(239, 68, 68, 0.08);
    }

    .status-badge.served,
    .status-badge.completed {
        border: 1px solid rgba(16, 185, 129, 0.06);
    }

    .table-card {
        border-radius: var(--radius);
        padding: 20px;
        text-align: center;
        transition: all .3s;
        border: 2px solid var(--border);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .table-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgb(0 0 0/.08);
    }

    .table-card.available {
        border-color: #86efac;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }

    .table-card.occupied {
        border-color: #fca5a5;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
    }

    .table-card.reserved {
        border-color: #93c5fd;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
    }

    .table-card.maintenance {
        border-color: #d1d5db;
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
    }

    .table-card .table-num {
        font-size: 24px;
        font-weight: 800;
        margin: 8px 0 4px;
    }

    .table-card .table-cap {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Kitchen */
    .kitchen-ticket {
        border-radius: 12px;
        border-top: 5px solid;
        background: var(--card-bg);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .kitchen-ticket.pending {
        border-color: var(--warning);
    }

    .kitchen-ticket.preparing {
        border-color: var(--primary);
    }

    .ticket-header {
        padding: 16px;
        border-bottom: 2px dashed var(--border);
        background: rgba(0, 0, 0, 0.02);
        gap: 8px;
        flex-wrap: wrap;
    }

    .ticket-body {
        padding: 16px;
        min-height: 150px;
    }

    .ticket-footer {
        padding: 16px;
        background: rgba(0, 0, 0, 0.02);
        display: flex;
        gap: 10px;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-weight: 500;
        font-size: 15px;
        gap: 8px;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .item-notes {
        font-size: 12px;
        color: var(--danger);
        font-style: italic;
        margin-top: -5px;
        margin-bottom: 10px;
    }

    .qty-badge {
        background: var(--dark);
        color: #fff;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: bold;
    }

    /* Orders / Menu Select */
    .menu-select-card.selected {
        border-color: var(--primary) !important;
        background: rgba(196, 92, 38, .06);
        box-shadow: none;
    }

    .menu-items-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .menu-search-wrap {
        position: relative;
        width: min(280px, 100%);
    }

    .menu-search-wrap i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 12px;
        pointer-events: none;
    }

    .menu-search-wrap .form-control {
        padding-left: 32px;
    }

    @media (max-width: 575.98px) {
        .menu-search-wrap {
            width: 100%;
        }
    }

    /* Mobile */
    .sidebar-toggle {
        display: none;
    }

    @media(max-width:991px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .sidebar-toggle {
            display: flex;
        }

        .content-area {
            padding: 20px 16px;
        }

        .top-navbar {
            padding: 12px 16px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .fade-in {
        animation: fadeIn .4s ease forwards;
    }

    .fade-in-delay-1 {
        animation-delay: .1s;
        opacity: 0;
    }

    .fade-in-delay-2 {
        animation-delay: .2s;
        opacity: 0;
    }

    .fade-in-delay-3 {
        animation-delay: .3s;
        opacity: 0;
    }

    .admin-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .admin-toolbar-form {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        flex: 1 1 280px;
        min-width: 0;
    }

    .admin-search {
        flex: 1 1 220px;
        min-width: 0;
        max-width: 360px;
    }

    .admin-filter {
        width: min(180px, 100%);
    }

    .admin-media-card {
        overflow: hidden;
        min-width: 0;
        max-width: 100%;
    }

    .admin-media-card__image {
        height: 170px;
        overflow: hidden;
        background: #1a1510;
    }

    .admin-media-card__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .admin-media-card__body {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .admin-media-card__desc {
        font-size: 13px;
        min-height: 0;
        overflow-wrap: anywhere;
    }

    .admin-media-card__foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: auto;
    }

    .admin-media-card__price {
        font-size: 18px;
        color: var(--primary);
        word-break: break-word;
    }

    .admin-media-card__actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .admin-image-preview {
        width: 100%;
        height: 160px;
        border-radius: 12px;
        overflow: hidden;
        background: #1a1510;
        border: 1px solid var(--border);
    }

    .admin-image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .menu-select-card {
        min-width: 0;
        overflow: hidden;
    }

    .menu-select-card__media {
        height: 110px;
        margin: -16px -16px 12px;
        overflow: hidden;
        background: #1a1510;
        border-radius: 10px 10px 0 0;
    }

    .menu-select-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(20, 17, 14, .46);
        z-index: 999;
    }

    body.sidebar-open {
        overflow: hidden;
    }

    .min-w-0 { min-width: 0; }

    .stat-value {
        font-size: clamp(18px, 2.4vw, 28px);
        overflow-wrap: anywhere;
        line-height: 1.15;
    }

    .table-card {
        min-width: 0;
        max-width: 100%;
        padding: 14px 10px;
    }

    .table-card .table-num {
        font-size: clamp(14px, 2vw, 22px);
        word-break: break-word;
    }

    .top-navbar {
        flex-wrap: wrap;
        gap: 10px;
    }

    .top-navbar > div:first-child {
        min-width: 0;
        flex: 1 1 180px;
        display: flex;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 10px 12px;
    }

    .top-navbar > div:first-child .page-title,
    .top-navbar > div:first-child .page-subtitle {
        flex: 1 1 140px;
    }

    .page-title {
        overflow-wrap: anywhere;
    }

    .content-area .row > [class*="col-"] {
        min-width: 0;
    }

    .content-area canvas {
        max-width: 100%;
    }

    @media (max-width: 991px) {
        body.sidebar-open .sidebar-backdrop {
            display: block;
        }

        .ops-banner {
            padding: 16px;
        }

        .stat-card {
            padding: 16px;
        }

        .card-body {
            padding: 16px;
        }

        .card-header {
            padding: 14px 16px;
        }

        .table thead th,
        .table tbody td {
            padding: 10px;
        }

        .admin-media-card__image {
            height: 150px;
        }
    }

    @media (max-width: 575.98px) {
        .content-area {
            padding: 14px 12px;
        }

        .admin-search,
        .admin-filter {
            max-width: none;
            width: 100%;
        }

        .navbar-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .table-card {
            padding: 12px 8px;
        }

        .table-card .btn {
            padding: 4px 8px;
        }

        .admin-media-card__actions .btn {
            min-width: 36px;
        }

        .filter-control-auto {
            width: 100%;
        }
    }
</style>
