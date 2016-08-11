<?php
/**
 * Options Page
 *
 * @package    Cherry Team
 * @subpackage View
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>

<form id="cherry-team-options-form" method="post">
	<div class="cherry-team-options-page-wrapper">
		<div class="cherry-team-options-list-wrapper">
		<?php foreach ( $__data['settings']['ui-settings'] as $key => $settings ) { ?>
				<div class="option-section <?php echo $settings['master']; ?>">
					<div class="option-info-wrapper">
						<h3 class="option-title"><?php echo $settings['title']; ?></h3>
						<span class="option-description"><?php echo $settings['description']; ?></span>
					</div>
					<div class="option-ui-element-wrapper">
						<?php echo $settings['ui-html']; ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="cherry-team-options-control-wrapper">
			<div id="cherry-team-save-options" class="custom-button save-button">
				<span> <?php echo $__data['settings']['labels']['save-button-text']; ?></span>
			</div>
			<div id="cherry-team-define-as-default" class="custom-button define-as-default-button">
				<span><?php echo $__data['settings']['labels']['define-as-button-text']; ?></span>
			</div>
			<div id="cherry-team-restore-options" class="custom-button restore-button">
				<span><?php echo $__data['settings']['labels']['restore-button-text']; ?></span>
			</div>
			<div class="cherry-spinner-wordpress">
				<div class="double-bounce-1"></div>
				<div class="double-bounce-2"></div>
			</div>
		</div>
		<div class="cherry-team-options-control-wrapper">
			<h3>Shortcode:</h3>
			<code>[cherry_team super_title="" title="" subtitle="" columns="3" columns_tablet="2" columns_phone="1" posts_per_page="6" group="" id="0" excerpt_length="20" more="true" more_text="More" more_url="#" ajax_more="true" pagination="false" show_name="true" show_photo="true" show_desc="true" show_position="true" show_social="true" show_filters="false" image_size="thumbnail" template="default" use_space="true" use_rows_space="true"]</code>
			<p>Parameters list:</p>
			<ul>
				<li><strong>super_title</strong> - <?php _e( 'Text before main title', 'cherry-team' ); ?></li>
				<li><strong>title</strong> - <?php _e( 'Main title', 'cherry-team' ); ?></li>
				<li><strong>subtitle</strong> - <?php _e( 'Text after main title', 'cherry-team' ); ?></li>
				<li><strong>columns</strong> - <?php _e( 'Columns number', 'cherry-team' ); ?></li>
				<li><strong>columns_tablet</strong> - <?php _e( 'Tablets columns number', 'cherry-team' ); ?></li>
				<li><strong>columns_phone</strong> - <?php _e( 'Phones columns number', 'cherry-team' ); ?></li>
				<li><strong>posts_per_page</strong> - <?php _e( 'Posts number to show', 'cherry-team' ); ?></li>
				<li><strong>group</strong> - <?php _e( 'Select posts from group (use goup slug, pass multiplie groups via comma)', 'cherry-team' ); ?></li>
				<li><strong>excerpt_length</strong> - <?php _e( 'Words number in excerpt', 'cherry-team' ); ?></li>
				<li><strong>more</strong> - <?php _e( 'Show more button', 'cherry-team' ); ?></li>
				<li><strong>more_text</strong> - <?php _e( 'More button text', 'cherry-team' ); ?></li>
				<li><strong>more_url</strong> - <?php _e( 'More button URL', 'cherry-team' ); ?></li>
				<li><strong>ajax_more</strong> - <?php _e( 'Use more as AJAX load more button', 'cherry-team' ); ?></li>
				<li><strong>pagination</strong> - <?php _e( 'Show pagination', 'cherry-team' ); ?></li>
				<li><strong>show_name</strong> - <?php _e( 'Show person name', 'cherry-team' ); ?></li>
				<li><strong>show_photo</strong> - <?php _e( 'Show person photo', 'cherry-team' ); ?></li>
				<li><strong>show_desc</strong> - <?php _e( 'Show person description', 'cherry-team' ); ?></li>
				<li><strong>show_position</strong> - <?php _e( 'Show person position', 'cherry-team' ); ?></li>
				<li><strong>show_social</strong> - <?php _e( 'Show person social profiles links', 'cherry-team' ); ?></li>
				<li><strong>show_filters</strong> - <?php _e( 'Show filters by groups before listing', 'cherry-team' ); ?></li>
				<li><strong>image_size</strong> - <?php _e( 'Person photo size', 'cherry-team' ); ?></li>
				<li><strong>template</strong> - <?php _e( 'Template name to use (default or grid-boxes)', 'cherry-team' ); ?></li>
				<li><strong>use_space</strong> - <?php _e( 'Use space between columns', 'cherry-team' ); ?></li>
				<li><strong>use_rows_space</strong> - <?php _e( 'Use space between rows', 'cherry-team' ); ?></li>
			</ul>
		</div>
	</div>
</form>