<?php
/**
 * Add Filters to all the things that your readers need to read faster!
 * @since    1.0.0
 * @version  1.0.0
 */

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Rhythms_Filters {

	/**
	 * Constructor
	 * Add teh filters
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function __construct() {

		add_filter( 'the_content', array( $this, 'these_are_easy' ), 999, 1 );
		add_filter( 'the_title', array( $this, 'these_are_easy' ), 999, 1 );
		add_filter( 'widget_title', array( $this, 'these_are_easy' ), 999, 1 );
		add_filter( 'nav_menu_item_title', array( $this, 'these_are_easy' ), 999, 1 );
		add_filter( 'bloginfo', array( $this, 'this_ones_for_bloginfo' ), 999, 2 );


	}

	/**
	 * Filter bloginfo that's read by visitors
	 * @param    string     $content  content of the bloginfo
	 * @param    string     $key      bloginfo key
	 * @return   a better string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function this_ones_for_bloginfo( $content, $key ) {

		switch ( $key ) {
			case 'description':
			case 'name':
				return Rhythms()->do_the_thing( $content );
			break;

			default:
				return $content;
		}

	}

	/**
	 * Basic rhythms functionality
	 * Used for most filters
	 * @param    string     $content  content
	 * @return   string               better content
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function these_are_easy( $content ) {

		return Rhythms()->do_the_thing( $content );

	}

}

return new Rhythms_Filters();
