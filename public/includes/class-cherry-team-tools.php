<?php
/**
 * Cherry Team Tools
 *
 * @package   Cherry_Team_Members
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Register plugin-related tools.
 *
 * @since 1.0.0
 */
class Cherry_Team_Members_Tools {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Returns default page template
	 *
	 * @param  string $page Page to get template for.
	 * @return string
	 */
	public function get_template( $page = 'listing' ) {

		$default = array(
			'listing' => 'default.tmpl',
			'single'  => 'single.tmpl',
		);

		$template  = cherry_team_members()->get_option( $page . '-template' );
		$templates = cherry_team_members_templater()->get_templates_list();

		if ( ! $template || ! isset( $templates[ $template ] ) ) {
			return $default[ $page ];
		}

		return $templates[ $template ];

	}

	/**
	 * Returns column classes for team listing page
	 *
	 * @return array
	 */
	public function get_cols() {

		$mobile = array(
			1 => array( 1, 1 ),
			2 => array( 2, 1 ),
			3 => array( 2, 1 ),
			4 => array( 2, 1 ),
			5 => array( 2, 1 ),
			6 => array( 3, 1 ),
		);

		$cols = cherry_team_members()->get_option( 'archive-columns', 3 );
		$cols = intval( $cols );
		$cols = ! ( 0 >= $cols || 4 < $cols ) ? $cols : 3;

		return array(
			'md' => $cols,
			'sm' => $mobile[ $cols ][0],
			'xs' => $mobile[ $cols ][1],
		);

	}

	/**
	 * Get templates list
	 *
	 * @return array
	 */
	public function get_templates() {

		$templates = cherry_team_members_templater()->get_templates_list();

		if ( ! is_array( $templates ) ) {
			return array();
		}

		$options = array_keys( $templates );

		return array_combine( $options, array_map( 'ucwords', $options ) );
	}

	/**
	 * Get pages list
	 *
	 * @return array
	 */
	public function get_pages() {

		$pages      = get_pages();
		$pages_list = array( esc_html__( 'Select page...', 'cherry-team' ) );

		foreach ( $pages as $page ) {
			$pages_list[ $page->ID ] = $page->post_title;
		}

		return $pages_list;
	}

	/**
	 * Returns groups list
	 *
	 * @return array
	 */
	public function get_groups() {

		global $wp_version;

		if ( version_compare( $wp_version, '4.5.0', '>=' ) ) {
			$groups = get_terms( 'group', array(
				'hide_empty' => false,
			) );
		} else {
			$groups = get_terms( array(
				'taxonomy'   => 'group',
				'hide_empty' => false,
			) );
		}

		if ( ! $groups ) {
			return array();
		}

		$groups = wp_list_pluck( $groups, 'name', 'term_id' );

		return $groups;
	}

	/**
	 * Returns available image sizes list.
	 *
	 * @return array
	 */
	public function get_image_sizes() {

		global $_wp_additional_image_sizes;

		$sizes  = get_intermediate_image_sizes();
		$result = array();

		foreach ( $sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
			} else {
				$result[ $size ] = sprintf(
					'%1$s (%2$sx%3$s)',
					ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					$_wp_additional_image_sizes[ $size ]['width'],
					$_wp_additional_image_sizes[ $size ]['height']
				);
			}
		}

		return $result;

	}

	/**
	 * Prints current page title.
	 *
	 * @return void
	 */
	public function page_title( $format = '%s' ) {

		$object = get_queried_object();

		if ( isset( $object->post_title ) ) {
			printf( $format, $object->post_title );
		} elseif ( isset( $object->name ) ) {
			printf( $format, $object->name );
		}

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

/**
 * Returns instance of Tools class,
 *
 * @return Cherry_Team_Members_Tools
 */
function cherry_team_members_tools() {
	return Cherry_Team_Members_Tools::get_instance();
}
