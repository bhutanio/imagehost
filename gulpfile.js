var fs = require('fs');
var gulp = require('gulp');
var elixir = require('laravel-elixir');
var gulpReplace = require('gulp-replace');
var gulpConcat = require('gulp-concat');

gulp.task('mergeFineUploaderFiles', function () {
    gulp.src([
        './bower_components/fine-uploader/_build/fine-uploader.css',
        './bower_components/fine-uploader/_build/fine-uploader-new.css',
        './bower_components/fine-uploader/_build/fine-uploader-gallery.css'
    ])
        .pipe(gulpReplace('continue.gif', '../images/fucontinue.gif'))
        .pipe(gulpReplace('edit.gif', '../images/fuedit.gif'))
        .pipe(gulpReplace('loading.gif', '../images/fuloading.gif'))
        .pipe(gulpReplace('pause.gif', '../images/fupause.gif'))
        .pipe(gulpReplace('processing.gif', '../images/fuprocessing.gif'))
        .pipe(gulpReplace('retry.gif', '../images/furetry.gif'))
        .pipe(gulpReplace('trash.gif', '../images/futrash.gif'))
        .pipe(gulpConcat("fine-uploader.css"))
        .pipe(gulp.dest('resources/assets/vendor/css/'));
});

gulp.task('writeVersionFile', function (cb) {
    var version = Math.floor(Date.now() / 1000);
    fs.writeFile('version.txt', version, cb);
});

gulp.task('findAndReplace', function () {
    gulp.src(['./public/css/style.css'])
        .pipe(gulpReplace('\@import url\(\"\"\)\;', ''))
        .pipe(gulp.dest('./public/css/'));
});

elixir(function (mix) {

    // fine-uploader
    mix.copy('./bower_components/fine-uploader/_build/continue.gif', 'resources/assets/images/fucontinue.gif');
    mix.copy('./bower_components/fine-uploader/_build/edit.gif', 'resources/assets/images/fuedit.gif');
    mix.copy('./bower_components/fine-uploader/_build/loading.gif', 'resources/assets/images/fuloading.gif');
    mix.copy('./bower_components/fine-uploader/_build/pause.gif', 'resources/assets/images/fupause.gif');
    mix.copy('./bower_components/fine-uploader/_build/processing.gif', 'resources/assets/images/fuprocessing.gif');
    mix.copy('./bower_components/fine-uploader/_build/retry.gif', 'resources/assets/images/furetry.gif');
    mix.copy('./bower_components/fine-uploader/_build/trash.gif', 'resources/assets/images/futrash.gif');
    mix.copy('./bower_components/fine-uploader/_build/placeholders/not_available-generic.png', 'resources/assets/images/funot_available-generic.png');
    mix.copy('./bower_components/fine-uploader/_build/placeholders/waiting-generic.png', 'resources/assets/images/fuwaiting-generic.png');
    mix.task('mergeFineUploaderFiles');
    mix.copy('./resources/assets/vendor/css/fine-uploader.css', './resources/assets/sass/fine-uploader.scss');

    mix.sass([
        'app.scss'
    ], './public/css/style.css');

    mix.combine([
        './bower_components/bootstrap-sass/assets/javascripts/bootstrap.min.js',
        './bower_components/fine-uploader/_build/jquery.fine-uploader.min.js',
        './resources/assets/js/**/*.js'
    ], './public/js/app.js');

    mix.combine([
        './bower_components/html5shiv/dist/html5shiv.min.js',
        './bower_components/Respond/dest/respond.min.js'
    ], './public/js/html5shiv.respond.min.js');

    mix.copy('./bower_components/bootstrap-sass/assets/fonts/bootstrap', 'public/fonts/');
    mix.copy('./resources/assets/images/', './public/images');

    mix.task('findAndReplace');

    mix.task('writeVersionFile');
});
