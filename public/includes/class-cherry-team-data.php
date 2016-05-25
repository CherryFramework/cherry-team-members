<?php
/**
 * Cherry Team Data class.
 * main public class. Grab team data form database and output it
 *
 * @package   Cherry_Team_Members
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Team data.
 *
 * @since 1.0.0
 */
class Cherry_Team_Members_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $query_args = array();

	/**
	 * Holder for the main query object, while team query processing
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $temp_query = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		 * Fires when you need to display team.
		 *
		 * @since 1.0.0
		 */
		add_action( 'cherry_get_team', array( $this, 'the_team' ) );

	}

	/**
	 * Display or return HTML-formatted team.
	 *
	 * @since  1.0.0
	 * @param  string|array $args Arguments.
	 * @return string
	 */
	public function the_team( $args = '' ) {

		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array Default arguments.
		 * @param array The 'the_team' function argument.
		 */
		$defaults = apply_filters( 'cherry_the_team_default_args', array(
			'limit'          => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'group'          => '',
			'id'             => 0,
			'show_photo'     => true,
			'show_name'      => true,
			'size'           => 'thumbnail',
			'echo'           => true,
			'title'          => '',
			'excerpt_length' => 20,
			'wrap_class'     => 'team-wrap',
			'col_xs'         => false,
			'col_sm'         => false,
			'col_md'         => false,
			'col_lg'         => false,
			'before_title'   => '<h2>',
			'after_title'    => '</h2>',
			'pager'          => false,
			'template'       => 'default.tmpl',
			'item_class'     => 'team-item',
			'container'      => '<div class="team-listing row">%s</div>',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'cherry_the_team_args', $args );
		$output = '';

		/**
		 * Fires before the team listing.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_team_before', $args );

		// The Query.
		$query = $this->get_team( $args );

		global $wp_query;

		$this->temp_query = $wp_query;
		$wp_query = null;
		$wp_query = $query;

		// Fix boolean.
		if ( isset( $args['pager'] ) && ( ( 'true' == $args['pager'] ) || true === $args['pager'] ) ) {
			$args['pager'] = true;
		} else {
			$args['pager'] = false;
		}

		// The Display.
		if ( is_wp_error( $query ) ) {
			return;
		}

		$css_classes = array();

		if ( ! empty( $args['wrap_class'] ) ) {
			$css_classes[] = esc_attr( $args['wrap_class'] );
		}

		if ( ! empty( $args['template'] ) ) {
			$css_classes[] = $this->get_template_class( $args['template'] );
		}

		if ( ! empty( $args['class'] ) ) {
			$css_classes[] = esc_attr( $args['class'] );
		}

		$css_class = implode( ' ', $css_classes );

		// Open wrapper.
		$output .= sprintf( '<div class="%s">', $css_class );

		if ( ! empty( $args['title'] ) ) {
			$output .= $args['before_title'] . $args['title'] . $args['after_title'];
		}

		if ( false !== $args['container'] ) {
			$output .= sprintf( $args['container'], $this->get_team_loop( $query, $args ) );
		} else {
			$output .= $this->get_team_loop( $query, $args );
		}

		// Close wrapper.
		$output .= '</div>';

		if ( true == $args['pager'] ) {
			$output .= get_the_posts_pagination();
		}

		$wp_query = null;
		$wp_query = $this->temp_query;

		/**
		 * Filters HTML-formatted team before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted team.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_team_html', $output, $query, $args );

		wp_reset_query();
		wp_reset_postdata();

		if ( true != $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;

		/**
		 * Fires after the team listing.
		 *
		 * This hook fires only when "echo" is set to true.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_team_after', $args );
	}

	/**
	 * Get team.
	 *
	 * @since  1.0.0
	 * @param  array|string $args Arguments to be passed to the query.
	 * @return array|bool         Array if true, boolean if false.
	 */
	public function get_team( $args = '' ) {

		$defaults = array(
			'limit'   => 5,
			'orderby' => 'date',
			'order'   => 'DESC',
			'id'      => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments to be passed to the query.
		 */
		$args = apply_filters( 'cherry_get_team_args', $args );

		// The Query Arguments.
		$this->query_args['post_type']        = cherry_team_members_init()->name();
		$this->query_args['posts_per_page']   = $args['limit'];
		$this->query_args['orderby']          = $args['orderby'];
		$this->query_args['order']            = $args['order'];
		$this->query_args['suppress_filters'] = false;

		if ( ! empty( $args['group'] ) ) {
			$group = str_replace( ' ', ',', $args['group'] );
			$group = explode( ',', $group );

			if ( is_array( $group ) ) {
				$this->query_args['tax_query'] = array(
					array(
						'taxonomy' => 'group',
						'field'    => 'slug',
						'terms'    => $group,
					),
				);
			}
		} else {
			$this->query_args['tax_query'] = false;
		}

		if ( isset( $args['pager'] ) && ( 'true' == $args['pager'] ) ) :

			if ( get_query_var( 'paged' ) ) {
				$this->query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$this->query_args['paged'] = get_query_var( 'page' );
			} else {
				$this->query_args['paged'] = 1;
			}

		endif;

		$ids = explode( ',', $args['id'] );

		if ( 0 < intval( $args['id'] ) && 0 < count( $ids ) ) :

			$ids = array_map( 'intval', $ids );

			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {

				$this->query_args['p'] = intval( $args['id'] );

			} else {

				$this->query_args['ignore_sticky_posts'] = 1;
				$this->query_args['post__in']            = $ids;

			}

		endif;

		$orderby_whitelist = array(
			'none',
			'ID',
			'author',
			'title',
			'date',
			'modified',
			'parent',
			'rand',
			'comment_count',
			'menu_order',
			'meta_value',
			'meta_value_num',
		);

		// Whitelist checks.
		if ( ! in_array( $this->query_args['orderby'], $orderby_whitelist ) ) {
			$this->query_args['orderby'] = 'date';
		}
		if ( ! in_array( strtoupper( $this->query_args['order'] ), array( 'ASC', 'DESC' ) ) ) {
			$this->query_args['order'] = 'DESC';
		}

		/**
		 * Filters the query.
		 *
		 * @since 1.0.0
		 * @param array The array of query arguments.
		 * @param array The array of arguments to be passed to the query.
		 */
		$this->query_args = apply_filters( 'cherry_get_team_query_args', $this->query_args, $args );

		// The Query.
		$query = new WP_Query( $this->query_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		return $query;

	}

	/**
	 * Get the image URL for the given ID. If no featured image, check for Gravatar e-mail.
	 *
	 * @since  1.0.0
	 * @param  int              $id   The post ID.
	 * @param  string|array|int $size The image dimension.
	 * @return string
	 */
	public function get_image_url( $id, $size ) {

		if ( ! has_post_thumbnail( $id ) ) {
			return false;
		}

		$image = '';

		$size = absint( $size );
		// If not a string or an array, and not an integer, default to 150x9999.
		if ( 0 < $size ) {
			$size = array( $size, $size );
		} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
			$size = array( 50, 50 );
		}

		$image_id = get_post_thumbnail_id( intval( $id ) );
		$image    = wp_get_attachment_image_src( $image_id, $size );

		if ( ! is_array( $image ) ) {
			return false;
		}

		return $image[0];
	}

	/**
	 * Get team items.
	 *
	 * @since  1.0.0
	 * @param  array $query WP_query object.
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_team_loop( $query, $args ) {

		global $post, $more;

		// Item template.
		$template = cherry_team_members_templater()->get_template_by_name(
			$args['template'],
			Cherry_Team_Members_Shortcode::$name
		);

		/**
		 * Filters template for team item.
		 *
		 * @since 1.0.0
		 * @param string.
		 * @param array   Arguments.
		 */
		$template = apply_filters( 'cherry_team_item_template', $template, $args );

		$count  = 1;
		$output = '';

		if ( ! is_object( $query ) || ! is_array( $query->posts ) ) {
			return false;
		}

		$callbacks = cherry_team_members_templater()->setup_template_data( $args );

		foreach ( $query->posts as $post ) {

			// Sets up global post data.
			setup_postdata( $post );

			$tpl       = $template;
			$post_id   = $post->ID;
			$link      = get_permalink( $post_id );

			$this->replace_args['link'] = $link;

			$tpl = cherry_team_members_templater()->parse_template( $tpl );

			$item_classes   = array( $args['item_class'], 'item-' . $count, 'clearfix' );
			$item_classes[] = ( $count % 2 ) ? 'odd' : 'even';

			foreach ( array( 'col_xs', 'col_sm', 'col_md', 'col_lg' ) as $col ) {

				if ( ! $args[ $col ] || 'none' == $args[ $col ] ) {
					continue;
				}

				$cols = absint( $args[ $col ] );

				if ( 12 < $cols ) {
					$cols = 12;
				}

				if ( 0 === $cols ) {
					$cols = 1;
				}

				$item_classes[] = str_replace( '_', '-', $col ) . '-' . absint( $args[ $col ] );
				$item_classes[] = ( ( $count - 1 ) % floor( 12 / $cols ) ) ? '' : 'clear-' . str_replace( '_', '-', $col );
			}

			$count++;

			$item_class = implode( ' ', array_filter( $item_classes ) );

			$output .= '<div id="team-' . $post_id . '" class="' . $item_class . '">';

				/**
				 * Filters team items.
				 *
				 * @since 1.0.0
				 * @param string.
				 * @param array  A post meta.
				 */
				$tpl = apply_filters( 'cherry_get_team_loop', $tpl );

				$output .= $tpl;

			$output .= '</div><!--/.team-item-->';

			$callbacks->clear_data();

		}

		// Restore the global $post variable.
		wp_reset_postdata();

		return $output;
	}

	/**
	 * Genereate microdata markup for team member single page
	 * JSON-LD markup is used for microdata formatting
	 *
	 * @since  1.0.0
	 */
	public function microdata_markup() {

		if ( ! is_singular( cherry_team_members_init()->name() ) ) {
			return;
		}

		$post_id = get_the_id();

		$result = '<script type="application/ld+json">%s</script>';

		$data = array(
			'@context' => 'http://schema.org/',
			'@type'    => 'Person',
			'name'     => get_the_title( $post_id ),
		);

		$image = $this->get_image_url( $post_id, 150 );

		if ( $image ) {
			$data['image'] = $image;
		}

		$relations = array(
			'cherry-team-position'  => 'jobTitle',
			'cherry-team-location'  => 'workLocation',
			'cherry-team-telephone' => 'telephone',
		);

		foreach ( $relations as $meta_key => $datakey ) {

			$meta = get_post_meta( $post_id, $meta_key, true );

			if ( ! $meta ) {
				continue;
			}

			$data[ $datakey ] = $meta;
		}

		printf( $result, json_encode( $data ) );

	}

	/**
	 * Callback function for social array walker
	 *
	 * @since  1.0.5
	 * @param  array $socials socials array.
	 * @return array
	 */
	public function get_social_urls( $socials ) {

		$urls = array();

		if ( ! is_array( $socials ) ) {
			return $urls;
		}

		foreach ( $socials as $key => $data ) {
			if ( ! isset( $data['external-link'] ) ) {
				continue;
			}

			$urls[] = esc_url( $data['external-link'] );
		}

		return $urls;

	}

	/**
	 * Get CSS class name for shortcode by template name
	 *
	 * @since  1.0.5
	 * @param  string $template template name.
	 * @return string|bool false
	 */
	public function get_template_class( $template ) {

		if ( ! $template ) {
			return false;
		}

		// Use the same filter for all cherry-related shortcodes
		$prefix = apply_filters( 'cherry_shortcodes_template_class_prefix', 'template' );
		$class  = sprintf( '%s-%s', esc_attr( $prefix ), esc_attr( str_replace( '.tmpl', '', $template ) ) );

		return $class;
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
