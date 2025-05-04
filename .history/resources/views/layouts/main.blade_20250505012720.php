<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-XXXXXXX');</script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Primary Meta Tags -->
    <title>{{ isset($title) ? $title . ' - ' : '' }}Hartono Motor | Bengkel Mobil Terpercaya di Sidoarjo</title>
    <meta name="title"
        content="{{ isset($title) ? $title . ' - ' : '' }}Hartono Motor | Bengkel Mobil Terpercaya di Sidoarjo">
    <meta name="description"
        content="{{ $metaDescription ?? 'Hartono Motor - Bengkel mobil terpercaya di Sidoarjo, Jawa Timur. Melayani berbagai merek dan jenis mobil dengan sparepart lengkap dan mekanik berpengalaman. Booking servis online sekarang!' }}">
    <meta name="keywords"
        content="{{ $metaKeywords ?? 'bengkel mobil sidoarjo, servis mobil, sparepart mobil, tune up mesin, ganti oli, servis ac mobil, bengkel terpercaya, hartono motor' }}">
    <meta name="author" content="Hartono Motor">
    <meta name="robots" content="{{ $metaRobots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title"
        content="{{ isset($title) ? $title . ' - ' : '' }}Hartono Motor | Bengkel Mobil Terpercaya di Sidoarjo">
    <meta property="og:description"
        content="{{ $metaDescription ?? 'Hartono Motor - Bengkel mobil terpercaya di Sidoarjo, Jawa Timur. Melayani berbagai merek dan jenis mobil dengan sparepart lengkap dan mekanik berpengalaman.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/hero-bg.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title"
        content="{{ isset($title) ? $title . ' - ' : '' }}Hartono Motor | Bengkel Mobil Terpercaya di Sidoarjo">
    <meta property="twitter:description"
        content="{{ $metaDescription ?? 'Hartono Motor - Bengkel mobil terpercaya di Sidoarjo, Jawa Timur. Melayani berbagai merek dan jenis mobil dengan sparepart lengkap dan mekanik berpengalaman.' }}">
    <meta property="twitter:image" content="{{ $ogImage ?? asset('images/hero-bg.png') }}">

    <!-- Favicon and App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#e11d48">
    <meta name="msapplication-TileColor" content="#e11d48">
    <meta name="theme-color" content="#ffffff">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    <link rel="preconnect" href="https://www.google-analytics.com" crossorigin>

    <!-- Critical CSS inline -->
    <style>
        /* Critical CSS for above-the-fold content */
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .bg-white {
            background-color: #fff;
        }

        .text-gray-900 {
            color: #111827;
        }

        .flex-grow {
            flex-grow: 1;
        }
    </style>

    <!-- Fonts - load asynchronously -->
    <link rel="preload" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    </noscript>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Non-critical CSS - load asynchronously -->
    <link rel="preload" href="{{ asset('css/animations.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    </noscript>

    <link rel="preload" href="{{ asset('css/testimonial-carousel.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/testimonial-carousel.css') }}">
    </noscript>

    <!-- Preload helper -->
    <script>
        /* Preload polyfill for older browsers */
        !function (n) { "use strict"; n.loadCSS || (n.loadCSS = function () { }); var t = loadCSS.relpreload = {}; if (t.support = function () { var e; try { e = n.document.createElement("link").relList.supports("preload") } catch (t) { e = !1 } return function () { return e } }(), t.bindMediaToggle = function (t) { var e = t.media || "all"; function a() { t.addEventListener ? t.removeEventListener("load", a) : t.attachEvent && t.detachEvent("onload", a), t.setAttribute("onload", null), t.media = e } t.addEventListener ? t.addEventListener("load", a) : t.attachEvent && t.attachEvent("onload", a), setTimeout(function () { t.rel = "stylesheet", t.media = "only x" }), setTimeout(a, 3e3) }, t.poly = function () { if (!t.support()) for (var e = n.document.getElementsByTagName("link"), a = 0; a < e.length; a++) { var o = e[a]; "preload" !== o.rel || "style" !== o.getAttribute("as") || o.getAttribute("data-loadcss") || (o.setAttribute("data-loadcss", !0), t.bindMediaToggle(o)) } }, !t.support()) { t.poly(); var e = n.setInterval(t.poly, 500); n.addEventListener ? n.addEventListener("load", function () { t.poly(), n.clearInterval(e) }) : n.attachEvent && n.attachEvent("onload", function () { t.poly(), n.clearInterval(e) }) } "undefined" != typeof exports ? exports.loadCSS = loadCSS : n.loadCSS = loadCSS }("undefined" != typeof global ? global : this);
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
</head>

<body class="bg-white text-gray-900 min-h-screen flex flex-col">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX" height="0" width="0"
            style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Header -->
    @include('components.header')

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/6282135202581" target="_blank" aria-label="Hubungi kami via WhatsApp"
        class="fixed bottom-6 right-6 bg-green-500 text-white rounded-full p-3 shadow-lg hover:bg-green-600 transition-all z-50 hover-scale">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"
            aria-hidden="true">
            <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
        </svg>
    </a>

    <!-- Animation Scripts -->
    <script src="{{ asset('js/animations.min.js') }}" defer></script>

    <!-- Testimonial Carousel Scripts -->
    <script src="{{ asset('js/testimonial-carousel.min.js') }}" defer></script>

    <!-- Page-specific scripts -->
    @yield('scripts')

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "AutoRepair",
        "name": "Hartono Motor",
        "image": "{{ asset('images/hero-bg.png') }}",
        "url": "{{ url('/') }}",
        "telephone": "+6282135202581",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Samanhudi No 2, Kebonsari",
            "addressLocality": "Sidoarjo",
            "addressRegion": "Jawa Timur",
            "postalCode": "61253",
            "addressCountry": "ID"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": -7.4377883,
            "longitude": 112.6851449
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                "opens": "08:00",
                "closes": "16:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Sunday",
                "opens": "08:00",
                "closes": "14:00"
            }
        ],
        "priceRange": "$$",
        "description": "Bengkel mobil terpercaya di Sidoarjo, Jawa Timur. Melayani berbagai merek dan jenis mobil dengan sparepart lengkap dan mekanik berpengalaman.",
        "sameAs": [
            "https://www.facebook.com/hartonomotorsidoarjo",
            "https://instagram.com/hartonomotorsidoarjo",
            "https://www.tiktok.com/@hartonomotorsidoarjo"
        ],
        "potentialAction": {
            "@type": "ReserveAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ route('booking') }}",
                "inLanguage": "id-ID",
                "actionPlatform": [
                    "http://schema.org/DesktopWebPlatform",
                    "http://schema.org/MobileWebPlatform"
                ]
            },
            "result": {
                "@type": "Reservation",
                "name": "Booking Servis"
            }
        }
    }
    </script>

    @yield('schema')
</body>

</html>