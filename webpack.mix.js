let mix = require('laravel-mix');

mix.js('resources/js/paver.js', 'assets/js');
mix.js('resources/js/frame.js', 'assets/js');

mix.postCss('resources/css/paver.css', 'assets/css');
mix.postCss('resources/css/frame.css', 'assets/css');

mix.options({
    postCss: [
        require('postcss-nested'),
        require("postcss-import"),
        require('postcss-prefixer')({
            prefix: 'paver__',
            ignore: [/tippy/]
        })
    ]
});

// mix.sourceMaps();
