import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'custom-color': {
                    'sidebar': '#0e7490',
                    'topnav': '#0e7490',
                    'banner': '#0e7490',
                    'sidebar-dark': '#083344',
                    'topnav-dark': '#083344',
                    'banner-dark': '#083344',
                    1: '#a16207',
                    2: '#854d0e',
                    3: '#facc15',
                },
                'farm-marker': "#083344",
                'marker-danger': "#ed071c"

            },
        },
    },
}
