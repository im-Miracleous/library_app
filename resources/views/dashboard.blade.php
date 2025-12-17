<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - Library App</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    
    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">
        
        <!-- MOBILE OVERLAY (Latar Belakang Gelap saat Menu Buka di HP) -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0"></div>

        <!-- SIDEBAR (Responsive: Fixed di HP, Relative di Desktop) -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 w-72 h-full bg-background-dark border-r border-[#36271F] p-6 flex flex-col justify-between z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
            
            <!-- Tombol Close Sidebar (Hanya di HP) -->
            <button id="close-sidebar" class="lg:hidden absolute top-4 right-4 text-white/60 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="flex flex-col gap-8 mt-2 lg:mt-0">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-accent/20 flex items-center justify-center rounded-full size-12 hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-accent" style="font-size: 28px;">local_library</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-white text-lg font-bold leading-tight">Library App</h1>
                        <p class="text-white/60 text-xs font-medium">Panel Manajemen</p>
                    </div>
                </div>
                
                <!-- Menu -->
                <nav class="flex flex-col gap-2">
                    <a class="flex items-center gap-3 px-4 py-3 rounded-full bg-accent text-primary-dark transition-all hover:brightness-110 shadow-[0_0_15px_rgba(236,177,118,0.3)]" href="#">
                        <span class="material-symbols-outlined filled" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                        <p class="text-sm font-bold">Dashboard</p>
                    </a>
                </nav>
            </div>

            <!-- User Profile & Logout -->
            <div class="flex flex-col gap-4">
                <div class="bg-surface-dark p-4 rounded-xl flex items-center gap-3 border border-[#36271F] hover-card cursor-default">
                    <div class="size-10 rounded-full bg-accent/20 flex items-center justify-center text-accent font-bold">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <p class="text-sm font-bold truncate">{{ Auth::user()->nama }}</p>
                        <p class="text-xs text-white/60 truncate capitalize">{{ Auth::user()->peran }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="w-full form-logout">
                    @csrf
                    <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full h-12 bg-[#36271F] hover:bg-[#4D3A2F] text-white text-sm font-bold transition-all active:scale-95">
                        <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <!-- Header Atas -->
            <header class="animate-enter flex items-center justify-between sticky top-0 bg-background-dark/95 backdrop-blur-sm z-30 px-4 sm:px-8 py-4 border-b border-[#36271F]">
                
                <div class="flex items-center gap-4">
                    <!-- Tombol Hamburger (Hanya Muncul di HP) -->
                    <button id="open-sidebar" class="lg:hidden text-white hover:text-accent transition-colors">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>

                    <h2 class="text-white text-xl sm:text-2xl font-bold tracking-tight">Overview</h2>
                </div>
                
                <!-- Search Bar (Hidden di Mobile Kecil) -->
                <div class="flex-1 max-w-xl px-8 hidden md:block">
                    <div class="relative group input-focus-effect">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-accent">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input class="block w-full p-3 pl-12 text-sm text-white bg-surface-dark border-none rounded-full placeholder-white/40 focus:ring-2 focus:ring-accent focus:bg-[#36271F] transition-all" placeholder="Cari buku, ISBN, atau anggota..." type="text" />
                    </div>
                </div>

                <!-- Kanan Atas -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="hidden sm:flex flex-col items-end mr-2">
                        <span class="text-white text-sm font-bold">{{ Auth::user()->nama }}</span>
                        <span class="text-accent text-xs uppercase tracking-wider font-bold">{{ Auth::user()->id }}</span>
                    </div>

                    <button class="flex items-center justify-center size-10 rounded-full bg-surface-dark text-white hover:bg-[#36271F] transition-all hover:rotate-12 relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-surface-dark animate-pulse"></span>
                    </button>
                </div>
            </header>

            <!-- Konten Dashboard -->
            <div class="p-4 sm:p-8 flex flex-col gap-8 max-w-[1600px] mx-auto w-full h-full justify-center items-center text-center">
                <div class="bg-surface-dark p-8 sm:p-12 rounded-2xl border border-[#36271F] max-w-lg animate-enter delay-200 hover-card w-full mx-4">
                    <span class="material-symbols-outlined text-5xl sm:text-6xl text-accent mb-4 animate-bounce">dashboard</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Dashboard Kosong</h3>
                    <p class="text-white/60 text-sm sm:text-base">
                        Selamat datang, <b>{{ Auth::user()->nama }}</b>! <br>
                        Menu dan fitur akan segera ditambahkan di sini.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>