<?php
/**
 * Cherry Team
 *
 * @package   Cherry_Team_Members
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Class for register post types.
 *
 * @since 1.0.0
 */
class Cherry_Team_Members_Init {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Post type name
	 *
	 * @var string
	 */
	public static $name = 'team';

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Adds the team post type.
		add_action( 'init', array( __CLASS__, 'register_post' ) );
		add_action( 'init', array( __CLASS__, 'register_tax' ) );

	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register_post() {

		$labels = array(
			'name'               => __( 'Team', 'cherry-team' ),
			'singular_name'      => __( 'Team', 'cherry-team' ),
			'add_new'            => __( 'Add New', 'cherry-team' ),
			'add_new_item'       => __( 'Add New Person', 'cherry-team' ),
			'edit_item'          => __( 'Edit Person', 'cherry-team' ),
			'new_item'           => __( 'New Person', 'cherry-team' ),
			'view_item'          => __( 'View Person', 'cherry-team' ),
			'search_items'       => __( 'Search Persons', 'cherry-team' ),
			'not_found'          => __( 'No persons found', 'cherry-team' ),
			'not_found_in_trash' => __( 'No persons found in trash', 'cherry-team' ),
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail',
			'revisions',
			'page-attributes',
			'cherry-grid-type',
			'cherry-layouts',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'hierarchical'    => false, // Hierarchical causes memory issues - WP loads all records!
			'rewrite'         => array(
				'slug'       => 'team',
				'with_front' => false,
				'feeds'      => true,
			),
			'query_var'       => true,
			'menu_position'   => null,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-businessman' : '',
			'can_export'      => true,
			'has_archive'     => true,
		);

		$args = apply_filters( 'cherry_team_post_type_args', $args );

		register_post_type( self::$name, $args );

	}

	/**
	 * Register taxonomy for custom post type.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function register_tax() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => __( 'Team Groups', 'cherry-team' ),
			'singular_name'              => __( 'Edit Group', 'cherry-team' ),
			'search_items'               => __( 'Search Groups', 'cherry-team' ),
			'popular_items'              => __( 'Popular Groups', 'cherry-team' ),
			'all_items'                  => __( 'All Groups', 'cherry-team' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Group', 'cherry-team' ),
			'update_item'                => __( 'Update Group', 'cherry-team' ),
			'add_new_item'               => __( 'Add New Group', 'cherry-team' ),
			'new_item_name'              => __( 'New Group Name', 'cherry-team' ),
			'separate_items_with_commas' => __( 'Separate groups with commas', 'cherry-team' ),
			'add_or_remove_items'        => __( 'Add or remove groups', 'cherry-team' ),
			'choose_from_most_used'      => __( 'Choose from the most used groups', 'cherry-team' ),
			'not_found'                  => __( 'No groups found.', 'cherry-team' ),
			'menu_name'                  => __( 'Groups', 'cherry-team' ),
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'group' ),
		);

		register_taxonomy( 'group', self::$name, $args );

	}

	/**
	 * Returns team post type name
	 *
	 * @return string
	 */
	public function name() {
		return self::$name;
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
 * Returns instance of init class,
 *
 * @return Cherry_Team_Members_Init
 */
function cherry_team_members_init() {
	return Cherry_Team_Members_Init::get_instance();
}

cherry_team_members_init();
