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
	</div>
</form>