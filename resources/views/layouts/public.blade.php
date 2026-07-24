<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'الضمان') - iPhone Azaz
    </title>

    @production
        @vite([
            'resources/css/app.css',
            'resources/js/app.js',
        ])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endproduction

    <style>
        :root {
            --brand-primary: #542579;
            --brand-secondary: #7c3faa;
            --brand-soft: #f3edf8;
            --page-bg: #f4f4f5;
            --text-main: #18181b;
            --text-muted: #71717a;
            --border: #e4e4e7;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(
                    circle at top,
                    rgba(124, 63, 170, 0.08),
                    transparent 32%
                ),
                var(--page-bg);
            color: var(--text-main);
            font-family:
                "Segoe UI",
                Tahoma,
                Arial,
                sans-serif;
        }

        button,
        input {
            font-family: inherit;
        }

        .public-page {
            min-height: 100vh;
        }
    </style>

    @stack('styles')
</head>

<body>
    <main class="public-page">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
