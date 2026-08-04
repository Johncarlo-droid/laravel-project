<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'NU Clark Asset Management'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Lexend:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950:#080D22; --navy-900:#0C1330; --navy-800:#141B42; --navy-700:#1D2657;
            --gold-600:#C9932E; --gold-500:#E3B04E; --gold-400:#F0C876; --gold-050:#FBF3E1;
            --canvas:#F5F6FA; --surface:#FFFFFF; --surface-2:#FAFBFD;
            --ink-900:#12162B; --ink-700:#333A52; --ink-500:#666E88; --ink-400:#8991A8;
            --line:#E7E9F2; --line-2:#EFF1F7;
            --success-bg:#E6F6EE; --success-ink:#0F7A4E; --success-line:#BEE7D2;
            --danger-bg:#FCEBEC; --danger-ink:#C42A3B; --danger-line:#F5C6CB;
            --warning-bg:#FCF3E1; --warning-ink:#9C6B0B; --warning-line:#F2DBA8;
            --info-bg:#E9F1FE; --info-ink:#1E56B0; --info-line:#C6D9F7;
            --r-sm:10px; --r-md:14px; --r-lg:20px;
            --shadow-sm:0 1px 2px rgba(18,22,43,.04), 0 1px 1px rgba(18,22,43,.03);
            --shadow-md:0 8px 24px -8px rgba(18,22,43,.14), 0 2px 8px -2px rgba(18,22,43,.06);
            --shadow-lg:0 24px 48px -16px rgba(12,19,48,.28), 0 4px 16px -4px rgba(12,19,48,.10);
            --font-display:'Lexend',Inter,Segoe UI,Arial,sans-serif;
        }
        *{box-sizing:border-box}
        body{margin:0;background:var(--canvas);color:var(--ink-900);font-family:Inter,Segoe UI,Arial,sans-serif;-webkit-font-smoothing:antialiased;font-feature-settings:"tnum" 1,"cv05" 1}
        h1,h2,h3,.module-title,.page-title{font-family:var(--font-display)}
        .app-shell{display:flex;min-height:100vh}

        /* ---------- Sidebar ---------- */
        .sidebar{width:116px;background:linear-gradient(165deg,var(--navy-800) 0%,var(--navy-900) 55%,var(--navy-950) 100%);color:#fff;position:sticky;top:0;height:100vh;border-right:1px solid rgba(255,255,255,.05);z-index:20;display:flex;flex-direction:column;overflow:hidden;box-shadow:var(--shadow-lg)}
        .nav-list{padding:10px 10px 16px;display:grid;gap:4px;overflow-y:auto;flex:1;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.2) transparent}
        .nav-list::-webkit-scrollbar{width:5px}
        .nav-list::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:999px}
        .brand-wrap{padding:22px 14px 18px;position:relative}
        .brand-wrap::after{content:'';position:absolute;left:16px;right:16px;bottom:0;height:1px;background:linear-gradient(90deg,transparent,rgba(227,176,78,.35),transparent)}
        .brand-box{display:flex;align-items:center;gap:10px}
        .brand-mark{width:36px;height:36px;border-radius:11px;background:linear-gradient(150deg,var(--gold-400),var(--gold-600));color:var(--navy-950);display:grid;place-items:center;font-size:16px;font-weight:800;box-shadow:0 6px 16px rgba(227,176,78,.35),inset 0 1px 0 rgba(255,255,255,.4);font-family:var(--font-display)}
        .brand-title{font-weight:700;font-size:12.5px;line-height:1.25;font-family:var(--font-display);letter-spacing:.01em}
        .brand-sub{font-size:9.5px;color:#9FACD6;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-top:1px}
        .nav-linkx{position:relative;display:flex;align-items:center;gap:11px;padding:10px 12px;margin:0 2px;border-radius:11px;text-decoration:none;color:#AEB8D6;font-size:11.5px;font-weight:500;transition:background .15s ease,color .15s ease;letter-spacing:.01em}
        .nav-linkx i{font-size:15px;min-width:16px;color:#8592BC;transition:color .15s ease}
        .nav-linkx:hover{background:rgba(255,255,255,.055);color:#fff}
        .nav-linkx:hover i{color:var(--gold-400)}
        .nav-linkx.active{background:rgba(227,176,78,.12);color:#fff;font-weight:700}
        .nav-linkx.active i{color:var(--gold-500)}
        .nav-linkx.active::before{content:'';position:absolute;left:-2px;top:20%;bottom:20%;width:3px;border-radius:0 4px 4px 0;background:linear-gradient(180deg,var(--gold-400),var(--gold-600))}

        /* ---------- Topbar ---------- */
        .main{flex:1;min-width:0}
        .topbar{height:72px;background:rgba(255,255,255,.85);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:10;backdrop-filter:blur(10px) saturate(1.4)}
        .page-title{font-size:15px;font-weight:700;margin:0;color:var(--ink-900);letter-spacing:-.01em}
        .page-subtitle{font-size:11.5px;color:var(--ink-500);margin-top:1px}
        .top-actions{display:flex;align-items:center;gap:18px}
        .top-icon{font-size:20px;color:var(--ink-700)}
        .notif-link{position:relative;color:var(--ink-700);text-decoration:none;display:inline-flex;align-items:center;width:38px;height:38px;justify-content:center;border-radius:11px;transition:background .15s ease}
        .notif-link:hover{background:var(--canvas)}
        .notif-badge{position:absolute;top:4px;right:5px;background:var(--danger-ink);color:#fff;border-radius:999px;min-width:16px;height:16px;padding:0 4px;display:grid;place-items:center;font-size:9px;font-weight:700;border:2px solid #fff}
        .user-chip{display:flex;align-items:center;gap:10px;padding-left:14px;border-left:1px solid var(--line)}
        .avatar{width:36px;height:36px;border-radius:11px;background:linear-gradient(150deg,var(--navy-700),var(--navy-900));color:var(--gold-400);display:grid;place-items:center;font-size:14px;font-weight:700;font-family:var(--font-display);box-shadow:var(--shadow-sm)}
        .user-meta{line-height:1.2}.user-name{font-weight:700;font-size:12px;color:var(--ink-900)}.user-role{font-size:10.5px;color:var(--ink-500)}
        .logout-btn{color:var(--danger-ink);text-decoration:none;font-size:11.5px;font-weight:600;background:none;border:none;padding:0}

        /* ---------- Surfaces & layout ---------- */
        .content{padding:28px}
        .surface{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);box-shadow:var(--shadow-sm)}
        .module-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:18px;flex-wrap:wrap}
        .module-title{font-size:19px;font-weight:700;margin:0;color:var(--ink-900);letter-spacing:-.01em}
        .module-note{font-size:12.5px;color:var(--ink-500);margin-top:3px;max-width:640px;line-height:1.5}

        /* ---------- Buttons ---------- */
        .btn-primaryx{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;border:none;border-radius:var(--r-sm);padding:10px 18px;font-weight:600;font-size:12.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px -2px rgba(12,19,48,.35);transition:transform .12s ease,box-shadow .12s ease}
        .btn-primaryx:hover{color:#fff;transform:translateY(-1px);box-shadow:0 8px 20px -4px rgba(12,19,48,.4)}
        .btn-approve,.btn-reject,.btn-soft{border:none;border-radius:var(--r-sm);padding:9px 18px;font-size:11.5px;font-weight:700;color:#fff;text-decoration:none;display:inline-flex;gap:7px;align-items:center;transition:transform .12s ease,box-shadow .12s ease;letter-spacing:.01em}
        .btn-approve{background:linear-gradient(155deg,#1DAA65,var(--success-ink));box-shadow:0 4px 12px -3px rgba(15,122,78,.4)}
        .btn-reject{background:linear-gradient(155deg,#DE4A57,var(--danger-ink));box-shadow:0 4px 12px -3px rgba(196,42,59,.4)}
        .btn-soft{background:var(--navy-800);box-shadow:var(--shadow-sm)}
        .btn-approve:hover,.btn-reject:hover,.btn-soft:hover{transform:translateY(-1px)}
        .small-btn{padding:7px 13px;border-radius:9px;font-size:11.5px;font-weight:700}

        /* ---------- Search / filter ---------- */
        .search-strip{display:flex;align-items:center;gap:10px;padding:11px 14px;border:1px solid var(--line);background:var(--surface);border-radius:var(--r-sm);box-shadow:var(--shadow-sm);margin-bottom:16px}
        .search-input{border:none;background:transparent;outline:none;width:100%;font-size:12.5px;color:var(--ink-700)}
        .filter-box{min-width:110px;border-left:1px solid var(--line);padding-left:12px;display:flex;align-items:center;gap:8px}.filter-box select{border:none;background:transparent;width:100%;font-size:12.5px;outline:none;color:var(--ink-700)}

        /* ---------- Stat cards ---------- */
        .stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:16px}
        .stat-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:18px 18px 14px;position:relative;min-height:118px;box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow .15s ease,transform .15s ease}
        .stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
        .stat-card::before{content:'';position:absolute;left:0;top:0;right:0;height:3px;background:var(--stat-accent,var(--gold-500))}
        .stat-icon{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;color:#fff;font-size:18px;margin-bottom:16px;box-shadow:var(--shadow-sm)}
        .stat-mini{position:absolute;top:18px;right:20px;font-size:11px;font-weight:700}
        .stat-label{font-size:12.5px;color:var(--ink-500);margin-bottom:3px;font-weight:500}
        .stat-value{font-size:32px;font-weight:800;line-height:1;font-family:var(--font-display);color:var(--ink-900);letter-spacing:-.02em}
        .icon-cyan{background:linear-gradient(155deg,#3BC3E0,#1D8FAE);--stat-accent:#3BC3E0}
        .icon-green{background:linear-gradient(155deg,#2BC876,var(--success-ink));--stat-accent:#2BC876}
        .icon-amber{background:linear-gradient(155deg,var(--gold-400),var(--gold-600));--stat-accent:var(--gold-500)}
        .icon-red{background:linear-gradient(155deg,#E85D6A,var(--danger-ink));--stat-accent:#E85D6A}
        .mini-green{color:var(--success-ink)}.mini-red{color:var(--danger-ink)}

        /* ---------- Panels / charts / tables ---------- */
        .panel-grid-2,.report-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
        .chart-card,.report-box{padding:0;border-radius:var(--r-lg);overflow:hidden;background:var(--surface);border:1px solid var(--line);box-shadow:var(--shadow-sm)}
        .chart-head{height:42px;display:flex;align-items:center;gap:8px;padding:0 16px;border-bottom:1px solid var(--line);font-weight:700;font-size:13px;background:var(--surface-2);color:var(--ink-900)}
        .chart-body{padding:18px;background:var(--surface)}
        .chart-wrap{height:280px;position:relative}
        .data-panel{padding:16px;background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);box-shadow:var(--shadow-sm)}
        .data-table{width:100%;border-collapse:collapse;font-size:12.5px}
        .data-table thead th{padding:11px 14px;background:var(--surface-2);border-bottom:1px solid var(--line);color:var(--ink-500);text-transform:uppercase;font-size:10.5px;letter-spacing:.05em;font-weight:700;text-align:left}
        .data-table tbody td{padding:12px 14px;border-top:1px solid var(--line-2);vertical-align:middle;color:var(--ink-700)}
        .data-table tbody tr{transition:background .12s ease}
        .data-table tbody tr:hover{background:var(--surface-2)}
        .asset-card,.request-card,.issue-card,.supplier-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:14px 16px;margin-bottom:12px;box-shadow:var(--shadow-sm);transition:box-shadow .15s ease}
        .asset-card:hover,.request-card:hover,.issue-card:hover,.supplier-card:hover{box-shadow:var(--shadow-md)}
        .grid-cards{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .supplier-card{padding:16px 20px;min-height:230px}
        .supplier-meta{color:var(--ink-500);font-size:11.5px;margin-bottom:18px}
        .supplier-avatar{width:26px;height:26px;border-radius:50%;display:grid;place-items:center;background:var(--info-bg);color:var(--info-ink);font-size:12px;margin-bottom:10px;font-weight:700}

        /* ---------- Text helpers ---------- */
        .muted-line{color:var(--ink-500);font-size:12.5px;margin:8px 0;display:flex;gap:10px;align-items:flex-start}
        .tag{display:inline-block;padding:2px 9px;font-size:10.5px;border-radius:7px;background:var(--surface-2);border:1px solid var(--line);color:var(--ink-500);margin-right:6px;font-weight:600}
        .tiny{font-size:11.5px;color:var(--ink-500)}.tiny-2{font-size:10.5px;color:var(--ink-400)}
        .code-badge{display:inline-block;padding:2px 7px;border-radius:7px;font-size:9.5px;background:var(--info-bg);color:var(--info-ink);font-weight:700;letter-spacing:.02em}
        .pill-opex{background:var(--warning-bg);color:var(--warning-ink);border-radius:12px;padding:2px 8px;font-size:10.5px;font-weight:700}

        /* ---------- Status pills ---------- */
        .status{display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:3px 10px;font-size:10.5px;font-weight:700;border:1px solid transparent;letter-spacing:.01em}
        .status.available{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
        .status.in-use{background:var(--info-bg);color:var(--info-ink);border-color:var(--info-line)}
        .status.maintenance{background:var(--warning-bg);color:var(--warning-ink);border-color:var(--warning-line)}
        .status.pending{background:var(--warning-bg);color:var(--warning-ink);border-color:var(--warning-line)}
        .status.approved{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
        .status.low{background:var(--danger-bg);color:var(--danger-ink);border-color:var(--danger-line)}

        /* ---------- Stock bar ---------- */
        .stock-bar{height:5px;border-radius:999px;background:var(--line);overflow:hidden;width:90px;margin-top:4px}
        .stock-fill{height:100%;background:linear-gradient(90deg,#2BC876,var(--success-ink))}
        .stock-fill.low{background:linear-gradient(90deg,#E85D6A,var(--danger-ink))}

        .request-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap}
        .empty-state{padding:36px 20px;text-align:center;color:var(--ink-500);font-size:13px}

        /* ---------- Forms ---------- */
        .form-shell{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:24px;box-shadow:var(--shadow-sm)}
        .form-control,.form-select,.form-check-input{border-color:var(--line);border-radius:var(--r-sm);font-size:13px;padding:9px 12px}
        .form-control:focus,.form-select:focus{box-shadow:0 0 0 3px rgba(227,176,78,.18);border-color:var(--gold-500)}
        .form-label{font-size:12px;font-weight:700;color:var(--ink-700);margin-bottom:5px}

        /* ---------- Settings ---------- */
        .settings-list{display:grid;gap:14px;max-width:640px}
        .settings-item{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:18px 20px;box-shadow:var(--shadow-sm)}
        .settings-item h5{font-size:14px;font-weight:700;margin:0 0 6px;color:var(--ink-900)}

        /* ---------- Tabs ---------- */
        .page-tabs{display:flex;gap:22px;margin:12px 0 16px;font-size:12.5px;font-weight:600;flex-wrap:wrap;border-bottom:1px solid var(--line)}
        .page-tabs a,.page-tabs span{color:var(--ink-500);text-decoration:none;padding-bottom:10px;border-bottom:2px solid transparent;transition:color .15s ease,border-color .15s ease}
        .page-tabs .active,.page-tabs a.active{color:var(--navy-800);border-color:var(--gold-500);font-weight:700}
        .page-tabs a:hover{color:var(--navy-800)}

        /* ---------- QR / scanner ---------- */
        .qr-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:18px;box-shadow:var(--shadow-sm)}
        .qr-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
        .qr-tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .qr-tile{background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-md);padding:14px;text-align:center}
        .scanner-shell{background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-lg);padding:16px}
        .scanner-box{background:linear-gradient(155deg,var(--navy-800),var(--navy-950));border-radius:var(--r-lg);padding:14px;min-height:340px;color:#fff}
        .scanner-result{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:16px;box-shadow:var(--shadow-sm)}
        .report-stat{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:16px;box-shadow:var(--shadow-sm)}

        .mobile-menu{display:none}
        @media (max-width: 991px){
            .sidebar{position:fixed;left:-120px;transition:.25s ease;box-shadow:var(--shadow-lg)}
            body.sidebar-open .sidebar{left:0}
            .mobile-menu{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border:none;border-radius:12px;background:var(--canvas)}
            .topbar{padding:0 16px}
            .content{padding:18px}
            .stat-grid,.panel-grid-2,.grid-cards,.report-grid,.qr-grid,.qr-tiles{grid-template-columns:1fr}
            .user-meta{display:none}
            .request-actions{justify-content:flex-start}
        }
        @media (max-width: 576px){
            .data-table{font-size:11.5px}
            .data-table thead{display:none}
            .data-table tbody tr{display:block;padding:10px 0;border-top:1px solid var(--line)}
            .data-table tbody td{display:flex;justify-content:space-between;gap:16px;padding:8px 10px;border-top:none}
            .data-table tbody td::before{content:attr(data-label);font-weight:700;color:var(--ink-500);text-transform:uppercase;font-size:10px}
            .top-actions{gap:10px}
            .page-title{font-size:14px}
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-wrap">
            <div class="brand-box">
                <div class="brand-mark">NU</div>
                <div>
                    <div class="brand-title">NU Clark</div>
                    <div class="brand-sub">Asset Management</div>
                </div>
            </div>
        </div>
        <nav class="nav-list">
            <?php
                $navUser = auth()->user();
                $navAll = $navUser->isSuperAdmin();
                $navAssetAdmin = $navUser->isAssetManagementAdmin();
                $navRequestor = $navUser->isRequestor();
                $navFmo = $navUser->isFmo();
                $navHousekeeping = $navUser->isHousekeeping();
                $navDean = $navUser->isDeanApprover();
                $navExecutive = $navUser->isExecutiveApprover();
                $navProposalSigner = $navUser->isAdviserApprover() || $navUser->isSdaoApprover() || $navUser->isAcademicDirectorApprover() || $navDean || $navExecutive;
            ?>
            <?php if($navAll || $navAssetAdmin): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('items.*') && request('type', 'CAPEX') === 'CAPEX' ? 'active' : ''); ?>" href="<?php echo e(route('items.index', ['type' => 'CAPEX'])); ?>"><i class="bi bi-pc-display"></i><span>Capex</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin || $navRequestor): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('items.*') && request('type') === 'OPEX' ? 'active' : ''); ?>" href="<?php echo e(route('items.index', ['type' => 'OPEX'])); ?>"><i class="bi bi-layers"></i><span>Opex</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin || $navRequestor || $navDean || $navExecutive): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('requisitions.*') ? 'active' : ''); ?>" href="<?php echo e(route('requisitions.index')); ?>"><i class="bi bi-file-earmark-text"></i><span>Requisitions</span></a>
            <?php endif; ?>
            <?php if($navAll || $navFmo): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('facilities.*') ? 'active' : ''); ?>" href="<?php echo e(route('facilities.index')); ?>"><i class="bi bi-calendar-event"></i><span>Facilities</span></a>
            <?php endif; ?>
            <?php if($navAll || $navRequestor || $navFmo || $navProposalSigner): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('activity-proposals.*') ? 'active' : ''); ?>" href="<?php echo e(route('activity-proposals.index')); ?>"><i class="bi bi-file-earmark-check"></i><span>Activity Proposals</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin || $navHousekeeping): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('asset-scans.*') ? 'active' : ''); ?>" href="<?php echo e(route('asset-scans.index')); ?>"><i class="bi bi-qr-code-scan"></i><span>Scans</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('issuances.*') ? 'active' : ''); ?>" href="<?php echo e(route('issuances.index')); ?>"><i class="bi bi-arrow-repeat"></i><span>Issuance & Returns</span></a>
            <a class="nav-linkx <?php echo e(request()->routeIs('forecast.*') ? 'active' : ''); ?>" href="<?php echo e(route('forecast.index')); ?>"><i class="bi bi-graph-up-arrow"></i><span>Forecast</span></a>
            <a class="nav-linkx <?php echo e(request()->routeIs('suppliers.*') ? 'active' : ''); ?>" href="<?php echo e(route('suppliers.index')); ?>"><i class="bi bi-truck"></i><span>Suppliers</span></a>
            <a class="nav-linkx <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.index')); ?>"><i class="bi bi-graph-up"></i><span>Reports</span></a>
            <a class="nav-linkx <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people"></i><span>Users</span></a>
            <?php endif; ?>
            <?php if($navAll): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('reference-data.*') ? 'active' : ''); ?>" href="<?php echo e(route('reference-data.index')); ?>"><i class="bi bi-sliders"></i><span>Reference Data</span></a>
            <?php endif; ?>
            <?php if($navAll || $navAssetAdmin): ?>
            <a class="nav-linkx <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.index')); ?>"><i class="bi bi-gear"></i><span>Settings</span></a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu" type="button" onclick="document.body.classList.toggle('sidebar-open')"><i class="bi bi-list"></i></button>
                <div>
                    <p class="page-title"><?php echo e($title ?? 'Dashboard'); ?></p>
                    <div class="page-subtitle">Welcome back, <?php echo e(auth()->user()->name ?? 'Admin'); ?> · Assets Office</div>
                </div>
            </div>
            <div class="top-actions">
                <a href="<?php echo e(route('notifications.index')); ?>" class="notif-link" title="Notifications"><i class="bi bi-bell top-icon"></i><?php if(auth()->user()->unreadNotifications->count()): ?><span class="notif-badge"><?php echo e(auth()->user()->unreadNotifications->count()); ?></span><?php endif; ?></a>
                <div class="user-chip">
                    <?php ($__name = auth()->user()->name ?? 'Admin'); ?>
                    <?php ($__initials = collect(explode(' ', trim($__name)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('')); ?>
                    <div class="avatar"><?php echo e(strtoupper($__initials) ?: 'A'); ?></div>
                    <div class="user-meta">
                        <div class="user-name"><?php echo e($__name); ?></div>
                        <div class="user-role"><?php echo e(ucwords(str_replace('_', ' ', auth()->user()->role ?? 'admin'))); ?></div>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
            </div>
        </div>

        <div class="content">
            <?php if(session('success')): ?>
                <div class="alert alert-success rounded-4 border-0 shadow-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(isset($errors) && $errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Admin\Desktop\capex-opex-web-system\capex-opex\resources\views/layouts/admin.blade.php ENDPATH**/ ?>