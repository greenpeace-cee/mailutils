/**
 * @file
 * Contains gulp tasks for the application
 *
 * Available Tasks
 * default
 * sass:sync
 * sass
 * watch
 */

'use strict';

var gulp = require('gulp');

var sassTask = require('./gulp-tasks/sass.js');
var sassSyncTask = require('./gulp-tasks/sass-sync.js');
var watchTask = require('./gulp-tasks/watch.js');

/**
 * Updates and sync the scssRoot paths
 */
gulp.task('sass:sync', sassSyncTask);

/**
 * Compiles mailutils.scss under scss folder to CSS counterpart
 */
gulp.task('sass', gulp.series('sass:sync', sassTask));

/**
 * Watches for scss and js file changes and run sass task
 */
gulp.task('watch', watchTask);
gulp.task('default', gulp.series('sass'));
