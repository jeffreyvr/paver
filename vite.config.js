import { defineConfig } from 'vite'

/**
 * The bundles are inlined into <script> tags by PHP rather than loaded as
 * modules, so they are built as IIFEs under fixed file names. IIFE takes a
 * single input, so each entry is built in its own pass:
 *
 *     vite build --mode paver
 *     vite build --mode frame
 */
export default defineConfig(({ mode }) => {
    const entry = mode === 'frame' ? 'frame' : 'paver'

    return {
        // Alpine and tippy branch on process.env.NODE_ENV, which has no
        // meaning in a browser bundle. Webpack substituted it; in library
        // mode Vite leaves it alone, so it has to be defined here or the
        // bundle throws "process is not defined" at runtime.
        define: {
            'process.env.NODE_ENV': JSON.stringify('production'),
        },

        build: {
            outDir: 'assets/js',
            emptyOutDir: false,
            target: 'es2018',
            lib: {
                entry: `resources/js/${entry}.js`,
                formats: ['iife'],
                // The bundles assign their own globals; this name is only
                // required by the IIFE format.
                name: `paverBundle_${entry}`,
                fileName: () => `${entry}.js`,
            },
        },
    }
})
