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
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class Cherry_Team_Members_Templater {

	/**
	 * Templater macros regular expression
	 *
	 * @var string
	 */
	private $macros_regex = '/%%.+?%%/';

	/**
	 * Templates data to replace
	 *
	 * @var array
	 */
	private $replace_data = array();

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path.
		add_filter( 'template_include', array( $this, 'view_template' ) );

	}

	/**
	 * Checks if the template is assigned to the page.
	 *
	 * @since  1.0.0
	 * @param  string $template current template name.
	 * @return string
	 */
	public function view_template( $template ) {

		$find = array();
		$file = '';

		if ( is_single() && cherry_team_members_init()->name() === get_post_type() ) {

			$file   = 'single-team.php';
			$find[] = $file;
			$find[] = cherry_team_members()->template_path() . $file;

		} elseif ( is_tax( 'group' ) ) {

			$term = get_queried_object();
			$file = 'archive-team.php';

			$file_term = 'taxonomy-' . $term->taxonomy . '.php';

			$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = cherry_team_members()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = cherry_team_members()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = $file_term;
			$find[] = cherry_team_members()->template_path() . $file_term;
			$find[] = $file;
			$find[] = cherry_team_members()->template_path() . $file;

		} elseif ( is_post_type_archive( cherry_team_members_init()->name() ) ) {

			$file 	= 'archive-team.php';
			$find[] = $file;
			$find[] = cherry_team_members()->template_path() . $file;

		} elseif ( cherry_team_members()->get_option( 'archive-page' ) && is_page( cherry_team_members()->get_option( 'archive-page' ) ) ) {
			$file   = 'archive-team.php';
			$find[] = $file;
			$find[] = cherry_team_members()->template_path() . $file;
		}

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );
			if ( ! $template ) {
				$template = cherry_team_members()->plugin_path( 'templates/' . $file );
			}
		}

		return $template;
	}

	/**
	 * Returns macros regular expression.
	 *
	 * @return string
	 */
	public function macros_regex() {
		return $this->macros_regex;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {

		require_once cherry_team_members()->plugin_path( 'public/includes/class-cherry-team-template-callbacks.php' );

		$callbacks = new Cherry_Team_Members_Template_Callbacks( $atts );

		$data = array(
			'photo'    => array( $callbacks, 'get_photo' ),
			'name'     => array( $callbacks, 'get_name' ),
			'position' => array( $callbacks, 'get_position' ),
			'content'  => array( $callbacks, 'get_content' ),
			'excerpt'  => array( $callbacks, 'get_excerpt' ),
			'location' => array( $callbacks, 'get_location' ),
			'phone'    => array( $callbacks, 'get_phone' ),
			'socials'  => array( $callbacks, 'get_socials' ),
			'skills'   => array( $callbacks, 'get_skills' ),
			'link'     => array( $callbacks, 'get_link' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.2
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->replace_data = apply_filters( 'cherry_team_data_callbacks', $data, $atts );

		return $callbacks;
	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @since  1.0.3 - Use output buffering for getting template content.
	 * @return bool|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! file_exists( $template ) ) {
			return false;
		}

		ob_start();
		include $template;
		return ob_get_clean();
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {

		$file       = '';
		$default    = cherry_team_members()->plugin_path( 'templates/shortcodes/' . $shortcode . '/default.tmpl' );
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;

		/**
		 * Filters a default fallback-template.
		 *
		 * @since 1.0.0
		 * @param string $content.
		 */
		$content = apply_filters( 'cherry_team_members_fallback_template', '<div class="inner-wrapper">%%title%%%%image%%%%content%%</div>' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( $theme_template = locate_template( array( 'cherry-team/' . $template ) ) ) {
			$file = $theme_template;
		} elseif ( file_exists( cherry_team_members()->plugin_path( $subdir ) ) ) {
			$file = cherry_team_members()->plugin_path( $subdir );
		} else {
			$file = $default;
		}

		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
	}

	/**
	 * Parse template content and replace macros with real data.
	 *
	 * @param  string $content Content to parse.
	 * @return string
	 */
	public function parse_template( $content ) {
		return preg_replace_callback( $this->macros_regex(), array( $this, 'replace_callback' ), $content );
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$item   = trim( $matches[0], '%%' );
		$arr    = explode( ' ', $item, 2 );
		$macros = strtolower( $arr[0] );
		$attr   = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();

		if ( ! isset(  $this->replace_data[ $macros ] ) ) {
			return;
		}

		$callback = $this->replace_data[ $macros ];

		if ( ! is_callable( $callback ) || ! isset( $this->replace_data[ $macros ] ) ) {
			return;
		}

		if ( ! empty( $attr ) ) {

			// Call a WordPress function.
			return call_user_func( $callback, $attr );
		}

		return call_user_func( $callback );
	}

	/**
	 * Returns available templates list
	 *
	 * @return array
	 */
	public function get_templates_list() {
		return apply_filters( 'cherry_team_templates_list', array(
			'default'    => 'default.tmpl',
			'single'     => 'single.tmpl',
			'grid-boxes' => 'grid-boxes.tmpl',
		) );
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
 * Returns instance of templater class.
 *
 * @return Cherry_Team_Members_Templater
 */
function cherry_team_members_templater() {
	return Cherry_Team_Members_Templater::get_instance();
}

cherry_team_members_templater();
