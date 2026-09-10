<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $site['name'] }} | {{ $site['tagline'] }}</title>
    <meta name="theme-color" content="{{ $site['primary_color'] }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @if ($websiteAssets['vite_ready'])
        @vite(['resources/js/app.js'])
    @endif
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Outfit", sans-serif;
            color: var(--text);
            background: #fff;
        }

        img {
            max-width: 100%;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1440px, 96%);
            margin: 0 auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition:
                transform 0.2s,
                box-shadow 0.2s,
                border-color 0.2s,
                background 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(30, 17, 9, 0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #f28b3b);
            color: #fff;
            border-color: transparent;
        }

        .btn-outline {
            background: transparent;
            border-color: var(--brand2);
            color: var(--brand2);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.4);
        }

        .btn-rect {
            border-radius: 10px;
            min-width: 148px;
            padding: 12px 22px;
        }

        .btn:disabled,
        .btn[disabled] {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .nav {
            position: sticky;
            top: 0;
            z-index: 120;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
        }

        .nav-inner {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            padding: 18px 0;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 16px;
        }

        .brand-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: inline-grid;
            place-items: center;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--brand2), var(--brand));
        }

        .hero {
            padding: 84px 0 60px;
            background: var(--bg);
        }

        .hero-content {
            max-width: 620px;
        }

        .hero h1 {
            font-family: "Playfair Display", serif;
            font-size: clamp(36px, 5vw, 56px);
            margin: 10px 0 14px;
        }

        .hero h1 span {
            color: var(--brand);
        }

        .hero-sub {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 78, 137, 0.08);
            color: var(--brand2);
            border: 1px solid rgba(0, 78, 137, 0.15);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .hero-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--brand2);
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin: 22px 0 24px;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(4, auto);
            gap: 24px;
            align-items: center;
            font-size: 12px;
            color: var(--muted);
        }

        .hero-stats strong {
            display: block;
            font-size: 18px;
            color: var(--text);
        }

        .stat-divider {
            width: 1px;
            height: 36px;
            background: var(--border);
        }

        .section-label {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11px;
            color: var(--brand2);
            font-weight: 700;
        }

        .section-title {
            font-family: "Playfair Display", serif;
            font-size: clamp(22px, 3vw, 32px);
            margin: 8px 0 6px;
        }

        .section-sub {
            color: var(--muted);
            font-size: 14px;
            margin: 0;
        }

        .features {
            padding: 70px 0;
            background: #fff7f1;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 28px;
        }

        .feature-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: 0 10px 24px rgba(40, 40, 40, 0.06);
        }

        .feature-card h3 {
            margin: 10px 0 6px;
            font-size: 15px;
        }

        .feature-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(242, 139, 59, 0.12);
            font-size: 20px;
        }

        .footer {
            padding: 48px 0 28px;
            background: #f3f6fb;
            color: var(--text);
            border-top: 1px solid var(--border);
        }

        .signature {
            background: #f7fbff;
        }

        .location {
            background: #f8fff9;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: grid;
            gap: 8px;
            font-size: 13px;
        }

        .footer-links a {
            color: var(--text);
            opacity: 0.85;
        }

        .footer-desc {
            font-size: 13px;
            color: var(--muted);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
        }

        .footer-bottom {
            margin-top: 24px;
            border-top: 1px solid var(--border);
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        .account-page {
            padding: 80px 0 100px;
            background: #fafbfd;
        }

        .account-wrap {
            max-width: 980px;
        }

        .account-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .account-title {
            margin: 6px 0 0;
            font-family: "Playfair Display", serif;
            font-size: clamp(28px, 4vw, 40px);
        }

        .account-head-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .account-card {
            border: 1px solid var(--border);
            border-radius: 20px;
            background: #fff;
            padding: 32px;
            box-shadow: 0 18px 40px rgba(20, 20, 20, 0.06);
        }

        .account-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .account-field {
            margin-bottom: 0;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 16px;
            background: #fcfdff;
            display: grid;
            gap: 6px;
        }

        .account-field-full {
            margin-top: 16px;
        }

        .account-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .account-value {
            font-size: 18px;
            font-weight: 700;
        }

        .account-input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            font: inherit;
            background: #fff;
            color: var(--text);
        }

        .account-input:focus {
            outline: none;
            border-color: var(--brand2);
            box-shadow: 0 0 0 3px rgba(0, 78, 137, 0.12);
        }

        .account-help {
            margin: 20px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .account-actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .account-notice {
            margin-bottom: 16px;
            border: 1px solid rgba(34, 197, 94, 0.25);
            background: rgba(34, 197, 94, 0.1);
            color: #166534;
            padding: 10px 14px;
            border-radius: 10px;
        }

        .orders-page {
            padding: 80px 0 100px;
            background: #fafbfd;
        }

        .orders-wrap {
            max-width: 1100px;
        }

        .orders-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .orders-title {
            margin: 6px 0 0;
            font-family: "Playfair Display", serif;
            font-size: clamp(28px, 4vw, 40px);
        }

        .orders-head-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .orders-notice {
            margin-bottom: 16px;
            border: 1px solid rgba(34, 197, 94, 0.25);
            background: rgba(34, 197, 94, 0.1);
            color: #166534;
            padding: 10px 14px;
            border-radius: 10px;
        }

        .orders-alert {
            margin-bottom: 16px;
            border: 1px solid rgba(239, 68, 68, 0.25);
            background: rgba(239, 68, 68, 0.08);
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 10px;
        }

        .orders-card {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 18px;
            padding: 20px 22px;
            box-shadow: 0 16px 40px rgba(20, 20, 20, 0.06);
            margin-bottom: 16px;
        }

        .orders-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .orders-number {
            font-weight: 800;
        }

        .orders-muted {
            color: var(--muted);
            font-size: 13px;
        }

        .orders-badge {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .orders-approval {
            margin-top: 10px;
            font-size: 13px;
        }

        .orders-track,
        .status-stepper {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0;
            margin-top: 18px;
        }

        .status-step {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-align: center;
            padding: 0 6px;
        }

        .status-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 14px;
            left: calc(50% + 16px);
            right: calc(-50% + 16px);
            height: 3px;
            background: #e5e7eb;
            border-radius: 99px;
        }

        .status-step.is-complete:not(:last-child)::after,
        .status-step.is-current:not(:last-child)::after {
            background: #16a34a;
        }

        .status-step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            background: #f3f4f6;
            color: #6b7280;
            border: 2px solid #d1d5db;
            z-index: 1;
        }

        .status-step.is-complete .status-step-dot {
            background: #16a34a;
            border-color: #16a34a;
            color: #fff;
        }

        .status-step.is-current .status-step-dot {
            background: #fff;
            border-color: #16a34a;
            color: #166534;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .15);
        }

        .status-step-label {
            font-size: 11px;
            font-weight: 800;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .status-step.is-complete .status-step-label,
        .status-step.is-current .status-step-label {
            color: #166534;
        }

        .status-step.is-cancelled .status-step-dot,
        .status-step.is-cancelled:not(:last-child)::after {
            background: #e5e7eb;
            border-color: #d1d5db;
            color: #9ca3af;
        }

        .orders-badge.pending { background: #fff7ed; color: #9a3412; }
        .orders-badge.approved { background: #ecfdf5; color: #166534; }
        .orders-badge.preparing { background: #eff6ff; color: #1d4ed8; }
        .orders-badge.ready { background: #f0fdf4; color: #15803d; }
        .orders-badge.completed { background: #dcfce7; color: #166534; }
        .orders-badge.cancelled { background: #fef2f2; color: #991b1b; }
        .orders-badge.confirmed { background: #ecfdf5; color: #166534; }

        .orders-items {
            margin-top: 10px;
        }

        .orders-total {
            font-weight: 800;
        }

        .orders-pay-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
            border-top: 1px solid var(--border);
            padding-top: 12px;
        }

        .orders-empty {
            font-weight: 700;
        }

        @media (max-width: 900px) {
            .account-grid {
                grid-template-columns: 1fr;
            }

            .orders-track {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 980px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-stats {
                grid-template-columns: repeat(2, auto);
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .hero-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @if (!empty($websiteAssets['inline_css']))
        <style>
            {!! $websiteAssets['inline_css'] !!}
        </style>
    @endif
    <style>
        :root {
            --brand: {{ $site['primary_color'] }};
            --brand2: {{ $site['secondary_color'] }};
            --accent: {{ $site['accent_color'] }};
            --bg: #f4efe6;
            --surface: #fffdf8;
            --card: #fffdf8;
            --border: #e4d8c8;
            --text: #1a1510;
            --muted: #74685c;
            --radius: 14px;
            --radius-lg: 22px;
        }

        /* SHARED GRID UTILITIES */
        .grid {
            display: grid;
            grid-template-columns: repeat(var(--grid-cols, 1), minmax(0, 1fr));
            gap: var(--grid-gap, 16px);
            align-items: var(--grid-align, center);
        }

        /* ABOUT */
        .about {
            padding: 70px 0;
            background: var(--bg)
        }

        .about-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(280px, .95fr);
            gap: 36px;
            align-items: start
        }

        .about-copy {
            display: grid;
            gap: 18px;
            max-width: 760px
        }

        .about-story {
            display: grid;
            gap: 14px
        }

        .about-story p {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.8
        }

        .about-details {
            display: grid;
            gap: 0;
            margin-top: 8px;
            border-top: 1px solid var(--border)
        }

        .about-detail {
            display: grid;
            grid-template-columns: minmax(120px, 180px) 1fr;
            gap: 18px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border)
        }

        .about-detail strong {
            font-size: 14px
        }

        .about-detail span {
            font-size: 14px;
            line-height: 1.65;
            color: var(--muted)
        }

        .about-media {
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 4/3;
            background: var(--surface);
            position: relative
        }

        .about-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .about-media-title {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            width: fit-content;
            max-width: calc(100% - 32px);
            padding: 8px 12px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .92);
            color: var(--text);
            font-size: 13px;
            font-weight: 800;
            box-shadow: 0 10px 24px rgba(30, 17, 9, .14)
        }

        /* SIGNATURE styles removed per request */

        /* LOCATION */
        .location {
            padding: 70px 0;
            background: var(--bg)
        }

        .location-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: grid;
            gap: 12px
        }

        .hours-list {
            display: grid;
            gap: 8px
        }

        .hours-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--muted)
        }

        .location-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px
        }

        /* MENU */
        .menu-section {
            padding: 70px 0
        }

        .menu-toolbar {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px
        }

        .menu-search {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 11px 14px;
            font: inherit;
            color: var(--text);
            font-size: 14px;
            width: 100%
        }

        .menu-search:focus {
            outline: none;
            border-color: var(--accent)
        }

        .menu-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .category-dropdown {
            position: relative;
            width: 100%;
            max-width: 360px
        }

        .category-dropdown-btn {
            width: 100%;
            justify-content: space-between
        }

        .category-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            z-index: 120;
            width: 100%;
            max-height: 320px;
            overflow-y: auto;
            display: grid;
            gap: 8px;
            padding: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .28)
        }

        .category-dropdown-menu[hidden] {
            display: none !important;
        }

        .category-dropdown-menu .cat-tab {
            text-align: left;
            width: 100%
        }

        .category-dropdown-menu .cat-tab.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff
        }

        .menu-reset-btn {
            white-space: nowrap
        }

        .cat-tab {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 8px 14px;
            font: inherit;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s
        }

        .cat-tab:hover {
            border-color: var(--accent);
            color: var(--accent)
        }

        .dish {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: transform .2s, border-color .2s, box-shadow .2s;
            animation: fadeUp .5s ease both
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .dish:hover {
            transform: translateY(-4px);
            border-color: var(--brand);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .3)
        }

        .dish-img {
            height: 160px;
            background: linear-gradient(135deg, #2a1f15, #3d2d1f);
            position: relative;
            overflow: hidden
        }

        .dish-img-trigger {
            display: block;
            width: 100%;
            height: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: zoom-in;
        }

        .dish-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .dish-stock-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            background: #166534;
        }

        .dish-stock-badge.is-low {
            background: #b45309;
        }

        .dish-stock-badge.is-out {
            background: #991b1b;
        }

        .dish-img-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: rgba(255, 255, 255, .2)
        }

        .dish-body {
            padding: 14px
        }

        .dish-name {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 5px;
            overflow-wrap: anywhere
        }

        .dish-desc {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
            min-height: 34px;
            margin-bottom: 10px
        }

        .dish-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap
        }

        .dish-price {
            font-weight: 700;
            font-size: 14px;
            color: var(--brand2)
        }

        .dish-order {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            background: var(--brand2);
            border: 1px solid var(--brand2);
            border-radius: 10px;
            padding: 7px 12px;
            transition: all .2s;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: .02em;
        }

        .dish-order-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
        }

        .dish-order:hover {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .dish-order.is-disabled {
            background: #e5e7eb;
            border-color: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
        }

        .menu-empty {
            display: none;
            grid-column: 1/-1;
            text-align: center;
            padding: 40px;
            color: var(--muted);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            font-size: 14px
        }

        .menu-footer {
            text-align: center;
            margin-top: 24px
        }

        .cart-nav-btn {
            position: relative
        }

        .cart-count {
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            background: var(--accent);
            color: #fff;
            display: inline-grid;
            place-items: center;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 800
        }

        .cart-backdrop {
            position: fixed;
            inset: 0;
            z-index: 140;
            background: rgba(0, 0, 0, .55);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease
        }

        .cart-backdrop.open {
            opacity: 1;
            pointer-events: auto
        }

        .cart-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 150;
            width: min(420px, 92vw);
            background: var(--surface);
            border-left: 1px solid var(--border);
            box-shadow: -18px 0 42px rgba(0, 0, 0, .45);
            transform: translateX(100%);
            transition: transform .24s ease;
            overflow-y: auto;
            padding: 18px
        }

        .cart-drawer.open {
            transform: translateX(0)
        }

        .cart-close {
            width: 34px;
            height: 34px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--card);
            color: var(--text);
            cursor: pointer;
            font-size: 22px;
            line-height: 1
        }

        .order-cart {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            display: grid;
            gap: 16px
        }

        .order-cart-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap
        }

        .order-cart-head h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin: 0
        }

        .order-cart-total {
            color: black;
            font-weight: 800;
            font-size: 20px
        }

        .order-cart-items {
            display: grid;
            gap: 10px
        }

        .order-cart-empty {
            color: var(--muted);
            font-size: 13px
        }

        .order-cart-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-top: 1px solid var(--border)
        }

        .order-cart-row:first-child {
            border-top: 0
        }

        .order-cart-row strong {
            display: block;
            font-size: 14px
        }

        .order-cart-row span {
            color: var(--muted);
            font-size: 12px
        }

        .cart-item-title {
            display: grid;
            grid-template-columns: 130px 1fr;
            align-items: center;
            gap: 14px
        }

        .cart-item-media {
            width: 130px;
            height: 82px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: rgba(42, 125, 95, .08)
        }

        .cart-item-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .cart-item-fallback {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            font-size: 11px;
            color: var(--muted)
        }

        .cart-item-info {
            display: grid;
            gap: 6px
        }

        .cart-item-head {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .cart-remove-btn {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: rgba(212, 98, 42, .08);
            color: var(--brand2);
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            display: grid;
            place-items: center
        }

        .cart-remove-btn:hover {
            background: rgba(212, 98, 42, .18)
        }

        @media (max-width: 720px) {
            .cart-item-title {
                grid-template-columns: 110px 1fr
            }

            .cart-item-media {
                width: 110px;
                height: 72px
            }
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .qty-input {
            width: 72px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 8px;
            font: inherit;
            text-align: center
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            font-weight: 800
        }

        .qty-value {
            min-width: 24px;
            text-align: center;
            font-weight: 800
        }

        .order-cart-controls {
            display: grid;
            grid-template-columns: minmax(180px, .5fr) 1fr;
            gap: 12px
        }

        .order-cart-actions {

            display: flex;
            flex-wrap: wrap;
            gap: 12px
        }

        .order-cart label {
            display: grid;
            gap: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700
        }

        .order-cart .label-title {
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 11px;
            color: var(--muted)
        }

        .order-cart select,
        .order-cart textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--text);
            font: inherit;
            padding: 10px 12px;
            min-height: 44px
        }

        .order-cart textarea {
            resize: vertical;
            min-height: 90px
        }

        .order-cart textarea::placeholder {
            color: rgba(122, 90, 67, .6)
        }

        .order-cart select:focus,
        .order-cart textarea:focus {
            outline: none;
            border-color: rgba(212, 98, 42, .5);
            box-shadow: 0 0 0 3px rgba(212, 98, 42, .15)
        }

        .cod-only {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: rgba(212, 98, 42, .08);
            color: var(--text);
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700
        }

        .policy-box {
            margin-top: 18px;
            padding: 16px 18px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
        }

        .policy-box h3 {
            margin: 0 0 8px;
            font-size: 15px;
        }

        .policy-box ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .reserve-panel {
            margin-top: 36px;
            padding: 28px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--card);
        }

        .reserve-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .reserve-form label,
        .order-cart label {
            display: grid;
            gap: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .reserve-form input,
        .reserve-form select,
        .reserve-form textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            color: var(--text);
            font: inherit;
            padding: 10px 12px;
            min-height: 44px;
        }

        .reserve-notes,
        .reserve-submit {
            grid-column: 1 / -1;
        }

        .reserve-upcoming {
            margin-bottom: 16px;
            display: grid;
            gap: 8px;
        }

        .reserve-upcoming-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            font-size: 14px;
        }

        .img-lightbox {
            position: fixed;
            inset: 0;
            z-index: 400;
            display: none;
            place-items: center;
            padding: 24px;
            background: rgba(15, 10, 6, .78);
        }

        .img-lightbox.is-open {
            display: grid;
        }

        .img-lightbox img {
            max-width: min(920px, 92vw);
            max-height: 78vh;
            border-radius: 16px;
            object-fit: contain;
            background: #111;
        }

        .img-lightbox-caption {
            color: #fff;
            font-weight: 800;
            margin: 12px 0 0;
            text-align: center;
        }

        .img-lightbox-close {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 10px;
            background: #fff;
            color: #111;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        /* CTA */
        .cta-section {
            padding: 70px 0;
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border)
        }

        .cta-inner {
            background: linear-gradient(120deg, rgba(212, 98, 42, .12), rgba(232, 137, 58, .08));
            border: 1px solid rgba(212, 98, 42, .25);
            border-radius: var(--radius-lg);
            padding: 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap
        }

        .cta-inner h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 3.5vw, 34px);
            font-weight: 700;
            margin-bottom: 8px
        }

        .cta-inner p {
            color: var(--muted);
            font-size: 14px
        }


        @media(max-width:980px) {

            .about-layout {
                grid-template-columns: 1fr
            }

            .about-media {
                max-width: 720px
            }

            .menu-toolbar {
                grid-template-columns: 1fr
            }

            .order-cart-controls {
                grid-template-columns: 1fr
            }

            .reserve-form,
            .status-stepper {
                grid-template-columns: 1fr 1fr;
            }

            .grid {
                grid-template-columns: repeat(var(--grid-cols-md, 2), minmax(0, 1fr))
            }
        }

        @media(max-width:620px) {

            .about {
                padding: 48px 0
            }

            .about-detail {
                grid-template-columns: 1fr;
                gap: 6px
            }

            .grid {
                grid-template-columns: repeat(var(--grid-cols-sm, 1), minmax(0, 1fr))
            }

            .hero-stats {
                gap: 16px
            }

            .cta-inner {
                padding: 28px
            }

            .nav-links .btn-ghost {
                display: none
            }

            .order-cart-row {
                grid-template-columns: 1fr;
                align-items: stretch
            }

            .cart-item-title {
                grid-template-columns: 88px 1fr
            }

            .cart-item-media {
                width: 88px;
                height: 64px
            }

            .cta-inner {
                padding: 28px 20px
            }

            .status-stepper {
                grid-template-columns: 1fr;
            }

            .status-step:not(:last-child)::after {
                display: none;
            }
        }
    </style>
    @include('website.partials.theme')
</head>

<body>

    @include('website.partials.header')

    @include('layouts.partials.alerts')

    @yield('content')
    @include('website.sections.cta')

    @include('website.partials.footer')

    @include('website.partials.home-scripts')
    <div class="img-lightbox" id="imgLightbox" hidden>
        <button class="img-lightbox-close" type="button" id="imgLightboxClose" aria-label="Close preview">&times;</button>
        <div>
            <img id="imgLightboxImage" alt="">
            <p class="img-lightbox-caption" id="imgLightboxCaption"></p>
        </div>
    </div>
</body>

</html>
