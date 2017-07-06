<?php
/**
 * Cherry Team Shortcode.
 *
 * @package   Cherry_Team_Members
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Team shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Team_Members_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'cherry_team';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Storage for data object
	 * @since 1.0.0
	 * @var   null|object
	 */
	public $data = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register shortcode on 'init'.
		add_action( 'init', array( $this, 'register_shortcode' ), 0 );
		add_action( 'cherry_team_members_elementor_get_shortcode_args', array( $this, 'shortcode_args' ) );

		$this->data = Cherry_Team_Members_Data::get_instance();
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {

		add_shortcode( $this->tag(), array( $this, 'do_shortcode' ) );

		$base = cherry_team_members();

		if ( defined( 'ELEMENTOR_VERSION' ) ) {

			require $base->plugin_path( 'public/includes/ext/class-cherry-team-elementor-compat.php' );

			cherry_team_members()->elementor_compat = cherry_team_members_elementor_compat( array(
				$this->tag() => array(
					'title' => esc_html__( 'Cherry Team', 'cherry-team' ),
					'file'  => $base->plugin_path( 'public/includes/ext/class-cherry-team-elementor-module.php' ),
					'class' => 'Cherry_Team_Elementor_Widget',
					'icon'  => 'eicon-person',
					'atts'  => $this->shortcode_args(),
				),
			) );
		}

		if ( is_admin() ) {
			$this->register_shortcode_for_builder();
		}
	}

	/**
	 * Returns shortcode tag.
	 *
	 * @return string
	 */
	public function tag() {

		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.0.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		return $tag;
	}

	/**
	 * Register shortcode arguments.
	 *
	 * @return array
	 */
	public function shortcode_args() {

		$column_opt = array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			6 => 6,
		);

		return apply_filters( 'cherry_team_list_shortcode_arguments', array(
			'super_title'    => array(
				'type'  => 'text',
				'title' => esc_html__( 'Super title', 'cherry-team' ),
				'value' => '',
			),
			'title'          => array(
				'type'  => 'text',
				'title' => esc_html__( 'Title', 'cherry-team' ),
				'value' => '',
			),
			'subtitle'       => array(
				'type'  => 'text',
				'title' => esc_html__( 'Subtitle', 'cherry-team' ),
				'value' => '',
			),
			'columns'        => array(
				'type'    => 'select',
				'title'   => esc_html__( 'Desktop columns', 'cherry-team' ),
				'value'   => 3,
				'options' => $column_opt,
			),
			'columns_laptop' => array(
				'type'    => 'select',
				'title'   => esc_html__( 'Laptop columns', 'cherry-team' ),
				'value'   => 3,
				'options' => $column_opt,
			),
			'columns_tablet' => array(
				'type'    => 'select',
				'title'   => esc_html__( 'Tablet columns', 'cherry-team' ),
				'value'   => 1,
				'options' => $column_opt,
			),
			'columns_phone'  => array(
				'type'    => 'select',
				'title'   => esc_html__( 'Phone columns', 'cherry-team' ),
				'value'   => 1,
				'options' => $column_opt,
			),
			'posts_per_page' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Posts per page', 'cherry-team' ),
				'description' => esc_html__( 'Select how many posts per page do you want to display(-1 means that will show all team)', 'cherry-team' ),
				'max_value'   => 50,
				'min_value'   => -1,
				'value'       => 6,
			),
			'group'       => array(
				'type'       => 'select',
				'title'      => esc_html__( 'Show team members from groups', 'cherry-team' ),
				'multiple'   => true,
				'value'      => '',
				'class'      => 'cherry-multi-select',
				'options'    => false,
				'options_cb' => array( $this, 'get_categories' ),
			),
			'id'             => array(
				'type'  => 'text',
				'title' => esc_html__( 'Show persons by ID', 'cherry-team' ),
				'value' => '',
			),
			'excerpt_length' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Description length', 'cherry-team' ),
				'description' => esc_html__( 'Select how many words show in desciption', 'cherry-team' ),
				'max_value'   => 200,
				'min_value'   => 0,
				'value'       => 20,
			),
			'more'           => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show more button', 'cherry-team' ),
				'description' => esc_html__( 'Show/hide more button', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
					'true_slave'   => 'team-more-filter-visible-true',
				),
			),
			'more_text'      => array(
				'type'   => 'text',
				'title'  => esc_html__( 'More button text', 'cherry-team' ),
				'value'  => esc_html__( 'More', 'cherry-team' ),
				'master' => 'team-more-filter-visible-true',
			),
			'more_url'       => array(
				'type'   => 'text',
				'title'  => esc_html__( 'More button URL', 'cherry-team' ),
				'value'  => '#',
				'master' => 'team-more-filter-visible-true',
			),
			'ajax_more'      => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'AJAX load more', 'cherry-team' ),
				'description' => esc_html__( 'Enable AJAX load more event on more button', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
				'master' => 'team-more-filter-visible-true',
			),
			'pagination'     => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Pagination', 'cherry-team' ),
				'description' => esc_html__( 'Enable paging navigation', 'cherry-team' ),
				'value'       => 'false',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_name'     => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show person name', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_photo'     => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show person photo', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_desc'   => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show person bio', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_position'   => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show person position', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_social'   => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show person social links', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'show_item_more'   => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show service item Read More button (if allowed in template)', 'cherry-team' ),
				'value'       => 'false',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'item_more_text' => array(
				'type'   => 'text',
				'title'  => esc_html__( 'Item Read More button text (if empty - used default value from template)', 'cherry-team' ),
				'value'  => '',
			),
			'show_filters'   => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Show filter by groups before team listing', 'cherry-team' ),
				'value'       => 'false',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'image_size'     => array(
				'type'       => 'select',
				'title'      => esc_html__( 'Listing item image size (if used in template)', 'cherry-team' ),
				'value'      => 'thumbnail',
				'options'    => false,
				'options_cb' => array( cherry_team_members_tools(), 'get_image_sizes' ),
			),
			'template'       => array(
				'type'       => 'select',
				'title'      => esc_html__( 'Listing item template', 'cherry-team' ),
				'value'      => 'default',
				'options'    => false,
				'options_cb' => array( cherry_team_members_tools(), 'get_templates' ),
			),
			'use_space'      => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Add space between team coumns', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
			'use_rows_space' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Add space between team rows', 'cherry-team' ),
				'value'       => 'true',
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-team' ),
					'false_toggle' => esc_html__( 'No', 'cherry-team' ),
				),
			),
		) );

	}

	/**
	 * Returns team categories list.
	 *
	 * @return array
	 */
	public function get_categories() {

		$categories = cherry_team_members()->utilities->utility->satellite->get_terms_array( 'group', 'slug' );

		if ( empty( $categories ) ) {
			$categories = array();
		}

		return array_merge( array( '' => esc_html__( 'From All', 'cherry-team' ) ), $categories );
	}

	/**
	 * Register team shortcode for shortcodes builder
	 *
	 * @return void
	 */
	public function register_shortcode_for_builder() {

		cherry_team_members()->get_core()->init_module( 'cherry5-insert-shortcode', array() );

		cherry5_register_shortcode(
			array(
				'title'       => esc_html__( 'Team', 'cherry-team' ),
				'description' => esc_html__( 'Showcase your team with Cherry Team Members plugin', 'cherry-team' ),
				'icon'        => '<span class="dashicons dashicons-businessman"></span>',
				'slug'        => 'cherry-team-plugin',
				'shortcodes'  => array(
					array(
						'title'       => esc_html__( 'Team', 'cherry-projects' ),
						'description' => esc_html__( 'Shortcode is used to display the team members list', 'cherry-team' ),
						'icon'        => '<span class="dashicons dashicons-businessman"></span>',
						'slug'        => $this->tag(),
						'options'     => $this->shortcode_args(),
					),
				),
			)
		);
	}

	/**
	 * Set defaults callback.
	 *
	 * @param array &$item Shortcode fields data.
	 */
	public function set_defaults( &$item ) {
		$item = $item['value'];
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
	 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = 'cherry_team' ) {

		// Set up the default arguments.
		$defaults = array(
			'super_title'    => '',
			'title'          => '',
			'subtitle'       => '',
			'columns'        => 3,
			'columns_laptop' => 3,
			'columns_tablet' => 2,
			'columns_phone'  => 1,
			'posts_per_page' => 6,
			'group'          => '',
			'id'             => 0,
			'excerpt_length' => 20,
			'more'           => true,
			'more_text'      => __( 'More', 'cherry-team' ),
			'more_url'       => '#',
			'ajax_more'      => true,
			'pagination'     => false,
			'show_name'      => true,
			'show_photo'     => true,
			'show_desc'      => true,
			'show_position'  => true,
			'show_social'    => true,
			'show_filters'   => false,
			'image_size'     => 'thumbnail',
			'template'       => 'default',
			'use_space'      => true,
			'use_rows_space' => true,
		);

		// Set up the default arguments.
		$defaults = $this->shortcode_args();
		array_walk( $defaults, array( $this, 'set_defaults' ) );

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		// Fix integers.
		if ( isset( $atts['posts_per_page'] ) ) {
			$atts['posts_per_page'] = intval( $atts['posts_per_page'] );
		}

		if ( isset( $atts['image_size'] ) &&  ( 0 < intval( $atts['image_size'] ) ) ) {
			$atts['image_size'] = intval( $atts['image_size'] );
		} else {
			$atts['image_size'] = esc_attr( $atts['image_size'] );
		}

		$col_classes = '';

		// Fix columns
		foreach ( array( 'columns', 'columns_laptop', 'columns_tablet', 'columns_phone' ) as $col ) {
			$atts[ $col ] = ( 0 !== intval( $atts[ $col ] ) ) ? intval( $atts[ $col ] ) : 3;
		}

		$templates        = cherry_team_members_templater()->get_templates_list();
		$atts['template'] = isset( $templates[ $atts['template'] ] ) ? $templates[ $atts['template'] ] : 'default.tmpl';

		$bool_to_fix = array(
			'show_name',
			'show_photo',
			'show_desc',
			'show_position',
			'show_social',
			'show_item_more',
			'show_filters',
			'ajax_more',
			'more',
			'pagination',
			'use_space',
			'use_rows_space',
		);

		// Fix booleans.
		foreach ( $bool_to_fix as $v ) {
			$atts[ $v ] = filter_var( $atts[ $v ], FILTER_VALIDATE_BOOLEAN );
		}

		if ( true === $atts['more'] ) {
			$atts['pagination'] = false;
		}

		$relations = array(
			'limit'          => 'posts_per_page',
			'id'             => 'id',
			'group'          => 'group',
			'size'           => 'image_size',
			'excerpt_length' => 'excerpt_length',
			'col_xs'         => 'columns_phone',
			'col_sm'         => 'columns_tablet',
			'col_md'         => 'columns_laptop',
			'col_xl'         => 'columns',
			'show_name'      => 'show_name',
			'show_photo'     => 'show_photo',
			'show_desc'      => 'show_desc',
			'show_position'  => 'show_position',
			'show_social'    => 'show_social',
			'show_item_more' => 'show_item_more',
			'item_more_text' => 'item_more_text',
			'show_filters'   => 'show_filters',
			'template'       => 'template',
			'pager'          => 'pagination',
			'more'           => 'more',
			'more_text'      => 'more_text',
			'more_url'       => 'more_url',
			'ajax_more'      => 'ajax_more',
			'use_space'      => 'use_space',
			'use_rows_space' => 'use_rows_space',
		);

		foreach ( $relations as $data_key => $atts_key ) {

			if ( ! isset( $atts[ $atts_key ] ) ) {
				continue;
			}

			$data_args[ $data_key ] = $atts[ $atts_key ];
		}

		// Make sure we return and don't echo.
		$data_args['echo'] = false;

		if ( ! empty( $data_args['item_class'] ) ) {
			$data_args['item_class'] .= $col_classes;
		} else {
			$data_args['item_class'] = trim( $col_classes );
		}

		$data_args['item_class'] .= ' team-item';

		$heading = apply_filters(
			'cherry_team_shortcode_heading_format',
			array(
				'super_title' => '<h5 class="team-heading_super_title">%s</h5>',
				'title'       => '<h3 class="team-heading_title">%s</h3>',
				'subtitle'    => '<h6 class="team-heading_subtitle">%s</h6>',
			)
		);

		$before  = '<div class="team-container">';

		foreach ( $heading as $item => $format ) {

			if ( empty( $atts[ $item ] ) ) {
				continue;
			}

			$before .= sprintf( $format, $atts[ $item ] );
		}

		$after = '</div>';

		return $before . $this->data->the_team( $data_args ) . $after;
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

Cherry_Team_Members_Shortcode::get_instance();
