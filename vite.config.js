import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
                "resources/js/custom-map.js",
                // "resources/js/maps-autocomplete-fix.js",
            ],
            refresh: true,
        }),
    ],
});
