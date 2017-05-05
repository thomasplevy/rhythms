/**
 * -----------------------------------------------------------
 * pot
 * -----------------------------------------------------------
 *
 * Generate a new .pot file
 *
 */

var   gulp  = require( 'gulp' )
	, sort  = require( 'gulp-sort' )
	, wpPot = require( 'gulp-wp-pot' )
;

gulp.task( 'pot', function() {

	gulp.src( [ '*.php', './**/*.php' ] )

		.pipe( sort() )

		.pipe( wpPot( {
			domain: 'rhythms',
			package: 'rhythms',
			bugReport: 'https://github.com/thomasplevy/rhythms/issues',
			lastTranslator: 'Thomas Patrick Levy <thomas@gocodebox.com>',
			team: 'Thomas Patrick Levy <thomas@gocodebox.com>',
		} ) )

		.pipe( gulp.dest( 'i18n/rhythms.pot' ) )

} );
