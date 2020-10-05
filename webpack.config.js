var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())
    .enableVersioning(false)

    // uncomment to define the assets of the project
    .addEntry('app', './assets/js/app.js')
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('site', './assets/js/site.js')
    .addEntry('login', './assets/js/login.js')
    // .addStyleEntry('css/app', './assets/css/app.scss')
    // .addStyleEntry('css/admin', './assets/css/admin.scss')
    // .addStyleEntry('css/site', './assets/css/site.scss')
    // .addStyleEntry('css/login', './assets/css/login.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

;

module.exports = Encore.getWebpackConfig();
