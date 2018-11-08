<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once('inspire/pluginPostTypeFactory3.php');
require_once('invoicePostTypeCapabilities.php');

class invoicePostType extends inspire_pluginPostTypeFactory3 {

    protected $_postType = 'inspire_invoice';
	protected $_prefix = "_invoice_";

    protected $_increase_number_on_save = false;
	protected $_increase_correction_number_on_save = false;

	/** @var invoicePostTypeCapabilities */
	private $capabilities;

	const WP_OPTION_INVOICE_CAPABILITIES_POPULATED = 'invoice_capabilities_populated';

	const INVOICE_CAPABILITIES_VERSION = 1;

	public function __construct(inspire_Plugin3 $plugin)
	{
	    parent::__construct($plugin);

	    $this->_plugin = $plugin;

		$this->capabilities = new invoicePostTypeCapabilities();

	    add_action('wp_ajax_invoice-get-invoice', array( $this, 'getInvoiceAction') );
	    add_action('wp_ajax_invoice-get-pdf-invoice', array( $this, 'getInvoicePdfAction') );
	    add_action('wp_ajax_nopriv_invoice-get-invoice', array( $this, 'getInvoiceAction') );
	    add_action('wp_ajax_nopriv_invoice-get-pdf-invoice', array( $this, 'getInvoicePdfAction') );

	    add_action('wp_ajax_invoice-get-client-data', array( $this, 'getClientDataAction') );
	    add_action('wp_ajax_invoice-send-by-email', array( $this, 'sendInvoiceByEmailAction') );

		add_action('wp_ajax_woocommerce-invoice-batch-generate', array( $this, 'batchGenerateAction') );
		add_action('wp_ajax_woocommerce-invoice-batch-download', array( $this, 'batchDownloadAction') );

	    //add_filter('manage_inspire_invoice_posts_columns' , array ($this, 'book_cpt_columns') );


	    if (is_admin())
	    {
	        add_filter('post_row_actions', array($this, 'removeQuickEditAction'), 10, 2);
	        // default values for new invoice
	        add_filter( 'default_title', array($this, 'newInvoiceDefaultTitleFilter'), 80, 2);

	        add_action('admin_init', array($this, 'setDefaultLayoutAction'));

		    add_action('admin_init', array($this->capabilities, 'assignBasicRolesCapabilitiesAction'));

	        add_action('restrict_manage_posts', array( $this, 'addInvoiceListingFiltersAction') );

	        add_filter('months_dropdown_results', array( $this, 'modifyInvoiceListingMonthsFilter'), 80, 2);

	        add_filter('parse_query', array( $this, 'filterInvoiceListingFilter') );

	        add_filter( 'bulk_actions-edit-inspire_invoice', array( $this, 'setBulkActionsFilter') );

		    add_action( 'admin_notices', array( $this, 'bulkActionsFilterAdminNotices') );

		    add_filter( 'handle_bulk_actions-edit-inspire_invoice', array( $this, 'setBulkActionsHandler' ), 10, 3 );

	        add_filter('post_updated_messages', array( $this, 'changeDefaultWordpressPostMessagesFilter' ) );

			add_action( 'save_post',  array( $this, 'updatePdfInvoiceAction' ), 10, 3  );

			add_action( 'before_delete_post', array( $this, 'removePdfInvoiceAction' )  );

			add_action( 'flexible_invoices_head', array( $this, 'head_scripts' ) );

	    }
	}

	public function updatePdfInvoiceAction( $id, $post, $update ) {

	    // We check if the global post type isn't ours and just return
	    global $post_type;

	    if ( $post_type != 'inspire_invoice' ) return;

        $invoice = $this->invoiceFactory( $id );

        $path = $this->getPdfPath($invoice);
        $file = $path . '/' . str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf';

        if( file_exists( $file ) ) {
            unlink( $file );
        }

        wp_mkdir_p( $path );
        $pdfData = $this->generatePdfFileContent($invoice);

        file_put_contents( $file, $pdfData );
	}

	public function removePdfInvoiceAction( $id ){

	    // We check if the global post type isn't ours and just return
	    global $post_type;
	    if ( $post_type != 'inspire_invoice' ) return;

			$invoice = $this->invoiceFactory( $id );

			$path = $this->getPdfPath($invoice);
			$file = $path . '/' . str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf';

			if( file_exists( $file ) ) {
				unlink( $file );
			}
	}

