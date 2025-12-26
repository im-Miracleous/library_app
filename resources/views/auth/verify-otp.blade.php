<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Verifikasi OTP</h3>
        
        <p class="text-muted text-center">
            Kode OTP telah dikirim ke email Anda.<br>
            Masukkan kode tersebut di bawah ini.
        </p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('otp.action', $id) }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Kode OTP (6 Digit)</label>
                <input type="number" name="otp" class="form-control text-center" placeholder="123456" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
        </form>
    </div>

</body>
</html>