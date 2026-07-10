<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Lupa Password - GAOne</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --primary: #2563EB;
            --primary-hover: #1D4ED8;
            --primary-soft: #DBEAFE;

            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
        }

        *{
            font-family:'Poppins',sans-serif;
            box-sizing:border-box;
        }

        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;

            background:
                radial-gradient(
                    circle at top left,
                    rgba(37,99,235,.25),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(59,130,246,.20),
                    transparent 40%
                ),
                linear-gradient(
                    135deg,
                    #F8FAFC 0%,
                    #EFF6FF 100%
                );

            padding:24px;
        }

        .forgot-card{
            width:100%;
            max-width:460px;

            background:white;

            border:1px solid var(--border);
            border-radius:24px;

            padding:40px;

            box-shadow:
                0 1px 2px rgba(0,0,0,.05),
                0 10px 30px rgba(15,23,42,.06);
        }

        .logo{
            width:64px;
            height:64px;

            background:var(--primary-soft);
            color:var(--primary);

            display:flex;
            align-items:center;
            justify-content:center;

            margin:auto;

            border-radius:18px;

            font-size:24px;
            font-weight:700;
        }

        .title{
            text-align:center;
            margin-top:20px;

            color:var(--text);

            font-size:32px;
            font-weight:700;
        }

        .subtitle{
            text-align:center;
            color:var(--muted);

            margin-top:10px;
            margin-bottom:32px;

            line-height:1.6;
            font-size:14px;
        }

        .form-group{
            margin-bottom:18px;
        }

        .input{
            width:100%;

            padding:14px 18px;

            border:1px solid var(--border);
            border-radius:12px;

            outline:none;

            font-size:14px;
        }

        .input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 4px rgba(37,99,235,.12);
        }

        .btn-primary{
            width:100%;

            border:none;

            background:var(--primary);
            color:white;

            padding:14px;

            border-radius:12px;

            font-size:14px;
            font-weight:600;

            cursor:pointer;
        }

        .btn-primary:hover{
            background:var(--primary-hover);
        }

        .footer{
            text-align:center;
            margin-top:24px;

            color:var(--muted);
            font-size:14px;
        }

        .footer a{
            color:var(--primary);
            font-weight:600;
            text-decoration:none;
        }

        .alert{
            margin-bottom:18px;
        }
    </style>
</head>
<body>

<div class="forgot-card">

    <div class="logo">
        GA
    </div>

    <div class="title">
        Lupa Password
    </div>

    <div class="subtitle">
        Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk membuat password baru.
    </div>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <input
                type="email"
                name="email"
                class="input"
                placeholder="Alamat Email"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <button type="submit" class="btn-primary">
            Kirim Link Reset
        </button>
    </form>

    <div class="footer">
        Ingat password?
        <a href="{{ route('login') }}">
            Kembali ke Login
        </a>
    </div>

</div>

</body>
</html>
