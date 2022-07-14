var gulp = require('gulp');
var csso = require('gulp-csso');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
// var sourcemaps = require('gulp-sourcemaps');

function styles() {
	return gulp.src('./assets/styles/scss/index.scss')
		.pipe(sass().on('error', sass.logError))
		// .pipe(sourcemaps.init())
		.pipe(csso({
			restructure: true,
			// sourceMap: true,
			debug: true
		}))
		// .pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('./assets/styles/css'));
};

function adminStyles() {
	return gulp.src('./assets/styles/scss/admin-index.scss')
		.pipe(sass().on('error', sass.logError))
		// .pipe(sourcemaps.init())
		.pipe(csso({
			restructure: false,
			// sourceMap: true,
			// debug: true
		}))
		// .pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('./assets/styles/css'));
};

function scripts() {
	return gulp.src([
		'./assets/js/common.js',
		'./assets/js/functions.js',
		'./assets/js/search.js'
	])
		.pipe(concat('common-scripts.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./assets/js/'));
};

function tracker() {
	return gulp.src(['./assets/js/t.js'])
		.pipe(concat('t.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./assets/js/'));
};

function watch() {
	gulp.watch('./assets/styles/scss/**/*.scss', gulp.parallel(styles, adminStyles));
	gulp.watch([
		'./assets/js/**/*.js',
		'!./assets/js/t.min.js',
		'!./assets/js/common-scripts.min.js'
	], gulp.parallel(scripts, tracker));
};

const build = gulp.parallel(styles, adminStyles, scripts, tracker, watch);

// gulp.task(build);
gulp.task('default', build);
