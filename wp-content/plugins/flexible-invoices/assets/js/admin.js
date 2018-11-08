function parseFloatLocal(num) {
	return parseFloat(num.replace(",", "."));
}
jQuery.noConflict();
(function($) {
	$(function() {
		var dates = $( "#generate_form input[name=start_date], #generate_form input[name=end_date], body.post-type-inspire_invoice input.datepicker" ).datepicker({
			dateFormat: "yy-mm-dd",
		    changeMonth: true,
		    changeYear: true,
			showButtonPanel: true
		});

		if ( jQuery('#inspire_invoice_client_select').length ) {
			jQuery('.tablenav #inspire_invoice_client_select').select2({
    			width: '200px'
			});

			jQuery('#inspire_invoice_client_select_wrap #inspire_invoice_client_select').select2({
    			width: '100%'
			});
		}

        // Hide Owner Metabox Content in Invoice Edit
        $( '#owner.postbox' ).addClass( 'closed' );

		$( '#generate_submit' ).click( function ( e ) {
			e.preventDefault();
			$( 'form#generate .response strong' ).text( inspire_invoice_params.message_generating );
			var data = {
				'action': 'woocommerce-invoice-batch-generate',
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if ( response == '0' ) {
					$( 'form#generate .response strong' ).text( inspire_invoice_params.message_generating_successful );
				} else if ( response == '2' ) {
					$( '#generate_submit' ).click();
				} else {
					$( 'form#generate .response strong' ).text( inspire_invoice_params.message_generating_error + response );
				}
			});
		});

		$('.order_actions.column-order_actions a.button.tips, .generate-invoice').each(function(index, item) {
			if ($(item).attr('href').indexOf('woocommerce-invoice-generate-invoice') !== -1 || $(item).attr('href').indexOf('woocommerce-invoice-generate-bill') !== -1)
			{
				$(item).click(function(e) {
					e.preventDefault();
					$.post($(item).attr('href'), '', function(result) {
						if (result.result == 'OK')
						{
							var parent = $(item).parent();
							$(item).after(result.newbtn_download);
							$(item).after(' ');
							$(item).after(result.newbtn);
							$(item).after('<br/><br/>');
							$(item).unbind('click').replaceWith(result.newbtn_edit);
							$(parent).find('a').each(function(){$(this).tipTip({
                                'attribute': 'data-tip',
                                'fadeIn': 50,
                                'fadeOut': 50,
                                'delay': 200
                            })});
						}
					});
				});
			}

		});

		function moneyMultiply(a, b)
		{
			if (a == 0 || b == 0)
			{
				return 0;
			}
		    var log_10 = function (c) { return Math.log(c) / Math.log(10); },
		        ten_e  = function (d) { return Math.pow(10, d); },
		        pow_10 = -Math.floor(Math.min(log_10(a), log_10(b))) + 1;
		    var mul = ((a * ten_e(pow_10)) * (b * ten_e(pow_10))) / ten_e(pow_10 * 2);

		    if (isNaN(mul) || !isFinite(mul))
	    	{
		    	return 0;
	    	} else {
	    		return mul;
	    	}
		}

		function getVatRateFromField(field)
		{
			return parseInt( field.val().split('|')[1], 10);
		}


		function invoiceRefreshProductNetPriceSum($productHandle)
		{
			$('[name=product\\[net_price_sum\\]\\[\\]]', $productHandle).val(
				moneyMultiply(
					parseFloatLocal( $('[name=product\\[net_price\\]\\[\\]]', $productHandle).val() ),
					parseFloatLocal( $('[name=product\\[quantity\\]\\[\\]]', $productHandle).val() )
				).toFixed(2)
			);
			invoiceRefreshProductVatRate($productHandle);
		}

		function invoiceRefreshProductVatRate($productHandle)
		{
			var vatType = getVatRateFromField($('[name=product\\[vat_type\\]\\[\\]]', $productHandle));

			$('[name=product\\[vat_sum\\]\\[\\]]', $productHandle).val(
				moneyMultiply(
					parseFloatLocal( $('[name=product\\[net_price_sum\\]\\[\\]]', $productHandle).val() ),
					(isNaN(vatType)? 0: vatType) / 100
				).toFixed(2)
			);
			invoiceRefreshProductTotal($productHandle);
		}

		function invoiceRefreshProductTotal($productHandle)
		{
			var total = parseFloatLocal( $('[name=product\\[vat_sum\\]\\[\\]]', $productHandle).val() ) +
			parseFloatLocal( $('[name=product\\[net_price_sum\\]\\[\\]]', $productHandle).val() );
			$('[name=product\\[total_price\\]\\[\\]]', $productHandle).val(
				(
					(isNaN(total)? 0: total).toFixed(2)
				)
			);
			invoiceRefreshTotal();
		}

		function invoiceRefreshTotal()
		{
			var price = 0.0;
			$('.product_row [name=product\\[total_price\\]\\[\\]]').each(function(index, item) {
				var val = parseFloatLocal( $(item).val() );
				price += isNaN(val)? 0: val;
			});

			$('[name=total_price]').val( price.toFixed(2) );
		}

		$('body.post-type-inspire_invoice .products_metabox')
			.on('click', '.remove_product', function(e) {
				e.preventDefault();

				$(this).parents('.product_row').remove();
				invoiceRefreshTotal();
			})
			.on('click', '.add_product', function(e) {
				e.preventDefault();

				var $container = $('.products_container');
				$('.product_prototype').clone()
					.find('input, select').prop('disabled', false).end()
					.removeClass('product_prototype')
					.appendTo($container)
					.show();
			})
			.on('change', '.refresh_net_price_sum', function(e) {
				var productHandle = $(this).parents('.product_row');
				invoiceRefreshProductNetPriceSum(productHandle);
			})
			.on('change', '.refresh_vat_sum', function(e) {
				var productHandle = $(this).parents('.product_row');
				invoiceRefreshProductVatRate(productHandle);
			})
			.on('change', '.refresh_total_price', function(e) {
				var productHandle = $(this).parents('.product_row');
				invoiceRefreshProductTotal(productHandle);
			})
			.on('change', '.refresh_total', function(e) {
				invoiceRefreshTotal();
			});

		$('body.post-type-inspire_invoice .get_user_data').click(function(e) {
			e.preventDefault();
			var $this = $(this);

			$.post(ajaxurl, {
					action: 'invoice-get-client-data',
					client: $this.parents('.form-field').find('select').val()
				}, function(result) {
					if (result.code == 100)
					{
						for (i in result.userdata)
						{
							$('[name=client\\[' + i + '\\]]').val(result.userdata[i]);
						}
					}
				}
			);
		});

		$('body.post-type-inspire_invoice .print_invoice').click(function(e) {
			e.preventDefault();
			var $this = $(this);

			function doAction()
			{
				//location.href = ajaxurl + '?action=invoice-get-pdf-invoice&id=' + $this.data('id') + '&hash=' + $this.data('hash');
				var url = ajaxurl + '?action=invoice-get-pdf-invoice&id=' + $this.data('id') + '&hash=' + $this.data('hash');
				window.open(url, '_blank');
			}

			if ($('body').hasClass('invoice-changed'))
			{
				if ( confirm(inspire_invoice_params.message_confirm) == true ) {
					doAction();
				}
			} else {
				doAction();
			}
		});

		$('body.post-type-inspire_invoice .download_invoice').click(function(e) {
			e.preventDefault();
			var $this = $(this);

			function doAction()
			{
				//location.href = ajaxurl + '?action=invoice-get-pdf-invoice&id=' + $this.data('id') + '&hash=' + $this.data('hash');
				var url = ajaxurl + '?action=invoice-get-pdf-invoice&id=' + $this.data('id') + '&hash=' + $this.data('hash') + '&save_file=1';
				window.open(url, '_blank');
			}

			if ($('body').hasClass('invoice-changed'))
			{
				if (confirm(inspire_invoice_params.message_confirm) == true) {
					doAction();
				}
			} else {
				doAction();
			}
		});

		$('body.post-type-inspire_invoice, body.post-type-inspire_invoice').on('change', 'select, input', function(e) {
			$('body').addClass('invoice-changed');
		});

		$('body.post-type-inspire_invoice .send_invoice').click(function(e) {
			e.preventDefault();
			var $this = $(this);

			function doAction()
			{
				$.post(ajaxurl, {
					action: 'invoice-send-by-email',
					id: $this.data('id'),
					hash: $this.data('hash'),
					email: $('#inspire_invoices_client_email').val()
				}, function(result) {
					if (result.code == 100)
					{
						alert(inspire_invoice_params.message_invoice_sent + result.email);
					} else {
						if (result.code == 102)
						{
							alert(inspire_invoice_params.message_invoice_not_sent_woo);
						} else {
							alert(inspire_invoice_params.message_not_sent);
						}

					}
				}
			);
			}

			if ($('body').hasClass('invoice-changed'))
			{
				if (confirm(inspire_invoice_params.message_not_saved_changes) == true) {
					doAction();
				}
			} else {
				doAction();
			}
		});
	});
})(jQuery);
