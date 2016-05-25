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
		<h1 class="page-title"><?php the_title(); ?></h1>
	<?php endif; ?>
	<?php

		global $wp_query;

		$args = array(
			'template'   => 'default.tmpl',
			'wrap_class' => 'team-wrap container',
			'container'  => '<div class="team-listing row">%s</div>',
			'item_class' => 'team-listing_item',
			'col_xs'     => '12',
			'col_sm'     => '6',
			'col_md'     => '4',
			'col_lg'     => false,
			'size'       => 'thumbnail',
			'pager'      => true,
			'limit'      => cherry_team_members()->get_option( 'posts-per-page', 10 ),
			'group'      => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
		);

		$data = new Cherry_Team_Members_Data;
		$data->the_team( $args );
	?>
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
