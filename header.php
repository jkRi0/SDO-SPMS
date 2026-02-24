<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPMS - Supplier Transaction Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        :root {
            --primary-blue: #3b7ca8;
            --primary-dark: #2c5a7f;
            --primary-light: #4a8fc7;
            --bg-light: #f5f7fa;
            --bg-white: #ffffff;
            --text-dark: #1a1a1a;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --status-success: #10b981;
            --status-warning: #f59e0b;
            --status-danger: #ef4444;
            --status-info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            color: var(--text-dark);
        }

        .navbar {
            background: var(--bg-white);
            border-bottom: 4px solid var(--border-color);
            padding: 0.5rem 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark) !important;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .site-logo {
            height: 55px; 
            width: auto;
            border-radius: 50%;
            object-fit: contain;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-right: 0.5erem;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.08rem;
        }

        .navbar-brand .main-title {
            font-size: 1.05rem;
            color: var(--text-dark);
        }

        .navbar-brand small {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-left: auto;
        }

        .user-info {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .logout-btn {
            background: var(--primary-blue);
            color: white !important;
            border: none;
            font-weight: 500;
            padding: 0.5rem 0.6rem !important;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(58, 124, 168, 0.2);
        }

        .container-main {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .page-header {
            margin-bottom: 2.5rem;
        }

        .page-title {
            font-family: times-new-roman, serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.80rem;
            opacity: 0.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--bg-white);
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--primary-blue);
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-card-icon {
            width: 15px;
            height: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }   

        .stat-card.blue {
            border-top-color: #3b7ca8;
        }

        .stat-card.blue .stat-card-icon {
            background-color: rgba(59, 124, 168, 0.1);
            color: #3b7ca8;
        }

        .stat-card.orange {
            border-top-color: #f59e0b;
        }

        .stat-card.orange .stat-card-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .stat-card.green {
            border-top-color: #10b981;
        }

        .stat-card.green .stat-card-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .stat-card.red {
            border-top-color: #ef4444;
        }

        .stat-card.red .stat-card-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {    
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .page-subtitle {
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .btn-primary {
            background: var(--primary-blue);
            border: none;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 124, 168, 0.3);
            color: white !important;
        }

        .btn-outline-primary {
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            font-weight: 600;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white !important;
        }

        .table-wrapper {
            background: var(--bg-white);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            background: var(--bg-white);
            font-size: 0.85rem; /* slightly larger for readability */
        }

        .table thead {
            background-color: #f9fafb;
            border-bottom: 2px solid var(--border-color);
        }

        .table thead th {
            border: none;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 1.5rem;
            background: #f9fafb;
        }

        .table thead th:not(:last-child),
        .table tbody td:not(:last-child) {
            border-right: 3px solid rgba(0,0,0,0.08);
        }

        .table tbody tr:hover {
            background-color: #f3f4f6;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.01);
        }

        .table tbody td {
            padding: 1rem;
            border-color: var(--border-color);
            color: var(--text-dark);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        .table a.btn-outline-primary {
            transition: all 0.12s ease-in-out;
            background: transparent;
        }

        .table a.btn-outline-primary:hover {
            background: rgba(59,124,168,0.06);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(59,124,168,0.08);
        }

        .table a.btn-outline-primary i {
            transition: transform 0.12s ease, color 0.12s ease;
            color: var(--primary-blue);
        }

        .table a.btn-outline-primary:hover i {
            color: var(--primary-dark);
            transform: scale(1.08);
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-info {
            background-color: rgba(59, 124, 168, 0.1);
            color: #3b7ca8;
            border: 1px solid rgba(59, 124, 168, 0.3);
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 0.2rem;
            font-size: 0.9rem;
        }

        .form-control {
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: var(--bg-white);
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 124, 168, 0.1);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #c2255c;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #087e3a;
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #92400e;
        }

        @media (max-width: 768px) {
            .container-main {
                padding: 0 1rem;
                margin: 1.5rem auto;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                flex-direction: column;
                gap: 1rem;
            }

            .stat-card-icon {
                align-self: flex-start;
            }

            .nav-user-info {
                gap: 1rem;
            }

            .user-info {
                display: none;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table {
                font-size: 0.95rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem 0.5rem;
            }
        }


            /* Polished admin UI */
            .admin-toolbar { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:0.5rem 0 1rem 0; }
            .admin-breadcrumb { font-size:0.92rem; color:var(--text-muted); }
            .admin-card { border-radius:12px; box-shadow: 0 6px 18px rgba(20,20,40,0.04); overflow:hidden; border: none; }
            .admin-card .card-header { background: linear-gradient(180deg, rgba(59,124,168,0.06), rgba(59,124,168,0.02)); border-bottom:1px solid rgba(0,0,0,0.04); font-weight:700; }
            .stats-grid .stat-card { padding:1rem 1.2rem; }
            .stat-card .stat-label { font-size:0.78rem; color:var(--text-muted); }
            .stat-card .stat-number { font-size:1.45rem; }
            .list-group-item { border-radius:8px; margin-bottom:6px; transition:all 0.15s ease; padding:0.75rem 0.9rem; }
            .list-group-item.active { background: linear-gradient(90deg, var(--primary-blue), var(--primary-light)); color:white; box-shadow: 0 6px 18px rgba(59,124,168,0.12); }
            .list-group-item .fa-lg { width:26px; text-align:center; color:var(--primary-blue); }
            .table thead th { background: #fbfcfd; color:var(--text-dark); font-weight:700; border-bottom: 2px solid rgba(0,0,0,0.04); }


            .table-compact thead th {
                font-size: 0.85rem;
                padding: 0.45rem 0.6rem;
                line-height: 1.1;
                font-weight: 600;
                /* subtle themed background */
                background: linear-gradient(180deg, rgba(59,124,168,0.06), rgba(59,124,168,0.02));
                color: var(--text-dark);
                border-bottom: 1px solid rgba(59,124,168,0.06);
            }
            /* Also slightly reduce body row padding for compact tables */
            .table-compact tbody td {
                padding: 0.45rem 0.6rem;
                font-size: 0.92rem;
            }

            /* Make sure thead style stays readable on very small screens */
            @media (max-width: 575px) {
                .table-compact thead th { font-size: 0.82rem; padding: 0.35rem 0.5rem; }
            }
            .table-hover tbody tr:hover { background:#f8fafc; }
            .admin-quick-links .btn { min-width:130px; }

            /* Admin alignment improvements */
            .admin-stats { display:flex; gap:1rem; flex-wrap:wrap; }
            .admin-stats .stat-card { display:flex; flex-direction:row; align-items:center; justify-content:space-between; padding:1rem; min-height:110px; }
            .admin-stats .stat-card .stat-content { flex:1; padding-right:1rem; }
            .admin-stats .stat-card .stat-label { font-size:0.78rem; margin-bottom:0.35rem; color:var(--text-muted); }
            .admin-stats .stat-card .stat-number { font-size:1.6rem; font-weight:700; }
            .admin-stats .stat-card .stat-card-icon { width:56px; height:56px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; background:rgba(0,0,0,0.03); }

            .admin-activity li { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:0.6rem 0; }
            .admin-activity li .meta { color:var(--text-muted); font-size:0.85rem; }

            @media (max-width: 767px) {
                .admin-stats .stat-card { flex-direction:column; align-items:flex-start; min-height: auto; }
                .admin-stats .stat-card .stat-card-icon { align-self:flex-end; margin-top:0.5rem; }
            }

            /* Logout button animation */
            .logout-animate {
                transition: transform .18s ease, box-shadow .18s ease;
                will-change: transform;
            }
            .logout-animate:hover {
                transform: translateY(-2px) scale(1.03);
            }
            .logout-animate .fa-sign-out-alt {
                transition: transform .2s ease;
            }
            .logout-animate:hover .fa-sign-out-alt { transform: rotate(-10deg); }
            .logout-animate.is-animating {
                animation: logout-pulse .56s ease;
                box-shadow: 0 8px 18px rgba(239,68,68,0.14);
            }
            @keyframes logout-pulse {
                0% { transform: scale(1); }
                50% { transform: scale(0.98); }
                100% { transform: scale(1); }
            }

            /* Logout modal entrance animation */
            .modal.fade .modal-dialog {
                transform: translateY(-12px) scale(.98);
                opacity: 0;
                transition: transform .26s cubic-bezier(.2,.9,.3,1), opacity .22s ease;
            }
            .modal.show .modal-dialog, .modal-dialog.modal-animate-in {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            .modal .modal-content {
                transition: box-shadow .22s ease, transform .22s ease;
                border-radius: 12px;
            }

        /* Enhanced Timeline Styles */
        .timeline {
            position: relative;
            padding: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #3b7ca8, #e5e7eb);
        }

        .timeline-item {
            position: relative;
            padding-left: 50px;
            padding-bottom: 1.2rem;
            transition: all 0.3s ease;
        }

        .timeline-item.completed .timeline-marker {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .timeline-item.completed .timeline-marker i {
            color: white;
        }

        .timeline-item.pending .timeline-marker {
            background: #e5e7eb;
            box-shadow: 0 0 0 3px rgba(229, 231, 235, 0.5);
        }

        .timeline-item.pending .timeline-marker i {
            color: #9ca3af;
        }

        .timeline-marker {
            position: absolute;
            left: 4px;
            top: 2px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .timeline-content {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.7rem;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .timeline-item.completed .timeline-content {
            border-left: 3px solid #10b981;
            background: #f0fdf4;
        }

        .timeline-item.pending .timeline-content {
            border-left: 3px solid #e5e7eb;
            background: #f9fafb;
        }

        .timeline-title {
            margin: 0 0 0.3rem 0;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .timeline-status {
            margin: 0.2rem 0;
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .timeline-meta {
            margin: 0.3rem 0;
            color: var(--text-muted);
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .timeline-remarks {
            margin: 0.4rem 0 0 0;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            color: var(--text-muted);
            font-size: 0.8rem;
            border-left: 3px solid var(--primary-light);
            font-style: italic;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        @media (max-width: 768px) {
            .timeline::before {
                left: 12px;
            }

            .timeline-item {
                padding-left: 42px;
                padding-bottom: 1rem;
            }

            .timeline-marker {
                width: 26px;
                height: 26px;
                font-size: 0.8rem;
                left: 2px;
            }

            .timeline-content {
                padding: 0.6rem;
            }

            .timeline-title {
                font-size: 0.85rem;
            }

            .timeline-status {
                font-size: 0.8rem;
            }
        }

        /* Enhanced Information Grid Styles */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .info-item-full {
            grid-column: 1 / -1;
        }

        .info-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.9rem;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        /* Keep info cards purely informational (no clickable-style hover) */
        .info-item:hover {
            border-color: #e5e7eb;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            transform: none;
            box-shadow: none;
            cursor: default;
        }

        .info-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: white;
            border-radius: 8px;
            flex-shrink: 0;
            font-size: 1rem;
        }

        .info-content {
            flex: 1;
            min-width: 0;
        }

        .info-label {
            margin: 0;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            margin: 0.3rem 0 0 0;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-dark);
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* Generic style for visually read-only form fields */
        .acct-readonly {
            background-color: #f3f4f6 !important;
            color: var(--text-muted) !important;
            cursor: not-allowed;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }

            .info-item-full {
                grid-column: 1;
            }

            .info-item {
                padding: 0.8rem;
            }

            .info-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .info-value {
                font-size: 0.9rem;
            }
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="assets/images/DEPED LOGO.jpg" alt="DepEd logo" class="site-logo" />
                <div class="brand-text">
                    <span class="main-title">DepEd Division Office</span>
                    <small>Supplier Transaction Monitoring System</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">

                <div class="nav-user-info">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-2" aria-hidden="true"></i>
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'User')); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <?php if (($_SESSION['role'] ?? '') === 'supplier'): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="change_password.php">
                                    <i class="fas fa-user-cog me-2"></i> Account settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li>
                            <button class="dropdown-item text-danger d-flex align-items-center logout-animate" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal" type="button" aria-label="Sign out">
                                <i class="fas fa-sign-out-alt me-2"></i> <strong>Sign out</strong>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </nav>
    <div class="container-main">

<!-- Create New PO Modal -->
<div class="modal fade" id="createPOModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: #1e3a8a; padding: 1rem; color: white; border-radius: 12px 12px 0 0;">
                <h5 style="font-weight: 450; font-size: 1rem; margin: 0;">NEW PURCHASE ORDER</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; right: 1rem; top: 1rem;"></button>
            </div>
            
            <!-- Body -->
            <div style="padding: 1rem;">
                <div id="poModalAlert"></div>
                <form id="createPOForm" novalidate>
                    <!-- PO Number and Type -->
                    <div class="row gx-2">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">PO Number <span style="color: #dc2626;">*</span></label>
                            <input type="text" name="po_number" class="form-control" required
                                   placeholder="Enter PO number"
                                   style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Type <span style="color: #dc2626;">*</span></label>
                            <select name="po_type" class="form-control" required
                                    style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                                <option value="">-- Select type --</option>
                                <option value="Transpo/venue">Transpo/Venue</option>
                                <option value="Supplies">Supplies</option>
                            </select>
                        </div>
                    </div>

                    <!-- Program Title -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Program Title <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="program_title" class="form-control" required
                               placeholder="Enter program title"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                    </div>

                    <!-- Proponent -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Proponent <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="proponent" class="form-control" required
                               placeholder="Enter proponent name"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                    </div>

                    <!-- Supplier -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Supplier <span style="color: #dc2626;">*</span></label>
                        <select name="supplier_name" class="form-control" id="supplierSelect" required
                                style="border-radius: 6px; border: 1px solid #ddd; padding: 0.6rem 0.8rem; font-size: 0.9rem;">
                            <option value="">-- Select supplier --</option>
                        </select>
                    </div>

                    <div class="row gx-2">
                        <div class="col-12 mb-3">
                            <!-- Coverage Date Range -->
                            <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Date (Coverage) <span style="color: #dc2626;">*</span></label>
                            <div class="d-flex gap-2">
                                <input type="date" name="coverage_start" class="form-control" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                                <span style="align-self: center; padding: 0 0.25rem;">to</span>
                                <input type="date" name="coverage_end" class="form-control" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Expected Date (Optional) -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">Expected Date (Optional)</label>
                        <input type="text" name="expected_date" class="form-control"
                               placeholder="e.g. Within March 2026 or Before opening of classes"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                    </div>

                    <div class="mb-3">
                        <!-- PO Amount -->
                        <label class="form-label" style="font-weight: 600; color: #333; font-size: 0.9rem;">PO (Gross Amount) <span style="color: #dc2626;">*</span></label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0"
                               placeholder="0.00"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 0.5rem 0.6rem; font-size: 0.9rem;">
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div style="border-top: 1px solid #eee; padding: 1.2rem; display: flex; gap: 0.8rem; justify-content: flex-end;">
                <button type="button" class="btn" data-bs-dismiss="modal" 
                        style="border-radius: 6px; padding: 0.2rem 1.5rem; font-weight: 400; background: #f3f4f6; color: #333; border: 1px solid #ddd; font-size: 0.9rem;">
                    Cancel
                </button>
                <button type="button" class="btn" id="submitPOBtn"
                        style="border-radius: 6px; padding: 0.2rem 1.5rem; font-weight: 400; background: #1e3a8a; color: white; border: none; font-size: 0.9rem;" 
                        onclick="submitPO()">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load suppliers on modal open
const createPOModal = document.getElementById('createPOModal');
if (createPOModal) {
    createPOModal.addEventListener('show.bs.modal', function () {
        loadSuppliers();
    });
}

function loadSuppliers() {
    fetch('api_load_suppliers.php')
        .then(response => response.json())
        .then(data => {
            const supplierSelect = document.getElementById('supplierSelect');
            supplierSelect.innerHTML = '<option value="">-- Select supplier --</option>';
            if (data.success) {
                data.suppliers.forEach(supplier => {
                    const option = document.createElement('option');
                    option.value = supplier.name;
                    option.textContent = supplier.name;
                    supplierSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading suppliers:', error));
}

function submitPO() {
    const form = document.getElementById('createPOForm');
    const alertDiv = document.getElementById('poModalAlert');
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitPOBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Submitting...';

    fetch('api_create_po.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerHTML = '<div class="alert alert-success" role="alert" style="border-radius: 10px; border: none; background: #d1fae5; color: #047857; padding: 1rem;">' + data.message + '</div>';
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(createPOModal);
                modal.hide();
                form.reset();
                form.classList.remove('was-validated');
                alertDiv.innerHTML = '';
                location.reload();
            }, 1500);
        } else {
            alertDiv.innerHTML = '<div class="alert alert-danger" role="alert" style="border-radius: 10px; border: none; background: #fee2e2; color: #991b1b; padding: 1rem;">' + data.message + '</div>';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit';
        }
    })
    .catch(error => {
        alertDiv.innerHTML = '<div class="alert alert-danger" role="alert" style="border-radius: 10px; border: none; background: #fee2e2; color: #991b1b; padding: 1rem;">Error submitting form</div>';
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit';
        console.error('Error:', error);
    });
}
</script>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutConfirmModalLabel">Confirm Sign out</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex align-items-center gap-3">
        <i class="fas fa-sign-out-alt fa-2x text-danger"></i>
        <div>
          <p class="mb-1"><strong><?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'User')); ?></strong>, are you sure you want to sign out?</p>
          <p class="small text-muted mb-0">You will need to log in again to access the dashboard.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" action="logout.php" class="d-inline">
          <button type="submit" class="btn btn-danger">Sign out</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Initialize Bootstrap tooltips for collapsed icons and icon tooltips
(function(){
    function initTooltips(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) {
            return new bootstrap.Tooltip(el, {boundary: 'window'});
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTooltips);
    } else {
        initTooltips();
    }

    // Small Chart.js default styling
    if (window.Chart) {
        Chart.defaults.font.family = "'Segoe UI', Roboto, Arial, sans-serif";
        Chart.defaults.color = '#334155';
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.elements.bar.borderRadius = 6;
    }

    // Logout button click animation handler + modal animations
    (function(){
        var logoutBtns = document.querySelectorAll('button.logout-animate');
        logoutBtns.forEach(function(btn){
            btn.addEventListener('click', function(){
                btn.classList.add('is-animating');
                setTimeout(function(){ btn.classList.remove('is-animating'); }, 700);
            });
        });

        var logoutModal = document.getElementById('logoutConfirmModal');
        if (logoutModal) {
            logoutModal.addEventListener('show.bs.modal', function(){
                var dlg = logoutModal.querySelector('.modal-dialog');
                if (dlg) dlg.classList.add('modal-animate-in');
            });
            logoutModal.addEventListener('hidden.bs.modal', function(){
                var dlg = logoutModal.querySelector('.modal-dialog');
                if (dlg) dlg.classList.remove('modal-animate-in');
            });
        }
    })();
})();
</script>
