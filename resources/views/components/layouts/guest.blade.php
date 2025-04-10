<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>{{ $pageTitle ?? 'HiRe' }} • {{ config('app.name', 'HiRe') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/genuine-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/rt-plugins.css') }}">
    <link href="https://unpkg.com/aos@2.3.0/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- START : Theme Config js-->
    <script src="{{ asset('js/settings.js') }}" sync></script>
    <!-- END : Theme Config js-->
    @livewireStyles
    <style>
        .guest-layout-wrapper {
            min-height: 100vh;
            background-color: #f1f5f9;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
        }
        
        .guest-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
        }

        .guest-header img {
            height: 60px;
            margin-bottom: 1rem;
        }

        .guest-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            flex: 1;
        }

        .guest-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            overflow: hidden;
        }

        .guest-card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem;
        }

        .guest-card-body {
            padding: 1.5rem;
        }

        .guest-footer {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        
        /* Override card styles */
        .card {
            margin-bottom: 0;
            box-shadow: none;
            border-radius: 0;
        }
        
        .card-header {
            padding: 1.5rem;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
    </style>
</head>

<body class="font-inter skin-default">
    <div class="guest-layout-wrapper">
        <div id="simpleToast"></div>

        <div class="guest-header">
            <img src="{{ asset('images/logo/logo.svg') }}" alt="{{ config('app.name', 'HiRe') }} Logo">
            <h1 class="text-2xl font-bold text-slate-900">{{ $title ?? 'Welcome' }}</h1>
            @if(isset($description))
                <p class="text-slate-500 mt-2">{{ $description }}</p>
            @endif
        </div>

        <div class="guest-container">
            <div class="guest-card">
                {{ $slot ?? '' }}
            </div>
        </div>

        <div class="guest-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'HiRe') }}. All rights reserved.</p>
        </div>
    </div>

    <!-- scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/rt-plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts

    <script>
        // Fix for Livewire v3 dispatched events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toastalert', (data) => {
                console.log('Toast event received:', data);
                var x = document.getElementById("simpleToast");

                const {
                    message,
                    type
                } = data[0];

                let icon = "";

                if (type === "success") {
                    x.style.backgroundColor = "#50C793";
                    icon = '<iconify-icon icon="material-symbols:check"></iconify-icon>';
                } else if (type === "failed") {
                    x.style.backgroundColor = "#F1595C";
                    icon = '<iconify-icon icon="ph:warning"></iconify-icon>';
                } else if (type === "info") {
                    x.style.backgroundColor = "black";
                    icon = '<iconify-icon icon="material-symbols:info-outline"></iconify-icon>';
                } else {
                    x.style.backgroundColor = "gray";
                }

                x.innerHTML = icon + "&nbsp;" + (message || " No message provided");
                x.className = "show";

                setTimeout(function() {
                    x.className = x.className.replace("show", "");
                }, 3000);
            });
        });
    </script>
</body>

</html>
