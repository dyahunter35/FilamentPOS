import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Site/**/*.php',
        './resources/views/filament/site/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
