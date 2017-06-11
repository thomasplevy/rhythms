<?php
/**
 * This is the main Rhytms optimizer class
 * @since    1.0.0
 * @version  1.1.1
 */

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Rhythms_Optimizer {

	/**
	 * Content to be optimized
	 * We're going to hold that we're storing this for debug purposes
	 * @var  string
	 */
	private $stuff_thats_gross = '';

	/**
	 * Content that has been optimized
	 * We like this content, it's shorter, it's missing some things,
	 * it's perfect, isn't it?
	 * @var  string
	 */
	private $we_fixed_it_omg = '';

	/**
	 * We really need to know how much better we've gotten
	 * So we must record the total number of characters in the gross
	 * stuff so we can let you know just how far we've come
	 * @var  integer
	 */
	private $we_started_from_the_bottom = 0;

	/**
	 * Look how far we've come, we mean seriously, look...
	 * @var  integer
	 */
	private $now_we_here = 0;

	/**
	 * We said we need to know
	 * @var  integer
	 */
	private $thats_a_total_savings_of = 0;

	/**
	 * Constructor
	 * @param    string     $content  the content that some silly copy writer dirtied up with slow characters
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function __construct( $content ) {

		if ( ! is_string( $content ) ) {
			return new WP_Error( 'invalid', __( 'Rhythms_Optimizer can only optimize strings!', 'rhythms' ), $content );
		}

		$this->set( 'stuff_thats_gross', $content );
		$this->set( 'we_started_from_the_bottom', strlen( $content ) );

	}

	/**
	 * Getter
	 * Let's be honest, we don't really need this but who can resist a little magic?
	 * @param    string     $key  name of the situation that needs to be gotten
	 * @return   mixed
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Getter
	 * We also don't actually need this, but, well, you know....
	 * @param    string     $key      name of the situation that needs to be gotten
	 * @param    mixed      $default  return this if we can't locate the actual thing we need because, well, this is good enough (we guess)
	 * @return   mixed
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get( $key, $default = null ) {
		return isset( $this->$key ) ? $this->$key : $default;
	}

	/**
	 * Retrieves a list of characters that mess up our ability to read quickly
	 * Takes into consideration the admin's politcal leanings towards the "y" character
	 * Additionally gives us an answer for nerds (we mean developers) who contact support
	 * 		in a panic because they told their clients that "x" (for example) was a slow
	 * 		character (even though that's clearly not in our feature set) and are now
	 * 		getting an an education in both client relationships and WordPress filters
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_all_those_characters_that_slow_us_down() {

		// Everyone agrees
		$chars = array( 'a', 'e', 'i', 'o', 'u' );

		// Does "y" slow us down? It's arguable...
		if ( 'yes' === get_option( 'rhythms_sometimes_y', 'no' ) ) {
			$chars[] = 'y';
		}

		// I don't want to figh so please customize these chars!
		return apply_filters( 'rhythms_get_all_those_characters_that_slow_us_down', $chars );

	}

	/**
	 * Get the length of that disgusting starting content
	 * @return   int
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_initial_length() {
		return $this->get( 'we_started_from_the_bottom' );
	}

	/**
	 * Get that sweet sweet optimized content, oh boy...
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_optimized_content() {
		return $this->get( 'we_fixed_it_omg' );
	}

	/**
	 * Get the length of our beautiful new optimized content
	 * @return   int
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_optimized_length() {
		return $this->get( 'now_we_here' );
	}

	/**
	 * Get the total number of ugly characters we've saved you from having to read, you're welcome btdubs
	 * @return   int
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function get_savings() {
		return $this->get( 'thats_a_total_savings_of' );
	}

	/**
	 * Check if a string is a closing HTML tag
	 * @param    string     $string  a string
	 * @return   boolean
	 * @since    1.1.1
	 * @version  1.1.1
	 */
	private function is_closing_tag( $string ) {
		return ( 0 === strpos( $string, '</' ) );
	}

	/**
	 * Check if a string is an opening html tag
	 * @param    string     $string  a string
	 * @return   boolean
	 * @since    1.1.1
	 * @version  1.1.1
	 */
	private function is_this_html( $string ) {
		return ( 0 === strpos( $string, '<' ) );
	}

	/**
	 * Oh the magic happens here
	 * @return   instance of the Rhythms_Optimizer because we're really into chains over here
	 * @since    1.0.0
	 * @version  1.1.1
	 */
	public function optimize() {

		$content = $this->get( 'stuff_thats_gross' );

		$split_it_up = preg_split('/(<.*?>)|\s/', $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		foreach ( $split_it_up as $i => &$part ) {
			if ( ! $this->is_this_html( $part ) ) {
				$part = str_replace( $this->get_all_those_characters_that_slow_us_down(), '', $part, $this->thats_a_total_savings_of );

				$next_part_i = $i + 1;
				if ( isset( $split_it_up[ $next_part_i ] ) && ! $this->is_this_html( $split_it_up[ $next_part_i ] ) ) {
					$part .= ' ';
				}
			} elseif ( $this->is_this_html( $part ) ) {
				$part .= ' ';
			}
		}
		$content = implode( '', $split_it_up );
		$this->set( 'we_fixed_it_omg', $content );
		$this->set( 'now_we_here', strlen( $content ) );
		return $this;

	}

	/**
	 * Setter
	 * Again, we don't need but if you're gonna get ya might as well set
	 * my mom used to say that it didn't apply when I was a kid and it
	 * surely doesn't apply here either...
	 * @param    string     $key  name of the thing that needs some setting
	 * @param    mixed      $val  the value with which to set
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function set( $key, $val ) {
		$this->$key = $val;
	}

}
