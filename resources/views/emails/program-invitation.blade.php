<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9f9f9;
            padding: 40px 20px;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            background: #ffffff;
            border-radius: 32px;
            padding: 48px;
            max-width: 540px;
            margin: auto;
            border: 1px solid #f0f0f0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
        }
        .logo-container {
            margin-bottom: 32px;
            text-align: left;
        }
        .logo-img {
            height: 35px;
            width: auto;
            display: block;
        }
        .header-title {
            color: #800000;
            font-weight: 900;
            text-transform: uppercase;
            font-style: italic;
            letter-spacing: -0.5px;
            font-size: 14px;
            margin-bottom: 24px;
            border-left: 3px solid #800000;
            padding-left: 12px;
        }
        .greeting {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .message-text {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .program-box {
            background: #000;
            color: #fff;
            padding: 30px;
            border-radius: 24px;
            margin: 24px 0;
            position: relative;
            overflow: hidden;
        }
        .program-label {
            font-size: 9px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .program-name {
            font-size: 22px;
            font-weight: 900;
            font-style: italic;
            color: #ffffff;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .btn-wrapper {
            text-align: center;
            margin-top: 40px;
        }
        .btn {
            background: #800000;
            color: #ffffff !important;
            text-decoration: none;
            padding: 18px 45px;
            border-radius: 18px;
            font-weight: 900;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .footer {
            font-size: 10px;
            color: #bbb;
            margin-top: 48px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="card">
        {{-- Logo Section --}}
        <div class="logo-container">
            <img src="{{ $message->embed(public_path('images/logoo.png')) }}" alt="Civic Logo" class="logo-img">
        </div>

        <div class="header-title">Platform Invitation</div>

        <div class="greeting">Halo, {{ $user->name }}!</div>

        <p class="message-text">
            Selamat! Akun Anda telah berhasil diaktivasi dan didaftarkan secara resmi ke dalam program eksklusif kami:
        </p>

        <div class="program-box">
            <div class="program-label">Active Program</div>
            <div class="program-name">{{ $program->name }}</div>
        </div>

        <p class="message-text" style="margin-top: 24px;">
            Silakan akses dashboard Anda menggunakan tombol di bawah ini untuk memulai perjalanan belajar Anda.
        </p>

        <div class="btn-wrapper">
            <a href="{{ url('/login') }}" class="btn">Gaskeun Login Sekarang</a>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} CIVIC PLATFORM &bull; SYSTEM SMART INVITE
        </div>
    </div>
</body>
</html>
