// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var livereload = require('gulp-livereload');
var minifyCss = require('gulp-cssnano');
var uglify = require('gulp-uglify');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('compress-extension', function (cb) {
    return gulp.src(['assets/js/extension*.js'])
        .pipe(concat('extension.js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js/minify/'))
        .pipe(livereload());
});

gulp.task('compress-form-builder', function (cb) {
    return gulp.src(['assets/js/form-builder*.js'])
        .pipe(concat('form-builder.js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js/minify/'))
        .pipe(livereload());
});

gulp.task('compress-woocommerce-sales-funnels', function (cb) {
    return gulp.src(['assets/js/woocommerce-sales-funnels*.js'])
        .pipe(concat('woocommerce-sales-funnels.js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js/minify/'))
        .pipe(livereload());
});

gulp.task('compress-widget', function (cb) {
    return gulp.src(['assets/js/widget*.js'])
        .pipe(concat('widget.js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js/minify/'))
        .pipe(livereload());
});

gulp.task('compress-pafe', function (cb) {
    return gulp.src(['assets/js/pafe*.js'])
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/js/minify/'))
        .pipe(livereload());
});

// Compile Our Sass
gulp.task('sass-extension', function () {
    return gulp.src('assets/css/extension*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(concat('extension.css'))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

// Compile Our Sass
gulp.task('sass-extension', function () {
    return gulp.src('assets/css/extension*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(concat('extension.css'))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

gulp.task('sass-form-builder', function () {
    return gulp.src('assets/css/form-builder*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(concat('form-builder.css'))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

gulp.task('sass-woocommerce-sales-funnels', function () {
    return gulp.src('assets/css/woocommerce-sales-funnels*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(concat('woocommerce-sales-funnels.css'))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

gulp.task('sass-widget', function () {
    return gulp.src('assets/css/widget*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(concat('widget.css'))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

gulp.task('sass-pafe', function () {
    return gulp.src('assets/css/pafe*.css')
        .pipe(sass().on( "error", sass.logError ))
        .pipe(autoprefixer({
            browsers: [
                'last 3 versions',
                'iOS >= 8',
                'Safari >= 8',
                'ie >= 11',
            ]
        }))
        .pipe(minifyCss({zindex: false}))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/minify/'))
        .pipe(livereload());
});

// Watch Files For Changes
gulp.task('watch', function () {
    livereload.listen();
    gulp.watch(['assets/css/extension*.css'], ['sass-extension']);
    gulp.watch(['assets/css/form-builder*.css'], ['sass-form-builder']);
    gulp.watch(['assets/css/woocommerce-sales-funnels*.css'], ['sass-woocommerce-sales-funnels']);
    gulp.watch(['assets/css/widget*.css'], ['sass-widget']);
    gulp.watch(['assets/css/pafe*.css'], ['sass-pafe']);

    gulp.watch(['assets/js/extension*.js'], ['compress-extension']);
    gulp.watch(['assets/js/form-builder*.js'], ['compress-form-builder']);
    gulp.watch(['assets/js/woocommerce-sales-funnels*.js'], ['compress-woocommerce-sales-funnels']);
    gulp.watch(['assets/js/widget*.js'], ['compress-widget']);
    gulp.watch(['assets/js/pafe*.js'], ['compress-pafe']);
});

// Default Task
gulp.task('default', ['watch']);