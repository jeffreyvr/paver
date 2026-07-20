module.exports = {
    plugins: [
        // Inline @imports first, so nesting inside partials is flattened and
        // the prefixer sees every rule.
        require('postcss-import'),
        require('postcss-nested'),
        require('postcss-prefixer')({
            prefix: 'paver__',
            // Third party styles keep their own class names.
            ignore: [/tippy/],
        }),
        require('cssnano')({ preset: 'default' }),
    ],
}
