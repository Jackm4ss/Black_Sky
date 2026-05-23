@viteReactRefresh

@if (request()->is('admin/login'))
    @vite('src/filament-auth-shape-grid.tsx')
@else
    @vite('src/filament-admin.ts')
@endif

<style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

    :root {
        --bsa-bg: #020817;
        --bsa-bg-2: #030712;
        --bsa-surface: #060b16;
        --bsa-surface-2: #080e1c;
        --bsa-border: #1d283a;
        --bsa-border-soft: rgba(148, 163, 184, 0.14);
        --bsa-text: #f8fafc;
        --bsa-muted: #94a3b8;
        --bsa-faint: #64748b;
        --bsa-blue: #2f80ed;
        --bsa-cyan: #06b6d4;
        --bsa-green: #10b981;
        --bsa-red: #e11d48;
        --bsa-violet: #8b5cf6;
        --neo-ink: #02030a;
        --neo-paper: #f8fafc;
        --neo-paper-muted: #e9ebef;
        --neo-blue: #0ea5e9;
        --neo-blue-dark: #0284c7;
        --neo-purple: #a855f7;
        --neo-rose: #e11d48;
        --neo-green: #10b981;
        --neo-yellow: #facc15;
        --neo-page: #030213;
        --neo-grid: rgba(148, 163, 184, 0.16);
        --neo-dark-surface: #07111f;
        --neo-dark-surface-2: #0b1626;
        --neo-dark-elevated: #101827;
        --neo-dark-text: #f8fafc;
        --neo-dark-muted: #cbd5e1;
        --neo-border: 3px solid var(--neo-ink);
        --neo-shadow-sm: 4px 4px 0 var(--neo-ink);
        --neo-shadow-md: 7px 7px 0 var(--neo-ink);
        --neo-shadow-lg: 10px 10px 0 var(--neo-ink);
    }

    html,
    html.dark,
    body.fi-body,
    .fi-body,
    .fi-layout,
    .fi-main,
    .fi-topbar,
    .fi-sidebar {
        background: var(--neo-page);
        color: var(--neo-dark-text);
        color-scheme: dark;
    }

    .fi-main {
        background:
            linear-gradient(var(--neo-grid) 1px, transparent 1px),
            linear-gradient(90deg, var(--neo-grid) 1px, transparent 1px),
            var(--neo-page) !important;
        background-size: 42px 42px;
        padding-inline: 24px;
    }

    body.fi-panel-admin,
    body.fi-panel-admin .fi-layout,
    body.fi-panel-admin .fi-main,
    body.fi-panel-admin .fi-main-ctn,
    body.fi-panel-admin .fi-page {
        background-color: var(--neo-page) !important;
        color: var(--neo-dark-text) !important;
    }

    .fi-main > .fi-page > section {
        padding-block: 24px;
        gap: 24px;
    }

    .fi-sidebar {
        border-right: var(--neo-border);
        background: var(--neo-dark-surface) !important;
        box-shadow: var(--neo-shadow-md);
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin .fi-sidebar,
    body.fi-panel-admin .fi-sidebar-nav {
        background-color: var(--neo-dark-surface) !important;
        color: var(--neo-dark-text) !important;
    }

    .fi-sidebar-header {
        height: 64px;
        border-bottom: var(--neo-border);
        background: var(--neo-yellow);
        box-shadow: 0 5px 0 var(--neo-ink);
        padding-inline: 16px;
    }

    .fi-sidebar-header a {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .fi-sidebar-header a::after {
        content: "Welcome";
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 0.06em;
        line-height: 1;
        text-transform: uppercase;
    }

    .fi-logo {
        color: var(--neo-ink) !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 21px;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    img.fi-logo {
        display: block;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: none;
        box-sizing: border-box;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-ink);
        box-shadow: 3px 3px 0 var(--neo-ink);
        padding: 5px;
        object-fit: contain !important;
        object-position: center;
    }

    .fi-sidebar img.fi-logo {
        height: 48px !important;
    }

    .fi-sidebar-nav {
        padding: 24px 16px 16px;
    }

    .fi-sidebar-group-label {
        color: var(--neo-dark-muted);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .fi-sidebar-item a {
        min-height: 36px;
        border: 2px solid transparent;
        border-radius: 2px;
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .fi-sidebar-item-label,
    .fi-sidebar-item-icon {
        color: inherit !important;
    }

    .fi-sidebar-item a:hover {
        border-color: var(--neo-ink);
        background: var(--neo-yellow);
        box-shadow: var(--neo-shadow-sm);
        color: var(--neo-ink);
    }

    .fi-sidebar-item.fi-active a,
    .fi-sidebar-item-active a {
        border-color: var(--neo-ink);
        background: var(--neo-blue);
        box-shadow: var(--neo-shadow-sm);
        color: #fff;
    }

    .fi-sidebar-item.fi-active svg,
    .fi-sidebar-item-active svg {
        color: #fff;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-active .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon,
    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: #fff !important;
    }

    .fi-topbar nav {
        position: relative;
        min-height: 68px;
        height: 68px;
        overflow: hidden;
        background: var(--neo-dark-surface) !important;
        border-bottom: var(--neo-border);
        box-shadow: 0 8px 0 var(--neo-blue);
        padding-inline: 24px;
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin .fi-topbar {
        background-color: var(--neo-page) !important;
        color: var(--neo-dark-text) !important;
    }

    .fi-topbar nav::before {
        content: "";
        position: absolute;
        top: 13px;
        left: clamp(92px, 18vw, 280px);
        width: 76px;
        height: 36px;
        border: 3px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-purple);
        box-shadow: 5px 5px 0 var(--neo-ink);
        transform: rotate(-3deg);
        pointer-events: none;
    }

    .fi-topbar nav::after {
        content: "";
        position: absolute;
        right: clamp(118px, 18vw, 260px);
        bottom: -10px;
        width: 92px;
        height: 28px;
        border: 3px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-blue);
        box-shadow: 5px 5px 0 var(--neo-ink);
        transform: rotate(4deg);
        pointer-events: none;
    }

    .fi-topbar nav > * {
        position: relative;
        z-index: 1;
    }

    .fi-topbar svg {
        color: var(--neo-dark-text);
    }

    .fi-topbar-open-sidebar-btn,
    .fi-topbar-close-sidebar-btn {
        border: var(--neo-border);
        border-radius: 2px;
        background: #fff;
        box-shadow: var(--neo-shadow-sm);
        color: var(--neo-ink);
    }

    .fi-user-menu-trigger {
        border: var(--neo-border);
        border-radius: 2px;
        background: #fff;
        box-shadow: var(--neo-shadow-sm);
    }

    .fi-simple-layout {
        position: relative;
        min-height: 100vh;
        overflow: hidden;
        background: #030213;
        color: #030213;
        font-family: 'Inter', sans-serif;
    }

    .bsa-auth-shape-grid-host {
        position: fixed;
        inset: 0;
        z-index: 0;
        opacity: 0.9;
        pointer-events: auto;
    }

    .bsa-auth-shape-grid-host .shapegrid-canvas {
        height: 100vh;
        min-height: 100%;
        width: 100vw;
    }

    .fi-simple-main-ctn {
        position: relative;
        z-index: 1;
        padding: 32px 20px;
    }

    .fi-simple-main {
        position: relative;
        width: min(100%, 456px) !important;
        max-width: 456px !important;
        margin-block: 0 !important;
        padding: 44px 40px 38px !important;
        border: 3px solid #02030a !important;
        border-radius: 2px !important;
        background: #f8fafc !important;
        color: #030213 !important;
        box-shadow: 12px 12px 0 #02030a !important;
        ring: 0 !important;
    }

    .fi-simple-main::before {
        content: "ADMIN ACCESS";
        position: absolute;
        top: -18px;
        left: 24px;
        padding: 6px 12px;
        border: 3px solid #02030a;
        background: #e11d48;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.18em;
        line-height: 1;
        text-transform: uppercase;
        box-shadow: 5px 5px 0 #02030a;
    }

    .fi-simple-header {
        align-items: flex-start !important;
        gap: 0;
        margin-bottom: 0 !important;
    }

    .fi-simple-page > section.grid.auto-cols-fr {
        gap: 16px !important;
        row-gap: 16px !important;
    }

    .fi-simple-header .fi-logo {
        margin-bottom: 10px !important;
    }

    .fi-simple-header img.fi-logo {
        width: 58px !important;
        height: auto !important;
        max-height: none !important;
        margin-bottom: 12px !important;
        border-width: 3px;
        box-shadow: 5px 5px 0 var(--neo-ink);
        padding: 4px;
    }

    .fi-simple-header-heading {
        color: #030213 !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 24px !important;
        font-weight: 800 !important;
        letter-spacing: 0.08em !important;
        line-height: 1 !important;
        text-align: left !important;
        text-transform: uppercase;
    }

    .fi-simple-header-subheading {
        max-width: 360px;
        margin-top: 6px !important;
        margin-bottom: 0 !important;
        color: #334155 !important;
        font-family: 'Barlow', sans-serif;
        font-size: 14px !important;
        font-weight: 500;
        line-height: 1.45 !important;
        text-align: left !important;
    }

    .fi-simple-layout .fi-form {
        gap: 18px !important;
    }

    .fi-simple-layout .fi-fo-field-wrp-label span,
    .fi-simple-layout .fi-checkbox-list-option-label,
    .fi-simple-layout .fi-fo-checkbox-list-option-label,
    .fi-simple-layout label span {
        color: #030213 !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px !important;
        font-weight: 700 !important;
        letter-spacing: 0.12em;
        line-height: 1.1 !important;
        text-transform: uppercase;
    }

    .fi-simple-layout .fi-fo-field-wrp-label sup {
        color: #e11d48 !important;
        font-size: 13px;
        line-height: 1;
    }

    .fi-simple-layout .fi-input-wrp {
        min-height: 46px;
        overflow: hidden;
        border: 3px solid #02030a !important;
        border-radius: 2px !important;
        background: #fff !important;
        box-shadow: 5px 5px 0 #02030a !important;
        outline: 0 !important;
        ring: 0 !important;
    }

    .fi-simple-layout .fi-input-wrp:focus-within {
        border-color: #0ea5e9 !important;
        box-shadow: 5px 5px 0 #a855f7 !important;
    }

    .fi-simple-layout .fi-input {
        min-height: 40px;
        color: #030213 !important;
        font-family: 'Inter', sans-serif;
        font-size: 14px !important;
        font-weight: 500;
    }

    .fi-simple-layout .fi-input::placeholder {
        color: #717182 !important;
    }

    .fi-simple-layout .fi-input-wrp-suffix {
        border-color: #02030a !important;
        background: #ececf0;
        color: #030213 !important;
    }

    .fi-simple-layout .fi-icon-btn {
        border-radius: 2px !important;
        color: #030213 !important;
    }

    .fi-simple-layout .fi-checkbox-input {
        width: 16px !important;
        height: 16px !important;
        border: 2px solid #02030a !important;
        border-radius: 2px !important;
        background: #fff !important;
        box-shadow: 3px 3px 0 #02030a !important;
        color: #0ea5e9 !important;
    }

    .fi-simple-layout .fi-checkbox-input:checked {
        background-color: #0ea5e9 !important;
    }

    .fi-simple-layout .fi-btn {
        min-height: 46px;
        border: 3px solid #02030a !important;
        border-radius: 2px !important;
        background: #0284c7 !important;
        color: #fff !important;
        box-shadow: 6px 6px 0 #02030a !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px !important;
        font-weight: 800 !important;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease;
    }

    .fi-simple-layout .fi-btn:hover {
        background: #0ea5e9 !important;
        transform: translate(2px, 2px);
        box-shadow: 4px 4px 0 #02030a !important;
    }

    .fi-simple-layout .fi-btn:focus-visible,
    .fi-simple-layout .fi-icon-btn:focus-visible,
    .fi-simple-layout .fi-checkbox-input:focus-visible {
        outline: 3px solid rgba(168, 85, 247, 0.65) !important;
        outline-offset: 3px;
    }

    .fi-simple-layout .fi-btn svg,
    .fi-simple-layout .fi-icon-btn svg {
        color: currentColor !important;
    }

    .fi-simple-layout .fi-section,
    .fi-simple-layout .fi-modal-window {
        border: 3px solid #02030a !important;
        border-radius: 2px !important;
        box-shadow: 9px 9px 0 #02030a !important;
    }

    .bsa-dashboard {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: min(100%, 1122px);
        margin-inline: auto;
        color: var(--neo-ink);
    }

    .bsa-hero,
    .bsa-stat-card,
    .bsa-panel {
        border: var(--neo-border);
        border-radius: 2px;
        background: var(--neo-paper);
        box-shadow: var(--neo-shadow-lg);
        color: var(--neo-ink);
    }

    .bsa-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 168px;
        padding: 32px 24px;
        overflow: hidden;
        background: var(--neo-purple);
    }

    .bsa-hero::before,
    .bsa-hero::after {
        content: "";
        position: absolute;
        border: var(--neo-border);
        border-radius: 2px;
        box-shadow: var(--neo-shadow-sm);
        pointer-events: none;
    }

    .bsa-hero::before {
        right: 162px;
        top: 28px;
        width: 78px;
        height: 42px;
        background: var(--neo-yellow);
        transform: rotate(5deg);
    }

    .bsa-hero::after {
        right: 36px;
        bottom: 30px;
        width: 96px;
        height: 46px;
        background: var(--neo-blue);
        transform: rotate(-4deg);
    }

    .bsa-hero > * {
        position: relative;
        z-index: 1;
    }

    .bsa-eyebrow,
    .bsa-panel-head span {
        margin: 0 0 6px;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .bsa-hero h1 {
        margin: 0;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 28px;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1.15;
        text-transform: uppercase;
    }

    .bsa-muted,
    .bsa-hero-date span,
    .bsa-panel-head p {
        color: var(--neo-ink);
    }

    .bsa-hero-date {
        min-width: 112px;
        padding: 12px 14px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-paper);
        box-shadow: var(--neo-shadow-sm);
        text-align: right;
    }

    .bsa-hero-date span {
        display: block;
        font-size: 13px;
        font-weight: 700;
    }

    .bsa-hero-date strong {
        display: block;
        margin-top: 8px;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 0;
    }

    .bsa-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
    }

    .bsa-stat-card {
        position: relative;
        min-height: 164px;
        padding: 24px;
    }

    .bsa-tone-blue {
        background: #dbeafe;
    }

    .bsa-tone-cyan {
        background: #cffafe;
    }

    .bsa-tone-green {
        background: #d1fae5;
    }

    .bsa-tone-violet {
        background: #ede9fe;
    }

    .bsa-stat-icon {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        margin-bottom: 12px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-paper);
        box-shadow: var(--neo-shadow-sm);
        color: var(--neo-ink);
    }

    .bsa-stat-icon svg {
        width: 20px;
        height: 20px;
    }

    .bsa-stat-card span {
        display: block;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .bsa-stat-card strong {
        display: block;
        margin-top: 8px;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 35px;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1;
    }

    .bsa-stat-card em,
    .bsa-panel-head em,
    .bsa-country-row em {
        border-radius: 999px;
        font-style: normal;
        font-weight: 800;
    }

    .bsa-stat-card em {
        position: absolute;
        right: 24px;
        bottom: 31px;
        border: 2px solid var(--neo-ink);
        padding: 5px 9px;
        background: var(--neo-green);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px;
        letter-spacing: 0.08em;
    }

    .bsa-stat-card em.bsa-empty-badge {
        background: var(--neo-dark-elevated);
        color: var(--neo-dark-muted);
    }

    .bsa-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
        gap: 24px;
    }

    .bsa-panel {
        padding: 24px;
    }

    .bsa-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .bsa-panel-head h2 {
        max-width: 430px;
        margin: 0;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1.25;
        text-transform: uppercase;
    }

    .bsa-panel-head em {
        padding: 6px 10px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-rose);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .bsa-panel-head button {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: #fff;
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: var(--neo-ink);
    }

    .bsa-panel-head button svg {
        width: 18px;
        height: 18px;
    }

    .bsa-empty-state {
        display: grid;
        place-items: center;
        gap: 12px;
        min-height: 260px;
        margin-top: 30px;
        padding: 32px;
        border: 2px dashed rgba(248, 250, 252, 0.26);
        border-radius: 2px;
        background: rgba(16, 24, 39, 0.72);
        text-align: center;
    }

    .bsa-empty-state-compact {
        min-height: 420px;
    }

    .bsa-empty-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        box-shadow: var(--neo-shadow-sm);
        color: var(--neo-dark-text);
    }

    .bsa-empty-icon svg {
        width: 24px;
        height: 24px;
    }

    .bsa-empty-state strong {
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 0.04em;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .bsa-empty-state span {
        max-width: 360px;
        color: var(--neo-dark-muted);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.45;
    }

    .bsa-traffic-body {
        display: grid;
        grid-template-columns: minmax(230px, 0.86fr) minmax(310px, 1.14fr);
        align-items: center;
        gap: 32px;
        margin-top: 30px;
    }

    .bsa-donut {
        position: relative;
        display: grid;
        place-items: center;
        width: min(100%, 250px);
        aspect-ratio: 1;
        margin-inline: auto;
        border: 3px solid var(--neo-ink);
        border-radius: 50%;
        background: var(--donut);
        box-shadow: var(--neo-shadow-md);
    }

    .bsa-donut::before {
        content: "";
        position: absolute;
        inset: 34px;
        border: 3px solid var(--neo-ink);
        border-radius: inherit;
        background: var(--neo-paper);
    }

    .bsa-donut div {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .bsa-donut span {
        display: block;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .bsa-donut strong {
        display: block;
        margin-top: 4px;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 0;
    }

    .bsa-source-list {
        display: flex;
        flex-direction: column;
    }

    .bsa-source-row {
        display: grid;
        grid-template-columns: 48px minmax(0, 1fr) auto;
        align-items: center;
        gap: 16px;
        min-height: 70px;
        border-bottom: 2px solid var(--neo-ink);
    }

    .bsa-source-row:last-child {
        border-bottom: 0;
    }

    .bsa-source-icon {
        width: 42px;
        height: 42px;
        display: grid !important;
        place-items: center;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--source-color);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff;
        flex: 0 0 auto;
        margin: 0 !important;
        line-height: 1 !important;
    }

    .bsa-source-icon svg {
        width: 22px;
        height: 22px;
        display: block;
        color: currentColor;
    }

    .bsa-source-icon[data-icon-style="stroke"] svg {
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .bsa-source-icon[data-icon-style="fill"] svg {
        fill: currentColor;
        stroke: none;
    }

    .bsa-source-icon--compact {
        width: 28px;
        height: 28px;
        box-shadow: 3px 3px 0 var(--neo-ink);
    }

    .bsa-source-icon--compact svg {
        width: 15px;
        height: 15px;
    }

    .bsa-source-row strong,
    .bsa-country-row strong {
        display: block;
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .bsa-source-row div > span,
    .bsa-country-row div > span {
        display: block;
        margin-top: 4px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .bsa-source-row b {
        color: var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.04em;
    }

    .bsa-table-source {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 34px;
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.04em;
        line-height: 1;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .bsa-table-member {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        min-width: 220px;
    }

    .bsa-table-member__avatar {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        flex: 0 0 auto;
        overflow: hidden;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.06em;
        line-height: 1;
    }

    .bsa-table-member__avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bsa-table-member__copy {
        display: grid;
        gap: 4px;
        min-width: 0;
    }

    .bsa-table-member__copy strong {
        overflow: hidden;
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.04em;
        line-height: 1.1;
        text-overflow: ellipsis;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .bsa-table-member__copy span {
        overflow: hidden;
        color: var(--neo-dark-muted);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bsa-table-country {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 34px;
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.04em;
        line-height: 1;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .bsa-table-source > span:last-child {
        color: inherit;
    }

    .bsa-table-source--empty,
    .bsa-table-country--empty {
        color: var(--neo-dark-muted);
        gap: 8px;
    }

    .bsa-table-source--empty svg {
        width: 18px;
        height: 18px;
        color: currentColor;
    }

    .bsa-country-list {
        margin-top: 22px;
    }

    .bsa-country-row {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        align-items: center;
        gap: 16px;
        min-height: 70px;
        border-bottom: 2px solid var(--neo-ink);
    }

    .bsa-country-row:last-child {
        border-bottom: 0;
    }

    .bsa-country-flag {
        display: grid !important;
        place-items: center;
        width: 42px !important;
        height: 42px !important;
        margin: 0 !important;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background-color: var(--neo-dark-elevated);
        box-shadow: 3px 3px 0 var(--neo-ink);
        line-height: 1 !important;
        overflow: hidden;
    }

    .bsa-country-flag img {
        display: block;
        width: 28px !important;
        height: 28px !important;
        margin: 0 !important;
    }

    .bsa-country-flag__fallback {
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .bsa-country-flag--compact {
        width: 28px !important;
        height: 28px !important;
        box-shadow: 2px 2px 0 var(--neo-ink);
    }

    .bsa-country-flag--compact img {
        width: 18px !important;
        height: 18px !important;
    }

    .bsa-country-row strong {
        font-size: 22px;
        letter-spacing: 0;
    }

    .bsa-country-row em {
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        padding: 6px 10px;
        box-shadow: 3px 3px 0 var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        letter-spacing: 0.08em;
    }

    .bsa-trend-up {
        background: var(--neo-green);
        color: var(--neo-ink);
    }

    .bsa-trend-down {
        background: var(--neo-rose);
        color: #fff;
    }

    body.fi-panel-admin .fi-topbar-open-sidebar-btn svg,
    body.fi-panel-admin .fi-topbar-close-sidebar-btn svg,
    body.fi-panel-admin .fi-user-menu-trigger svg {
        color: var(--neo-ink) !important;
    }

    body.fi-panel-admin .bsa-dashboard {
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin .bsa-hero,
    body.fi-panel-admin .bsa-stat-card,
    body.fi-panel-admin .bsa-panel {
        border: var(--neo-border);
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin .bsa-hero {
        background: var(--neo-purple);
        box-shadow: 10px 10px 0 var(--neo-blue);
        color: var(--neo-ink);
    }

    body.fi-panel-admin .bsa-hero h1,
    body.fi-panel-admin .bsa-hero .bsa-eyebrow,
    body.fi-panel-admin .bsa-hero .bsa-muted {
        color: var(--neo-ink);
    }

    body.fi-panel-admin .bsa-panel {
        background: var(--neo-dark-surface);
        box-shadow: 10px 10px 0 var(--neo-purple);
    }

    body.fi-panel-admin .bsa-stat-card {
        box-shadow: 8px 8px 0 var(--neo-ink);
    }

    body.fi-panel-admin .bsa-tone-blue {
        background: #082f49;
    }

    body.fi-panel-admin .bsa-tone-cyan {
        background: #083344;
    }

    body.fi-panel-admin .bsa-tone-green {
        background: #052e2b;
    }

    body.fi-panel-admin .bsa-tone-violet {
        background: #2e1065;
    }

    body.fi-panel-admin .bsa-stat-card span,
    body.fi-panel-admin .bsa-stat-card strong,
    body.fi-panel-admin .bsa-panel-head h2,
    body.fi-panel-admin .bsa-source-row strong,
    body.fi-panel-admin .bsa-country-row strong,
    body.fi-panel-admin .bsa-source-row b,
    body.fi-panel-admin .bsa-donut span,
    body.fi-panel-admin .bsa-donut strong {
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin .bsa-panel-head span,
    body.fi-panel-admin .bsa-source-row div > span,
    body.fi-panel-admin .bsa-country-row div > span {
        color: var(--neo-dark-muted);
    }

    body.fi-panel-admin .bsa-source-row,
    body.fi-panel-admin .bsa-country-row {
        border-bottom-color: rgba(248, 250, 252, 0.18);
    }

    body.fi-panel-admin .bsa-source-icon {
        border-color: #020617;
        box-shadow: 3px 3px 0 #020617;
    }

    body.fi-panel-admin .bsa-source-icon--compact {
        box-shadow: 3px 3px 0 #020617;
    }

    body.fi-panel-admin .bsa-donut::before {
        background: var(--neo-dark-surface-2);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) {
        --neo-paper: var(--neo-dark-surface);
        --neo-paper-muted: var(--neo-dark-surface-2);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-layout,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-main,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-page,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-main-ctn {
        background-color: var(--neo-page) !important;
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-nav,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-header {
        background: var(--neo-dark-surface) !important;
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-header {
        box-shadow: 0 5px 0 var(--neo-blue);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-logo {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-header a::after {
        color: var(--neo-dark-text);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-group-label {
        color: var(--neo-dark-muted) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item a:not(:hover):not([aria-current='page']) {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item a:hover {
        border-color: var(--neo-blue) !important;
        background: rgba(14, 165, 233, 0.18) !important;
        box-shadow: 4px 4px 0 #020617 !important;
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item a:hover svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item a:hover .fi-sidebar-item-label,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item a:hover .fi-sidebar-item-icon {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item.fi-active a:hover,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item-active a:hover {
        background: var(--neo-blue) !important;
        color: #fff !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item.fi-active a:hover svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item-active a:hover svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item.fi-active a:hover .fi-sidebar-item-label,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-sidebar-item-active a:hover .fi-sidebar-item-label {
        color: #fff !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar nav {
        background: var(--neo-dark-surface) !important;
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar nav {
        box-shadow: 0 8px 0 var(--neo-blue);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar-open-sidebar-btn,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar-close-sidebar-btn,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-user-menu-trigger,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-panel-head button,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero-date,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-stat-icon {
        background: var(--neo-dark-elevated) !important;
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar-open-sidebar-btn svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-topbar-close-sidebar-btn svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .fi-user-menu-trigger svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-panel-head button svg,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-stat-icon svg {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-dashboard {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero {
        background:
            linear-gradient(135deg, rgba(168, 85, 247, 0.18), rgba(14, 165, 233, 0.12)),
            var(--neo-dark-surface-2) !important;
        box-shadow: 10px 10px 0 var(--neo-purple);
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero::before {
        background: var(--neo-dark-elevated);
        box-shadow: 5px 5px 0 var(--neo-purple);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero::after {
        background: var(--neo-dark-surface);
        box-shadow: 5px 5px 0 var(--neo-blue);
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero h1,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero .bsa-eyebrow,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero-date strong {
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero .bsa-muted,
    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-hero-date span {
        color: var(--neo-dark-muted) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-panel {
        background: var(--neo-dark-surface) !important;
        box-shadow: 10px 10px 0 var(--neo-purple);
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-stat-card {
        box-shadow: 8px 8px 0 var(--neo-ink);
        color: var(--neo-dark-text) !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-tone-blue {
        background: #082f49 !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-tone-cyan {
        background: #083344 !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-tone-green {
        background: #052e2b !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-tone-violet {
        background: #2e1065 !important;
    }

    body.fi-panel-admin:not(:has(.fi-simple-layout)) .bsa-donut::before {
        background: var(--neo-dark-surface-2) !important;
    }

    .bsa-events {
        width: min(100%, 1180px);
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
        color: var(--neo-dark-text);
    }

    .bsa-events-hero,
    .bsa-events-card {
        border: var(--neo-border);
        border-radius: 2px;
        background: var(--neo-dark-surface);
        box-shadow: 10px 10px 0 var(--neo-purple);
    }

    .bsa-events-hero {
        position: relative;
        min-height: 164px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 24px;
        overflow: hidden;
        padding: 28px 30px;
        background:
            linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(168, 85, 247, 0.16)),
            var(--neo-dark-surface-2);
    }

    .bsa-events-hero::before,
    .bsa-events-hero::after {
        content: "";
        position: absolute;
        border: var(--neo-border);
        border-radius: 2px;
        pointer-events: none;
    }

    .bsa-events-hero::before {
        right: 150px;
        top: 28px;
        width: 84px;
        height: 38px;
        background: var(--neo-dark-elevated);
        box-shadow: 5px 5px 0 var(--neo-purple);
        transform: rotate(5deg);
    }

    .bsa-events-hero::after {
        right: 30px;
        bottom: 22px;
        width: 92px;
        height: 30px;
        background: var(--neo-blue);
        box-shadow: 5px 5px 0 var(--neo-ink);
        transform: rotate(-4deg);
    }

    .bsa-events-hero > * {
        position: relative;
        z-index: 1;
    }

    .bsa-events-hero h1,
    .bsa-events-card h2 {
        margin: 0;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        text-transform: uppercase;
        text-shadow: 2px 2px 0 var(--neo-blue);
    }

    .bsa-events-hero h1 {
        font-size: clamp(30px, 4vw, 48px);
        line-height: 0.9;
    }

    .bsa-events .bsa-muted,
    .bsa-events-hero .bsa-eyebrow {
        color: var(--neo-dark-muted) !important;
    }

    .bsa-events-total {
        min-width: 148px;
        border: var(--neo-border);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        box-shadow: var(--neo-shadow-sm);
        padding: 14px 16px;
        text-align: right;
    }

    .bsa-events-total span,
    .bsa-events-field span,
    .bsa-events-card-head .bsa-eyebrow {
        display: block;
        color: var(--neo-dark-muted);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .bsa-events-field .bsa-events-required-mark {
        display: inline-block;
        margin-left: 4px;
        color: #fb174f;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1;
        text-transform: none;
        vertical-align: top;
    }

    .bsa-events-total strong {
        display: block;
        margin-top: 4px;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 36px;
        font-weight: 900;
        line-height: 0.95;
    }

    .bsa-events-card {
        overflow: hidden;
    }

    .bsa-events-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: var(--neo-border);
        padding: 22px 24px 18px;
    }

    .bsa-events-card h2 {
        margin-top: 6px;
        font-size: 30px;
        line-height: 0.95;
    }

    .bsa-events-add,
    .bsa-events-modal-primary,
    .bsa-events-modal-secondary,
    .bsa-events-modal-danger,
    .bsa-events-modal-close {
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        box-shadow: 4px 4px 0 var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.12em;
        line-height: 1;
        text-transform: uppercase;
    }

    .bsa-events-add,
    .bsa-events-modal-primary,
    .bsa-events-modal-danger {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding-inline: 14px;
        color: #fff;
        text-decoration: none;
    }

    .bsa-events-add,
    .bsa-events-modal-primary {
        background: var(--neo-blue);
    }

    .bsa-events-add svg,
    .bsa-events-modal-primary svg,
    .bsa-events-modal-danger svg {
        width: 16px;
        height: 16px;
    }

    .bsa-events-toolbar {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(280px, 340px) 92px auto;
        align-items: end;
        gap: 12px;
        border-bottom: var(--neo-border);
        background: var(--neo-dark-surface-2);
        padding: 16px 18px;
    }

    .bsa-events-field {
        display: grid;
        gap: 7px;
        min-width: 0;
    }

    .bsa-events-field-range {
        position: relative;
    }

    .bsa-events-field input,
    .bsa-events-field textarea,
    .bsa-events-field select,
    .bsa-events-clear,
    .bsa-events-actions a,
    .bsa-events-actions button,
    .bsa-events-pagination button {
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        color: var(--neo-dark-text);
        box-shadow: 4px 4px 0 var(--neo-ink);
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .bsa-events-field input,
    .bsa-events-field select,
    .bsa-events-clear {
        height: 38px;
    }

    .bsa-events-field input,
    .bsa-events-field textarea,
    .bsa-events-field select {
        width: 100%;
        padding-inline: 12px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0;
        text-transform: none;
    }

    .bsa-events-field select {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 42px;
        background-color: var(--neo-dark-elevated);
        background-image:
            linear-gradient(45deg, transparent 50%, var(--neo-dark-text) 50%),
            linear-gradient(135deg, var(--neo-dark-text) 50%, transparent 50%),
            linear-gradient(to right, rgba(203, 213, 225, 0.18), rgba(203, 213, 225, 0.18));
        background-position:
            calc(100% - 19px) 52%,
            calc(100% - 13px) 52%,
            calc(100% - 35px) 50%;
        background-repeat: no-repeat;
        background-size: 6px 6px, 6px 6px, 1px 20px;
    }

    .bsa-events-field select::-ms-expand {
        display: none;
    }

    .bsa-events-field textarea {
        min-height: 88px;
        padding-block: 10px;
        resize: vertical;
    }

    .bsa-events-field input:focus,
    .bsa-events-field textarea:focus,
    .bsa-events-field select:focus {
        outline: 2px solid var(--neo-blue);
        outline-offset: 2px;
    }

    .bsa-events-field em {
        color: #fecdd3;
        font-family: 'Barlow', sans-serif;
        font-size: 12px;
        font-style: normal;
        font-weight: 700;
    }

    .bsa-events-field small {
        display: block;
        max-width: 720px;
        color: var(--neo-dark-muted);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 1.45;
        text-transform: none;
    }

    .bsa-events-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 94px;
        padding-inline: 12px;
        background: var(--neo-rose);
        color: #fff;
    }

    .bsa-events-clear svg,
    .bsa-events-actions svg,
    .bsa-events-pagination svg {
        width: 16px;
        height: 16px;
    }

    .bsa-events-table-wrap {
        overflow-x: auto;
        background: var(--neo-dark-surface);
    }

    .bsa-events-table {
        width: 100%;
        min-width: 940px;
        border-collapse: collapse;
        color: var(--neo-dark-text);
        font-size: 14px;
    }

    .bsa-events-table th,
    .bsa-events-table td {
        border-bottom: 1px solid rgba(203, 213, 225, 0.16);
        padding: 10px 18px;
        text-align: left;
        vertical-align: middle;
    }

    .bsa-events-table th {
        height: 42px;
        color: var(--neo-dark-muted);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .bsa-events-table tbody tr {
        min-height: 68px;
        background: rgba(11, 22, 38, 0.82);
        transition: background 160ms ease, color 160ms ease;
    }

    .bsa-events-table tbody tr:hover {
        background: rgba(14, 165, 233, 0.12);
    }

    .bsa-event-product {
        display: grid;
        grid-template-columns: 60px minmax(0, 1fr);
        align-items: center;
        gap: 14px;
        min-width: 300px;
    }

    .bsa-event-product > div {
        min-width: 0;
    }

    .bsa-event-product img {
        width: 60px;
        height: 40px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        box-shadow: 3px 3px 0 var(--neo-ink);
        object-fit: cover;
        background: var(--neo-dark-elevated);
    }

    .bsa-event-product strong,
    .bsa-event-date strong,
    .bsa-event-venue strong {
        display: block;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 17px;
        font-weight: 900;
        line-height: 1;
        text-transform: uppercase;
    }

    .bsa-event-product span,
    .bsa-event-date span,
    .bsa-event-venue span {
        display: block;
        margin-top: 4px;
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.25;
    }

    .bsa-event-product strong,
    .bsa-event-product span,
    .bsa-event-venue strong,
    .bsa-event-venue span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bsa-event-date,
    .bsa-event-venue {
        min-width: 150px;
    }

    .bsa-event-venue {
        max-width: 230px;
    }

    .bsa-event-chip,
    .bsa-event-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        box-shadow: 3px 3px 0 var(--neo-ink);
        padding: 5px 10px;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .bsa-event-chip {
        background: var(--event-accent, var(--neo-blue));
    }

    .bsa-event-status {
        background: var(--neo-dark-elevated);
    }

    .bsa-event-status-published {
        background: var(--neo-green);
        color: var(--neo-ink);
    }

    .bsa-event-status-sold-out {
        background: var(--neo-rose);
    }

    .bsa-events-actions-head {
        text-align: right !important;
    }

    .bsa-events-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .bsa-events-actions a,
    .bsa-events-actions button {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .bsa-events-actions a:hover,
    .bsa-events-actions button:hover,
    .bsa-events-pagination button:hover:not(:disabled),
    .bsa-events-pagination button.is-active {
        background: var(--neo-yellow);
        color: var(--neo-ink);
    }

    .bsa-events-actions .bsa-events-danger-action:hover {
        background: var(--neo-rose);
        color: #fff;
    }

    .bsa-events-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: grid;
        place-items: center;
        overflow-y: auto;
        background: rgba(3, 2, 19, 0.82);
        padding: 28px;
    }

    .bsa-events-modal,
    .bsa-events-delete-modal {
        width: min(100%, 980px);
        max-height: calc(100vh - 56px);
        overflow-y: auto;
        border: var(--neo-border);
        border-radius: 2px;
        background: var(--neo-dark-surface);
        box-shadow: 12px 12px 0 var(--neo-purple);
        color: var(--neo-dark-text);
    }

    .bsa-events-delete-modal {
        width: min(100%, 520px);
        padding: 24px;
    }

    .bsa-events-delete-modal h2,
    .bsa-events-modal-head h2 {
        margin: 6px 0 0;
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 30px;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 0.95;
        text-transform: uppercase;
        text-shadow: 2px 2px 0 var(--neo-blue);
    }

    .bsa-events-delete-modal p {
        margin: 16px 0 0;
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        line-height: 1.5;
    }

    .bsa-events-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        border-bottom: var(--neo-border);
        background: var(--neo-dark-surface-2);
        padding: 22px 24px;
    }

    .bsa-events-modal-close {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--neo-dark-elevated);
        color: #fff;
    }

    .bsa-events-modal-close svg {
        width: 18px;
        height: 18px;
    }

    .bsa-events-form {
        display: grid;
        gap: 18px;
        padding: 22px 24px 24px;
    }

    .bsa-events-form-page .bsa-events-hero {
        align-items: end;
    }

    .bsa-events-form-card {
        overflow: visible;
    }

    .bsa-events-back-link {
        min-width: 118px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .bsa-events-back-link svg {
        width: 16px;
        height: 16px;
    }

    .bsa-events-form-panel {
        display: grid;
        gap: 16px;
        border: 2px solid rgba(203, 213, 225, 0.18);
        background: rgba(11, 22, 38, 0.68);
        padding: 16px;
    }

    .bsa-events-form-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-bottom: 1px solid rgba(203, 213, 225, 0.16);
        padding-bottom: 12px;
    }

    .bsa-events-form-panel-head span {
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.12em;
        line-height: 1;
        text-transform: uppercase;
    }

    .bsa-events-advanced summary small,
    .bsa-events-upload-preview span {
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0;
        text-transform: none;
    }

    .bsa-events-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .bsa-events-form-grid-simple {
        grid-template-columns: minmax(0, 1.1fr) minmax(180px, 0.9fr);
    }

    .bsa-events-form-section {
        display: grid;
        gap: 12px;
        border-top: 1px solid rgba(203, 213, 225, 0.16);
        padding-top: 20px;
    }

    .bsa-events-field-wide {
        grid-column: 1 / -1;
    }

    .bsa-events-advanced {
        border: 2px solid rgba(203, 213, 225, 0.18);
        background: rgba(15, 23, 42, 0.62);
        padding: 0;
    }

    .bsa-events-advanced[open] {
        padding-bottom: 16px;
    }

    .bsa-events-advanced summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        cursor: pointer;
        list-style: none;
        padding: 15px 16px;
    }

    .bsa-events-advanced summary::-webkit-details-marker {
        display: none;
    }

    .bsa-events-advanced summary span {
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .bsa-events-advanced summary::after {
        content: '+';
        width: 28px;
        height: 28px;
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--neo-ink);
        background: var(--neo-blue);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 20px;
        font-weight: 900;
        line-height: 1;
    }

    .bsa-events-advanced[open] summary::after {
        content: '-';
    }

    .bsa-events-advanced .bsa-events-form-grid {
        padding: 0 16px;
    }

    .bsa-events-section-builder {
        display: grid;
        gap: 14px;
        padding: 0 16px;
    }

    .bsa-events-detail-grid {
        display: grid;
        gap: 14px;
    }

    .bsa-events-section-add {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 12px;
        border: 2px dashed rgba(203, 213, 225, 0.2);
        background: rgba(14, 165, 233, 0.06);
        padding: 14px;
    }

    .bsa-events-section-list {
        display: grid;
        gap: 14px;
    }

    .bsa-events-section-block,
    .bsa-events-section-empty {
        border: 2px solid rgba(203, 213, 225, 0.18);
        background: rgba(11, 22, 38, 0.72);
        padding: 14px;
    }

    .bsa-events-section-block {
        display: grid;
        gap: 12px;
    }

    .bsa-events-section-block-head {
        display: grid;
        grid-template-columns: minmax(150px, 0.9fr) minmax(180px, 1.2fr) auto auto;
        align-items: end;
        gap: 12px;
    }

    .bsa-events-section-toggle {
        min-height: 38px;
        align-items: center;
        padding-bottom: 2px;
    }

    .bsa-events-section-empty {
        display: grid;
        gap: 6px;
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }

    .bsa-events-section-empty strong {
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: 0.08em;
        line-height: 1;
        text-transform: uppercase;
    }

    .bsa-events-upload {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px;
        gap: 16px;
        align-items: stretch;
        border: 2px solid rgba(203, 213, 225, 0.16);
        background: rgba(11, 22, 38, 0.72);
        padding: 14px;
    }

    .bsa-events-upload .bsa-events-field input[type='file'] {
        height: auto;
        min-height: 46px;
        padding: 10px;
        cursor: pointer;
    }

    .bsa-events-upload .bsa-events-field input[type='file']::file-selector-button {
        margin-right: 12px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-blue);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff;
        cursor: pointer;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.1em;
        padding: 8px 10px;
        text-transform: uppercase;
    }

    .bsa-events-upload-preview {
        min-height: 112px;
        display: grid;
        place-items: center;
        align-content: center;
        gap: 8px;
        border: 2px solid var(--neo-ink);
        background: var(--neo-dark-elevated);
        box-shadow: 5px 5px 0 var(--neo-ink);
        overflow: hidden;
        padding: 10px;
        text-align: center;
    }

    .bsa-events-upload-preview img {
        width: 100%;
        height: 96px;
        object-fit: cover;
        border: 2px solid rgba(203, 213, 225, 0.24);
    }

    .bsa-events-upload-preview svg {
        width: 32px;
        height: 32px;
        color: var(--neo-blue);
    }

    .bsa-events-toggle {
        min-height: 68px;
        display: inline-flex;
        align-items: end;
        gap: 10px;
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .bsa-events-toggle input {
        width: 18px;
        height: 18px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        box-shadow: 3px 3px 0 var(--neo-ink);
        accent-color: var(--neo-blue);
    }

    .bsa-events-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        border-top: var(--neo-border);
        margin-top: 4px;
        padding-top: 18px;
    }

    .bsa-events-delete-modal .bsa-events-modal-footer {
        margin-top: 20px;
    }

    .bsa-events-modal-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding-inline: 14px;
        background: var(--neo-dark-elevated);
        color: #fff;
        text-decoration: none;
    }

    .bsa-events-modal-danger {
        background: var(--neo-rose);
    }

    .bsa-events-empty {
        min-height: 240px;
        display: grid;
        place-items: center;
        align-content: center;
        gap: 10px;
        border: 2px dashed rgba(203, 213, 225, 0.22);
        margin: 18px;
        padding: 32px;
        text-align: center;
    }

    .bsa-events-empty div {
        width: 50px;
        height: 50px;
        display: grid;
        place-items: center;
        border: var(--neo-border);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        box-shadow: var(--neo-shadow-sm);
    }

    .bsa-events-empty svg {
        width: 24px;
        height: 24px;
    }

    .bsa-events-empty strong {
        color: #fff;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 24px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .bsa-events-empty span {
        max-width: 420px;
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        line-height: 1.5;
    }

    .bsa-events-footer {
        min-height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-top: var(--neo-border);
        background: var(--neo-dark-surface-2);
        padding: 14px 18px;
    }

    .bsa-events-footer p {
        margin: 0;
        color: var(--neo-dark-muted);
        font-family: 'Barlow', sans-serif;
        font-size: 13px;
        font-weight: 600;
    }

    .bsa-events-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bsa-events-pagination button {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .bsa-events-pagination button:disabled {
        opacity: 0.42;
        cursor: not-allowed;
        box-shadow: none;
    }

    .fi-resource-list-records-page,
    .fi-resource-create-record-page,
    .fi-resource-edit-record-page {
        width: min(100%, 1122px);
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-inline: auto;
        color: var(--neo-dark-text);
    }

    .fi-resource-list-records-page .fi-header,
    .fi-resource-create-record-page .fi-header,
    .fi-resource-edit-record-page .fi-header {
        position: relative;
        min-height: 164px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 24px;
        overflow: hidden;
        border: var(--neo-border);
        border-radius: 2px;
        background:
            linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(168, 85, 247, 0.16)),
            var(--neo-dark-surface-2);
        box-shadow: 10px 10px 0 var(--neo-purple);
        padding: 28px 30px;
    }

    .fi-resource-list-records-page .fi-header::before,
    .fi-resource-create-record-page .fi-header::before,
    .fi-resource-edit-record-page .fi-header::before,
    .fi-resource-list-records-page .fi-header::after,
    .fi-resource-create-record-page .fi-header::after,
    .fi-resource-edit-record-page .fi-header::after {
        content: "";
        position: absolute;
        border: var(--neo-border);
        border-radius: 2px;
        pointer-events: none;
    }

    .fi-resource-list-records-page .fi-header::before,
    .fi-resource-create-record-page .fi-header::before,
    .fi-resource-edit-record-page .fi-header::before {
        right: 150px;
        top: 28px;
        width: 84px;
        height: 38px;
        background: var(--neo-dark-elevated);
        box-shadow: 5px 5px 0 var(--neo-purple);
        transform: rotate(5deg);
    }

    .fi-resource-list-records-page .fi-header::after,
    .fi-resource-create-record-page .fi-header::after,
    .fi-resource-edit-record-page .fi-header::after {
        right: 30px;
        bottom: 22px;
        width: 92px;
        height: 30px;
        background: var(--neo-blue);
        box-shadow: 5px 5px 0 var(--neo-ink);
        transform: rotate(-4deg);
    }

    .fi-resource-list-records-page .fi-header > *,
    .fi-resource-create-record-page .fi-header > *,
    .fi-resource-edit-record-page .fi-header > * {
        position: relative;
        z-index: 1;
    }

    .fi-resource-list-records-page .fi-breadcrumbs,
    .fi-resource-create-record-page .fi-breadcrumbs,
    .fi-resource-edit-record-page .fi-breadcrumbs {
        display: block !important;
        margin: 0 0 8px !important;
    }

    .fi-resource-list-records-page .fi-breadcrumbs a,
    .fi-resource-create-record-page .fi-breadcrumbs a,
    .fi-resource-edit-record-page .fi-breadcrumbs a,
    .fi-resource-list-records-page .fi-breadcrumbs li,
    .fi-resource-create-record-page .fi-breadcrumbs li,
    .fi-resource-edit-record-page .fi-breadcrumbs li,
    .fi-resource-list-records-page .fi-breadcrumbs span,
    .fi-resource-create-record-page .fi-breadcrumbs span,
    .fi-resource-edit-record-page .fi-breadcrumbs span {
        color: var(--neo-dark-muted) !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .fi-resource-list-records-page .fi-header-heading,
    .fi-resource-create-record-page .fi-header-heading,
    .fi-resource-edit-record-page .fi-header-heading {
        margin: 0;
        color: #fff !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: clamp(30px, 4vw, 48px) !important;
        font-style: italic;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
        line-height: 0.9 !important;
        text-transform: uppercase;
        text-shadow: 2px 2px 0 var(--neo-blue);
    }

    .fi-resource-list-records-page .fi-ta,
    .fi-resource-list-records-page .fi-ta-ctn,
    .fi-resource-create-record-page .fi-section,
    .fi-resource-edit-record-page .fi-section {
        overflow: hidden;
        border: var(--neo-border) !important;
        border-radius: 2px !important;
        background: var(--neo-dark-surface) !important;
        box-shadow: 10px 10px 0 var(--neo-purple) !important;
        color: var(--neo-dark-text) !important;
        ring: 0 !important;
    }

    .fi-resource-list-records-page .fi-ta-header-toolbar,
    .fi-resource-list-records-page .fi-ta-filters,
    .fi-resource-list-records-page .fi-pagination,
    .fi-resource-create-record-page .fi-section-header,
    .fi-resource-edit-record-page .fi-section-header {
        border-bottom: var(--neo-border);
        background: var(--neo-dark-surface-2);
    }

    .fi-resource-list-records-page .fi-ta-header-toolbar,
    .fi-resource-list-records-page .fi-ta-filters,
    .fi-resource-create-record-page .fi-section-header,
    .fi-resource-edit-record-page .fi-section-header {
        padding: 20px 24px !important;
    }

    .fi-resource-list-records-page .fi-ta-content {
        overflow-x: auto;
        background: var(--neo-dark-surface);
    }

    .fi-resource-list-records-page .fi-ta-table {
        min-width: 940px;
        border-collapse: collapse;
        color: var(--neo-dark-text) !important;
    }

    .fi-resource-list-records-page .fi-ta-header-cell,
    .fi-resource-list-records-page .fi-ta-cell {
        border-bottom: 1px solid rgba(203, 213, 225, 0.16) !important;
        background: transparent !important;
        padding: 10px 18px !important;
    }

    .fi-resource-list-records-page .fi-ta-header-cell-label,
    .fi-resource-list-records-page .fi-ta-header-cell button,
    .fi-resource-list-records-page .fi-ta-text,
    .fi-resource-list-records-page .fi-ta-text-item,
    .fi-resource-list-records-page .fi-ta-actions a,
    .fi-resource-list-records-page .fi-ta-actions button,
    .fi-resource-list-records-page .fi-pagination,
    .fi-resource-list-records-page .fi-pagination label {
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .fi-resource-list-records-page .fi-ta-header-cell-label,
    .fi-resource-list-records-page .fi-ta-header-cell button {
        color: var(--neo-dark-muted) !important;
        font-size: 12px !important;
    }

    .fi-resource-users .fi-ta-table thead th:last-child > .group {
        justify-content: center !important;
    }

    .fi-resource-users .fi-ta-actions-cell > div {
        display: flex;
        justify-content: center;
    }

    .fi-resource-list-records-page .fi-ta-row:hover {
        background: rgba(14, 165, 233, 0.12) !important;
    }

    .fi-resource-list-records-page .fi-ta-text,
    .fi-resource-list-records-page .fi-ta-text-item,
    .fi-resource-list-records-page .fi-ta-text-item-label,
    .fi-resource-list-records-page .fi-ta-cell a {
        color: var(--neo-dark-text) !important;
    }

    .fi-resource-list-records-page .fi-ta-image {
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        box-shadow: 3px 3px 0 var(--neo-ink);
        object-fit: cover;
    }

    .fi-resource-list-records-page .fi-badge {
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-blue) !important;
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .fi-resource-list-records-page .fi-ta-actions a,
    .fi-resource-list-records-page .fi-ta-actions button {
        min-height: 32px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-dark-elevated);
        box-shadow: 3px 3px 0 var(--neo-ink);
        color: #fff !important;
        font-size: 12px;
    }

    .fi-resource-list-records-page .fi-ta-actions a:hover,
    .fi-resource-list-records-page .fi-ta-actions button:hover {
        background: var(--neo-yellow);
        color: var(--neo-ink) !important;
    }

    .fi-resource-list-records-page .fi-pagination {
        padding: 14px 18px !important;
        color: var(--neo-dark-muted) !important;
    }

    .fi-resource-create-record-page .fi-section-header-heading,
    .fi-resource-edit-record-page .fi-section-header-heading {
        color: #fff !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 22px !important;
        font-weight: 900 !important;
        letter-spacing: 0.08em;
        line-height: 1;
        text-transform: uppercase;
    }

    .fi-resource-create-record-page .fi-section-header-description,
    .fi-resource-edit-record-page .fi-section-header-description,
    .fi-resource-create-record-page .fi-fo-field-wrp-helper-text,
    .fi-resource-edit-record-page .fi-fo-field-wrp-helper-text {
        color: var(--neo-dark-muted) !important;
        font-family: 'Barlow', sans-serif;
        font-weight: 700;
        line-height: 1.45;
    }

    .fi-resource-create-record-page .fi-section-content,
    .fi-resource-edit-record-page .fi-section-content {
        background: var(--neo-dark-surface);
        padding: 22px 24px !important;
    }

    .fi-resource-edit-record-page .bsa-user-saved-events {
        display: grid;
        gap: 12px;
        color: var(--neo-dark-text);
    }

    .fi-resource-edit-record-page .bsa-user-saved-events > div {
        display: inline-flex;
        align-items: baseline;
        gap: 8px;
    }

    .fi-resource-edit-record-page .bsa-user-saved-events > div strong {
        color: var(--neo-blue);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 0.04em;
        line-height: 1;
    }

    .fi-resource-edit-record-page .bsa-user-saved-events > div span,
    .fi-resource-edit-record-page .bsa-user-saved-events li span,
    .fi-resource-edit-record-page .bsa-user-saved-events em,
    .fi-resource-edit-record-page .bsa-form-muted {
        color: var(--neo-dark-muted);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
    }

    .fi-resource-edit-record-page .bsa-user-saved-events ul {
        display: grid;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .fi-resource-edit-record-page .bsa-user-saved-events li {
        display: grid;
        gap: 4px;
        padding: 10px 12px;
        border: 1px solid rgba(203, 213, 225, 0.16);
        background: rgba(15, 23, 42, 0.42);
    }

    .fi-resource-edit-record-page .bsa-user-saved-events li strong {
        color: var(--neo-dark-text);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.05em;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .fi-resource-create-record-page .fi-fo-field-wrp-label span,
    .fi-resource-edit-record-page .fi-fo-field-wrp-label span,
    .fi-resource-create-record-page .fi-fo-field-wrp-label sup,
    .fi-resource-edit-record-page .fi-fo-field-wrp-label sup {
        color: var(--neo-dark-muted) !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px !important;
        font-weight: 900 !important;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .fi-resource-create-record-page .fi-fo-field-wrp-label sup,
    .fi-resource-edit-record-page .fi-fo-field-wrp-label sup {
        color: #fb174f !important;
        font-family: 'Inter', sans-serif;
        letter-spacing: 0;
    }

    .fi-resource-create-record-page .fi-input-wrp,
    .fi-resource-edit-record-page .fi-input-wrp,
    .fi-resource-create-record-page .fi-select-input,
    .fi-resource-edit-record-page .fi-select-input,
    .fi-resource-create-record-page .choices__inner,
    .fi-resource-edit-record-page .choices__inner,
    .fi-resource-create-record-page .fi-fo-rich-editor,
    .fi-resource-edit-record-page .fi-fo-rich-editor,
    .fi-resource-create-record-page .fi-rich-editor,
    .fi-resource-edit-record-page .fi-rich-editor {
        border: 2px solid var(--neo-ink) !important;
        border-radius: 2px !important;
        background: var(--neo-dark-elevated) !important;
        color: var(--neo-dark-text) !important;
        box-shadow: 4px 4px 0 var(--neo-ink) !important;
        ring: 0 !important;
    }

    .fi-resource-create-record-page .fi-input,
    .fi-resource-edit-record-page .fi-input,
    .fi-resource-create-record-page .fi-select-input,
    .fi-resource-edit-record-page .fi-select-input,
    .fi-resource-create-record-page textarea,
    .fi-resource-edit-record-page textarea {
        color: var(--neo-dark-text) !important;
        font-family: 'Inter', sans-serif;
        font-size: 13px !important;
        font-weight: 600;
    }

    .fi-resource-create-record-page .fi-input::placeholder,
    .fi-resource-edit-record-page .fi-input::placeholder,
    .fi-resource-create-record-page textarea::placeholder,
    .fi-resource-edit-record-page textarea::placeholder {
        color: rgba(203, 213, 225, 0.48) !important;
    }

    .fi-resource-create-record-page .fi-fo-select .fi-select-input,
    .fi-resource-edit-record-page .fi-fo-select .fi-select-input {
        appearance: none !important;
        background-color: var(--neo-dark-elevated) !important;
        background-image:
            linear-gradient(45deg, transparent 50%, rgba(226, 232, 240, 0.82) 50%),
            linear-gradient(135deg, rgba(226, 232, 240, 0.82) 50%, transparent 50%) !important;
        background-position:
            calc(100% - 18px) 17px,
            calc(100% - 12px) 17px !important;
        background-repeat: no-repeat !important;
        background-size: 6px 6px, 6px 6px !important;
        padding-right: 42px !important;
    }

    .fi-resource-create-record-page .fi-fo-file-upload .filepond--root,
    .fi-resource-edit-record-page .fi-fo-file-upload .filepond--root {
        margin-bottom: 0 !important;
        font-family: 'Inter', sans-serif;
    }

    .fi-resource-create-record-page .fi-fo-file-upload .filepond--panel-root,
    .fi-resource-edit-record-page .fi-fo-file-upload .filepond--panel-root {
        border: 2px solid var(--neo-ink) !important;
        border-radius: 2px !important;
        background: var(--neo-dark-elevated) !important;
        box-shadow: 4px 4px 0 var(--neo-ink) !important;
    }

    .fi-resource-create-record-page .fi-fo-file-upload .filepond--drop-label,
    .fi-resource-edit-record-page .fi-fo-file-upload .filepond--drop-label {
        min-height: 112px !important;
        color: var(--neo-dark-text) !important;
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 15px !important;
        font-weight: 900 !important;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .fi-resource-create-record-page .fi-fo-file-upload .filepond--label-action,
    .fi-resource-edit-record-page .fi-fo-file-upload .filepond--label-action {
        color: var(--neo-blue) !important;
        text-decoration: none !important;
    }

    .fi-resource-create-record-page .fi-fo-file-upload .filepond--item-panel,
    .fi-resource-edit-record-page .fi-fo-file-upload .filepond--item-panel {
        border-radius: 2px !important;
        background: var(--neo-blue) !important;
    }

    .fi-resource-create-record-page .fi-placeholder,
    .fi-resource-edit-record-page .fi-placeholder {
        color: var(--neo-dark-text) !important;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 700;
    }

    .fi-resource-create-record-page .bsa-current-media-link,
    .fi-resource-edit-record-page .bsa-current-media-link {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        border: 2px solid var(--neo-ink);
        border-radius: 2px;
        background: var(--neo-yellow);
        box-shadow: 4px 4px 0 var(--neo-ink);
        color: var(--neo-ink) !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.12em;
        padding: 8px 12px;
        text-decoration: none;
        text-transform: uppercase;
    }

    .fi-resource-create-record-page .fi-input-wrp:focus-within,
    .fi-resource-edit-record-page .fi-input-wrp:focus-within,
    .fi-resource-create-record-page .fi-fo-rich-editor:focus-within,
    .fi-resource-edit-record-page .fi-fo-rich-editor:focus-within,
    .fi-resource-create-record-page .fi-fo-file-upload:focus-within,
    .fi-resource-edit-record-page .fi-fo-file-upload:focus-within {
        outline: 2px solid var(--neo-blue);
        outline-offset: 2px;
    }

    .fi-resource-create-record-page .fi-checkbox-input,
    .fi-resource-edit-record-page .fi-checkbox-input,
    .fi-resource-create-record-page .fi-toggle input,
    .fi-resource-edit-record-page .fi-toggle input {
        border: 2px solid var(--neo-ink) !important;
        border-radius: 2px !important;
        box-shadow: 3px 3px 0 var(--neo-ink) !important;
    }

    .fi-resource-list-records-page .fi-btn,
    .fi-resource-create-record-page .fi-btn,
    .fi-resource-edit-record-page .fi-btn,
    .fi-resource-list-records-page .fi-icon-btn,
    .fi-resource-create-record-page .fi-icon-btn,
    .fi-resource-edit-record-page .fi-icon-btn {
        border: 2px solid var(--neo-ink) !important;
        border-radius: 2px !important;
        background: var(--neo-blue) !important;
        box-shadow: 4px 4px 0 var(--neo-ink) !important;
        color: #fff !important;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px !important;
        font-weight: 900 !important;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .fi-resource-list-records-page .fi-btn:hover,
    .fi-resource-create-record-page .fi-btn:hover,
    .fi-resource-edit-record-page .fi-btn:hover,
    .fi-resource-list-records-page .fi-icon-btn:hover,
    .fi-resource-create-record-page .fi-icon-btn:hover,
    .fi-resource-edit-record-page .fi-icon-btn:hover {
        background: var(--neo-yellow) !important;
        color: var(--neo-ink) !important;
        transform: translate(1px, 1px);
        box-shadow: 3px 3px 0 var(--neo-ink) !important;
    }

    .fi-resource-list-records-page .fi-btn.fi-color-danger,
    .fi-resource-create-record-page .fi-btn.fi-color-danger,
    .fi-resource-edit-record-page .fi-btn.fi-color-danger,
    .fi-resource-list-records-page .fi-icon-btn.fi-color-danger,
    .fi-resource-create-record-page .fi-icon-btn.fi-color-danger,
    .fi-resource-edit-record-page .fi-icon-btn.fi-color-danger {
        background: var(--neo-rose) !important;
    }

    .fi-resource-list-records-page .fi-dropdown-panel,
    .fi-resource-create-record-page .fi-dropdown-panel,
    .fi-resource-edit-record-page .fi-dropdown-panel,
    .fi-resource-list-records-page .fi-modal-window,
    .fi-resource-create-record-page .fi-modal-window,
    .fi-resource-edit-record-page .fi-modal-window {
        border: var(--neo-border) !important;
        border-radius: 2px !important;
        background: var(--neo-dark-surface) !important;
        box-shadow: 10px 10px 0 var(--neo-purple) !important;
        color: var(--neo-dark-text) !important;
    }

    @media (max-width: 1180px) {
        .bsa-stats-grid,
        .bsa-dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .bsa-traffic-panel {
            grid-column: 1 / -1;
        }

        .bsa-events-toolbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .bsa-events-field-search {
            grid-column: 1 / -1;
        }

        .bsa-events-clear {
            width: max-content;
        }
    }

    @media (max-width: 760px) {
        .fi-main {
            padding-inline: 16px;
        }

        .fi-simple-main-ctn {
            padding: 28px 20px;
        }

        .bsa-auth-shape-grid-host {
            opacity: 0.82;
        }

        .fi-simple-main {
            width: calc(100vw - 48px) !important;
            max-width: 456px !important;
            padding: 42px 24px 30px !important;
            box-shadow: 8px 8px 0 #02030a !important;
        }

        .fi-simple-header .fi-logo {
            margin-bottom: 10px !important;
        }

        .fi-simple-header img.fi-logo {
            width: 54px !important;
            height: auto !important;
            max-height: none !important;
        }

        .fi-simple-header-heading {
            font-size: 21px !important;
        }

        .fi-simple-header-subheading {
            max-width: 100%;
            margin-top: 6px !important;
            font-size: 13px !important;
        }

        .fi-simple-layout .fi-form {
            gap: 16px !important;
        }

        .fi-simple-layout .fi-input-wrp,
        .fi-simple-layout .fi-btn {
            box-shadow: 4px 4px 0 #02030a !important;
        }

        .bsa-dashboard {
            gap: 16px;
        }

        .bsa-hero {
            min-height: 146px;
            padding: 22px;
        }

        .bsa-hero,
        .bsa-traffic-body {
            grid-template-columns: 1fr;
        }

        .bsa-hero {
            display: grid;
            gap: 18px;
        }

        .bsa-hero-date {
            text-align: left;
        }

        .bsa-stats-grid,
        .bsa-dashboard-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .bsa-stat-card {
            min-height: 134px;
        }

        .bsa-panel {
            padding: 20px;
        }

        .bsa-panel-head {
            align-items: center;
        }

        .bsa-donut {
            width: min(100%, 230px);
        }

        .bsa-source-row,
        .bsa-country-row {
            min-height: 64px;
        }

        .bsa-source-row {
            grid-template-columns: 42px 1fr auto;
            gap: 12px;
        }

        .bsa-source-icon {
            width: 36px;
            height: 36px;
            box-shadow: 3px 3px 0 var(--neo-ink);
        }

        .bsa-source-icon svg {
            width: 19px;
            height: 19px;
        }

        .bsa-source-icon--compact {
            width: 28px;
            height: 28px;
        }

        .bsa-source-icon--compact svg {
            width: 15px;
            height: 15px;
        }

        .bsa-events {
            gap: 16px;
        }

        .bsa-events-hero {
            min-height: 156px;
            grid-template-columns: 1fr;
            padding: 22px;
        }

        .bsa-events-hero::before {
            right: 26px;
            top: 18px;
            width: 54px;
            height: 28px;
        }

        .bsa-events-hero::after {
            right: 24px;
            bottom: 16px;
            width: 58px;
            height: 22px;
        }

        .bsa-events-total {
            width: max-content;
            min-width: 120px;
            text-align: left;
        }

        .bsa-events-card-head {
            padding: 20px;
            align-items: flex-start;
            flex-direction: column;
        }

        .bsa-events-card h2 {
            font-size: 26px;
        }

        .bsa-events-add {
            width: 100%;
        }

        .bsa-events-toolbar {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .bsa-events-clear {
            width: 100%;
        }

        .bsa-events-table {
            min-width: 860px;
        }

        .bsa-events-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .bsa-events-modal-backdrop {
            align-items: start;
            padding: 16px;
        }

        .bsa-events-modal,
        .bsa-events-delete-modal {
            max-height: calc(100vh - 32px);
            box-shadow: 8px 8px 0 var(--neo-purple);
        }

        .bsa-events-modal-head,
        .bsa-events-form,
        .bsa-events-delete-modal {
            padding: 18px;
        }

        .bsa-events-form-grid {
            grid-template-columns: 1fr;
        }

        .bsa-events-section-add,
        .bsa-events-section-block-head {
            grid-template-columns: 1fr;
        }

        .bsa-events-section-add .bsa-events-modal-secondary,
        .bsa-events-section-block-head .bsa-events-modal-close {
            width: 100%;
        }

        .bsa-events-upload {
            grid-template-columns: 1fr;
        }

        .bsa-events-modal-footer {
            align-items: stretch;
            flex-direction: column-reverse;
        }

        .bsa-events-modal-secondary,
        .bsa-events-modal-primary,
        .bsa-events-modal-danger {
            width: 100%;
        }
    }
</style>
