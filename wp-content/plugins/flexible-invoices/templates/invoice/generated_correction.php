<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!DOCTYPE HTML>
<?php
    if ( ! function_exists( 'getInvoice' ) ) {
        /**
         * for the ide syntax
         *
         * @return InvoicePost
         */
        function getInvoice( $args ) {
            return $args['invoice'];
        }
    }

	$invoice = getInvoice($args);

	//$order = $invoice->getOrder();
	$client = $invoice->getClient();
	$client_country = $client['country'];
	$owner = $invoice->getOwner();
	$products = $invoice->getProducts();
	$shipping = $invoice->getShipping();
	if ( ! empty( $shipping) ) {
	   $products = array_merge( $products, $shipping );
	}

	$pkwiuEmpty = true;
	if ( !is_array( $products ) ) {
		$products = array();
	}
    foreach ( $products as $product ) {
        if ( ! empty( $product['sku'] ) ) {
            $pkwiuEmpty = false;
        }
    }

    $hideVat = $this->getSettingValue( 'hide_vat' ) == 'on' && ! $invoice->getTotalTax();
    $hideVatNumber = $this->getSettingValue( 'hide_vat_number' ) == 'on' && ! $invoice->getTotalTax();

    $corrected_invoice = $invoice->getCorrectedInvoice();
/*
    $order_number = '';

    if ( $invoice instanceof InvoicePostWoocommerce && $invoice->isOrder() ) {
	    $order = $invoice->getOrder();
	    $order_number = $order->get_order_number();
    }
*/
?>
<html>
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    	<title><?php echo $invoice->getFormattedInvoiceNumber(); ?></title>

    	<?php do_action( 'flexible_invoices_head' ); ?>

    </head>
    <body>
    	<div id="wrapper" class="invoice">
    		<div id="header">
    			<table>
    				<tbody>
    					<tr>
    						<td>
								<?php if ( ! empty( $owner['logo'] ) ): ?>
								    <div id="logo">
								    	<img src="<?php echo $owner['logo']; ?>" />
								    </div>
								<?php endif; ?>
    						</td>

							<td id="dates">
							    <p><?php echo $this->getSettingValue( 'date_of_sale' ); ?>: <strong><?php echo $invoice->getDateOfSale(); ?></strong></p>
							    <p><?php _e( 'Issue date:', 'flexible-invoices' ); ?> <strong><?php echo $invoice->getDateOfIssue(); ?></strong></p>
							    <?php if ($invoice->getDateOfPay() > 0): ?>
							        <p><?php _e( 'Due date:', 'flexible-invoices' ); ?> <strong><?php echo $invoice->getDateOfPay(); ?></strong></p>
							    <?php endif; ?>
							    <?php $paymentMethod = $invoice->getPaymentMethodString( $invoice ); ?>
							    <?php if (! empty($paymentMethod ) ): ?>
                                    <p><?php _e( 'Payment method:', 'flexible-invoices' ); ?> <strong><?php echo $paymentMethod; ?></strong></p>
                                <?php endif; ?>
                                <br/>
                                <p><?php _e( 'Related to invoice:', 'flexible-invoices' ); ?> <strong><?php echo $corrected_invoice->getFormattedInvoiceNumber();  ?></strong></p>
                                <p><?php _e( 'Invoice issue date:', 'flexible-invoices' ); ?> <strong><?php echo $corrected_invoice->getDateOfIssue(); ?></strong></p>
							</td>
    					</tr>
    				</tbody>
    			</table>

    			<div class="fix"></div>

    			<div id="companies">
    				<div class="seller">
    					<p class="title"><?php _e( 'Seller:', 'flexible-invoices' ); ?></p>

    					<?php if ( ! empty( $owner['name'] ) ): ?>
    					   <p class="name"><?php echo $owner['name']; ?></p>
    					<?php endif; ?>

    					<p class="details"><?php echo nl2br( isset( $owner['address'] ) ? $owner['address'] : '' ); ?></p>

    					<?php if ( ! empty( $owner['nip'] ) && ! $hideVatNumber ): ?>
    					   <p class="nip"><?php _e( 'VAT Number:', 'flexible-invoices' ); ?> <?php echo $owner['nip']; ?></p>
    					<?php endif; ?>

					    <?php if ( $this->isSettingValue( 'bank_name' ) ): ?>
                            <p><?php _e( 'Bank:', 'flexible-invoices' ); ?> <?php echo $owner['bank']; ?></p>
					    <?php endif; ?>

					    <?php if ( $this->isSettingValue( 'account_number' ) ): ?>
                            <p><?php _e( 'Account number:', 'flexible-invoices' ); ?> <?php echo $owner['account']; ?></p>
					    <?php endif; ?>

    				</div>

    				<div class="buyer">
    					<p class="title"><?php _e( 'Buyer:', 'flexible-invoices' ); ?></p>

    					<p>
    					   <?php if ( ! empty( $client['name'] ) ): ?>
    					       <span><?php echo $client['name'] ?></span><br />
    					   <?php endif; ?>
    					   <?php if ( ! empty( $client['street'] ) ): ?>
    					       <span><?php echo $client['street'] ?></span><br />
    					   <?php endif; ?>

    					   <?php if ( ! empty( $client['postcode'] ) ): ?>
    					       <span><?php echo $client['postcode'] ?></span>
    					   <?php endif; ?>

    					   <?php if ( ! empty( $client['city' ] )): ?>
    					       <span><?php echo $client['city'] ?>,</span>
                               <?php if ( ! empty($client['country'] ) ): ?>
    					           <span><?php echo $client['country'] ?></span><br />
					           <?php endif; ?>
    					   <?php elseif( ! empty($client['postcode'] ) ): ?>
    					       <span><?php echo $client['country'] ?></span>
    					       <br />
					       <?php else: ?>
					           <span><?php echo isset( $client['country'] ) ? $client['country'] : '' ; ?></span>
    					   <?php endif; ?>
    					</p>

    					<?php if ( ! empty($client['nip'] ) ): ?>
    					   <p><?php _e( 'VAT Number:', 'flexible-invoices' ); ?> <?php echo $client['nip']; ?></p>
    				    <?php endif; ?>
    				</div>

    				<div class="fix"></div>
    			</div>
    			<div class="fix"></div>
    		</div>

    		<p class="report-title">
                <?php echo $invoice->getFormattedInvoiceNumber();  ?>
