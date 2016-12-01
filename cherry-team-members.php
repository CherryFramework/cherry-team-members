<?php
/**
 * Plugin Name: Cherry Team Members
 * Plugin URI:  http://www.templatemonster.com/wordpress-themes.php
 * Description: Cherry Team Members plugin allows you to showcase your team and personnel.
 * Version:     1.0.4
 * Author:      TemplateMonster
 * Author URI:  http://www.templatemonster.com/
 * Text Domain: cherry-team
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package  Cherry Team
 * @category Core
 * @author   Cherry Team
 * @license  GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Team_Members' not exists.
if ( ! class_exists( 'Cherry_Team_Members' ) ) {

	/**
	 * Sets up and initializes the Cherry Team plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Team_Members {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '1.0.4';

		/**
		 * Plugin dir URL
		 *
		 * @var string
		 */
		private $plugin_url = null;

		/**
		 * Plugin dir path
		 *
		 * @var string
		 */
		private $plugin_path = null;

		/**
		 * Plugin slug
		 *
		 * @var string
		 */
		private $plugin_slug = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $core = null;

		/**
		 * Options holder
		 *
		 * @var array
		 */
		private $options = null;


		public static $options_key = 'cherry-team';

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Global includes
			$this->includes();

			// Load localization.
			add_action( 'plugins_loaded', array( $this, 'lang' ), 2 );
			// Load the installer core.
			add_action( 'after_setup_theme', require( $this->plugin_path( 'cherry-framework/setup.php' ) ), 0 );
			// Load the core functions/classes required by the rest of the theme.
			add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

			if ( is_admin() ) {
				$this->_admin();
			} else {
				$this->_public();
			}

		}

		/**
		 * Loads files from the 'public/includes' folder.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			require_once( $this->plugin_path( 'public/includes/class-cherry-team-init.php' ) );
			require_once( $this->plugin_path( 'public/includes/class-cherry-team-tools.php' ) );
			require_once( $this->plugin_path( 'public/includes/class-cherry-team-templater.php' ) );
			require_once( $this->plugin_path( 'public/includes/class-cherry-team-data.php' ) );
			require_once( $this->plugin_path( 'public/includes/class-cherry-team-shortcode.php' ) );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		public function lang() {
			load_plugin_textdomain( 'cherry-team', false, $this->plugin_path( 'languages' ) );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		public function _admin() {

			require_once( $this->plugin_path( 'admin/includes/class-cherry-team-ajax.php' ) );
			require_once( $this->plugin_path( 'admin/includes/class-cherry-team-admin.php' ) );
			require_once( $this->plugin_path( 'admin/includes/class-cherry-team-admin-columns.php' ) );
			require_once( $this->plugin_path( 'admin/includes/class-cherry-team-meta-boxes.php' ) );
			require_once( $this->plugin_path( 'admin/includes/class-cherry-team-options-page.php' ) );

		}

		/**
		 * Public-related hooks
		 *
		 * @return void
		 */
		public function _public() {
			add_action( 'wp_enqueue_scripts', array( $this, 'public_assets' ), 20 );
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function public_assets() {

			$styles = apply_filters( 'cherry_team_styles', array(
				'cherry-team' => array(
					'src' => $this->plugin_url( 'public/assets/css/cherry-team.css' ),
					'ver' => $this->version(),
				),
				'cherry-team-grid' => array(
					'src' => $this->plugin_url( 'public/assets/css/cherry-team-grid.css' ),
					'ver' => $this->version(),
				),
				'font-awesome' => array(
					'src' => $this->plugin_url( 'public/assets/css/font-awesome.min.css' ),
					'ver' => '4.6.3',
				),
			) );

			foreach ( $styles as $handle => $data ) {

				$data = array_merge(
					array(
						'src'   => '',
						'deps'  => '',
						'ver'   => '',
						'media' => 'all',
					),
					$data
				);

				wp_enqueue_style( $handle, $data['src'], $data['deps'], $data['ver'], $data['media'] );
			}

			wp_register_script(
				'cherry-team',
				$this->plugin_url( 'public/assets/js/cherry-team.js' ),
				array( 'cherry-js-core' ),
				'1.0.0',
				true
			);

			wp_localize_script(
				'cherry-team',
				'cherryTeam',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'loader'  => apply_filters(
						'cherry_team_loader_html',
						'<div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div>'
					)
				)
			);
		}

		/**
		 * Loads the core functions. These files are needed before loading anything else in the
		 * theme because they have required functions for use.
		 *
		 * @since  1.0.0
		 */
		public function get_core() {

			/**
			 * Fires before loads the core theme functions.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cherry_team_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );
				require_once( $core_paths[0] );
			} else {
				die( 'Class Cherry_Core not found' );
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => $this->plugin_path( 'cherry-framework' ),
				'base_url' => $this->plugin_url( 'cherry-framework' ),
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => true,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-interface-builder' => array(
						'autoload' => false,
					),
					'cherry-utility' => array(
						'autoload' => true,
					),
					'cherry-term-meta' => array(
						'autoload' => false,
					),
					'cherry-post-meta' => array(
						'autoload' => false,
					),
				),
			) );

			return $this->core;
		}

		/**
		 * Get team-related option
		 *
		 * @param  string $option  Option name.
		 * @param  mixed  $default default option value.
		 * @return mixed
		 */
		public function get_option( $option, $default = false ) {

			if ( ! empty( $this->options ) && isset( $this->options[ $option ] ) ) {
				return $this->options[ $option ];
			}

			$key = self::$options_key;

			$this->options = get_option( $key );

			if ( ! $this->options ) {
				$this->options = get_option( $key . '_default' );
			}

			if ( isset( $this->options[ $option ] ) ) {
				return $this->options[ $option ];
			}

			return $default;
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;

		}

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;

		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'cherry_team_members_template_path', 'cherry-team-members/' );
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function version() {
			return $this->version;
		}

		/**
		 * Returns plugin slug
		 *
		 * @return string
		 */
		public function slug() {
			return $this->slug;
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		public static function activation() {
			Cherry_Team_Members_Init::register_post();
			Cherry_Team_Members_Init::register_tax();
			Cherry_Team_Members_Options_Page::create_defaults();
			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		public static function deactivation() {
			flush_rewrite_rules();
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
	 * Returns instance of main class
	 *
	 * @return Cherry_Team_Members
	 */
	function cherry_team_members() {
		return Cherry_Team_Members::get_instance();
	}

	cherry_team_members();

}
