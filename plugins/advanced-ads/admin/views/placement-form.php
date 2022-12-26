<?php
/**
 * Render form to create new placements.
 *
 * @var array $placement_types types of placements.
 */
?>
<form method="POST" class="advads-placements-new-form advads-form" id="advads-placements-new-form">
	<h3>1. <?php esc_html_e( 'Choose a placement type', 'advanced-ads' ); ?></h3>
	<p class="description">
		<?php
		printf(
			wp_kses(
			// translators: %s is a URL.
				__( 'Placement types define where the ad is going to be displayed. Learn more about the different types from the <a href="%s">manual</a>', 'advanced-ads' ),
				[
					'a' => [
						'href' => [],
					],
				]
			),
			esc_url( ADVADS_URL ) . 'manual/placements/#utm_source=advanced-ads&utm_medium=link&utm_campaign=placements'
		);
		?>
	</p>
	<?php require_once 'placement-types.php'; ?>
	<?php

	// show Pro placements if Pro is not activated.
	if ( ! defined( 'AAP_VERSION' ) ) :
		include ADVADS_BASE_PATH . 'admin/views/upgrades/pro-placements.php';
	else :
		?>
		<div class="clear"></div>
		<?php
	endif;
	?>
	<p class="advads-notice-inline advads-error advads-form-type-error"><?php esc_html_e( 'Please select a type.', 'advanced-ads' ); ?></p>
	<br/>
	<h3>2. <?php esc_html_e( 'Choose a Name', 'advanced-ads' ); ?></h3>
	<p>
		<input name="advads[placement][name]" class="advads-form-name" type="text" value="" placeholder="<?php esc_html_e( 'Placement Name', 'advanced-ads' ); ?>" />
		<span class="advads-help">
			<span class="advads-tooltip">
				<?php esc_html_e( 'The name of the placement is only visible to you. Tip: choose a descriptive one, e.g. Below Post Headline.', 'advanced-ads' ); ?>
			</span>
		</span>
	</p>
	<p class="advads-notice-inline advads-error advads-form-name-error"><?php esc_html_e( 'Please enter a name.', 'advanced-ads' ); ?></p>
	<h3>3. <?php esc_html_e( 'Choose the Ad or Group', 'advanced-ads' ); ?></h3>
	<p><select name="advads[placement][item]">
			<option value=""><?php esc_html_e( '--not selected--', 'advanced-ads' ); ?></option>
			<?php if ( isset( $items['groups'] ) ) : ?>
				<optgroup label="<?php esc_html_e( 'Ad Groups', 'advanced-ads' ); ?>">
					<?php foreach ( $items['groups'] as $_item_id => $_item_title ) : ?>
						<option value="<?php echo esc_attr( $_item_id ); ?>"><?php echo esc_html( $_item_title ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endif; ?>
			<?php if ( isset( $items['ads'] ) ) : ?>
				<optgroup label="<?php esc_html_e( 'Ads', 'advanced-ads' ); ?>">
					<?php foreach ( $items['ads'] as $_item_id => $_item_title ) : ?>
						<option value="<?php echo esc_attr( $_item_id ); ?>"><?php echo esc_html( $_item_title ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endif; ?>
		</select></p>
	<?php wp_nonce_field( 'advads-placement', 'advads_placement', true ); ?>
</form>
