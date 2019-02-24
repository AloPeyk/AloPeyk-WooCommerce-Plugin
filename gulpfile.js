var gulp     = require('gulp');
var uglify   = require('gulp-uglify');
var cleanCss = require('gulp-clean-css');
var rename   = require("gulp-rename");
var scripts_input = './';

// Minify - styles
gulp.task('minify:styles', function() {
    return gulp.src([
            '!**/node_modules{,/**}',
            scripts_input+'/**/*.css',
            '!'+scripts_input+'/**/*.min.css'
        ])
        .pipe(cleanCss(null))
        .pipe(rename(function(path) {
            path.extname = ".min.css";
        }))
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});

// Minify - Scripts
gulp.task('minify:scripts', function() {
    return gulp.src([
            '!**/node_modules{,/**}',
            '!{./,**/}gulpfile.js',
            scripts_input+'/**/*.js',
            '!'+scripts_input+'/**/*.min.js'
        ])
        .pipe(uglify(null,{
            mangle: true,
            outSourceMap: true
        }))
        .pipe(rename(function(path) {
            path.extname = ".min.js";
        }))
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});

gulp.task('default', ['minify:styles', 'minify:scripts']);