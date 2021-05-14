const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    // .js('node_modules/selectize/dist/js/standalone/selectize.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    // .css('node_modules/selectize/dist/css/selectize.default.css', 'public/css')
    ;
