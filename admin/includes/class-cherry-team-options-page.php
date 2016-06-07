<?php
/**
 * Team options page
 *
 * @package   Cherry_Projects_Options_Page
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

class Cherry_Team_Members_Options_Page {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * UI builder instance
	 *
	 * @var object
	 */
	public $ui_builder = null;

	/**
	 * Existing field types
	 *
	 * @var array
	 */
	public $field_types = array();

	/**
	 * Team options
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Options page slug
	 *
	 * @var string
	 */
	public $page_slug = 'cherry-team-options';

	/**
	 * Plugin options cell name
	 *
	 * @var string
	 */
	public static $options_key = 'cherry-team';

	/**
	 * Default options list
	 *
	 * @var array
	 */
	public static $default_options = array(
		'archive-page'       => '',
		'posts-per-page'     => 9,
		'archive-columns'    => 3,
		'single-template'    => 'single',
		'single-image-size'  => 'thumbnail',
		'listing-template'   => 'default',
		'listing-image-size' => 'thumbnail',
	);

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'render_page' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'after_setup_theme', array( $this, 'init' ), 10 );

		add_action( 'wp_ajax_cherry_team_members_process_options', array( $this, 'process_options' ) );

	}

	/**
	 * Run initialization of modules.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( ! $this->is_options_page() ) {
			return;
		}

		$this->options = array(
			'archive-page' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Select team archive page', 'cherry-team' ),
				'label'            => '',
				'description'      => '',
				'value'            => array(),
				'options'          => false,
				'options_callback' => array( cherry_team_members_tools(), 'get_pages' ),
			),
			'posts-per-page' => array(
				'type'       => 'stepper',
				'title'      => esc_html__( 'Set posts number per archive page', 'cherry-team' ),
				'value'      => self::$default_options['posts-per-page'],
				'max_value'  => '100',
				'min_value'  => '1',
				'step_value' => '1',
			),
			'archive-columns' => array(
				'type'       => 'stepper',
				'title'      => esc_html__( 'Select archive page columns number', 'cherry-team' ),
				'value'      => self::$default_options['archive-columns'],
				'max_value'  => '4',
				'min_value'  => '1',
				'step_value' => '1',
			),
			'single-template' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Select template for single team member page', 'cherry-team' ),
				'label'            => '',
				'description'      => '',
				'value'            => self::$default_options['single-template'],
				'options'          => false,
				'options_callback' => array( cherry_team_members_tools(), 'get_templates' ),
			),
			'single-image-size' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Select image size for single team member page', 'cherry-team' ),
				'label'            => '',
				'description'      => '',
				'value'            => self::$default_options['single-image-size'],
				'options'          => false,
				'options_callback' => array( cherry_team_members_tools(), 'get_image_sizes' ),
			),
			'listing-template' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Select template for team listing page', 'cherry-team' ),
				'label'            => '',
				'description'      => '',
				'value'            => self::$default_options['listing-template'],
				'options'          => false,
				'options_callback' => array( cherry_team_members_tools(), 'get_templates' ),
			),
			'listing-image-size' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Select image size for listing team member page', 'cherry-team' ),
				'label'            => '',
				'description'      => '',
				'value'            => self::$default_options['listing-image-size'],
				'options'          => false,
				'options_callback' => array( cherry_team_members_tools(), 'get_image_sizes' ),
			),
		);

		add_filter( 'cherry_core_js_ui_init_settings', array( $this, 'init_ui_js' ), 10 );

		array_walk( $this->options, array( $this, 'set_field_types' ) );

		$this->ui_builder = cherry_team_members()->get_core()->init_module(
			'cherry-ui-elements',
			array( 'ui_elements' => $this->field_types )
		);

		return true;
	}

	/**
	 * Init UI elements JS
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function init_ui_js() {

		$settings['auto_init'] = true;
		$settings['targets'] = array( 'body' );

		return $settings;
	}

	/**
	 * Store field types used in this widget into class property
	 *
	 * @since  1.0.0
	 * @param  array  $field field data.
	 * @param  string $id    field key.
	 * @return bool
	 */
	public function set_field_types( $field, $id ) {

		if ( ! isset( $field['type'] ) ) {
			return false;
		}

		if ( ! in_array( $field['type'], $this->field_types ) ) {
			$this->field_types[] = $field['type'];
		}

		return true;
	}

	/**
	 * Check if is options page
	 *
	 * @return boolean
	 */
	public function is_options_page() {
		return ( ! empty( $_GET['page'] ) && $this->page_slug === $_GET['page'] );
	}

	/**
	 * [render_page description]
	 * @return [type] [description]
	 */
	public function render_page() {
		add_submenu_page(
			'edit.php?post_type=' . cherry_team_members_init()->name(),
			esc_html__( 'Cherry Team Options', 'cherry-team' ),
			esc_html__( 'Settings', 'cherry-team' ),
			'edit_theme_options',
			$this->page_slug,
			array( $this, 'options_page' ),
			'',
			64
		);
	}

	/**
	 *
	 */
	public function options_page() {
		$html = '';

		$options = get_option( Cherry_Team_Members::$options_key );

		$settings = $this->get_fields( $options );

		$html = Cherry_Toolkit::render_view(
			cherry_team_members()->plugin_path( 'admin/views/options-page.php' ),
			array(
				'settings' => $settings,
			)
		);

		echo $html;
	}

	/**
	 * Get registered control fields
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_fields( $current_options ) {

		$elements = array(
			'ui-settings' => array(),
			'labels'      => array(
				'save-button-text'      => esc_html__( 'Save', 'cherry-team' ),
				'define-as-button-text' => esc_html__( 'Define as default', 'cherry-team' ),
				'restore-button-text'   => esc_html__( 'Restore', 'cherry-team' ),
			),
		);

		foreach ( $this->options as $key => $field ) {

			$value = isset( $current_options[ $key ] ) ? $current_options[ $key ] : false;
			$value = ( false !== $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

			if ( isset( $field['options_callback'] ) ) {

				$callback = $this->get_callback_data( $field['options_callback'] );
				$options  = call_user_func_array( $callback['callback'], $callback['args'] );
			} else {
				$options = Cherry_Toolkit::get_arg( $field, 'options', array() );
			}

			$args = array(
				'type'               => Cherry_Toolkit::get_arg( $field, 'type', 'text' ),
				'id'                 => $key,
				'name'               => $key,
				'value'              => $value,
				'label'              => Cherry_Toolkit::get_arg( $field, 'label', '' ),
				'options'            => $options,
				'multiple'           => Cherry_Toolkit::get_arg( $field, 'multiple', false ),
				'filter'             => Cherry_Toolkit::get_arg( $field, 'filter', false ),
				'size'               => Cherry_Toolkit::get_arg( $field, 'size', 1 ),
				'null_option'        => Cherry_Toolkit::get_arg( $field, 'null_option', 'None' ),
				'multi_upload'       => Cherry_Toolkit::get_arg( $field, 'multi_upload', true ),
				'library_type'       => Cherry_Toolkit::get_arg( $field, 'library_type', 'image' ),
				'upload_button_text' => Cherry_Toolkit::get_arg( $field, 'upload_button_text', 'Choose' ),
				'max_value'          => Cherry_Toolkit::get_arg( $field, 'max_value', '100' ),
				'min_value'          => Cherry_Toolkit::get_arg( $field, 'min_value', '0' ),
				'max'                => Cherry_Toolkit::get_arg( $field, 'max', '100' ),
				'min'                => Cherry_Toolkit::get_arg( $field, 'min', '0' ),
				'step_value'         => Cherry_Toolkit::get_arg( $field, 'step_value', '1' ),
				'style'              => Cherry_Toolkit::get_arg( $field, 'style', 'normal' ),
				'display_input'      => Cherry_Toolkit::get_arg( $field, 'display_input', true ),
				'fields'             => Cherry_Toolkit::get_arg( $field, 'fields', array() ),
				'controls'           => Cherry_Toolkit::get_arg( $field, 'controls', array() ),
				'toggle'             => Cherry_Toolkit::get_arg( $field, 'toggle', array(
					'true_toggle'  => 'On',
					'false_toggle' => 'Off',
					'true_slave'   => '',
					'false_slave'  => '',
				) ),
				'required'           => Cherry_Toolkit::get_arg( $field, 'required', false ),
				'master'             => Cherry_Toolkit::get_arg( $field, 'master', '' ),
			);

			$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );
			$elements['ui-settings'][] = array(
				'title'       => Cherry_Toolkit::get_arg( $field, 'title', '' ),
				'description' => Cherry_Toolkit::get_arg( $field, 'description', '' ),
				'master'      => Cherry_Toolkit::get_arg( $field, 'master', '' ),
				'ui-html'     => $current_element->render(),
			);

		}

		return $elements;
	}

	/**
	 * Parse callback data.
	 *
	 * @since  1.0.0
	 * @param  array $options_callback Callback data.
	 * @return array
	 */
	public function get_callback_data( $options_callback ) {

		if ( 2 === count( $options_callback ) ) {

			$callback = array(
				'callback' => $options_callback,
				'args'     => array(),
			);

			return $callback;
		}

		$callback = array(
			'callback' => array_slice( $options_callback, 0, 2 ),
			'args'     => $options_callback[2],
		);

		return $callback;
	}

	/**
	 * Ajax request
	 *
	 * @since 1.0.0
	 */
	public function process_options() {

		if ( empty( $_POST['post_array'] ) || empty( $_POST['nonce'] ) || empty( $_POST['type'] ) ) {
			exit( 'Invalid data' );
		}

		$post_array = $_POST['post_array'];
		$nonce      = $_POST['nonce'];
		$type       = $_POST['type'];


		if ( ! wp_verify_nonce( $nonce, 'cherry_ajax_nonce' ) ) {
			exit( 'Invalid data' );
		}

		switch ( $type ) {
			case 'save':
				update_option( Cherry_Team_Members::$options_key, $post_array );
				$response = array(
					'message' => esc_html__( 'Options have been saved', 'cherry-team' ),
					'type'    => 'success-notice'
				);

				break;
			case 'define_as_default':
				update_option( Cherry_Team_Members::$options_key . '_default', $post_array );
				$response = array(
					'message' => esc_html__( 'Options have been saved as default', 'cherry-team' ),
					'type'    => 'success-notice'
				);

				break;
			case 'restore':
				$default_options = get_option( Cherry_Team_Members::$options_key . '_default' );
				update_option( Cherry_Team_Members::$options_key, $default_options );

				$response = array(
					'message' => esc_html__( 'Options have been restored', 'cherry-team' ),
					'type'    => 'success-notice'
				);

				break;
		}

		wp_send_json( $response );

	}

	/**
	 * Store default options into database on plugin activation.
	 *
	 * @return void
	 */
	public static function create_defaults() {

		$options = array(
			Cherry_Team_Members::$options_key => get_option( Cherry_Team_Members::$options_key ),
			Cherry_Team_Members::$options_key . '_default' => get_option( Cherry_Team_Members::$options_key . '_default' ),
		);

		foreach ( $options as $key => $value ) {
			if ( empty( $value ) ) {
				update_option( $key, self::$default_options );
			}
		}

	}

	/**
	 * Enqueue admin styles function.
	 *
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, $this->page_slug ) ) {
			return null;
		}

		wp_enqueue_style( 'cherry-team-admin-style' );
	}

	/**
	 * Enqueue admin scripts function.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, $this->page_slug ) ) {
			return null;
		}

		wp_enqueue_script( 'serialize-object' );

		wp_enqueue_script( 'cherry-team-admin-scripts' );

		$options_page_settings = array(
			'please_wait_processing' => esc_html__( 'Please wait, processing the previous request', 'cherry-team' ),
			'redirect_url'           => menu_page_url( 'cherry-team-options', false ),
		);

		wp_localize_script( 'cherry-team-admin-scripts', 'cherryTeamPluginSettings', $options_page_settings );
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

Cherry_Team_Members_Options_Page::get_instance();
