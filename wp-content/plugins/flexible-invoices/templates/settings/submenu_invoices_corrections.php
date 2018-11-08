<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
	global $woocommerce;
?>
<form action="" method="post">
	<?php settings_fields( 'inspire_invoices_settings' ); ?>

 	<?php if (!empty($_POST['option_page']) && $_POST['option_page'] === 'inspire_invoices_settings'): ?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Settings saved.', 'flexible-invoices' ); ?></strong></p></div>
	<?php endif; ?>

	<h3><?php _e( 'Correction Settings', 'flexible-invoices' ); ?></h3>

    <p><a href="<?php echo $args['docs_link']; ?>" target="_blank"><?php _e( 'Read user\'s manual &rarr;', 'flexible-invoices' ); ?></a></p>

	<table class="form-table">
		<tbody>
            <tr valign="top">
                <th class="titledesc" scope="row"><?php _e( 'Automatic Corrections', 'flexible-invoices' ); ?></th>

                <td class="forminp forminp-checkbox">
                    <label for="inspire_enable_corrections"> <input <?php if($this->getSettingValue('enable_corrections') == 'on'):  ?>checked="checked"<?php endif; ?> id="inspire_enable_corrections" name="inspire_invoices[enable_corrections]" type="checkbox" /> <?php _e( 'Enable automatic corrections generation for order refunds.', 'flexible-invoices' ); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_number_reset_type"><?php _e( 'Correction Number Reset', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <select id="inspire_invoices_correction_number_reset_type" name="inspire_invoices[correction_number_reset_type]">
                        <option value="year" <?php echo $this->getSettingValue('correction_number_reset_type', 'year') == 'year' ? 'selected' : '' ; ?>><?php _e( 'Yearly', 'flexible-invoices' ); ?></option>
                        <option value="month" <?php echo $this->getSettingValue('correction_number_reset_type', 'year') == 'month' ? 'selected' : '' ; ?>><?php _e( 'Monthly', 'flexible-invoices' ); ?></option>
                        <option value="none" <?php echo $this->getSettingValue('correction_number_reset_type', 'year') == 'none' ? 'selected' : '' ; ?>><?php _e( 'None', 'flexible-invoices' ); ?></option>
                    </select>
                    <br/>
                    <span class="description"><?php _e( 'Select when to reset the correction number to 1.', 'flexible-invoices' ); ?></span>
                    <!-- Last number date = <?php echo get_option( 'inspire_invoices_correction_start_invoice_number_timestamp', '' ) != '' ? date( 'd.m.Y', get_option( 'inspire_invoices_correction_start_invoice_number_timestamp' ) ) : ''; ?> -->
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_correction_start_number"><?php _e( 'Next Correction Number', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <input value="<?php echo $this->getSettingValue('correction_start_invoice_number', 1); ?>" id="inspire_invoices_correction_start_number" name="inspire_invoices[correction_start_invoice_number]" type="text" />
        			<br />
                    <span class="description"><?php _e( 'Enter the next correction number. Default value is 1 and changes every time an correction is issued. Existing corrections won\'t be changed.', 'flexible-invoices' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_correction_prefix"><?php _e( 'Correction Prefix', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <input value="<?php echo $this->getSettingValue('correction_prefix', __( 'Corrected invoice ', 'flexible-invoices' )); ?>" id="inspire_invoices_correction_prefix" name="inspire_invoices[correction_prefix]" type="text" />
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_correction_suffix"><?php _e( 'Correction Suffix', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <input value="<?php echo $this->getSettingValue('correction_suffix', __( '/{MM}/{YYYY}', 'flexible-invoices' ) ); ?>" id="inspire_invoices_correction_suffix" name="inspire_invoices[correction_suffix]" type="text" />
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_correction_default_due_time"><?php _e( 'Correction Default Due Time', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <input value="<?php echo $this->getSettingValue('correction_default_due_time', 0 ); ?>" id="inspire_invoices_correction_default_due_time" name="inspire_invoices[correction_default_due_time]" type="text" />
                </td>
            </tr>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label for="inspire_invoices_correction_reason"><?php _e( 'Correction Reason', 'flexible-invoices' ); ?></label>
                </th>

                <td class="forminp forminp-text">
                    <input value="<?php echo $this->getSettingValue('correction_reason', __( 'Refund', 'flexible-invoices' ) ); ?>" id="inspire_invoices_correction_reason" name="inspire_invoices[correction_reason]" type="text" />
                </td>
            </tr>
		</tbody>
	</table>

	<?php do_action('inspire_invoices_after_display_tab_settings'); ?>

	<p class="submit"><input type="submit" value="<?php _e( 'Save changes', 'flexible-invoices' ); ?>" class="button button-primary" id="submit" name=""></p>
</form>
