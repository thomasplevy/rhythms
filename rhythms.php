<?php
/**
 * Plugin Name: Rhythms
 * Plugin URI: https://github.com/thomasplevy/rhythms
 * Description: Rhythms, the only WordPress plugin that automatically optimizes your website with lesser-known speed-reading hacks so that your readers can read your content faster than anywhere else on the web.
 * Version: 1.1.2
 * Author: Thomas Patrick Levy
 * Author URI: https://github.com/thomasplevy
 * Text Domain: rhythms
 * Domain Path: /i18n
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.7.2
 * Tested up to: 4.7.2
 */

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

// prevent conflicts
if ( ! class_exists( 'Rhythms' ) ) :
/**
 * Main Rhythms Class
 */
final class Rhythms {

	/**
	 * Version number
	 * @var  string
	 */
	public $version = '1.1.2';

	/**
	 * Our sick nerdy facts
	 * @var  array
	 */
	public $nerd_facts = array(
		'invoked' => 0,
		'start' => 0,
		'finish' => 0,
		'savings' => 0,
		'optimization_score' => 0,
	);

	/**
	 * Main instance of Rhythms
	 * @var  null
	 */
	protected static $_instance = null;

	/**
	 * Main Instance of Rhythms
	 * @see Rhythms()
	 * @return Rhythms
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function __construct() {

		$this->constants();

		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );
		// add_action( 'admin_init', array( $this, 'admin_init' ), 0 );

		add_action( 'admin_bar_menu', array( $this, 'output_facts' ), 999 );

	}

	/**
	 * This has to be a GPL compliant plugin so please don't share our super secret optimization
	 * algorithm with the world, please and thanks
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function calculate_optimization_score() {
		$start = $this->nerd_facts['start'];
		$finish = $this->nerd_facts['finish'];

		if ( $start ) {
			$this->nerd_facts['optimization_score'] = round( 4 - ( ( $finish / $start ) * 4 ), 3 );
		}

	}

	/**
	 * Define some constants
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function constants() {

		if ( ! defined( 'RHYTHMS_DIR' ) ) {
			define( 'RHYTHMS_DIR', dirname( __FILE__ ) );
		}

	}

	/**
	 * Do the Rhythms thing on a string
	 * @param    string     $content  some content
	 * @return   a better string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function do_the_thing( $content ) {
		$optimizer = new Rhythms_Optimizer( $content );
		$optimizer->optimize();
		$this->nerd_facts['invoked']++;
		$this->nerd_facts['start'] += $optimizer->get_initial_length();
		$this->nerd_facts['finish'] += $optimizer->get_optimized_length();
		$this->nerd_facts['savings'] += $optimizer->get_savings();
		return $optimizer->get_optimized_content();
	}

	/**
	 * Include Required files
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function includes() {

		require_once RHYTHMS_DIR . '/inc/class-rhythms-optimizer.php';

		if ( is_admin() ) {
			require_once RHYTHMS_DIR . '/inc/class-rhythms-admin-settings.php';
		}

	}

	/**
	 * Initialize
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function init() {
		$this->localize();
		require_once RHYTHMS_DIR . '/inc/class-rhythms-filters.php';
	}

	/**
	 * Load Localization files
	 *
	 * The first loaded file takes priority
	 *
	 * Files can be found in the following order:
	 * 		WP_LANG_DIR/rhythms/rhythms-LOCALE.mo
	 * 		WP_LANG_DIR/plugins/rhythms-LOCALE.mo
	 *
	 * @return void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function localize() {

		// load locale
		$locale = apply_filters( 'plugin_locale', get_locale(), 'rhythms' );

		// load a rhythms specific locale file if one exists
		load_textdomain( 'rhythms', WP_LANG_DIR . '/rhythms/rhythms-' . $locale . '.mo' );

		// load localization files
		load_plugin_textdomain( 'rhythms', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );

	}

	/**
	 * Output our facts on the admin menu bar so you admins know how good you now have it
	 * @param    obj     $wp_admin_bar  instance of the wp_admin_bar obj
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function output_facts( $wp_admin_bar ) {

		if ( is_admin() ) { return; }

		$this->calculate_optimization_score();

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts',
			'title' => '<span class="dashicons dashicons-format-audio" style="font-family:dashicons;"></span> ' . __( 'Rhythms Facts', 'rhythms' ),
		) );

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts-score',
			'parent' => 'rhythms-nerd-facts',
			'title' => sprintf( __( 'Optimization Score: %s', 'rhythms' ), $this->nerd_facts['optimization_score'] ),
		) );

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts-start',
			'parent' => 'rhythms-nerd-facts',
			'title' => sprintf( __( 'Initial Characters: %s', 'rhythms' ), number_format_i18n( $this->nerd_facts['start'] ) ),
		) );

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts-finish',
			'parent' => 'rhythms-nerd-facts',
			'title' => sprintf( __( 'Final Characters: %s', 'rhythms' ), number_format_i18n( $this->nerd_facts['finish'] ) ),
		) );

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts-saved',
			'parent' => 'rhythms-nerd-facts',
			'title' => sprintf( __( 'Characters Saved: %s', 'rhythms' ), number_format_i18n( $this->nerd_facts['savings'] ) ),
		) );

		$wp_admin_bar->add_node( array(
			'id' => 'rhythms-nerd-facts-invoked',
			'parent' => 'rhythms-nerd-facts',
			'title' => sprintf( __( 'Invocations: %s', 'rhythms' ), number_format_i18n( $this->nerd_facts['invoked'] ) ),
		) );

	}

}
endif;

/**
 * Get the main instance of the Rhythms class
 * @return   obj
 * @since    1.0.0
 * @version  1.0.0
 */
function Rhythms() {
	return Rhythms::instance();
}

return Rhythms();
