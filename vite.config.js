import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.jsx"],
            refresh: true,
        }),
        react(),
    ],
    optimizeDeps: {
        include: ["wowjs", "react-slider"],
    },
    build: {
        rollupOptions: {
            onwarn(warning, warn) {
                // Suppress wowjs and react-slider warnings
                if (warning.code === "MISSING_EXPORT") return;
                warn(warning);
            },
        },
    },
});
