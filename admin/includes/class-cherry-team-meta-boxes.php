<?php
/**
 * Handles custom post meta boxes for the 'team' post type.
 *
 * @package   Cherry_Team_Members_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Admin meta boxes management class
 */
class Cherry_Team_Members_Meta_Boxes {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Init metaboxes
		add_action( 'admin_init', array( $this, 'init_metaboxes' ) );

		// Enqueue assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Loads custom meta boxes on the "Add New Testimonial" and "Edit Testimonial" screens.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_metaboxes() {
		cherry_team_members()->get_core()->init_module(
			'cherry-post-meta',
			apply_filters( 'cherry_team_members_meta_args', array(
				'id'            => 'post-layout',
				'title'         => esc_html__( 'Person Options', 'cherry-team' ),
				'page'          => array( cherry_team_members_init()->name() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'fields'        => array(
					'cherry-team-position' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Position', 'cherry-team' ),
						'label'       => esc_html__( 'Position', 'cherry-team' ),
					),
					'cherry-team-location' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Location', 'cherry-team' ),
						'label'       => esc_html__( 'Location', 'cherry-team' ),
					),
					'cherry-team-phone' => array(
						'type'        => 'text',
						'placeholder' => esc_html__( 'Phone Number', 'cherry-team' ),
						'label'       => esc_html__( 'Phone Number', 'cherry-team' ),
					),
					'cherry-team-social' => array(
						'type'        => 'repeater',
						'label'       => esc_html__( 'Social profiles', 'cherry-team' ),
						'add_label'   => esc_html__( 'Add Social Network', 'cherry-team' ),
						'title_field' => 'label',
						'fields'      => array(
							'icon' => array(
								'type'        => 'iconpicker',
								'id'          => 'icon',
								'name'        => 'icon',
								'label'       => esc_html__( 'Choose icon', 'cherry-team' ),
								'icon_data'   => array(
									'icon_set'    => 'cherryTeamFontAwesome',
									'icon_css'    => cherry_team_members()->plugin_url( 'public/assets/css/font-awesome.min.css' ),
									'icon_base'   => 'fa',
									'icon_prefix' => 'fa-',
									'icons'       => $this->get_icons_set(),
								),
							),
							'label' => array(
								'type'        => 'text',
								'id'          => 'label',
								'name'        => 'label',
								'placeholder' => esc_html__( 'Label', 'cherry-team' ),
								'label'       => esc_html__( 'Label', 'cherry-team'  ),
							),
							'url' => array(
								'type'        => 'text',
								'id'          => 'url',
								'name'        => 'url',
								'placeholder' => esc_html__( 'URL', 'cherry-team' ),
								'label'       => esc_html__( 'URL', 'cherry-team'  ),
							),
						),
					),
					'cherry-team-skills' => array(
						'type'        => 'repeater',
						'label'       => esc_html__( 'Skills', 'cherry-team' ),
						'add_label'   => esc_html__( 'Add New Skill', 'cherry-team' ),
						'title_field' => 'label',
						'fields'      => array(
							'color' => array(
								'type'        => 'colorpicker',
								'id'          => 'color',
								'name'        => 'color',
								'value'       => '#007ACC',
								'placeholder' => esc_html__( 'Skill bar color', 'cherry-team' ),
								'label'       => esc_html__( 'Skill bar color', 'cherry-team'  ),
							),
							'label' => array(
								'type'        => 'text',
								'id'          => 'label',
								'name'        => 'label',
								'placeholder' => esc_html__( 'Skill Label', 'cherry-team' ),
								'label'       => esc_html__( 'Skill Label', 'cherry-team'  ),
							),
							'value' => array(
								'type'        => 'stepper',
								'id'          => 'value',
								'name'        => 'value',
								'value'       => 0,
								'max_value'   => '100',
								'min_value'   => '0',
								'step_value'  => '1',
								'placeholder' => esc_html__( 'Skill Value', 'cherry-team' ),
								'label'       => esc_html__( 'Skill Value', 'cherry-team'  ),
							),
						),
					),
				),
			)
		) );
	}

	/**
	 * Returns social icons set
	 *
	 * @return array
	 */
	public function get_icons_set() {

		ob_start();
		include cherry_team_members()->plugin_path( 'admin/assets/js/icons.json' );
		$json = ob_get_clean();

		$result = array();

		$icons = json_decode( $json, true );

		foreach ( $icons['icons'] as $icon ) {
			if ( ! in_array( 'Brand Icons', $icon['categories'] ) ) {
				continue;
			}
			$result[] = $icon['id'];
		}

		return $result;
	}

	/**
	 * Enqueue admin styles function.
	 *
	 * @param  string $hook_suffix Current page hook name.
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {

		$allowed_pages = array( 'post-new.php', 'post.php' );

		if ( ! in_array( $hook_suffix, $allowed_pages ) || cherry_team_members_init()->name() !== get_post_type() ) {
			return;
		}

		wp_enqueue_style( 'cherry-team-admin-style' );
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

Cherry_Team_Members_Meta_Boxes::get_instance();
