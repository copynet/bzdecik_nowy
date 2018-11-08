<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
	<div class="inspire-settings">
		<div class="inspire-main-content">
			<?php
    		    $settings_pages = apply_filters('inspire_invoices_settings_pages', array(
    		        'settings' => array(
		                'page' => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=settings',
		                'title' => __( 'Settings', 'flexible-invoices' )
		            ),
    		    	'currency' => array(
		                    'page' => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=currency',
		                    'title' => __( 'Currency', 'flexible-invoices' )
		            ),
    		    	'tax' => array(
		                    'page' => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=tax',
		                    'title' => __( 'Tax rates', 'flexible-invoices' )
		            ),
    		    	'reports' => array(
		                    'page' => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=reports',
		                    'title' => __( 'Reports', 'flexible-invoices' )
		            ),
					'generate' => array(
		                    'page' => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=generate',
		                    'title' => __( 'Download', 'flexible-invoices' )
		            ),
    		    ));

			    if ( is_flexible_invoices_woocommerce_active() ) {
				    $settings_pages['corrections'] = array(
					    'page'  => 'edit.php?post_type=inspire_invoice&page=invoices_settings&tab=corrections',
					    'title' => __( 'Corrections', 'flexible-invoices' )
				    );
			    }

			?>

			<h2 class="nav-tab-wrapper">
			   <?php foreach ($settings_pages as $key => $item): ?>
			       <a class="nav-tab <?php if ($args['current_tab'] === $key): ?>nav-tab-active<?php endif; ?>" href="<?php echo admin_url( $item['page'] ); ?>"><?php echo $item['title']; ?></a>
			   <?php endforeach; ?>

			</h2>

			<?php
			   if ($args['current_tab'] == 'settings') {
			       echo $this->loadTemplate('submenu_invoices_settings', 'settings', $args);
			   } elseif ($args['current_tab'] == 'currency') {
			       echo $this->loadTemplate('submenu_invoices_currency', 'settings', $args);
			   } elseif ($args['current_tab'] == 'tax') {
			       echo $this->loadTemplate('submenu_invoices_tax', 'settings', $args);
			   } elseif ($args['current_tab'] == 'reports') {
			       echo $this->loadTemplate('submenu_invoices_reports', 'settings', $args);
			   } elseif ($args['current_tab'] == 'generate') {
			       echo $this->loadTemplate('submenu_invoices_generate', 'settings', $args);
			   } elseif ($args['current_tab'] == 'corrections') {
				   echo $this->loadTemplate('submenu_invoices_corrections', 'settings', $args);
			   }
			 ?>

			<?php do_action( 'inspire_invoices_after_display_settings', $args['current_tab'] ); ?>
		</div>

		<div class="inspire-sidebar metabox-holder">
            <?php if ( ! is_flexible_invoices_woocommerce_active() ): ?>
			    <div class="stuffbox">
			        <h3 class="hndle"><?php _e( 'WooCommerce Integration', 'flexible-invoices' ); ?></h3>

                    <div class="inside">
                        <div class="main">
                            <p><?php printf( __( 'Using WooCommerce? Make sure to check our invoicing plugin - %sFlexible Invoices for WooCommerce%s.', 'flexible-invoices' ), '<a href="'. __( 'https://www.wpdesk.net/products/flexible-invoices-woocommerce/', 'flexible-invoices' ) .'?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-woocommerce-extension" target="_blank">', '</a>' ); ?></p>
                        </div>
                    </div>
			    </div>
            <?php endif; ?>

            <?php if ( is_flexible_invoices_woocommerce_active() ): ?>
                <div class="stuffbox">
                    <h3 class="hndle"><?php _e( 'Get more WP Desk Plugins!', 'flexible-invoices' ); ?></h3>

                    <div class="inside">
                        <div class="main">
                            <p><a href="<?php _e( 'https://www.wpdesk.net/products/flexible-shipping-pro-woocommerce/', 'flexible-invoices' ); ?>?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-shipping-pro-plugin" target="_blank"><?php _e( 'Flexible Shipping', 'flexible-invoices' ); ?></a> - <?php _e( 'Create shipping methods based on weight, totals and more.', 'flexible-invoices' ); ?></p>

                            <p><a href="<?php _e( 'https://www.wpdesk.net/products/active-payments-woocommerce/', 'flexible-invoices' ); ?>?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=active-payments-plugin" target="_blank"><?php _e( 'Active Payments', 'flexible-invoices' ); ?></a> - <?php _e( 'Conditionally display payment methods based on shipping.', 'flexible-invoices' ); ?></p>

                            <p><a href="<?php _e( 'https://www.wpdesk.net/products/woocommerce-checkout-fields/', 'flexible-invoices' ); ?>?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-checkout-fields-plugin" target="_blank"><?php _e( 'Flexible Checkout Fields', 'flexible-invoices' ); ?></a> - <?php _e( 'Manage WooCommerce checkout fields and add your own.', 'flexible-invoices' ); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
		</div>
	</div>
</div>
