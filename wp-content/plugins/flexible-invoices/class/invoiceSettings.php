<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class invoiceSettings extends inspire_pluginDependant3
    {

        public function __construct($plugin)
        {
            parent::__construct($plugin);

            add_action( 'admin_init', array($this, 'updateSettingsAction') );
            add_action( 'admin_menu', array($this, 'initAdminMenuAction') );

            add_action('wp_ajax_woocommerce-invoice-generate-report', array( $this, 'generateReportAction') );
        }

        public function initAdminMenuAction()
        {
            $invoices_page = add_submenu_page( 'edit.php?post_type=inspire_invoice', __( 'Invoices Settings', 'flexible-invoices' ),  __( 'Settings', 'flexible-invoices' ) , 'manage_options', 'invoices_settings', array( $this, 'renderInvoicesSettingsPage') );
        }

        /**
         * wordpress action
         *
         * renders invoices submenu page
         */
        public function renderInvoicesSettingsPage()
        {
            $current_tab = ( empty( $_GET['tab'] ) ) ? 'settings' : sanitize_text_field( urldecode( $_GET['tab'] ) );

            include 'wc-functions.php';

	        $docs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/faktury-wordpress-docs/' : 'https://www.wpdesk.net/docs/flexible-invoices-wordpress-docs/';
	        $docs_link .= '?utm_source=flexible-invoices-settings&utm_medium=link&utm_campaign=flexible-invoices-docs-link';

            echo $this->loadTemplate('submenu_invoices', 'settings', array(
                    'current_tab'   => $current_tab,
                    'plugin'        => $this->getPlugin(),
		            'docs_link'     => $docs_link,
                )
            );

        }

        /**
         * wordpress action
         *
         * should-be-protected method to save/update settings when changed by POST
         */
        public function updateSettingsAction()
        {
            if (!empty($_POST))
            {
                // checkboxes
                if (!empty($_POST['option_page']) && $_POST['option_page'] === 'inspire_invoices_settings' && (@$_REQUEST['tab'] == 'settings' || empty($_REQUEST['tab'])) )
                {

                	$order_start_invoice_number = get_option( 'inspire_invoices_order_start_invoice_number', '' );
                    update_option('inspire_invoices_tax_payer', '');
                    update_option('inspire_invoices_show_signatures', '');
                    update_option('inspire_invoices_hide_vat', '');
                    update_option('inspire_invoices_hide_vat_number', '');

                    foreach ($_POST[$this->getNamespace()] as $name => $value) {
	                    update_option('inspire_invoices_' . $name, sanitize_text_field( wp_unslash( ( $value ) ) ) );
                        if ( $name == 'payment_methods' ) {
                        	update_option('inspire_invoices_' . $name, sanitize_textarea_field( wp_unslash( ( $value ) ) ) );
                        }
	                    if ( $name == 'invoices_notice' ) {
		                    update_option('inspire_invoices_' . $name, sanitize_textarea_field( wp_unslash( ( $value ) ) ) );
	                    }
	                    if ( $name == 'company_address' ) {
		                    update_option('inspire_invoices_' . $name, sanitize_textarea_field( wp_unslash( ( $value ) ) ) );
	                    }
	                    if ( $name == 'order_number_prefix' ) {
		                    update_option('inspire_invoices_' . $name, $value );
	                    }
	                    if ( $name == 'order_number_suffix' ) {
		                    update_option('inspire_invoices_' . $name, $value );
	                    }
                    }

                    if ( $order_start_invoice_number != '' && $order_start_invoice_number != get_option( 'inspire_invoices_order_start_invoice_number', '' ) ) {
                    	update_option( 'inspire_invoices_order_start_invoice_number_timestamp', current_time( 'timestamp' ) );
                    }

                }
	            if (!empty($_POST['option_page']) && $_POST['option_page'] === 'inspire_invoices_settings' && (@$_REQUEST['tab'] == 'corrections') ) {
		            $correction_start_invoice_number = get_option( 'inspire_invoices_correction_start_invoice_number', '' );
		            update_option('inspire_invoices_enable_corrections', '');
		            foreach ($_POST[$this->getNamespace()] as $name => $value) {
			            update_option('inspire_invoices_' . $name, sanitize_text_field( wp_unslash( ( $value ) ) ) );
			            if ( $name == 'correction_prefix' ) {
				            update_option('inspire_invoices_' . $name, $value );
			            }
			            if ( $name == 'correction_suffix' ) {
				            update_option('inspire_invoices_' . $name, $value );
			            }
		            }
		            if ( $correction_start_invoice_number != '' && $correction_start_invoice_number != get_option( 'inspire_invoices_correction_start_invoice_number', '' ) ) {
			            update_option( 'inspire_invoices_correction_start_invoice_number_timestamp', current_time( 'timestamp' ) );
		            }
	            }
                if (!empty($_POST['option_page']) && $_POST['option_page'] === 'inspire_invoices_settings' && (@$_REQUEST['tab'] == 'currency') ) {
                    update_option('inspire_invoices_currency', $_POST['inspire_invoices_currency'] );
                }
                if (!empty($_POST['option_page']) && $_POST['option_page'] === 'inspire_invoices_settings' && (@$_REQUEST['tab'] == 'tax') ) {

                	include 'wc-functions.php';

                	$inspire_invoices_tax = array();
                	if ( isset( $_POST['inspire_invoices_tax'] ) ) {
                		$inspire_invoices_tax = $_POST['inspire_invoices_tax'];
                		foreach ( $inspire_invoices_tax as $key => $val ) {
                			$inspire_invoices_tax[$key]['rate'] = wc_format_decimal( $inspire_invoices_tax[$key]['rate'] );
                		}
                	}
                    update_option('inspire_invoices_tax', $inspire_invoices_tax );
                }
            }
        }

        public function getSettingValue($name, $default = null) {
        	$ret = parent::getSettingValue( $name, $default );
        	return esc_attr( $ret );
        }

        public function generateReportAction() {

	        $currency = $_GET['currency'];

	        $currency_decimal_separator = '.';

	        $inspire_invoices_currency = get_option('inspire_invoices_currency', array() );
	        if ( is_array( $inspire_invoices_currency ) ) {
		        foreach ( $inspire_invoices_currency as $currency_config ) {
			        if ( $currency_config['currency'] == $currency ) {
				        $currency_decimal_separator  = $currency_config['decimal_separator'];
				        break;
			        }
		        }
	        }

		    echo $this->loadTemplate('generated_report', 'invoice', array(
                'plugin' => $this->getPlugin(),
			    'currency_decimal_separator' => $currency_decimal_separator
            ));
            die();
        }



    }
