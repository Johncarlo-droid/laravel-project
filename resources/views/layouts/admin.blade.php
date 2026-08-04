<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function(){
            try{
                var saved = localStorage.getItem('nu-theme');
                if(saved === 'dark'){ document.documentElement.setAttribute('data-theme','dark'); }
            }catch(e){}
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NU Clark Asset Management' }}</title>
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
        html[data-theme="dark"]{
            --canvas:#0B0E1A; --surface:#131729; --surface-2:#181D34;
            --ink-900:#F2F3FA; --ink-700:#C7CCE2; --ink-500:#9AA1C2; --ink-400:#7981A6;
            --line:#272C4A; --line-2:#20243F;
            --success-bg:#0F2A20; --success-ink:#3FDC93; --success-line:#1C4A34;
            --danger-bg:#331620; --danger-ink:#FF7A88; --danger-line:#552631;
            --warning-bg:#332608; --warning-ink:#F0C876; --warning-line:#54401A;
            --info-bg:#0E1E3B; --info-ink:#7FB1FF; --info-line:#1E3B6B;
            --shadow-sm:0 1px 2px rgba(0,0,0,.3), 0 1px 1px rgba(0,0,0,.24);
            --shadow-md:0 8px 24px -8px rgba(0,0,0,.5), 0 2px 8px -2px rgba(0,0,0,.3);
            --shadow-lg:0 24px 48px -16px rgba(0,0,0,.6), 0 4px 16px -4px rgba(0,0,0,.35);
        }
        *{box-sizing:border-box}
        html{scrollbar-width:thin;scrollbar-color:var(--line) transparent}
        *::-webkit-scrollbar{width:9px;height:9px}
        *::-webkit-scrollbar-track{background:transparent}
        *::-webkit-scrollbar-thumb{background:var(--line);border-radius:999px;border:2px solid transparent;background-clip:padding-box}
        *::-webkit-scrollbar-thumb:hover{background:var(--ink-400);background-clip:padding-box}
        *::-webkit-scrollbar-button{display:none;width:0;height:0}
        *::-webkit-scrollbar-corner{background:transparent}
        body{margin:0;background:var(--canvas);color:var(--ink-900);font-family:Inter,Segoe UI,Arial,sans-serif;-webkit-font-smoothing:antialiased;font-feature-settings:"tnum" 1,"cv05" 1;transition:background .2s ease,color .2s ease}
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
        .small-btn{padding:7px 13px;border-radius:9px;font-size:11.5px;font-weight:700;line-height:1;overflow:hidden}
        /* Hard cap on icon glyph size — clips any icon that tries to render
           larger than its button (font-loading races, browser extensions,
           zoom/translate tools can otherwise blow an icon up to huge size). */
        .btn-primaryx,.btn-approve,.btn-reject,.btn-soft,.small-btn,.notif-link,.theme-toggle,.mobile-menu,.top-icon{line-height:1}
        .btn-primaryx i,.btn-approve i,.btn-reject i,.btn-soft i,.small-btn i,.notif-link i,.theme-toggle i,.mobile-menu i,
        i[class^="bi-"],i[class*=" bi-"]{font-size:1em!important;line-height:1!important;display:inline-block;vertical-align:-.125em;max-width:1.4em;max-height:1.4em;overflow:hidden}

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

        /* ---------- Theme toggle ---------- */
        .theme-toggle{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:11px;border:1px solid var(--line);background:var(--surface);color:var(--ink-700);cursor:pointer;transition:background .15s ease,color .15s ease,transform .15s ease}
        .theme-toggle:hover{background:var(--canvas);transform:translateY(-1px)}
        .theme-toggle i{font-size:16px;transition:transform .3s ease}
        html[data-theme="dark"] .theme-toggle i.bi-moon-stars-fill{display:none}
        html:not([data-theme="dark"]) .theme-toggle i.bi-sun-fill{display:none}

        /* ---------- Key/value info table ---------- */
        .kv-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;border:1px solid var(--line);border-radius:var(--r-md);overflow:hidden;margin-bottom:18px;box-shadow:var(--shadow-sm)}
        .kv-table tr:not(:first-child) th,.kv-table tr:not(:first-child) td{border-top:1px solid var(--line-2)}
        .kv-table tr:nth-child(even){background:var(--surface-2)}
        .kv-table th{width:190px;text-align:left;padding:11px 16px;background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;font-weight:600;font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;vertical-align:top;white-space:nowrap}
        .kv-table td{padding:11px 16px;color:var(--ink-700);line-height:1.5}
        .kv-table td.kv-wide{white-space:normal}
        .kv-table tr{animation:fadeInUp .35s ease both}
        .kv-table tr:nth-child(1){animation-delay:.02s}.kv-table tr:nth-child(2){animation-delay:.05s}.kv-table tr:nth-child(3){animation-delay:.08s}
        .kv-table tr:nth-child(4){animation-delay:.11s}.kv-table tr:nth-child(5){animation-delay:.14s}.kv-table tr:nth-child(6){animation-delay:.17s}
        .kv-table tr:nth-child(7){animation-delay:.2s}.kv-table tr:nth-child(8){animation-delay:.23s}.kv-table tr:nth-child(9){animation-delay:.26s}

        /* ---------- Approval timeline ---------- */
        .approval-timeline{list-style:none;margin:0 0 6px;padding:0;position:relative}
        .approval-timeline li{position:relative;padding:2px 0 22px 38px;animation:fadeInUp .4s ease both}
        .approval-timeline li:nth-child(1){animation-delay:.03s}.approval-timeline li:nth-child(2){animation-delay:.09s}.approval-timeline li:nth-child(3){animation-delay:.15s}
        .approval-timeline li:nth-child(4){animation-delay:.21s}.approval-timeline li:nth-child(5){animation-delay:.27s}.approval-timeline li:nth-child(6){animation-delay:.33s}
        .approval-timeline li:last-child{padding-bottom:0}
        .approval-timeline li::before{content:'';position:absolute;left:11px;top:26px;bottom:-2px;width:2px;background:var(--line)}
        .approval-timeline li:last-child::before{display:none}
        .approval-timeline .step-dot{position:absolute;left:0;top:0;width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:12px;color:#fff;background:var(--ink-400);box-shadow:0 0 0 4px var(--surface)}
        .approval-timeline li.signed .step-dot{background:linear-gradient(155deg,#2BC876,var(--success-ink))}
        .approval-timeline li.pending .step-dot{background:linear-gradient(155deg,var(--gold-400),var(--gold-600));animation:pulseDot 2s ease-in-out infinite}
        .approval-timeline .step-row{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap}
        .approval-timeline .step-role{font-weight:700;font-size:12px;color:var(--ink-900)}
        .approval-timeline .step-name{font-size:11.5px;color:var(--ink-500);margin-top:1px}
        .approval-timeline .step-meta{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;letter-spacing:.01em}
        .approval-timeline .step-meta.signed{background:var(--success-bg);color:var(--success-ink);border:1px solid var(--success-line)}
        .approval-timeline .step-meta.pending{background:var(--warning-bg);color:var(--warning-ink);border:1px solid var(--warning-line)}
        @keyframes pulseDot{0%,100%{box-shadow:0 0 0 4px var(--surface),0 0 0 0 rgba(227,176,78,0)}50%{box-shadow:0 0 0 4px var(--surface),0 0 0 6px rgba(227,176,78,.28)}}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

        /* ---------- Callout note ---------- */
        .note-callout{display:flex;gap:10px;align-items:flex-start;background:var(--info-bg);border:1px solid var(--info-line);color:var(--info-ink);border-radius:var(--r-md);padding:11px 14px;font-size:11.5px;line-height:1.5;margin-top:4px}
        .note-callout i{font-size:15px;margin-top:1px}

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
    @stack('styles')
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
            @php
                $navUser = auth()->user();
                $navAll = $navUser->isSuperAdmin();
                $navAssetAdmin = $navUser->isAssetManagementAdmin();
                $navRequestor = $navUser->isRequestor();
                $navFmo = $navUser->isFmo();
                $navHousekeeping = $navUser->isHousekeeping();
                $navDean = $navUser->isDeanApprover();
                $navExecutive = $navUser->isExecutiveApprover();
                $navProposalSigner = $navUser->isAdviserApprover() || $navUser->isSdaoApprover() || $navUser->isAcademicDirectorApprover() || $navDean || $navExecutive;
            @endphp
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            @endif
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type', 'CAPEX') === 'CAPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'CAPEX']) }}"><i class="bi bi-pc-display"></i><span>Capex</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navRequestor)
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type') === 'OPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'OPEX']) }}"><i class="bi bi-layers"></i><span>Opex</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navRequestor || $navDean || $navExecutive)
            <a class="nav-linkx {{ request()->routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}"><i class="bi bi-file-earmark-text"></i><span>Requisitions</span></a>
            @endif
            @if($navAll || $navFmo)
            <a class="nav-linkx {{ request()->routeIs('facilities.*') ? 'active' : '' }}" href="{{ route('facilities.index') }}"><i class="bi bi-calendar-event"></i><span>Facilities</span></a>
            @endif
            @if($navAll || $navRequestor || $navFmo || $navProposalSigner)
            <a class="nav-linkx {{ request()->routeIs('activity-proposals.*') ? 'active' : '' }}" href="{{ route('activity-proposals.index') }}"><i class="bi bi-file-earmark-check"></i><span>Activity Proposals</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navHousekeeping)
            <a class="nav-linkx {{ request()->routeIs('asset-scans.*') ? 'active' : '' }}" href="{{ route('asset-scans.index') }}"><i class="bi bi-qr-code-scan"></i><span>Scans</span></a>
            @endif
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('issuances.*') ? 'active' : '' }}" href="{{ route('issuances.index') }}"><i class="bi bi-arrow-repeat"></i><span>Issuance & Returns</span></a>
            <a class="nav-linkx {{ request()->routeIs('forecast.*') ? 'active' : '' }}" href="{{ route('forecast.index') }}"><i class="bi bi-graph-up-arrow"></i><span>Forecast</span></a>
            <a class="nav-linkx {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-truck"></i><span>Suppliers</span></a>
            <a class="nav-linkx {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
            <a class="nav-linkx {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people"></i><span>Users</span></a>
            @endif
            @if($navAll)
            <a class="nav-linkx {{ request()->routeIs('reference-data.*') ? 'active' : '' }}" href="{{ route('reference-data.index') }}"><i class="bi bi-sliders"></i><span>Reference Data</span></a>
            @endif
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
            @endif
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu" type="button" onclick="document.body.classList.toggle('sidebar-open')"><i class="bi bi-list"></i></button>
                <div>
                    <p class="page-title">{{ $title ?? 'Dashboard' }}</p>
                    <div class="page-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }} · Assets Office</div>
                </div>
            </div>
            <div class="top-actions">
                <button type="button" class="theme-toggle" onclick="__toggleTheme()" title="Toggle dark mode">
                    <i class="bi bi-moon-stars-fill"></i>
                    <i class="bi bi-sun-fill"></i>
                </button>
                <a href="{{ route('notifications.index') }}" class="notif-link" title="Notifications"><i class="bi bi-bell top-icon"></i>@if(auth()->user()->unreadNotifications->count())<span class="notif-badge">{{ auth()->user()->unreadNotifications->count() }}</span>@endif</a>
                <div class="user-chip">
                    @php($__name = auth()->user()->name ?? 'Admin')
                    @php($__initials = collect(explode(' ', trim($__name)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode(''))
                    <div class="avatar">{{ strtoupper($__initials) ?: 'A' }}</div>
                    <div class="user-meta">
                        <div class="user-name">{{ $__name }}</div>
                        <div class="user-role">{{ ucwords(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function __toggleTheme(){
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        if(isDark){
            document.documentElement.removeAttribute('data-theme');
            try{ localStorage.setItem('nu-theme','light'); }catch(e){}
        } else {
            document.documentElement.setAttribute('data-theme','dark');
            try{ localStorage.setItem('nu-theme','dark'); }catch(e){}
        }
    }
</script>
@stack('scripts')
</body>
</html>
