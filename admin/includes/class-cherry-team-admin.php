<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package   Cherry_Team_Members_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class contains admin-related functionality
 */
class Cherry_Team_Members_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		// Register admin assets
		add_action( 'admin_init', array( $this, 'register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editor_fix' ), 999 );
	}

	/**
	 * Register admin-related scripts and styles
	 *
	 * @return void
	 */
	public function register_assets() {

		wp_register_style(
			'cherry-team-admin-style',
			cherry_team_members()->plugin_url( 'admin/assets/css/admin-style.css' ),
			array(),
			cherry_team_members()->version()
		);

		wp_register_script(
			'cherry-serialize-object',
			cherry_team_members()->plugin_url( 'admin/assets/js/serialize-object.js' ),
			array( 'jquery' ),
			cherry_team_members()->version(),
			true
		);

		wp_register_script(
			'cherry-team-admin-scripts',
			cherry_team_members()->plugin_url( 'admin/assets/js/cherry-team-admin-scripts.js' ),
			array( 'jquery', 'cherry-js-core' ),
			cherry_team_members()->version(),
			true
		);

	}

	public function editor_fix() {

		if ( ! defined( 'TM_BUILDER_VERSION' ) ) {
			return;
		}

		global $wp_customize;

		if ( $wp_customize ) {
			return;
		}

		if ( ! wp_script_is( 'wp-color-picker-alpha' ) ) {
			return;
		}

		wp_enqueue_style(
			'cherry-team-editor',
			cherry_team_members()->plugin_url( 'admin/assets/css/editor.css' ),
			array(),
			cherry_team_members()->version()
		);

		wp_dequeue_script( 'wp-color-picker-alpha' );
		wp_deregister_script( 'wp-color-picker-alpha' );

		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_script(
			'wp-color-picker-alpha-new',
			cherry_team_members()->plugin_url( 'admin/assets/js/wp-color-picker-alpha.min.js' ),
			array( 'jquery' ),
			cherry_team_members()->version(),
			true
		);

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

Cherry_Team_Members_Admin::get_instance();
