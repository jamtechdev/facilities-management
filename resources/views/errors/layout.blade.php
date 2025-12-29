<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') - {{ config('app.name', 'KEYSTONE') }}</title>
    <!-- Bootstrap Icons - Load first for icons to display -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" crossorigin="anonymous">
    @vite(['resources/css/app.css'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #69b266 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 50%, rgba(132, 195, 115, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(107, 168, 90, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(132, 195, 115, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, -30px) scale(1.1); }
        }

        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 650px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-card {
            background: white;
            border-radius: 28px;
            padding: 4rem 3rem;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.06),
                0 16px 64px rgba(132, 195, 115, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(132, 195, 115, 0.12);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #84c373 0%, #6ba85a 50%, #84c373 100%);
            background-size: 200% 100%;
            animation: shimmer 4s ease-in-out infinite;
            box-shadow: 0 2px 12px rgba(132, 195, 115, 0.4);
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .error-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(132, 195, 115, 0.03) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .error-card:hover::after {
            opacity: 1;
        }

        .error-icon-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-icon {
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 70px;
            color: white;
            box-shadow:
                0 12px 48px rgba(132, 195, 115, 0.35),
                0 6px 24px rgba(132, 195, 115, 0.25),
                inset 0 -2px 8px rgba(107, 168, 90, 0.3);
            position: relative;
            animation: iconPulse 3s ease-in-out infinite;
            z-index: 2;
        }

        .error-icon::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(132, 195, 115, 0.2) 0%, rgba(107, 168, 90, 0.1) 100%);
            animation: iconRipple 3s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow:
                    0 12px 48px rgba(132, 195, 115, 0.35),
                    0 6px 24px rgba(132, 195, 115, 0.25),
                    inset 0 -2px 8px rgba(107, 168, 90, 0.3);
            }
            50% {
                transform: scale(1.08);
                box-shadow:
                    0 16px 64px rgba(132, 195, 115, 0.45),
                    0 8px 32px rgba(132, 195, 115, 0.35),
                    inset 0 -2px 8px rgba(107, 168, 90, 0.3);
            }
        }

        @keyframes iconRipple {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.3;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .error-code-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .error-code {
            font-size: 140px;
            font-weight: 900;
            background: linear-gradient(135deg, #1a1f2e 0%, #2d3748 50%, #1a1f2e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 100%;
            line-height: 1;
            letter-spacing: -8px;
            position: relative;
            animation: textShimmer 4s ease-in-out infinite;
        }

        @keyframes textShimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .error-code-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
            color: rgba(132, 195, 115, 0.15);
            z-index: 1;
            pointer-events: none;
        }

        .error-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1f2e;
            margin-bottom: 1.25rem;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .error-message {
            font-size: 17px;
            color: #64748b;
            margin-bottom: 3rem;
            line-height: 1.7;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-error {
            padding: 14px 32px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-error::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-error:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-error-primary {
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            color: white;
            box-shadow:
                0 6px 20px rgba(132, 195, 115, 0.3),
                0 2px 8px rgba(132, 195, 115, 0.2);
            position: relative;
            z-index: 1;
        }

        .btn-error-primary:hover {
            transform: translateY(-3px);
            box-shadow:
                0 10px 32px rgba(132, 195, 115, 0.4),
                0 4px 12px rgba(132, 195, 115, 0.3);
            color: white;
        }

        .btn-error-primary:active {
            transform: translateY(-1px);
        }

        .btn-error-secondary {
            background: white;
            color: #64748b;
            border: 2px solid #e2e8f0;
            position: relative;
            z-index: 1;
        }

        .btn-error-secondary:hover {
            background: #f8faf9;
            border-color: #84c373;
            color: #84c373;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(132, 195, 115, 0.15);
        }

        .btn-error i {
            font-size: 18px;
            position: relative;
            z-index: 2;
        }

        .btn-error span {
            position: relative;
            z-index: 2;
        }

        @media (max-width: 768px) {
            .error-card {
                padding: 3rem 2rem;
                border-radius: 24px;
            }

            .error-code {
                font-size: 100px;
                letter-spacing: -6px;
            }

            .error-icon {
                width: 120px;
                height: 120px;
                font-size: 60px;
            }

            .error-icon-wrapper {
                width: 120px;
                height: 120px;
            }

            .error-title {
                font-size: 26px;
            }

            .error-message {
                font-size: 16px;
            }
        }

        @media (max-width: 576px) {
            .error-card {
                padding: 2.5rem 1.5rem;
                border-radius: 20px;
            }

            .error-code {
                font-size: 80px;
                letter-spacing: -4px;
            }

            .error-icon {
                width: 100px;
                height: 100px;
                font-size: 50px;
            }

            .error-icon-wrapper {
                width: 100px;
                height: 100px;
                margin-bottom: 2rem;
            }

            .error-title {
                font-size: 22px;
            }

            .error-message {
                font-size: 15px;
                margin-bottom: 2.5rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-error {
                width: 100%;
                justify-content: center;
                padding: 14px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon-wrapper">
                <div class="error-icon">
                    <i class="bi @yield('icon', 'bi-exclamation-circle')"></i>
                </div>
            </div>
            <div class="error-code-wrapper">
                <div class="error-code">@yield('code')</div>
            </div>
            <h1 class="error-title">@yield('title')</h1>
            <p class="error-message">@yield('message')</p>
            <div class="error-actions">
                @yield('actions')
            </div>
        </div>
    </div>
</body>
</html>
