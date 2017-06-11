<?php
/**
 * Add Filters to all the things that your readers need to read faster!
 * @since    1.0.0
 * @version  1.1.2
 */

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Rhythms_Filters {

	/**
	 * Constructor
	 * Add teh filters
	 * @since    1.0.0
	 * @version  1.1.2
	 */
	public function __construct() {

		add_filter( 'rhythms_filters', array( $this, 'get_integration_filters' ), 10, 1 );

		$filters = apply_filters( 'rhythms_filters', array(
			'bloginfo' => 'this_ones_for_bloginfo',
			'get_comment_text' => 'these_are_easy',
			'get_term' => 'this_ones_for_the_terms',
			'nav_menu_item_title' => 'these_are_easy',
			'the_author' => 'these_are_easy',
			'the_content' => 'these_are_easy',
			'the_title' => 'these_are_easy',
			'widget_title' => 'these_are_easy',
		) );

		foreach ( $filters as $hook => $func ) {

			add_filter( $hook, array( $this, $func ), 999, 2 );

		}


	}

	/**
	 * Get an array of all integration filters
	 * @return   array
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	public function get_integration_filters( $filters ) {

		$integrations = array(
			'yoast' => 'wpseo_init',
		);

		foreach ( $integrations as $id => $check ) {

			if ( 'yes' === get_option( sprintf( 'rhythms_do_it_to_%s', $id  ), 'no' ) && function_exists( 'wpseo_init' ) ) {

				$func = sprintf( 'get_%s_integration_filters', $id );

				if ( method_exists( $this, $func ) ) {

					$filters = array_merge( $filters, $this->$func() );

				}

			}

		}

		return $filters;

	}

	/**
	 * Get integration filters for WP SEO by Yoast
	 * @return   array
	 * @since    1.1.0
	 * @version  1.1.0
	 */
	private function get_yoast_integration_filters() {
		return apply_filters( 'rhythms_yoast_integration_filters', array(
			'wpseo_title' => 'these_are_easy',
			'wpseo_metadesc' => 'these_are_easy',
			'wp_seo_get_bc_title' => 'these_are_easy',
			'wp_seo_get_bc_ancestors' => 'these_are_easy',
			'wpseo_metakeywords' => 'these_are_easy', // why?
			'wpseo_opengraph_title' => 'these_are_easy',
			'wpseo_opengraph_site_name' => 'these_are_easy',
		) );
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
	 * Filter term name and description
	 * @param    array     $term  WP_Term object
	 * @return   array
	 * @since    1.1.2
	 * @version  1.1.2
	 */
	public function this_ones_for_the_terms( $term ) {
		$term->name = Rhythms()->do_the_thing( $term->name );
		$term->description = Rhythms()->do_the_thing( $term->description );
		return $term;
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
