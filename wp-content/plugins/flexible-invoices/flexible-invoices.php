<?php
/*
	Plugin Name: Flexible Invoices for WordPress
	Plugin URI: https://wordpress.org/plugins/flexible-invoices/
	Description: Invoicing for WordPress made simple. Available <a href="https://www.wpdesk.net/products/flexible-invoices-woocommerce/" target="_blank">extension for WooCommerce</a>.
	Version: 3.8.4
	Author: WP Desk
	Author URI: https://www.wpdesk.net/
	Text Domain: flexible-invoices
	Domain Path: /lang/
	Requires at least: 4.5
    Tested up to: 4.9.8
    WC requires at least: 3.1.0
    WC tested up to: 3.5.0

	Copyright 2017 WP Desk Ltd.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	ini_set('user_agent', 'mpdf');

	require_once('class/inspire/plugin3.php');
	require_once('class/inspire/pluginDependant3.php');
	require_once('class/inspire/pluginPostTypeFactory3.php');
	require_once('class/inspire/pluginPostType3.php');

	require_once('class/invoicePostType.php');
	require_once('class/invoicePost.php');

	require_once('class/invoiceSettings.php');
	require_once('class/invoiceUser.php');

	require_once('class/core-functions.php');

	require_once('class/tracker.php');

	require_once( __DIR__ . '/vendor/autoload.php' );

	//require_once('invoices_admin.php');

	class Invoice extends inspire_Plugin3 {

	    private static $_oInstance = false;

		private $script_version = '3.8.4';

		protected $_pluginNamespace = 'inspire_invoices';
		protected $_textDomain = 'flexible-invoices';

		protected $_templatePath = 'templates/flexible-invoices';

		//protected $_invoicesAdmin;
		public $invoicePostType;

		public function __construct() {
			$this->_initBaseVariables();

			$this->invoicePostType = new invoicePostType($this);
			$this->invoiceSettings = new invoiceSettings($this);
			$this->invoiceUser = new invoiceUser($this);

			// load locales
			load_plugin_textdomain( 'flexible-invoices', FALSE, dirname( plugin_basename(__FILE__) ) . '/lang/' );

			if ( is_admin() ) {
			    add_action( 'admin_enqueue_scripts', array($this, 'initAdminCssAction'), 75 );
			    add_action( 'admin_enqueue_scripts', array($this, 'initAdminJsAction'), 75 );
			}

			add_action( 'init', array( $this, 'init' ) );

			// Activate
			register_activation_hook( __FILE__, array( $this, 'pluginActivated' ) );

            // Templates Path
            $this->_templatePath = 'flexible-invoices';

            // invoice numbering actions and filters
			add_filter( 'pre_option_' . $this->_pluginNamespace . '_order_start_invoice_number', array( $this, 'pre_option_inspire_invoices_order_start_invoice_number' ), 10, 2 );
			add_filter( 'option_' . $this->_pluginNamespace . '_order_start_invoice_number', array( $this, 'option_inspire_invoices_start_invoice_number' ), 10, 2 );
			add_filter( 'option_' . $this->_pluginNamespace . '_correction_start_invoice_number', array( $this, 'option_inspire_invoices_start_invoice_number' ), 10, 2 );
		}

		public function pre_option_inspire_invoices_order_start_invoice_number( $ret, $option ) {
			/*
			global $wpdb;
			$wpdb->query(
				'LOCK TABLES ' . $wpdb->options .' WRITE, '
				. $wpdb->posts . ' WRITE, '
				. $wpdb->postmeta . ' WRITE, '
				. $wpdb->prefix . 'woocommerce_order_items WRITE '
			);
			//$wpdb->query( 'FLUSH TABLES WITH READ LOCK' );
			*/
			return $ret;
		}

		public function option_inspire_invoices_start_invoice_number( $value, $option ) {
			global $wpdb;
			$wpdb->query(
				$wpdb->prepare( "UPDATE $wpdb->options SET option_value = option_value WHERE option_name = %s",	$option	)
			);
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
			if ( is_object( $row ) ) {
				$value = $row->option_value;
			}
			return $value;
		}

		public function loadTemplate($name, $path = '', $args = array()) {

			$args = array_merge( $this->_defaultViewArgs, array( 'textDomain', $this->_textDomain ), $args );
			$path = trim( $path, '/') ;
			if ( file_exists( $templateName = implode( '/', array( get_stylesheet_directory(), $this->getTemplatePath(), $path, $name . '.php') ) ) ) {
			} else {
				$templateName = implode( '/', array($this->_pluginPath, 'templates', $path, $name . '.php' ) );
			}

			ob_start();
			include($templateName);
			return ob_get_clean();
		}

		public function pluginActivated() {
		    // get options from old version of plugin
		    if ( !get_option( 'inspire_invoices_old_defaults_loaded' ) ) {
                update_option('inspire_invoices_company_name', get_option('woocommerce_invoices_company_name'));
                update_option('inspire_invoices_company_address', get_option('woocommerce_invoices_company_data'));
                update_option('inspire_invoices_company_data', get_option('woocommerce_invoices_company_data'));
                update_option('inspire_invoices_company_logo', get_option('woocommerce_invoices_company_logo'));
                update_option('inspire_invoices_bank_name', get_option('woocommerce_invoices_bank_name'));
                update_option('inspire_invoices_account_number', get_option('woocommerce_invoices_account_number'));
                update_option('inspire_invoices_notice', get_option('woocommerce_invoices_invoices_notice'));
                update_option('inspire_invoices_hide_vat', get_option('woocommerce_invoices_hide_vat'));
                update_option('inspire_invoices_hide_vat_number', get_option('woocommerce_invoices_hide_vat_number'));
                update_option('inspire_invoices_order_start_number', get_option('woocommerce_invoices_order_start_number'));
                update_option('inspire_invoices_order_number_prefix', get_option('woocommerce_invoices_order_number_prefix'));
                update_option('inspire_invoices_order_number_suffix', get_option('woocommerce_invoices_order_number_suffix'));
                update_option('inspire_invoices_company_nip', get_option('woocommerce_invoices_company_nip'));

                // WooCommerce
                update_option('inspire_invoices_sequential_orders', get_option('woocommerce_invoices_sequential_orders'));

                update_option('inspire_invoices_old_defaults_loaded', true);
		    }
		}

		public function init() {
			if ( !get_option( 'inspire_invoices_payment_name_updated' ) ) {

				$invoices = get_posts( array( 'post_type' => 'inspire_invoice', 'post_status' => 'any', 'posts_per_page' => -1 ) );

				$payment_methods = array(
						'transfer' => __('Bank transfer', 'flexible-invoices'),
						'cash' => __('Cash', 'flexible-invoices'),
						'orher' => __('Other', 'flexible-invoices')
				);

				foreach ( $invoices as $invoice ) {
					$_payment_method = get_post_meta( $invoice->ID, '_payment_method', true );
					$_payment_method_name = get_post_meta( $invoice->ID, '_payment_method_name', true );
					if ( $_payment_method_name == '' ) {
						$_wc_order_id = get_post_meta( $invoice->ID, '_wc_order_id', true );
						if ( $_wc_order_id != '' ) {
							$_payment_method_name = get_post_meta( $_wc_order_id, '_payment_method_title', true );
						}
						else {
							$_payment_method_name = $payment_methods[$_payment_method];
						}
						update_post_meta( $invoice->ID, '_payment_method_name', $_payment_method_name );
					}
				}

				update_option( 'inspire_invoices_payment_name_updated', true );
			}

			if ( !get_option( 'inspire_invoices_currency_updated' ) ) {
				update_option( 'inspire_invoices_currency', unserialize( 'a:3:{i:2;a:5:{s:8:"currency";s:3:"PLN";s:17:"currency_position";s:11:"right_space";s:18:"thousand_separator";s:1:" ";s:17:"decimal_separator";s:1:",";s:12:"num_decimals";s:1:"2";}i:1;a:5:{s:8:"currency";s:3:"USD";s:17:"currency_position";s:4:"left";s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";s:12:"num_decimals";s:1:"2";}i:3;a:5:{s:8:"currency";s:3:"EUR";s:17:"currency_position";s:4:"left";s:18:"thousand_separator";s:1:",";s:17:"decimal_separator";s:1:".";s:12:"num_decimals";s:1:"2";}}' ) );
				update_option( 'inspire_invoices_currency_updated', true );
			}

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( !get_option( 'inspire_invoices_currency_woo_updated' ) ) {
					$inspire_invoices_currency = get_option( 'inspire_invoices_currency', array() );
					$woo_currency = get_option( 'woocommerce_currency', '' );
					if ( $woo_currency != '' ) {
						$add_currency = true;
						foreach ( $inspire_invoices_currency as $inspire_currency ) {
							if ( $inspire_currency['currency'] == $woo_currency ) {
								$add_currency = false;
							}
						}
						if ( $add_currency ) {
							$inspire_invoices_currency[] = array(
									'currency' 				=> $woo_currency,
									'currency_position' 	=> get_option( 'woocommerce_currency_pos', 'left' ),
									'thousand_separator'	=> get_option( 'woocommerce_price_thousand_sep', 'left' ),
									'decimal_separator'		=> get_option( 'woocommerce_price_decimal_sep', 'left' ),
									'num_decimals'			=> get_option( 'woocommerce_price_num_decimals', 'left' ),
							);
							update_option( 'inspire_invoices_currency', $inspire_invoices_currency );
						}
					}
					update_option( 'inspire_invoices_currency_woo_updated', true );
				}
			}

			if ( !get_option( 'inspire_invoices_tax_updated' ) ) {
				update_option( 'inspire_invoices_tax',  array(
						array( 'rate' => 23, 	'name' =>   '23%' ),
						array( 'rate' => 22, 	'name' =>   '22%' ),
						array( 'rate' => 21, 	'name' =>   '21%' ),
						array( 'rate' => 8, 	'name' =>    '8%' ),
						array( 'rate' => 7, 	'name' =>    '7%' ),
						array( 'rate' => 5, 	'name' =>    '5%' ),
						array( 'rate' => 3, 	'name' =>    '3%' ),
						array( 'rate' => 0, 	'name' =>    '0%' ),
						array( 'rate' => '0', 	'name' =>   'zw.' ),
						array( 'rate' => '0', 	'name' =>   'np.' ),
				));
				update_option( 'inspire_invoices_tax_updated', true );
			}
		}

		/**
		 * wordpress action
		 *
		 * inits css
		 */
		public function initAdminCssAction( $hook ) {
			$current_screen = get_current_screen();
			if ( in_array( $current_screen->id, array( 'inspire_invoice', 'edit-inspire_invoice', 'inspire_invoice_page_invoices_settings' ) ) ) {
			    wp_enqueue_style( 'flexible-invoices-admin-style', $this->getPluginUrl() . 'assets/css/admin.css', array(), $this->script_version );
			    wp_enqueue_style( 'flexible-invoices-admin-actions-style', $this->getPluginUrl() . 'assets/css/admin-order.css', array(), $this->script_version );
			    wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . '1.9.2' . '/themes/smoothness/jquery-ui.css', array(), $this->script_version );
			}

            if ( $current_screen->id == 'inspire_invoice' || $current_screen->id == 'edit-inspire_invoice' ) {
                wp_enqueue_style( 'fi-select2-style', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), $this->script_version );
            }

			if ( in_array( $current_screen->id, array( 'edit-shop_order', 'shop_order' ) ) ) {
			    wp_enqueue_style( 'flexible-invoices-admin-actions-style', $this->getPluginUrl() . 'assets/css/admin-order.css', array(), $this->script_version );
            }
		}

		/**
		 * wordpress action
		 *
		 * inits js
		 */
		public function initAdminJsAction() {

			$current_screen = get_current_screen();

		    wp_enqueue_script( 'jquery' );
		    wp_enqueue_script( 'jquery-ui' );
		    wp_enqueue_script( 'jquery-ui-datepicker' );

            if ( in_array( $current_screen->id, array( 'inspire_invoice', 'edit-inspire_invoice', 'inspire_invoice_page_invoices_settings', 'edit-shop_order', 'shop_order' ) ) ) {
		        $inspire_invoice_params = array(
                    'plugin_url'                    =>  $this->getPluginUrl(),
			        'message_generating'            => __( 'Generate, please wait ...', 'flexible-invoices' ), // Generowanie, proszę czekać...
			        'message_generating_successful' => __( 'Completed successfully.', 'flexible-invoices' ), // Zakończono pomyślnie.
                    'message_generating_error'      => __( 'An unexpected error occurred: ', 'flexible-invoices' ), // Wystąpił nieoczekiwaniy błąd:
                    'message_confirm'               => __( 'Note, all unsaved changes will be lost.', 'flexible-invoices' ), // Uwaga, wszystkie niezapisane zmiany zostaną utracone.
                    'message_invoice_sent'          => __( 'You have sent an invoice to: ', 'flexible-invoices' ), // Wysłano fakturę na adres:
			        'message_invoice_not_sent_woo'  => __( 'You can not send an invoice not issued for the WooCommerce order.', 'flexible-invoices' ), // Nie można wysłać faktury nie wystawionej dla zamówienia WooCommerce.
			        'message_not_sent'              => __( 'Could not send invoice.', 'flexible-invoices' ), // Nie udało się wysłać faktury.
					'message_not_saved_changes'     => __( 'Note, unsaved changes will not be included in the email you send.', 'flexible-invoices' ), // Uwaga, niezapisane zmiany nie zostaną uwzględnione w wysyłanym e-mailu.
		        );

		        wp_enqueue_script( 'inspire-invoice-admin', $this->getPluginUrl() . 'assets/js/admin.js', array('jquery', 'jquery-ui-datepicker') );
		        wp_localize_script( 'inspire-invoice-admin', 'inspire_invoice_params', $inspire_invoice_params );
            }

            if ( $current_screen->id == 'inspire_invoice' || $current_screen->id == 'edit-inspire_invoice' ) {
                wp_enqueue_script( 'fi-select2-script', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery', 'jquery-ui-sortable' ), '4.0.3' );
            }

		    if ( $current_screen->id == 'inspire_invoice_page_invoices_settings' ) {
		    	wp_enqueue_script( 'invoice_wc_tip_js', $this->getPluginUrl() . 'assets/js/woocommerce/jquery.tipTip.min.js', array('jquery') );
		    	wp_enqueue_script( 'invoice_wc_settings_js', $this->getPluginUrl() . 'assets/js/woocommerce/settings.min.js', array('jquery', 'jquery-ui-color', 'jquery-ui-sortable') );

		    	$locale  = localeconv();
	    		$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
	    		$mon_decimal = stripslashes( get_option( 'woocommerce_price_decimal_sep', '.' ) );
	    		$params = array(
	    			'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
	    			'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), $mon_decimal ),
	    			'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
	    			'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
	    			'decimal_point'                     => $decimal,
	    			'mon_decimal_point'                 => $mon_decimal
	    		);

	    		wp_enqueue_script( 'invoice_wc_admin', $this->getPluginUrl() . 'assets/js/woocommerce/woocommerce_admin.min.js', array('jquery', 'jquery-ui-sortable'), $this->script_version );
	    		wp_localize_script( 'invoice_wc_admin', 'woocommerce_admin', $params );
		    }
		}

		/**
		 * action_links function.
		 *
		 * @access public
		 * @param mixed $links
		 * @return void
		 */
		 public function linksFilter( $links ) {
		    $docs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/faktury-wordpress-docs/' : 'https://www.wpdesk.net/docs/flexible-invoices-wordpress-docs/';
		    $docs_link .= '?utm_source=wp-admin-plugins&utm_medium=quick-link&utm_campaign=flexible-invoices-docs-link';
            $support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';

            $plugin_links = array(
               '<a href="' . admin_url( 'edit.php?post_type=inspire_invoice&page=invoices_settings') . '">' . __( 'Settings', 'flexible-invoices' ) . '</a>',
               '<a href="' . $docs_link . '" target="_blank">' . __( 'Docs', 'flexible-invoices' ) . '</a>',
               '<a href="' . $support_link . '" target="_blank">' . __( 'Support', 'flexible-invoices' ) . '</a>'
            );

            $pro_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/faktury-woocommerce/' : 'https://www.wpdesk.net/products/flexible-invoices-woocommerce/';
            $utm = '?utm_source=wp-admin-plugins&utm_medium=link&utm_campaign=flexible-invoices-plugins-upgrade-link';

            if ( ! is_flexible_invoices_woocommerce_active() )
                $plugin_links[] = '<a href="' . $pro_link . $utm . '" target="_blank" style="color:#d64e07;font-weight:bold;">' . __( 'Upgrade for WooCommerce', 'flexible-invoices' ) . '</a>';

            return array_merge( $plugin_links, $links );
        }

		public static function getInstance() {
		    if ( self::$_oInstance == false ) {
		        self::$_oInstance = new Invoice();
		    }
		    return self::$_oInstance;
		}
	}

    /**
     * Checks if Flexible Invoices for WooCommerce is active
     *
     */
	function is_flexible_invoices_woocommerce_active() {
		return is_plugin_active( 'flexible-invoices-woocommerce/flexible-invoices-woocommerce.php' );
	}

    /**
     * Checks if plugin is active
     *
     */
    if ( ! function_exists( 'wpdesk_is_plugin_active' ) ) {
    	function wpdesk_is_plugin_active( $plugin_file ) {

    		$active_plugins = (array) get_option( 'active_plugins', array() );

    		if ( is_multisite() ) {
    			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
    		}

    		return in_array( $plugin_file, $active_plugins ) || array_key_exists( $plugin_file, $active_plugins );
    	}
    }

	add_action( 'plugins_loaded', 'flexible_invoices_plugins_loaded', 9 );
	function flexible_invoices_plugins_loaded() {
		if ( ! function_exists( 'should_enable_wpdesk_tracker' ) ) {
			function should_enable_wpdesk_tracker() {
				$tracker_enabled = true;
				if ( ! empty( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] === '127.0.0.1' ) {
					$tracker_enabled = false;
				}

				return apply_filters( 'wpdesk_tracker_enabled', $tracker_enabled );
			}
		}

		$tracker_factory = new WPDesk_Tracker_Factory();
		$tracker_factory->create_tracker( basename( dirname( __FILE__ ) ) );
	}


$_GLOBALS['inspire_invoices'] = $invoice = Invoice::getInstance();