	public function changeDefaultWordpressPostMessagesFilter($messages)
	{
	    global $post, $post_ID;
	    $post_type = get_post_type( $post_ID );

	    $obj = get_post_type_object($post_type);
	    $singular = $obj->labels->singular_name;

		$messages['inspire_invoice'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Invoice updated.', 'flexible-invoices' ),
			2 => __('Custom field updated.', 'flexible-invoices' ),
			3 => __('Custom field deleted.', 'flexible-invoices' ),
			4 => __('Invoice updated.', 'flexible-invoices' ),
			5 => isset($_GET['revision']) ? sprintf( __($singular.' rolled back to revision %s.', 'flexible-invoices' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('Invoice issued.', 'flexible-invoices' ),
			7 => __('Invoice saved.', 'flexible-invoices' ),
			8 => __('Invoice submitted.', 'flexible-invoices' ),
			9 => __('Invoice scheduled', 'flexible-invoices' ),
			10 => __('Invoice draft updated', 'flexible-invoices' ),
		);

	    return $messages;
	}

	public function setBulkActionsFilter($actions)
	{
	    if ( isset( $actions['edit'] ) )
	    {
	        unset( $actions['edit'] );
	    }

	    $actions['set_as_payed'] = __('Paid', 'flexible-invoices');

	    return $actions;
	}

	public function setBulkActionsHandler( $redirect_to, $doaction, $post_ids ) {
		if ( $doaction !== 'set_as_payed' ) {
			return $redirect_to;
		}
		foreach ( $post_ids as $post_id ) {
			$invoice = $this->invoiceFactory($post_id);
			$invoice->setPaymentStatus('paid');
			$invoice->save();
		}
		$redirect_to = add_query_arg( 'bulk_set_as_payed', count( $post_ids ), $redirect_to );
		return $redirect_to;
	}

	public function bulkActionsFilterAdminNotices() {
		if ( ! empty( $_REQUEST['bulk_set_as_payed'] ) ) {
			$invoices_count = intval( $_REQUEST['bulk_set_as_payed'] );
			printf( '<div id="message" class="updated notice"><p>' .
			        _n( '%s invoice marked as paid.',
				        '%s invoices marked as paid.',
				        $invoices_count,
				        'flexible-invoices'
			        ) . '</p></div>', $invoices_count );
		}
    }


	public function filterInvoiceListingFilter($query)
	{
	    global $pagenow;
	    $qv = &$query->query_vars;
	    if ($pagenow == 'edit.php' && isset( $qv['post_type'] ) && $qv['post_type'] == 'inspire_invoice')
	    {
	        $meta_query = array();
	        if (!empty($_GET['paystatus']))
	        {
	            if ($_GET['paystatus'] == 'exceeded')
	            {
	                $meta_query[] = array(
                        'key' => '_payment_status',
                        'value' => 'topay',
                        'compare' => 'LIKE'
	                );
	                $meta_query[] = array(
	                        'key' => '_date_pay',
	                        'value' => strtotime(date('Y-m-d 00:00:00')),
	                        'compare' => '<'
	                );
	            } else {
	                $meta_query[] = array(
                        'key' => '_payment_status',
                        'value' => $_GET['paystatus'],
                        'compare' => 'LIKE'
	                );
	            }

	        }

	        if (!empty($_GET['user']))
	        {
	            $user = new WP_User($_GET['user']);
	            if (empty($user->billing_company))
    	        {
    	            $name = $user->billing_first_name . ' ' . $user->billing_last_name;
    	        } else{
    	            $name = $user->billing_company;
    	        }

	            $meta_query[] = array(
                    'key' => '_client_filter_field',
                    'value' => $name,
                    'compare' => 'LIKE'
	            );
	        }

	        if (!empty($_GET['m']))
	        {
	            unset($qv['m']);
	            $m = strtotime(substr($_GET['m'], 0, 4) . '-' . substr($_GET['m'], 4, 2) . '-01 00:00:00');

	            $meta_query[] = array(
                    'key' => '_date_issue',
                    'value' => array($m, strtotime(date('Y-m-t 23:59:59', $m))),
                    'compare' => 'BETWEEN',
	                'type' => 'UNSIGNED'
	            );
	        }
	        if (!empty($meta_query))
	        {
	            $qv['meta_query'] = $meta_query;
	        }
	    }

	    return $query;
	}

	public function modifyInvoiceListingMonthsFilter($months, $post_type)
	{
	    if ($post_type == 'inspire_invoice')
	    {
	        global $wpdb;

	        $months = $wpdb->get_results( $wpdb->prepare( "
	                SELECT DISTINCT YEAR( FROM_UNIXTIME( pm.meta_value ) ) AS year, MONTH( FROM_UNIXTIME ( pm.meta_value ) ) AS month
	                FROM
	                   $wpdb->posts p,
	                   $wpdb->postmeta pm
	                WHERE
	                   pm.post_id = p.id AND
	                   p.post_type = %s AND
	                   pm.meta_key = '_date_issue'
	                ORDER BY
	                   pm.meta_value DESC
	                ", $post_type ) );
	    }
	    return $months;

	}

	public function addInvoiceListingFiltersAction() {
	    global $typenow;
	    global $wp_query;
	    if ($typenow == 'inspire_invoice') {

	    	$woocommerce_active = false;
	    	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$woocommerce_active = true;
			}
	    	//echo '<input type="text" name="date" class="datepicker" />';
    	    echo '<select name="user" id="inspire_invoice_client_select">';
    	    $users = get_users( array( 'orderby' => 'user_name' ) );

    	    $client_options = array();

    	    $selected_user = '';
    	    if ( isset( $_GET['user'] ) ) {
    	    	$selected_user = $_GET['user'];
    	    }
    	    echo '<option value="">' . __('All users', 'flexible-invoices') . '</option>';
/*
    	    foreach ($users as $index => $user)
    	    {
    	        if (empty($user->billing_company))
    	        {
    	            $name = $user->billing_first_name . ' ' . $user->billing_last_name;
    	        } else{
    	            $name = $user->billing_company;
    	        }

    	        echo '<option value="' . $user->ID . '" ' . ($user->ID == $selected_user ? 'selected="selected"': '') . '>' . $name . '</option>';
    	    }
*/
    	    foreach ( $users as $user ) :
    	    	$user_meta = get_user_meta( $user->ID );
    	    	$client_options[$user->ID] = '';
    	    	if ( $woocommerce_active ) :
    	    		if ( isset( $user_meta['billing_company'] ) ) :
    	    			$company = $user_meta['billing_company'][0];
    	    			if ( !empty( $company ) ) :
    	    				$client_options[$user->ID] .= $company . ', ';
    	    			endif;
    	    		endif;
    	    		if ( isset( $user_meta['billing_first_name'] ) ) :
    	    			$billing_first_name = $user_meta['billing_first_name'][0];
    	    			if ( !empty( $billing_first_name ) ) :
    	    				$client_options[$user->ID] .= $user_meta['billing_first_name'][0] . ' ';
    	    			endif;
    	    		endif;
    	    		if ( isset( $user_meta['billing_last_name'] ) ) :
    	    			$billing_last_name = $user_meta['billing_last_name'][0];
    	    			if ( !empty( $billing_last_name ) ) :
    	    				$client_options[$user->ID] .= $user_meta['billing_last_name'][0] . ', ';
    	    			endif;
					endif;
				endif;
				$client_options[$user->ID] .= $user->first_name;
				$client_options[$user->ID] .= ' ';
				$client_options[$user->ID] .= $user->last_name;
				$client_options[$user->ID] .= ' (' . $user->user_login . ')';
			endforeach;
			asort( $client_options );
			foreach ( $client_options as $key => $value ) :
				?>
    	    	<option value="<?php echo $key; ?>">
    	    		<?php echo $value; ?>
    	    	</option>
    	    	<?php
    	    endforeach;

    	    echo '</select>';

    	    echo '<select name="paystatus">';
    	    $statuses = $this->getPaymentStatuses();
    	    $statuses['exceeded'] = __('Overdue', 'flexible-invoices');
    	    echo '<option value="">' . __('All statuses', 'flexible-invoices') . '</option>';
    	    $paystatus = '';
    	    if ( isset( $_GET['paystatus'] ) ) {
    	        $paystatus = $_GET['paystatus'];
            }
    	    foreach ($statuses as $key => $status)
    	    {
    	        echo '<option value="' . $key . '" ' . ($key == $paystatus ? 'selected="selected"': '') . '>' . $status . '</option>';
    	    }
    	    echo '</select>';
	    }
	}

	public function addCustomColumnsFilter( $columns ) {

	    unset($columns['date']);
		unset($columns['title']);

		$columns['invoice_title'] = __('Invoice', 'flexible-invoices' );
		$columns['client'] = __('Customer', 'flexible-invoices' );
		$columns['netto'] = __('Net price', 'flexible-invoices' );
		$columns['brutto'] = __('Gross price', 'flexible-invoices' );
		$columns['issue'] = __('Issue date', 'flexible-invoices' );
		$columns['pay'] = __('Due date', 'flexible-invoices' );

		$columns['order'] = __('Order', 'flexible-invoices' );
		$columns['status'] = __('Payment status', 'flexible-invoices' );

		$columns['sale'] = __('Date of sale', 'flexible-invoices' );
		$columns['currency'] = __('Currency', 'flexible-invoices' );
		$columns['paymethod'] = __('Payment method', 'flexible-invoices' );

		$columns['actions'] = __('Actions', 'flexible-invoices' );

	    return $columns;
	}

	public function displayCustomColumnFilter($column_name, $post_id) {
	    global $post;
	    $invoice = $this->invoiceFactory($post_id);

		switch ($column_name)
		{
            case 'invoice_title':
                if ( $invoice->getCorrection() == '1' ) {
	                echo sprintf( '<strong>%s</strong>', $post->post_title );
                }
                else {
	                echo sprintf( '<strong><a href="%s">%s</a></strong>', get_edit_post_link( $post_id ), $post->post_title );
                }
                break;
		    case 'client':
		          $client = $invoice->getClient();
		          echo @$client['name'];
		        break;
		    case 'netto':
		          echo $invoice->stringAsMoneyField($invoice->getCalculatedTotalNetPrice());
		        break;
	        case 'brutto':
                echo $invoice->stringAsMoneyField($invoice->getTotalPrice());
            break;
            case 'issue':
                echo @$invoice->getDateOfIssue();
            break;

            case 'pay':
                echo @$invoice->getDateOfPay();
            break;
            case 'order':
                do_action('inspire_invoices_display_order_column_for_invoice', $invoice);
            break;
            case 'status':
                echo @$invoice->getPaymentStatusString();
            break;
            case 'sale':
                echo @$invoice->getDateOfSale();
            break;
            case 'currency':
                echo @$invoice->getCurrency();
            break;
            case 'paymethod':
                echo @$invoice->getPaymentMethodString();
            break;
            case 'actions':
            	echo @$invoice->getActions( $invoice );
            break;

			default:
			    echo get_post_meta( $post_id, $this->_prefix . $column_name, true );
			break;
		}

	}

	/**
	 *
	 * @param int $id
	 * @return invoicePost
	 */
	public function invoiceFactory($id)
	{
	    $invoicePostClass = apply_filters('inspire_invoices_invoice_post_class', 'InvoicePost');
	    return new $invoicePostClass($id, $this->getPlugin());
	}

	public function getPaymentStatuses()
	{
	    return apply_filters('inspire_invoices_payment_statuses', array(
            'topay' => __('Due', 'flexible-invoices'),
            'paid' => __('Paid', 'flexible-invoices'),
	    ));
	}


	public function getPaymentMethods() {
		$payment_methods = explode( "\n", $this->getPlugin()->getSettingValue( 'payment_methods', implode( "\n", array(
            'bank-transfer' => __('Bank transfer', 'flexible-invoices'),
            'cash' => __('Cash', 'flexible-invoices'),
            'orher' => __('Other', 'flexible-invoices')
        ) ) ) );
		$ret = array();
		foreach ( $payment_methods as $payment_method ) {
			$ret[sanitize_title( $payment_method )] = $payment_method;
		}
		return $ret;
        //return apply_filters( 'inspire_invoices_payment_methods', $ret );
		/*
        return apply_filters('inspire_invoices_payment_methods', array(
            'transfer' => __('Bank transfer', 'flexible-invoices'),
            'cash' => __('Cash', 'flexible-invoices'),
            'orher' => __('Other', 'flexible-invoices')
        ));
        */
	}

	public function getPaymentMethodsWoo( $append = false ) {
		return apply_filters('inspire_invoices_payment_methods', array(	));
	}

	public function appendPaymentMethod( $payment_methods, $payment_methods_woo, InvoicePost $invoice ) {
		if ( !empty( $invoice ) ) {
			$_payment_method = $invoice->getPaymentMethod();
			$_payment_method_name = $invoice->getPaymentMethodName();
			if ( isset( $_payment_method ) && $_payment_method != '' && !isset( $payment_methods[$_payment_method] ) && !isset( $payment_methods_woo[$_payment_method] ) ) {
				if ( isset( $_payment_method_name ) && $_payment_method_name != '' ) {
					$payment_methods[$_payment_method] = $_payment_method_name;
				}
				else {
					$payment_methods[$_payment_method] = $_payment_method;
				}
			}
		}
		return $payment_methods;
	}

	public function getPaymentCurrencies()
	{
		$inspire_invoices_currency = get_option('inspire_invoices_currency', array() );
		$ret = array();
		if ( is_array( $inspire_invoices_currency ) ) {
			foreach ( $inspire_invoices_currency as $currency ) {
				$ret[$currency['currency']] = $currency['currency'];
			}
		}
		return $ret;
/*
        return apply_filters('inspire_invoices_payment_currencies', array(
            'PLN' => 'PLN',
            'EUR' => 'EUR',
            'USD' => 'USD'
        ));
*/
	}

	public function getVatTypes()
	{
		$rates = array();
		$inspire_invoices_tax = get_option('inspire_invoices_tax', array() );

		$index = 0;
		foreach ( $inspire_invoices_tax as $tax ) {
			$rates[] = array( 'index' => $index, 'rate' => $tax['rate'], 'name' => $tax['name'] );
			$index++;
		}

        return apply_filters('inspire_invoices_vat_types', $rates );
		/*
        return apply_filters('inspire_invoices_vat_types', array(
            array( 'index' => 0, 'rate' => 23, 'name'    =>   '23%' ),
            array( 'index' => 1, 'rate' => 22, 'name'    =>   '22%' ),
            array( 'index' => 2, 'rate' => 8, 'name'     =>    '8%' ),
            array( 'index' => 3, 'rate' => 7, 'name'     =>    '7%' ),
            array( 'index' => 4, 'rate' => 5, 'name'     =>    '5%' ),
            array( 'index' => 5, 'rate' => 3, 'name'     =>    '3%' ),
            array( 'index' => 6, 'rate' => 0, 'name'     =>    '0%' ),
            array( 'index' => 7, 'rate' => 'zw.', 'name' =>   'zw.' ),
            array( 'index' => 8, 'rate' => 'np.', 'name' =>   'np.' ),
            array( 'index' => 9, 'rate' => '21', 'name'  =>   '21%' )
        ));
        */
	}

	public function getPostTypeArray()
	{
		global $menu;
		$menu_pos = 56;
		while ( isset( $menu[$menu_pos] ) ) {
			$menu_pos++;
		}
		return array(
				'label'               => 'inspire_invoice',
				'description'         => __('Invoices', 'flexible-invoices'),
				'labels'              => array(
						'name'                => __('Invoices', 'flexible-invoices'),
						'singular_name'       => __('Invoice', 'flexible-invoices'),
						'menu_name'           => __('Invoices', 'flexible-invoices'),
						'parent_item_colon'   => '',
						'all_items'           => __('All Invoices', 'flexible-invoices'),
						'view_item'           => __('View Invoice', 'flexible-invoices'),
						'add_new_item'        => __('Add New Invoice', 'flexible-invoices'),
						'add_new'             => __('Add New', 'flexible-invoices'),
						'edit_item'           => __('Edit Invoice', 'flexible-invoices'),
						'update_item'         => __('Save Invoice', 'flexible-invoices'),
						'search_items'        => __('Search Invoices', 'flexible-invoices'),
						'not_found'           => __('No invoices found.', 'flexible-invoices'),
						'not_found_in_trash'  => __('No invoices found in Trash.', 'flexible-invoices')
				),
				'supports'            => array( 'title' ),
				'taxonomies'          => array( ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => $menu_pos,
				'menu_icon'           => 'dashicons-media-spreadsheet',
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => [invoicePostTypeCapabilities::CAPABILITY_SINGULAR, invoicePostTypeCapabilities::CAPABILITY_PLURAL],
				'map_meta_cap'        => false,
                'cap' => $this->capabilities->getPostCapabilityMapAsObject()
		);
	}


	public function setDefaultLayoutAction()
	{
	    $user = wp_get_current_user();
	    $columns = get_user_meta($user, 'screen_layout_inspire_invoice', true);
	    if (empty($columns))
	    {
	       update_user_meta($user, 'screen_layout_inspire_invoice', 1);
	    }

	    $hidden = get_user_meta($user, 'manageedit-inspire_invoicecolumnshidden', true);
	    if ($hidden === '')
	    {
	        $hidden = array('sale', 'currency', 'paymethod');
	        update_user_meta($user, 'manageedit-inspire_invoicecolumnshidden', $hidden);
	    }
	}

	public function removeQuickEditAction($actions)
	{
	    global $post;
	    if( $post->post_type == $this->getPostTypeSlug() )
	    {
	        unset($actions['inline hide-if-no-js']);
	    }
	    return $actions;
	}

	public function sendInvoiceByEmailAction()
	{
	    if (!empty($_POST['email']))
	    {
    	    $invoice = $this->invoiceFactory($_POST['id']);
    	    $order = $invoice->getOrder();


    	    if (!empty($order))
    	    {
    	    	$invoice->sendByEmail($_POST['email']);
    	    	$result = array(
    	    			'result' => 'OK',
    	    			'email' => $_POST['email'],
    	    			'code' => 100
    	    	);
    	    } else {
    	    	$result = array(
    	    			'result' => 'OK',
    	    			'code' => 102
    	    	);
    	    }

	    } else {
	        $result = array(
                'result' => 'OK',
                'code' => 101
	        );
	    }


	    header('Content-Type: application/json');
	    echo json_encode($result);

	    die();
	}

	public function getClientDataAction()
	{
	    $user = get_user_by('id', $_REQUEST['client']);
	    if (!empty($user))
	    {

	        $userdata = array(
                'name' => $user->first_name . ' ' . $user->last_name,
                'street' => '',
                'postcode' => '',
                'city' => '',
                'nip' => '',
                'country' => '',
                'phone' => '',
                'email' => $user->user_email
           );

	        $result = array(
	        	'result' => 'OK',
	            'code' => 100,
	            'userdata' => apply_filters('inspire_invoices_client_data', $userdata, $_REQUEST['client'])
 	        );
	    } else {
	        $result = array(
	        	'result' => 'OK',
	            'code' => 101
 	        );
	    }
	    header('Content-Type: application/json');
        echo json_encode($result);

	    die();
	}

	public function newInvoiceDefaultTitleFilter($post_title, $post)
	{
	    if (empty($post_title) && $post->post_type == 'inspire_invoice')
	    {
	        $invoice = $this->invoiceFactory($post->ID);
	        $invoice->setDefaultValuesIfNumberEmpty();
	        //$invoice->save();
	        return $invoice->getFormattedInvoiceNumber();
	    } else {
	        return $post_title;
	    }
	}

	/**
	 * @param string $where
	 *
	 * @return string
	 */
	public function invoice_filter_where( $where = '' ) {
		$where .= " AND meta_value >= '" . strtotime(date('Y-m-d 00:00:00', strtotime($_GET['start_date']))) . "' AND meta_value <= '" . strtotime(date('Y-m-d 23:59:59', strtotime($_GET['end_date']))) . "'";
		return $where;
	}

	/**
	 * admin ajax action
	 */
	public function batchDownloadAction() {
		$zip = new ZipArchive();
		$zipName = "/tmp/invoices.zip";

		if ( file_exists( $zipName ) ) {
			unlink($zipName);
		}

		if ( $zip->open( $zipName, ZipArchive::CREATE ) !== TRUE ) {
			echo "error: cannot open $zipName";
			die ;
		}

		add_filter( 'posts_where', array( $this, 'invoice_filter_where' ) );

		$invoicesQuery = new WP_Query( array(
			'post_type' => 'inspire_invoice',
			'orderby' => 'date',
			'order' => 'ASC',
			'post_status' => 'publish',
			'nopaging' => true,
			'meta_key' => '_date_issue'
		) );

		$invoices = $invoicesQuery->get_posts();

		remove_filter( 'posts_where', 'filter_where' );

		if ( ! count($invoices) ) {
				$zip->addFromString('no_invoices', '');
		} else {
			foreach ( $invoices as $invoice_post ) {
				$id = $invoice_post->ID;
				$invoice = $this->invoiceFactory( $id );

				$path = $this->getPdfPath( $invoice );
				$file = $path . '/' . str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf';
				if ( ! file_exists( $file ) ) {
					$pdfData = $this->generatePdfFileContent( $invoice );
					file_put_contents( $file, $pdfData );
					$zip->addFromString( str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf', $pdfData );
				} else {
					$pdfData = file_get_contents( $file );
					$zip->addFromString( str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf', $pdfData );
				}

			}
		}

		if ( ! $zip->close() ) {

		}

		header( "Content-Type: application/zip" );
        header( "Content-Disposition: attachment; filename=invoices.zip" );
    //header( "Content-Length: " . filesize($yourfile) );
		readfile( $zipName );
	}

	/**
	 * admin ajax action
	 */
	public function batchGenerateAction()
 	{
			$maxTime = ini_get( 'max_execution_time' );
			if ( empty( $maxTime ) || $maxTime === FALSE || $maxTime > 30 ) {
			 	$maxTime = 30;
			}

			$maxTime = $maxTime - 5;

	    $startTime = time();

			$args = array(
								'posts_per_page' 		=> -1,
								'post_type'			 		=> 'inspire_invoice',
								'post_status'    		=> 'publish',
								'suppress_filters' 	=> true,
			);
			$invoices = get_posts( $args );

			foreach ( $invoices as $invoice_post ) {
				if (time() < $startTime + $maxTime) {
					$id = $invoice_post->ID;
					$invoice = $this->invoiceFactory( $id );

					$path = $this->getPdfPath( $invoice );
					$file = $path . '/' . str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf';
					if( ! file_exists( $file ) ) {
						$pdfData = $this->generatePdfFileContent( $invoice );
						file_put_contents( $file, $pdfData );
					}
				} else {
					echo 2; // czas na reset
					die();
				}
			}
			echo 0; // zakończone pomyślnie
 	    die();
 	}
	/**
	 * admin ajax action
	 */
	public function getInvoiceAction($id = null)
	{
	    if (empty($id))
	    {
	        $id = $_GET['id'];
	    }

	    if ( is_admin() || ( isset($_GET['hash']) && $_GET['hash'] == md5(NONCE_SALT . $_GET['id']) ) ) //|| current_user_can( 'manage_options' )
	    {
	        $invoice = $this->invoiceFactory($id);

	        do_action( 'flexible_invoices_pre_generate_pdf', $invoice );

	        if ( $invoice->getCorrection() == '1' ) {
		        echo $this->loadTemplate( 'generated_correction', 'invoice', array(
			        'invoice' => $invoice,
			        'plugin'  => $this->getPlugin()
		        ) );
	        }
	        else {
		        echo $this->loadTemplate( 'generated_invoice', 'invoice', array(
			        'invoice' => $invoice,
			        'plugin'  => $this->getPlugin()
		        ) );
	        }
	    }
	    die();
	}

	/**
	 * admin ajax action
	 */
	public function getInvoicePdfAction( $id = null) {
	    if (empty($id)) {
	       $id = $_GET['id'];
	    }
	    if ( ( isset( $_GET['hash'] ) && $_GET['hash'] == md5( NONCE_SALT . $id ) ) || current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
            $this->getInvoicePdf($id);
	    }
	    die();
	}

	public function getPdfPath($invoice) {
		$upload_dir = wp_upload_dir();
		$date = getdate( strtotime( $invoice->getDateOfIssue() ) );

		$year  = $date[ 'year' ];
		$month = str_pad( $date[ 'mon' ], 2, '0', STR_PAD_LEFT );
		$noAccessPath = $upload_dir[ 'basedir' ] . '/wordpress_invoices';
		wp_mkdir_p( $noAccessPath . '/' . $year . '/' . $month );

		if ( ! file_exists( $noAccessPath . '/.htaccess' ) ) {
			file_put_contents( $noAccessPath . '/.htaccess', 'deny from all' );
		}

		return $noAccessPath . '/' . $year . '/' . $month;
	}

	public function generatePdfFileContent($invoice) {
		require_once('mpdf/mpdf.php');
		$mpdf = new mPDF('pl-PL', 'A4'/*, 'DejaVuSerif'*/);
		$mpdf->img_dpi = 200;
		//$mpdf->debug = true;

		do_action( 'flexible_invoices_pre_generate_pdf', $invoice );

        if ( $invoice->getCorrection() == '1' ) {
	        $mpdf->WriteHTML( $this->loadTemplate( 'generated_correction', 'invoice', array(
		        'invoice' => $invoice,
		        'plugin'  => $this->getPlugin()
	        ) ) );
        }
        else {
	        $mpdf->WriteHTML( $this->loadTemplate( 'generated_invoice', 'invoice', array(
		        'invoice' => $invoice,
		        'plugin'  => $this->getPlugin()
	        ) ) );
        }
		$pdfData = $mpdf->Output( str_replace(array('/'), array('_'), $invoice->getFormattedInvoiceNumber()) . '.pdf', 'S' );
		return $pdfData;
	}

	public function getInvoicePdf($id) {
		$post = get_post( $id );
		if ( ! $post ) {
			wp_die( __( 'This document does noe exist or was deleted.', 'flexible-invoices' ) );
		}
		$invoice = $this->invoiceFactory( $id );
		$name = str_replace( array( '/' ), array( '_' ), $invoice->getFormattedInvoiceNumber() ) . '.pdf';

		$path = $this->getPdfPath($invoice);
		$file = $path . '/' . $name;

		if ( isset( $_GET['save_file']) ) {
			header( 'Content-type: application/pdf' );
			header( 'Content-Disposition: attachment; filename="' . $name . '"' );
		}
		else {
			header( 'Content-type: application/pdf' );
			header( 'Content-Disposition: inline; filename="' . $name . '"' );
		}
		if(false &&  file_exists( $file ) ) {
    	    readfile( $file );
		} else {

			$pdfData = $this->generatePdfFileContent($invoice);

			file_put_contents( $file, $pdfData );
			echo $pdfData;
		}
	}

	public function createCustomFieldsAction($post_type, $post = null)
	{
	    if ($post_type == 'inspire_invoice')
	    {
    	    $invoice = $this->invoiceFactory($post->ID);

            add_meta_box( 'owner', __('Seller', 'flexible-invoices'), array( $this, 'displayOwnerMetaboxAction' ), $this->getPostTypeSlug(), 'normal', 'high', array('invoice' => $invoice) );
    	    add_meta_box( 'client', __('Customer', 'flexible-invoices'), array( $this, 'displayClientMetaboxAction' ), $this->getPostTypeSlug(), 'normal', 'high', array('invoice' => $invoice) );
    	    add_meta_box( 'products', __('Products', 'flexible-invoices'), array( $this, 'displayProductsMetaboxAction' ), $this->getPostTypeSlug(), 'normal', 'high', array('invoice' => $invoice) );
    	    add_meta_box( 'payment', __('Payments and other info', 'flexible-invoices'), array( $this, 'displayPaymentMetaboxAction' ), $this->getPostTypeSlug(), 'normal', 'high', array('invoice' => $invoice) );
    	    add_meta_box( 'options', __('Dates and actions', 'flexible-invoices'), array( $this, 'displayOptionsMetaboxAction' ), $this->getPostTypeSlug(), 'side', 'low', array('invoice' => $invoice) );
    	    add_meta_box( 'submitdiv2', __( 'Save/Issue', 'flexible-invoices' ), 'post_submit_meta_box', null, 'normal', 'low' );
	    }
	}

	public function saveCustomFieldsAction($post_id, $post)
	{

		if ( ! isset( $_POST['flexible_invoices_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['flexible_invoices_nonce'], 'flexible_invoices_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

	    if (!empty($_POST) && $post->post_type == 'inspire_invoice')
	    {
    	    $invoice = $this->invoiceFactory($post->ID);

    	    $invoice->setDefaultValuesIfNumberEmpty();

	        $invoice->setDateOfIssue( $_POST['date_issue'] );
    	    $invoice->setDateOfSale($_POST['date_sale']);
    	    $invoice->setDateToPay($_POST['date_pay']);

    	    $invoice->setNumber($_POST['number']);
    	    $invoice->setFormattedNumber($_POST['post_title']);

    	    $invoice->setCurrency($_POST['currency']);
    	    $invoice->setPaymentMethod($_POST['payment_method']);

    	    $_payment_method_name = '';
    	    $payment_methods = $this->getPaymentMethods();
    	    $payment_methods_woo = $this->getPaymentMethodsWoo();
    	    $payment_methods = $this->appendPaymentMethod( $payment_methods, $payment_methods_woo, $invoice );

    	    if ( isset( $payment_methods[$_POST['payment_method']] ) ) {
    	    	$_payment_method_name = $payment_methods[$_POST['payment_method']];
    	    }
    	    else {
    	    	$_payment_method_name = $payment_methods_woo[$_POST['payment_method']];
    	    }

    	    $invoice->setPaymentMethodName( $_payment_method_name );

    	    $invoice->setPaymentStatus($_POST['payment_status']);
    	    $invoice->setTotalPrice($_POST['total_price']);
    	    $invoice->setTotalPaid($invoice->format_decimal( $_POST['total_paid'] ) );

    	    $invoice->setNotes( strip_tags( $_POST['notes'] ) );

    	    //$invoice->setAddOrderId($_POST['add_order_id'] == 1);

            $invoice->setOwnerFromArray($_POST['owner']);
    	    $invoice->setClientFromArray($_POST['client']);
    	    if (!empty($_POST['product'])) {
		        $invoice->setProductsFromPostArray($_POST['product']);
            }


    	    do_action('inspire_invoices_before_save_invoice_custom_fields', $invoice);

    	    $invoice->save();

    	    do_action('inspire_invoices_after_save_invoice_custom_fields', $invoice);
	    }
	}

	public function displayOptionsMetaboxAction($post, $args)
	{
	    $invoice = $args['args']['invoice'];

	    echo $this->loadTemplate('options_metabox', 'invoice_edit', array(
	            'invoice' => $invoice,
	            'plugin' => $this->getPlugin()
	    ));
	}

	public function displayOwnerMetaboxAction($post, $args)
	{
	    $invoice = $args['args']['invoice'];

	    echo $this->loadTemplate('owner_metabox', 'invoice_edit', array(
	            'invoice' => $invoice,
	            'plugin' => $this->getPlugin()
	    ));
	}

	public function displayClientMetaboxAction($post, $args)
	{
	    $invoice = $args['args']['invoice'];

		wp_nonce_field( 'flexible_invoices_nonce', 'flexible_invoices_nonce' );

	    echo $this->loadTemplate('client_metabox', 'invoice_edit', array(
	            'invoice' => $invoice,
	            'plugin' => $this->getPlugin()
	    ));
	}

	public function displayProductsMetaboxAction($post, $args)
	{
	    $invoice = $args['args']['invoice'];

	    echo $this->loadTemplate('products_metabox', 'invoice_edit', array(
	            'invoice' => $invoice,
	            'plugin' => $this->getPlugin()
	    ));
	}

	public function displayPaymentMetaboxAction($post, $args)
	{
	    $invoice = $args['args']['invoice'];

	    echo $this->loadTemplate('payment_metabox', 'invoice_edit', array(
	            'invoice' => $invoice,
	            'plugin' => $this->getPlugin()
	    ));
	}



	/**
     * @param string $number_reset_type
     * @param int $timestamp
	 * @return int
	 */
	public function generateNextInvoiceNumber( $number_reset_type, $timestamp, $mark_for_save = true )	{
	    $previous_timestamp = intval( $this->getPlugin()->getSettingValue('order_start_invoice_number_timestamp', $timestamp ) );
	    $reset_number = false;
	    if ( $number_reset_type == 'month' ) {
	        if ( date( 'm.Y', $previous_timestamp ) != date( 'm.Y', $timestamp ) ) {
	            $reset_number = true;
            }
        }
		if ( $number_reset_type == 'year' ) {
			if ( date( 'Y', $previous_timestamp ) != date( 'Y', $timestamp ) ) {
				$reset_number = true;
			}
		}
        if ( $reset_number ) {
	        $invoice_number = 1;
        }
        else {
	        $invoice_number = intval( $this->getPlugin()->getSettingValue( 'order_start_invoice_number', 1 ) );
        }
        if ( $mark_for_save ) {
	        $this->_increase_number_on_save = true;
        }
	    return $invoice_number;
	}


	/**
	 * @param $number_reset_type
	 * @param $timestamp
	 *
	 * @return int
	 */
	public function increaseNextInvoiceNumber( $number_reset_type, $timestamp ) {
		$invoice_number = $this->generateNextInvoiceNumber( $number_reset_type, $timestamp, false );
		if ( $this->_increase_number_on_save ) {
			$invoice_number ++;
			$this->setSettingValue( 'order_start_invoice_number', $invoice_number );
			$this->setSettingValue( 'order_start_invoice_number_timestamp', $timestamp );
			$this->_increase_number_on_save = false;
		}
		return $invoice_number;
    }


	/**
	 * @param $number_reset_type
	 * @param $timestamp
     *
	 * @return int
	 */
	public function generateNextCorrectionNumber( $number_reset_type, $timestamp, $mark_to_save = true )	{
		$previous_timestamp = intval( $this->getPlugin()->getSettingValue('correction_start_invoice_number_timestamp', $timestamp ) );
		$reset_number = false;
		if ( $number_reset_type == 'month' ) {
			if ( date( 'm.Y', $previous_timestamp ) != date( 'm.Y', $timestamp ) ) {
				$reset_number = true;
			}
		}
		if ( $number_reset_type == 'year' ) {
			if ( date( 'Y', $previous_timestamp ) != date( 'Y', $timestamp ) ) {
				$reset_number = true;
			}
		}
		if ( $reset_number ) {
			$invoice_number = 1;
		}
		else {
			$invoice_number = intval( $this->getPlugin()->getSettingValue( 'correction_start_invoice_number', 1 ) );
		}
		if ( $mark_to_save ) {
			$this->_increase_correction_number_on_save = true;
		}
		return $invoice_number;
	}

	/**
	 * @param $number_reset_type
	 * @param $timestamp
	 *
	 * @return int
	 */
	public function increaseNextCorrectionNumber( $number_reset_type, $timestamp ) {
		$invoice_number = $this->generateNextCorrectionNumber( $number_reset_type, $timestamp, false );
		if ( $this->_increase_correction_number_on_save ) {
			$invoice_number ++;
			$this->setSettingValue( 'correction_start_invoice_number', $invoice_number );
			$this->setSettingValue( 'correction_start_invoice_number_timestamp', $timestamp );
			$this->_increase_correction_number_on_save = false;
		}
		return $invoice_number;
	}

	/**
	 *
	 * @param WC_Order $order
	 * @return InvoicePost
	 */
	public function fetchInvoiceForOrder(WC_Order $order)
	{
	    $invoice = InvoicePost::createFromOrder($order, $this);

	    return $invoice;
	}

	public function head_scripts() {
		// wp_register_style( 'reset-css', plugins_url( 'assets/css/reset.css', __FILE__ ) );
		// wp_register_style( 'print-css', plugins_url( 'assets/css/print.css', __FILE__ ) );
		// wp_register_style( 'front-css', plugins_url( 'assets/css/front.css', __FILE__ ) );
		//
		// wp_enqueue_style( 'reset-css' );
		// wp_enqueue_style( 'print-css' );
		// wp_enqueue_style( 'front-css' );

		?>
		<link href="<?php echo $this->getPluginUrl(); ?>assets/css/reset.css" rel="stylesheet" type="text/css" media="screen,print" />
		<link href="<?php echo $this->getPluginUrl(); ?>assets/css/print.css" rel="stylesheet" type="text/css" media="print" />
		<link href="<?php echo $this->getPluginUrl(); ?>assets/css/front.css" rel="stylesheet" type="text/css" media="screen,print" />
		<?php
	}

}
