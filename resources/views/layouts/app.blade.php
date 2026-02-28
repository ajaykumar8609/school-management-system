<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management') - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: #1e3a5f;
            --sidebar-hover: #2a4a6f;
            --sidebar-active: #3d6ba8;
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --green: #10b981;
            --red: #ef4444;
            --orange: #f59e0b;
            --shadow: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        }
        body { font-family: 'Inter', sans-serif; background: var(--gray-50); min-height: 100vh; }
        .app-wrapper { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px; background: var(--sidebar-bg); color: white;
            flex-shrink: 0; display: flex; flex-direction: column;
        }
        .sidebar-header {
            padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo { width: 36px; height: 36px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .sidebar-title { font-weight: 700; font-size: 14px; }
        .sidebar-nav { padding: 16px 0; flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px;
            color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14px;
            transition: all 0.2s;
        }
        .nav-item:hover { background: var(--sidebar-hover); color: white; }
        .nav-item.active { background: var(--sidebar-active); color: white; }
        .nav-icon { width: 20px; text-align: center; opacity: 0.9; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .top-header {
            background: white; padding: 16px 24px; box-shadow: var(--shadow);
            display: flex; align-items: center; justify-content: space-between;
        }
        .header-title { font-size: 20px; font-weight: 600; color: var(--gray-900); }
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .header-actions a { color: var(--gray-600); }
        .user-profile { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .content-area { padding: 24px; overflow-y: auto; flex: 1; }
        .card {
            background: white; border-radius: 12px; box-shadow: var(--shadow);
            padding: 20px; margin-bottom: 20px;
        }
        .card-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--gray-900); }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;
            text-decoration: none; border: none; transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: white; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-secondary:hover { background: var(--gray-50); }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-danger { background: var(--red); color: white; }
        .btn-success { background: var(--green); color: white; }
        .btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--gray-200); }
        th { font-weight: 600; color: var(--gray-700); font-size: 13px; }
        td { font-size: 14px; color: var(--gray-700); }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--gray-700); }
        .form-control {
            width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300);
            border-radius: 8px; font-size: 14px;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        select.form-control { cursor: pointer; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        @media (max-width: 1024px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } .grid-2 { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { .grid-4 { grid-template-columns: 1fr; } .sidebar { width: 70px; } .sidebar-title, .nav-item span { display: none; } }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/school-logo.png') }}" alt="School Logo" class="sidebar-logo" style="width:40px;height:40px;object-fit:contain;border-radius:8px;">
                <span class="sidebar-title">Student Management</span>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span><span>Dashboard</span>
                </a>
                <a href="{{ route('students.index') }}" class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span><span>Students</span>
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <span class="nav-icon">📅</span><span>Attendance</span>
                </a>
                <a href="{{ route('academic.index') }}" class="nav-item {{ request()->routeIs('academic.*') ? 'active' : '' }}">
                    <span class="nav-icon">📚</span><span>Academic</span>
                </a>
                <a href="{{ route('fee.index') }}" class="nav-item {{ request()->routeIs('fee.*') ? 'active' : '' }}">
                    <span class="nav-icon">💰</span><span>Fee Management</span>
                </a>
                <a href="#" class="nav-item"><span class="nav-icon">📋</span><span>Reports</span></a>
                <a href="#" class="nav-item"><span class="nav-icon">⚙️</span><span>Settings</span></a>
            </nav>
        </aside>
        <main class="main-content">
            <header class="top-header">
                <h1 class="header-title">@yield('header-title', 'Student Management Module')</h1>
                <div class="header-actions">
                    <a href="#" title="Notifications">🔔</a>
                    <a href="#" title="Messages">✉️</a>
                    <div class="user-profile" style="display:flex;align-items:center;gap:12px;">
                        <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                        <span style="font-weight:500;color:var(--gray-700);">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <form action="{{ route('logout') }}" method="post" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none;border:1px solid var(--gray-300);padding:6px 12px;border-radius:6px;cursor:pointer;font-size:13px;">Logout</button>
                        </form>
                    </div>
                </div>
            </header>
            <div class="content-area">
                @if(session('success'))
                    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:20px;">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
