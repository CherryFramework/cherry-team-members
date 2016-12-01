<?php
/**
 * The Template for displaying archive CPT Team.
 *
 * @package   Cherry_Team_Members
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'team' );
?>

	<?php
		/**
		 * Fires before main content output started
		 */
		do_action( 'cherry_team_before_main_content' );
	?>
	<?php if ( apply_filters( 'cherry_team_show_page_title', true ) ) : ?>
		<?php cherry_team_members_tools()->page_title( '<h1 class="page-title">%s</h1>' ); ?>
	<?php endif; ?>
	<div class="team-container">
	<?php

		global $wp_query;

		$cols = cherry_team_members_tools()->get_cols();

		$args = array(
			'template'   => cherry_team_members_tools()->get_template( 'listing' ),
			'wrap_class' => 'team-wrap cherry-team-container',
			'container'  => '<div class="team-listing cherry-team-row">%s</div>',
			'item_class' => 'team-listing_item',
			'col_xs'     => $cols['xs'],
			'col_sm'     => $cols['sm'],
			'col_md'     => $cols['md'],
			'col_xl'     => false,
			'size'       => cherry_team_members()->get_option( 'listing-image-size', 'thumbnail' ),
			'pager'      => true,
			'more'       => false,
			'limit'      => cherry_team_members()->get_option( 'posts-per-page', 10 ),
			'group'      => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
		);

		$data = new Cherry_Team_Members_Data;
		$data->the_team( $args );
	?>
	</div>
	<?php
		/**
		 * Fires after main content output
		 */
		do_action( 'cherry_team_after_main_content' );
	?>

	<?php
		/**
		 * Hook for placing page sidebar
		 */
		do_action( 'cherry_team_sidebar' );
	?>

<?php get_footer( 'team' );
