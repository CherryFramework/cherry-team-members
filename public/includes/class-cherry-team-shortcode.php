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
	public static $name = 'team';

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
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Register shortcode and add it to the dialog.
		add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

		add_filter( 'cherry_templater_target_dirs', array( $this, 'add_target_dir' ), 11 );
		add_filter( 'cherry_templater_macros_buttons', array( $this, 'add_macros_buttons' ), 11, 2 );

		$this->data = Cherry_Team_Members_Data::get_instance();
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {

		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.0.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		add_shortcode( $tag, array( $this, 'do_shortcode' ) );
	}

	/**
	 * Filter to modify original shortcodes data and add [$this->name] shortcode.
	 *
	 * @since  1.0.0
	 * @param  array $shortcodes Original plugin shortcodes.
	 * @return array             Modified array.
	 */
	public function shortcodes( $shortcodes ) {

		$terms_list = array();

		if ( did_action( 'wp_ajax_cherry_shortcodes_generator_settings' ) ) {
			$terms = get_terms( 'group' );

			if ( ! is_wp_error( $terms ) ) {
				$terms_list = wp_list_pluck( $terms, 'name', 'slug' );
			}
		}

		$sizes_list = array();
		if ( class_exists( 'Cherry_Shortcodes_Tools' ) && method_exists( 'Cherry_Shortcodes_Tools', 'image_sizes' ) ) {
			$sizes_list = Cherry_Shortcodes_Tools::image_sizes();
		}

		$shortcodes[ self::$name ] = array(
			'name'  => __( 'Team', 'cherry-team' ), // Shortcode name.
			'desc'  => __( 'Cherry team shortcode', 'cherry-team' ),
			'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
			'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed
			'atts'  => array( // List of shortcode params (attributes).
				'limit' => array(
					'type'    => 'slider',
					'min'     => -1,
					'max'     => 100,
					'step'    => 1,
					'default' => 3,
					'name'    => __( 'Limit', 'cherry-team' ),
					'desc'    => __( 'Maximum number of posts.', 'cherry-team' ),
				),
				'order' => array(
					'type' => 'select',
					'values' => array(
						'desc' => __( 'Descending', 'cherry-team' ),
						'asc'  => __( 'Ascending', 'su' ),
					),
					'default' => 'DESC',
					'name' => __( 'Order', 'cherry-team' ),
					'desc' => __( 'Posts order', 'cherry-team' ),
				),
				'orderby' => array(
					'type' => 'select',
					'values' => array(
						'none'          => __( 'None', 'cherry-team' ),
						'id'            => __( 'Post ID', 'cherry-team' ),
						'author'        => __( 'Post author', 'cherry-team' ),
						'title'         => __( 'Post title', 'cherry-team' ),
						'name'          => __( 'Post slug', 'cherry-team' ),
						'date'          => __( 'Date', 'cherry-team' ),
						'modified'      => __( 'Last modified date', 'cherry-team' ),
						'rand'          => __( 'Random', 'cherry-team' ),
						'comment_count' => __( 'Comments number', 'cherry-team' ),
						'menu_order'    => __( 'Menu order', 'cherry-team' ),
					),
					'default' => 'date',
					'name'    => __( 'Order by', 'cherry-team' ),
					'desc'    => __( 'Order posts by', 'cherry-team' ),
				),
				'group' => array(
					'type'     => 'select',
					'multiple' => true,
					'values'   => $terms_list,
					'default'  => '',
					'name'     => __( 'Groups', 'cherry-team' ),
					'desc'     => __( 'Select groups to show team members from', 'cherry-team' ),
				),
				'id' => array(
					'default' => 0,
					'name'    => __( 'Post ID\'s', 'cherry-team' ),
					'desc'    => __( 'Enter comma separated ID\'s of the posts that you want to show', 'cherry-team' ),
				),
				'show_name' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name' => __( 'Show name?', 'cherry-team' ),
					'desc'    => __( 'Show name?', 'cherry-team' ),
				),
				'show_photo' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name' => __( 'Show photo?', 'cherry-team' ),
					'desc'    => __( 'Show photo?', 'cherry-team' ),
				),
				'size' => array(
					'type'    => 'select',
					'values'  => $sizes_list,
					'default' => 'thumbnail',
					'name'    => __( 'Featured image size', 'cherry-team' ),
					'desc'    => __( 'Select size for a Featured image', 'cherry-team' ),
				),
				'excerpt_length' => array(
					'type'    => 'slider',
					'min'     => 5,
					'max'     => 150,
					'step'    => 1,
					'default' => 20,
					'name'    => __( 'Excerpt Length', 'cherry-team' ),
					'desc'    => __( 'Excerpt length (if used in template)', 'cherry-team' ),
				),
				'col' => array(
					'type'    => 'responsive',
					'default' => array(
						'col_xs' => 'none',
						'col_sm' => 'none',
						'col_md' => 'none',
						'col_lg' => 'none',
					),
					'name'    => __( 'Column class', 'cherry-team' ),
					'desc'    => __( 'Column class for each item.', 'cherry-team' ),
				),
				'template' => array(
					'type'   => 'select',
					'values' => array(
						'default.tmpl' => 'default.tmpl',
					),
					'default' => 'default.tmpl',
					'name'    => __( 'Template', 'cherry-team' ),
					'desc'    => __( 'Shortcode template', 'cherry-team' ),
				),
				'class' => array(
					'default' => '',
					'name'    => __( 'Class', 'cherry-team' ),
					'desc'    => __( 'Extra CSS class', 'cherry-team' ),
				),
			),
			'icon'     => 'users', // Custom icon (font-awesome).
			'function' => array( $this, 'do_shortcode' ), // Name of shortcode function.
		);

		return $shortcodes;
	}

	/**
	 * Adds team template directory to shortcodes templater
	 *
	 * @since  1.0.0
	 * @param  array $target_dirs existing target dirs.
	 * @return array
	 */
	public function add_target_dir( $target_dirs ) {

		array_push( $target_dirs, cherry_team_members()->plugin_path() );
		return $target_dirs;

	}

	/**
	 * Add team shortcode macros buttons to templater
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $macros_buttons current buttons array.
	 * @param  string $shortcode      shortcode name.
	 * @return array
	 */
	public function add_macros_buttons( $macros_buttons, $shortcode ) {

		if ( self::$name != $shortcode ) {
			return $macros_buttons;
		}

		$macros_buttons = array(
			'photo' => array(
				'id'    => 'cherry_photo',
				'value' => __( 'Photo', 'cherry-team' ),
				'open'  => '%%PHOTO%%',
				'close' => '',
			),
			'name' => array(
				'id'    => 'cherry_name',
				'value' => __( 'Name', 'cherry-team' ),
				'open'  => '%%NAME%%',
				'close' => '',
			),
			'content' => array(
				'id'    => 'cherry_content',
				'value' => __( 'Person description', 'cherry-team' ),
				'open'  => '%%CONTENT%%',
				'close' => '',
			),
			'excerpt' => array(
				'id'    => 'cherry_excerpt',
				'value' => __( 'Short description', 'cherry-team' ),
				'open'  => '%%EXCERPT%%',
				'close' => '',
			),
			'position' => array(
				'id'    => 'cherry_position',
				'value' => __( 'Person Position', 'cherry-team' ),
				'open'  => '%%POSITION%%',
				'close' => '',
			),
			'location' => array(
				'id'    => 'cherry_location',
				'value' => __( 'Person Location', 'cherry-team' ),
				'open'  => '%%LOCATION%%',
				'close' => '',
			),
			'phone' => array(
				'id'    => 'cherry_phone',
				'value' => __( 'Telephone', 'cherry-team' ),
				'open'  => '%%PHONE%%',
				'close' => '',
			),
			'email' => array(
				'id'    => 'cherry_email',
				'value' => __( 'Email', 'cherry-team' ),
				'open'  => '%%EMAIL%%',
				'close' => '',
			),
			'website' => array(
				'id'    => 'cherry_website',
				'value' => __( 'Personal website', 'cherry-team' ),
				'open'  => '%%WEBSITE%%',
				'close' => '',
			),
			'socials' => array(
				'id'    => 'cherry_socials',
				'value' => __( 'Person social links block', 'cherry-team' ),
				'open'  => '%%SOCIALS%%',
				'close' => '',
			),
			'link' => array(
				'id'    => 'cherry_link',
				'value' => __( 'Profile page link', 'cherry-team' ),
				'open'  => '%%LINK%%',
				'close' => '',
			),
		);

		return $macros_buttons;

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
	public function do_shortcode( $atts, $content = null, $shortcode = 'team' ) {

		// Set up the default arguments.
		$defaults = array(
			'limit'          => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'group'          => '',
			'id'             => 0,
			'show_name'      => true,
			'show_photo'     => true,
			'size'           => 'thumbnail',
			'excerpt_length' => 20,
			'echo'           => false,
			'template'       => 'default.tmpl',
			'col_xs'         => '12',
			'col_sm'         => '6',
			'col_md'         => '3',
			'col_lg'         => 'none',
			'class'          => '',
		);

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		// Fix integers.
		if ( isset( $atts['limit'] ) ) {
			$atts['limit'] = intval( $atts['limit'] );
		}

		if ( isset( $atts['size'] ) &&  ( 0 < intval( $atts['size'] ) ) ) {
			$atts['size'] = intval( $atts['size'] );
		} else {
			$atts['size'] = esc_attr( $atts['size'] );
		}

		// Fix booleans.
		foreach ( array( 'show_name', 'show_photo' ) as $k => $v ) :

			if ( isset( $atts[ $v ] ) && ( 'true' == $atts[ $v ] || 'yes' == $atts[ $v ] ) ) {
				$atts[ $v ] = true;
			} else {
				$atts[ $v ] = false;
			}

		endforeach;

		return $this->data->the_team( $atts );
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
