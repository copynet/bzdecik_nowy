<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	
    if (!function_exists('getInvoice'))
    {
        /**
         * for the ide syntax
         *
         * @return InvoicePost
         */
        function getInvoice($args)
        {
            return $args['invoice'];
        }
    }

	$invoice = getInvoice($args);
	$client = $invoice->getClient();
	
	$woocommerce_active = false;
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$woocommerce_active = true;
	}
	
	$client_options = array();
	
?>

<div class="form-wrap inspire-panel">
	<div class="form-field">
		<label><?php _e( 'Customer', 'flexible-invoices' ); ?></label>
		<div id="inspire_invoice_client_select_wrap">
			<select id="inspire_invoice_client_select">
				<option><?php _e( 'Select', 'flexible-invoices' ); ?></option>
				<?php $users = get_users( array( 'orderby' => 'user_name' ) ); ?>				
				<?php foreach ( $users as $user ): ?>
					<?php $user_meta = get_user_meta( $user->ID ); ?>
					<?php $client_options[$user->ID] = ''; ?>
					<?php if ( $woocommerce_active ) : ?>
						<?php
                            $company = '';
                            if ( isset( $user_meta['billing_company'] ) ) {
                                $company = $user_meta['billing_company'][0];
                            }
                        ?>
						<?php if ( !empty( $company ) ) : ?>
							<?php $client_options[$user->ID] .= $company . ', '; ?>
						<?php endif; ?>
    	    			<?php if ( isset( $user_meta['billing_first_name'] ) ) : ?>
    	    				<?php $billing_first_name = $user_meta['billing_first_name'][0]; ?>
    	    				<?php if ( !empty( $billing_first_name ) ) : ?>
    	    					<?php $client_options[$user->ID] .= $user_meta['billing_first_name'][0] . ' '; ?> 
    	    				<?php endif; ?>
    	    			<?php endif; ?>
    	    			<?php if ( isset( $user_meta['billing_last_name'] ) ) : ?>
    	    				<?php $billing_last_name = $user_meta['billing_last_name'][0]; ?>
    	    				<?php if ( !empty( $billing_last_name ) ) : ?>
    	    					<?php $client_options[$user->ID] .= $user_meta['billing_last_name'][0] . ', '; ?>
    	    				<?php endif; ?>
    	    			<?php endif; ?>
					<?php endif; ?>
					<?php $client_options[$user->ID] .= $user->first_name; ?>
					<?php $client_options[$user->ID] .= ' '; ?> 
					<?php $client_options[$user->ID] .= $user->last_name; ?> 
					<?php $client_options[$user->ID] .= ' (' . $user->user_login . ')'; ?>
				<?php endforeach; ?>
				<?php asort( $client_options ); ?>
				<?php foreach ( $client_options as $key => $value ) : ?>
					<option value="<?php echo $key; ?>">					
						<?php echo $value; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<button class="button get_user_data"><?php _e( 'Fetch client data', 'flexible-invoices' ); ?></button>
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_name"><?php _e( 'Client Name', 'flexible-invoices' ); ?></label>
        <input id="inspire_invoices_client_name" type="text" class="medium" name="client[name]" value="<?php echo esc_attr( @$client['name'] ); ?>" />
	</div>

	 <div class="form-field">
		<label for="inspire_invoices_client_street"><?php _e( 'Address', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_street" type="text" class="medium" name="client[street]" value="<?php echo esc_attr( @$client['street'] ); ?>" />
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_postcode"><?php _e( 'Zip code', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_postcode" type="text" class="medium" name="client[postcode]" value="<?php echo esc_attr( @$client['postcode'] ); ?>" />
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_city"><?php _e( 'City', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_city" type="text" class="medium" name="client[city]" value="<?php echo esc_attr( @$client['city'] ); ?>" />
	</div>

	 <div class="form-field">
		<label for="inspire_invoices_client_nip"><?php _e( 'VAT Number', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_nip" type="text" class="medium" name="client[nip]" value="<?php echo esc_attr( @$client['nip'] ); ?>" />
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_country"><?php _e( 'Country', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_country" type="text" class="medium" name="client[country]" value="<?php echo esc_attr( @$client['country'] ); ?>" />
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_phone"><?php _e( 'Phone', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_phone" type="text" class="medium" name="client[phone]" value="<?php echo esc_attr( @$client['phone'] ); ?>" />
	</div>

	<div class="form-field">
		<label for="inspire_invoices_client_email"><?php _e( 'Email', 'flexible-invoices' ); ?></label>
		<input id="inspire_invoices_client_email" type="text" class="medium" name="client[email]" value="<?php echo esc_attr( @$client['email'] ); ?>" />
	</div>
</div>
