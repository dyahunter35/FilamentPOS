import preset from "./vendor/filament/support/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./vendor/jaocero/radio-deck/resources/views/**/*.blade.php",
        "./vendor/awcodes/mason/resources/**/*.blade.php",
        "./vendor/awcodes/mason/resources/**/*.blade.php",
        "./vendor/dotswan/filament-grapesjs-v3/resources/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#352F44", // اللون الافتراضي يظل هو نفسه لضمان التوافق
                    50: "#F7F6F8",
                    100: "#EEEDEE",
                    200: "#D5D2D9",
                    300: "#BBB6C3",
                    400: "#9E97A7",
                    500: "#352F44", // اللون الأساسي عند المستوى 500
                    600: "#2D2839",
                    700: "#26222F",
                    800: "#1E1B26",
                    900: "#16151C",
                    950: "#0F0E13",
                },
                secondary: {
                    DEFAULT: "#EFC52E",
                    50: "#FEFBF3",
                    100: "#FDF7E6",
                    200: "#FAECBF",
                    300: "#F7E197",
                    400: "#F3D550",
                    500: "#EFC52E", // اللون الثانوي عند المستوى 500
                    600: "#D4AD1A",
                    700: "#A68515",
                    800: "#785D10",
                    900: "#4B3A0A",
                    950: "#302506",
                },
                "brand-primary": "#EFC52E",
                "brand-primary-gray": "#B4B4B8",
                "brand-secondary-dark": "#352F44",
                "brand-secondary-light": "#817F8A",
            },
        },
    },
    plugins: [require("tailwindcss-rtl")],
};
