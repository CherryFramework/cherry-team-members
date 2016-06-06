<?php
/**
 * Ajax callbacks
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Team_Members_Ajax' ) ) {

	/**
	 * Define Cherry_Team_Members_Ajax class
	 */
	class Cherry_Team_Members_Ajax {

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
		 * Constructor for the class
		 */
		function __construct() {

			$this->data = Cherry_Team_Members_Data::get_instance();

			add_action( 'wp_ajax_cherry_team_filter_posts', array( $this, 'filter_posts' ) );
			add_action( 'wp_ajax_nopriv_cherry_team_filter_posts', array( $this, 'filter_posts' ) );

			add_action( 'wp_ajax_cherry_team_load_more', array( $this, 'more_posts' ) );
			add_action( 'wp_ajax_nopriv_cherry_team_load_more', array( $this, 'more_posts' ) );

			add_action( 'wp_ajax_cherry_team_pager', array( $this, 'pager' ) );
			add_action( 'wp_ajax_nopriv_cherry_team_pager', array( $this, 'pager' ) );
		}

		/**
		 * Validate boolean values
		 *
		 * @param  array $atts Attributes array.
		 * @return array
		 */
		public function validate_bool( $atts = array() ) {

			if ( empty( $atts ) ) {
				return $atts;
			}

			$bool_keys = array(
				'show_name',
				'show_photo',
				'show_desc',
				'show_position',
				'show_social',
				'show_filters',
				'ajax_more',
				'more',
				'pager',
				'use_space',
				'use_rows_space',
			);

			foreach ( $atts as $key => $value ) {

				if ( ! in_array( $key, $bool_keys ) ) {
					continue;
				}

				$atts[ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			}

			return $atts;
		}

		/**
		 * Maybe add selected groups into filter
		 *
		 * @return string
		 */
		public function maybe_add_groups() {

			$group = '';

			if ( isset( $_POST['groups'] ) ) {
				$group = esc_attr( $_POST['groups'] );
			}

			return $group;

		}

		/**
		 * Filter posts callback
		 *
		 * @return void
		 */
		public function filter_posts() {

			$group = ! empty( $_POST['group'] ) ? $_POST['group'] : 'all-groups';

			$atts = isset( $_POST['atts'] ) && is_array( $_POST['atts'] ) ? $_POST['atts'] : array();
			$atts = $this->validate_bool( $atts );

			$atts['echo']  = false;
			$atts['paged'] = 1;

			if ( 'all-groups' === $group ) {
				$atts['group'] = $this->maybe_add_groups();
			} else {
				$atts['group'] = $group;
			}

			$query  = $this->data->get_team( $atts );
			$result = $this->data->get_team_loop( $query, $atts );
			$pager  = '';

			if ( false === $atts['more'] && true === $atts['pager'] ) {
				$pager = $this->data->get_pagination( $query );
			}

			wp_send_json_success( array(
				'result' => $result,
				'atts'   => $atts,
				'pages'  => $query->max_num_pages,
				'pager'  => $pager,
			) );

		}

		/**
		 * Proces load more button
		 *
		 * @return void
		 */
		public function more_posts() {

			$page = ! empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

			$atts = isset( $_POST['atts'] ) && is_array( $_POST['atts'] ) ? $_POST['atts'] : array();
			$atts = $this->validate_bool( $atts );

			$atts['echo']  = false;
			$atts['paged'] = $page + 1;

			$query  = $this->data->get_team( $atts );
			$result = $this->data->get_team_loop( $query, $atts );

			wp_send_json_success( array(
				'result' => $result,
				'atts'   => $atts,
				'page'   => $atts['paged'],
			) );

		}

		/**
		 * Process pager calls
		 *
		 * @return void
		 */
		public function pager() {

			$page = ! empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

			$atts = isset( $_POST['atts'] ) && is_array( $_POST['atts'] ) ? $_POST['atts'] : array();
			$atts = $this->validate_bool( $atts );

			$atts['echo']  = false;
			$atts['paged'] = $page;
			$atts['pager'] = false;

			$query  = $this->data->get_team( $atts );
			$result = $this->data->get_team_loop( $query, $atts );

			$atts['pager'] = true;

			wp_send_json_success( array(
				'result' => $result,
				'atts'   => $atts,
				'page'   => $atts['paged'],
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

}

/**
 * Returns instance of Cherry_Team_Members_Ajax
 *
 * @return object
 */
function cherry_team_ajax() {
	return Cherry_Team_Members_Ajax::get_instance();
}

cherry_team_ajax();
