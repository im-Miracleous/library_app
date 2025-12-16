<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Library App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow mb-8">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-xl font-bold text-gray-800">Library App</div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Halo, <b>{{ $user->nama }}</b> ({{ ucfirst($user->peran) }})</span>
                
                <!-- Form Logout -->
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
            <p class="text-gray-700">
                Selamat datang di sistem manajemen perpustakaan.
                ID Anggota Anda adalah: <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-mono">{{ $user->id }}</span>
            </p>
        </div>
    </div>

</body>
</html>