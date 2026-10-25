<!DOCTYPE html>
<html lang="en">
<head>
    {{--
      ICTHospital sign-in page (2026 design). Same form contract as before: POST users/login
      with login + password, the CSRF token, the flash message and $error.

      Copyright (c) ICT Innovations <https://www.ictinnovations.com>
      Part of ICTHospital <https://www.icthospital.com>
      Licensed under the GNU General Public License v3.0.
    --}}
    <meta charset="utf-8">
    <title>Sign in · {{ $institute->name ?? 'ICTHospital' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/brand/icthospital-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/brand/favicon-180.png') }}">
    <link href="{{ asset('assets/vendor/fontawesome-5.15/css/all.min.css') }}" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; height: 100%; }
        body { font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Arial, sans-serif; color: #1e293b; background: #f4f7fa; }
        .ih-login { display: grid; grid-template-columns: 1.05fr 1fr; min-height: 100vh; }
        .ih-side { background: linear-gradient(150deg, #14b8a6 0%, #0f766e 55%, #0b4f4a 100%); color: #ccfbf1; padding: 56px 64px; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; }
        .ih-side::after { content: ""; position: absolute; right: -120px; bottom: -120px; width: 420px; height: 420px; border-radius: 50%; background: rgba(255,255,255,.06); }
        .ih-side img { height: 54px; width: auto; align-self: flex-start; filter: drop-shadow(0 6px 16px rgba(4,47,46,.35)); }
        .ih-side h1 { color: #fff; font-size: 34px; line-height: 1.2; margin: 0 0 14px; font-weight: 700; max-width: 460px; }
        .ih-side p { font-size: 16px; line-height: 1.6; margin: 0; max-width: 440px; }
        .ih-side ul { list-style: none; padding: 0; margin: 28px 0 0; display: grid; gap: 12px; }
        .ih-side li { display: flex; align-items: center; gap: 12px; font-size: 15px; }
        .ih-side li i { width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,.14); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; }
        .ih-side small { color: rgba(204,251,241,.75); font-size: 13px; position: relative; z-index: 1; }
        .ih-main { display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
        .ih-card { width: 100%; max-width: 400px; }
        .ih-card .ih-mobile-logo { display: none; height: 44px; margin-bottom: 28px; }
        .ih-card h2 { font-size: 26px; margin: 0 0 6px; color: #0B2B4A; font-weight: 700; }
        .ih-card .ih-sub { color: #64748b; margin: 0 0 28px; font-size: 15px; }
        .ih-field { margin-bottom: 18px; }
        .ih-field label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 7px; }
        .ih-input { position: relative; }
        .ih-input i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px; }
        .ih-input input { width: 100%; height: 48px; padding: 0 14px 0 42px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 15px; color: #0f172a; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; }
        .ih-input input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.18); }
        .ih-btn { width: 100%; height: 48px; border: 0; border-radius: 10px; background: #0f766e; color: #fff; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 6px; transition: background .15s; }
        .ih-btn:hover, .ih-btn:focus { background: #115e59; }
        .ih-alert { border-radius: 10px; padding: 12px 14px; font-size: 14px; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start; }
        .ih-alert.ok { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .ih-alert.err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .ih-foot { margin-top: 32px; color: #94a3b8; font-size: 13px; text-align: center; }
        .ih-foot a { color: #64748b; text-decoration: none; }
        @media (max-width: 900px) {
            .ih-login { grid-template-columns: 1fr; }
            .ih-side { display: none; }
            .ih-card .ih-mobile-logo { display: block; }
        }
    </style>
</head>
<body>
<div class="ih-login">
    <aside class="ih-side">
        <img src="{{ asset('img/brand/icthospital-logo-white.svg') }}" alt="ICTHospital">
        <div style="position:relative;z-index:1">
            <h1>Run your hospital from one place.</h1>
            <p>Patients, appointments, wards, pharmacy, laboratory and billing, with the front desk and the doctors on the same record.</p>
            <ul>
                <li><i class="fas fa-user-injured"></i> Patient records and medical history</li>
                <li><i class="fas fa-stethoscope"></i> OPD tokens, consultations and prescriptions</li>
                <li><i class="fas fa-file-invoice-dollar"></i> Billing, cash closing and doctor share</li>
            </ul>
        </div>
        <small>ICTHospital by ICT Innovations</small>
    </aside>

    <main class="ih-main">
        <div class="ih-card">
            <img class="ih-mobile-logo" src="{{ asset('img/brand/icthospital-logo.svg') }}" alt="ICTHospital">
            <h2>Sign in</h2>
            <p class="ih-sub">Welcome to {{ $institute->name ?? 'ICTHospital' }}. Use your staff account.</p>

            @if (Session::get('message'))
                <div class="ih-alert ok"><i class="fas fa-check-circle"></i><span>{{ Session::get('message') }}</span></div>
            @endif
            @if (isset($error))
                <div class="ih-alert err"><i class="fas fa-exclamation-circle"></i><span>{{ $error }}.</span></div>
            @endif

            <form action="{{ url('users/login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="ih-field">
                    <label for="login">Username</label>
                    <div class="ih-input"><i class="fas fa-user"></i>
                        <input type="text" id="login" name="login" placeholder="Username" autocomplete="username" autofocus required>
                    </div>
                </div>
                <div class="ih-field">
                    <label for="password">Password</label>
                    <div class="ih-input"><i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
                    </div>
                </div>
                <button type="submit" class="ih-btn">Sign in</button>
            </form>

            <div class="ih-foot">&copy; {{ date('Y') }} {{ $institute->name ?? 'ICTHospital' }} · Developed by <a href="https://www.ictinnovations.com/" target="_blank" rel="noopener">ICT Innovations</a></div>
        </div>
    </main>
</div>
</body>
</html>
