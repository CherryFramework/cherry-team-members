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
class Cherry_Team_Members_Admin_Columns {

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

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Team" screen.
		add_filter( 'manage_edit-team_columns',        array( $this, 'edit_team_columns' ) );
		add_action( 'manage_team_posts_custom_column', array( $this, 'manage_team_columns' ), 10, 2 );

		add_filter( 'post_row_actions', array( $this, 'duplicate_link' ), 10, 2 );
		add_action( 'admin_action_cherry_team_clone_post', array( $this, 'duplicate_post_as_draft' ) );


	}

	/**
	 * Add 'Clone' link into posts actions list
	 *
	 * @param  array  $actions Available actions.
	 * @param  object $post    Current post.
	 * @return [type]          [description]
	 */
	public function duplicate_link( $actions, $post ) {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		if ( cherry_team_members_init()->name() !== $post->post_type ) {
			return $actions;
		}

		$url = add_query_arg(
			array(
				'action' => 'cherry_team_clone_post',
				'post'   => $post->ID,
			),
			admin_url( 'admin.php' )
		);

		$actions['clone'] = sprintf(
			'<a href="%1$s" title="%3$s" rel="permalink">%2$s</a>',
			$url,
			__( 'Clone', 'cherry-team' ),
			__( 'Clone this post', 'cherry-team' )
		);

		return $actions;
	}

	/**
	 * Process post cloning
	 */
	function duplicate_post_as_draft() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( 'You don\'t have permissions to do this' );
		}

		if ( empty( $_REQUEST['action'] ) || 'cherry_team_clone_post' !== $_REQUEST['action'] ) {
			wp_die( 'Not allowed function call!' );
		}

		if ( empty( $_REQUEST['post'] ) ) {
			wp_die( 'No post to duplicate has been supplied!' );
		}

		global $wpdb;

		$post_id         = absint( $_REQUEST['post'] );
		$post            = get_post( $post_id );
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if ( ! $post ) {
			wp_die( 'Post creation failed, could not find original post: ' . $post_id );
		}

		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);

		$new_post_id = wp_insert_post( $args );

		$post_terms = wp_get_object_terms( $post_id, 'group', array( 'fields' => 'slugs' ) );
		wp_set_object_terms( $new_post_id, $post_terms, 'group', false );

		$post_meta_infos = $wpdb->get_results(
			"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = $post_id"
		);

		if ( 0 !== count( $post_meta_infos ) ) {

			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta_infos as $meta_info ) {

				$meta_key        = $meta_info->meta_key;
				$meta_value      = addslashes( $meta_info->meta_value );
				$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";

			}

			$sql_query.= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );
		}

		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	}

	/**
	 * Adds a custom filter on 'request' when viewing the "Testimonials" screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'team' === $screen->post_type ) {
			add_action( 'admin_head', array( $this, 'print_styles' ) );
		}
	}

	/**
	 * Style adjustments for the manage menu items screen.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function print_styles() {
		?>
		<style type="text/css">
		.edit-php .wp-list-table td.thumbnail.column-thumbnail,
		.edit-php .wp-list-table th.manage-column.column-thumbnail,
		.edit-php .wp-list-table td.author_name.column-author_name,
		.edit-php .wp-list-table th.manage-column.column-author_name {
			text-align: center;
		}
		</style>
		<?php
	}

	/**
	 * Filters the columns on the "Team" screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns current post table columns.
	 * @return array
	 */
	public function edit_team_columns( $post_columns ) {
		unset(
			$post_columns['taxonomy-group'],
			$post_columns['date']
		);

		// Add custom columns.
		$post_columns['thumbnail'] = __( 'Photo', 'cherry-team' );
		$post_columns['position']  = __( 'Position', 'cherry-team' );
		$post_columns['group']     = __( 'Group', 'cherry-team' );
		$post_columns['date']      = __( 'Added', 'cherry-team' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column  current post list categories.
	 * @param  int    $post_id current post ID.
	 * @return void
	 */
	public function manage_team_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'position' :

				$position = get_post_meta( $post_id, 'cherry-team-position', true );

				echo ! empty( $position ) ? $position : '&mdash;';

				break;

			case 'thumbnail' :

				$thumb = get_the_post_thumbnail( $post_id, array( 50, 50 ) );

				echo ! empty( $thumb ) ? $thumb : '&mdash;';

				break;

			case 'group' :

				echo get_the_term_list( $post_id, 'group', '', ', ', '' );

				break;

			default :
				break;
		}
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

Cherry_Team_Members_Admin_Columns::get_instance();
