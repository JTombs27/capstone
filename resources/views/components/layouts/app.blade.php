<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ config('app.name') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
            
        </style>

        @filamentStyles
        @vite('resources/css/app.css')
    </head>

    <body class="antialiased transition-all duration-300" id="home" >
    @livewire('partials.navbar')
        {{ $slot }}
    @livewire('partials.footer')
        @filamentScripts
        @vite('resources/js/app.js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <x-livewire-alert::scripts />
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sections = document.querySelectorAll("section");
            const navLinks = document.querySelectorAll(".nav-link");

            function changeActiveLink() {
            let fromTop = window.scrollY + 150; // Adjust offset if needed

            sections.forEach((section) => {
                if (
                section.offsetTop <= fromTop &&
                section.offsetTop + section.offsetHeight > fromTop
                ) {
                navLinks.forEach((link) => link.classList.remove("active"));
                document
                    .querySelector(`a[href="#${section.id}"]`)
                    .classList.add("active");
                }
            });
            }

            window.addEventListener("scroll", changeActiveLink);
        });
        </script>

    </body>
</html>