<?php /*
                <?php _e( 'to invoice nr', 'flexible-invoices' ); ?>
                <?php echo $corrected_invoice->getFormattedInvoiceNumber();  ?>
*/ ?>
            </p>

    		<table cellpadding="0" cellspacing="0">
    		    <thead>
                    <?php $correction_colspan = 7; ?>
    		    	<tr>
    		    		<th><?php _e( '#', 'flexible-invoices' ); ?></th>
    		    		<th width="30%"><?php _e( 'Name', 'flexible-invoices' ); ?></th>
    		    		<?php if (!$pkwiuEmpty): ?>
					        <?php $correction_colspan = $correction_colspan + 1; ?>
    		    		    <th><?php _e( 'SKU', 'flexible-invoices' ); ?></th>
    		    		<?php endif; ?>
    		    		<th><?php _e( 'Quantity', 'flexible-invoices' ); ?></th>
    		    		<th><?php _e( 'Unit', 'flexible-invoices' ); ?></th>
    		    		<th><?php _e( 'Net price', 'flexible-invoices' ); ?></th>
    		    		<th><?php _e( 'Net amount', 'flexible-invoices' ); ?></th>
                        <?php if (!$hideVat): ?>
	                        <?php $correction_colspan = $correction_colspan + 3; ?>
        		    		<th><?php _e( 'Tax rate', 'flexible-invoices' ); ?></th>
        		    		<th><?php _e( 'Tax amount', 'flexible-invoices' ); ?></th>
							<th><?php _e( 'Gross amount', 'flexible-invoices' ); ?></th>
    		    		<?php endif; ?>
    		    	</tr>
    		    </thead>

    		    <tbody>
                    <tr><td colspan="<?php echo $correction_colspan; ?>"><?php _e( 'Before correction', 'flexible-invoices' ); ?></td></tr>
    		    	<?php
    		    		$index = 0;
    		    		$total_tax_amount = 0;
    		    		$total_net_price = 0;
    		    		$total_gross_price = 0;

    		    		$total_tax_net_price = array();
    		    		$total_tax_tax_amount = array();
    		    		$total_tax_gross_price = array();
    		    	?>
    		    	<?php foreach ($products as $item): ?>
    		    		<?php
    		    			if ( isset( $item['before_correction'] ) && $item['before_correction'] == '1' ) {
						        $index++;
                            ?>
                            <tr>
                                <td class="center"><?php echo $index; ?></td>
                                <td><?php echo $item['name']; ?></td>
                                <?php if (!$pkwiuEmpty): ?>
                                   <td><?php echo $item['sku']; ?></td>
                                <?php endif; ?>
                                <td class="quantity number"><?php echo -1 * $item['quantity']; ?></td>
                                <td class="unit center"><?php echo $item['unit']; ?></td>
                                <td class="net-price number"><?php echo $invoice->stringAsMoney($item['net_price']); ?></td>

                                <td class="total-net-price number"><?php echo $invoice->stringAsMoney( -1 * $item['net_price_sum']); ?></td>
                                <?php if (!$hideVat): ?>
                                    <td class="tax-rate number"><?php echo $item['vat_type_name']; ?></td>
                                    <td class="tax-amount number"><?php echo $invoice->stringAsMoney( -1 * $item['vat_sum']); ?></td>
                                	<td class="total-gross-price number"><?php echo $invoice->stringAsMoney( -1 * $item['total_price']); ?></td>
                                <?php endif; ?>

                                <?php
                                    $total_net_price += $item['net_price_sum'];
                                    $total_tax_amount += $item['vat_sum'];
                                    $total_gross_price += $item['total_price'];

                                    if (!empty($item['vat_type_name']))
                                    {
                                        $total_tax_net_price[$item['vat_type_name']] = @floatval($total_tax_net_price[$item['vat_type_name']]) + $item['net_price_sum'];
                                        $total_tax_tax_amount[$item['vat_type_name']] = @floatval($total_tax_tax_amount[$item['vat_type_name']]) + $item['vat_sum'];
                                        $total_tax_gross_price[$item['vat_type_name']] = @floatval($total_tax_gross_price[$item['vat_type_name']]) + $item['total_price'];
                                    }
                                ?>
                            </tr>
                        <?php
    		    			}
    		    			?>
    			    <?php endforeach; ?>
                    <tr><td colspan="<?php echo $correction_colspan; ?>"><?php _e( 'After correction', 'flexible-invoices' ); ?></td></tr>
                    <?php
                        $index = 0;
                    ?>
                    <?php foreach ($products as $item): ?>
	                    <?php
	                    if ( !isset( $item['before_correction'] ) ) {
		                    $index++;
		                    ?>
                            <tr>
                                <td class="center"><?php echo $index; ?></td>
                                <td><?php echo $item['name']; ?></td>
			                    <?php if (!$pkwiuEmpty): ?>
                                    <td><?php echo $item['sku']; ?></td>
			                    <?php endif; ?>
                                <td class="quantity number"><?php echo $item['quantity']; ?></td>
                                <td class="unit center"><?php echo $item['unit']; ?></td>
                                <td class="net-price number"><?php echo $invoice->stringAsMoney($item['net_price']); ?></td>

                                <td class="total-net-price number"><?php echo $invoice->stringAsMoney($item['net_price_sum']); ?></td>
			                    <?php if (!$hideVat): ?>
                                    <td class="tax-rate number"><?php echo $item['vat_type_name']; ?></td>
                                    <td class="tax-amount number"><?php echo $invoice->stringAsMoney($item['vat_sum']); ?></td>
                                    <td class="total-gross-price number"><?php echo $invoice->stringAsMoney($item['total_price']); ?></td>
			                    <?php endif; ?>


			                    <?php
			                    $total_net_price += $item['net_price_sum'];
			                    $total_tax_amount += $item['vat_sum'];
			                    $total_gross_price += $item['total_price'];

			                    if (!empty($item['vat_type_name']))
			                    {
				                    $total_tax_net_price[$item['vat_type_name']] = @floatval($total_tax_net_price[$item['vat_type_name']]) + $item['net_price_sum'];
				                    $total_tax_tax_amount[$item['vat_type_name']] = @floatval($total_tax_tax_amount[$item['vat_type_name']]) + $item['vat_sum'];
				                    $total_tax_gross_price[$item['vat_type_name']] = @floatval($total_tax_gross_price[$item['vat_type_name']]) + $item['total_price'];
			                    }
			                    ?>
                            </tr>
		                    <?php
	                    }
	                    ?>
                    <?php endforeach; ?>

                </tbody>

    		    <tfoot>
    		    	<tr class="total">
    		    		<td class="empty">&nbsp;</td>
    		    		<td class="empty">&nbsp;</td>
    		    		<td class="empty">&nbsp;</td>
    		    		<td class="empty">&nbsp;</td>
    		    		<?php if (!$pkwiuEmpty): ?>
			    		   <td class="empty">&nbsp;</td>
			    		<?php endif; ?>

    		    		<td class="sum-title"><?php _e('Total', 'flexible-invoices'); ?></td>
    		    		<td class="number"><?php echo $invoice->stringAsMoney($total_net_price); ?></td><?php // suma "Total net price" ?>
                        <?php if (!$hideVat): ?>
        		    		<td class="number">X</td><?php // tu zawsze X ?>
        		    		<td class="number"><?php echo $invoice->stringAsMoney($total_tax_amount); ?></td><?php // suma "Tax amount" ?>
        		    		<td class="number"><?php echo $invoice->stringAsMoney($total_gross_price); ?></td><?php // suma "Total gross price" ?>
                        <?php endif; ?>
    		    	</tr>

    		    	<?php // poniższe sekcje to rozbicie podatków wg stawek ?>

                    <?php if (!$hideVat): ?>

        		    	<?php foreach ($total_tax_net_price as $taxType => $price): ?>
        			    	<tr>
        			    		<td class="empty">&nbsp;</td>
        			    		<td class="empty">&nbsp;</td>
        			    		<td class="empty">&nbsp;</td>
        			    		<td class="empty">&nbsp;</td>
        			    		<?php if (!$pkwiuEmpty): ?>
        			    		   <td class="empty">&nbsp;</td>
        			    		<?php endif; ?>
        			    		<td class="sum-title"><?php _e('Including', 'flexible-invoices'); ?></td>
        			    		<td class="number"><?php echo $invoice->stringAsMoney($price); ?></td><?php // suma "Total net price" dla danej stawki podatkowej ?>
        			    		<td class="number"><?php echo $taxType; ?></td><?php //tu stawka podatkowa ?>
        			    		<td class="number"><?php echo $invoice->stringAsMoney($total_tax_tax_amount[$taxType]); ?></td><?php // suma "Tax amount" dla danej stawki podatkowej ?>
        			    		<td class="number"><?php echo $invoice->stringAsMoney($total_tax_gross_price[$taxType]); ?></td><?php // suma "Total gross price" dla danej stawki podatkowej ?>
        			    	</tr>
        			    <?php endforeach; ?>

                    <?php endif; ?>

    		    </tfoot>
    		</table>
    		<table class="totals"><?php //tutaj wszystkie kwoty są brutto z podsumowania ?>
    			<tbody>
    				<tr>
    					<td width="33.3%"><?php _e('Total:', 'flexible-invoices'); ?> <strong><?php echo $invoice->stringAsMoney($invoice->getTotalPrice()); ?></strong></td>
						<td width="33.3%"><?php _e('Paid:', 'flexible-invoices'); ?> <strong><?php echo $invoice->stringAsMoney($invoice->getTotalPaid()); ?></strong></td>
						<td width="33.3%"><?php _e('Due:', 'flexible-invoices'); ?> <strong><?php echo $invoice->stringAsMoney($invoice->getTotalPrice() - $invoice->getTotalPaid()); ?></strong></td>
    				</tr>
    			</tbody>
    		</table>

    		<?php if ( $this->getSettingValue( 'show_signatures' ) ): ?>
	    		<div id="signatures">
	    			<table>
	    			     <tr>
	    			        <td>
	    			            <p>&nbsp;</p>
	    			            <p>........................................</p>
	    			        </td>

                            <td width="15%"></td>

	    			        <td>
	    			            <p class="user"><?php $current_user = wp_get_current_user(); echo $current_user->display_name; ?></p>
	    			            <p>........................................</p>
	    			        </td>
	    			    </tr>

                        <tr>
                            <td>
                                <p><?php _e( 'Buyer signature', 'flexible-invoices' ); ?></p>
                            </td>
                            <td width="15%"></td>
                            <td>
                                <p><?php _e( 'Seller signature', 'flexible-invoices' ); ?></p>
                            </td>
                        </tr>
	    			</table>
	    		</div>
    		<?php endif; ?>
    		<?php $note = $invoice->getNotes(); ?>
    		<?php if (!empty($note)): ?>
    			<div id="footer">
    				<p><strong><?php _e( 'Notes', 'flexible-invoices' ); ?></strong></p>
    				<p><?php echo str_replace( "\n", '<br/>', $note ); ?></p>
                </div>
            <?php endif; ?>

            <?php do_action( 'flexible_invoices_after_invoice_notes', $client_country, $hideVat, $hideVatNumber ); ?>

            <div class="fix"></div>
        </div>
        <div class="no-page-break"></div>
    </body>
</html>
