=== Flexible Invoices for WordPress ===
Contributors: wpdesk
Donate link: https://www.wpdesk.net/flexible-invoices-woocommerce/
Tags: invoices, order invoice, pdf invoice, pdf invoices, automatic billing, automatic invoice, billing invoice, download invoice, email invoice, shop invoice, wordpress invoicing, woocommerce invoice, woocommerce invoicing, woocommerce billing
Requires at least: 4.5
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 3.8.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy invoicing plugin for WordPress with PDF support. Supercharge it by installing additional extension for WooCommerce invoicing.

== Description ==

Flexible Invoices is a plugin that allows you to create PDF invoices in WordPress. You can also generate PDF invoices, manage currencies, tax rates and payment methods, generate reports, bulk download invoices and more.

Use it to bill clients or create invoices in your WooCommerce shop with [Flexible Invoices for WooCommerce](https://www.wpdesk.net/products/flexible-invoices-woocommerce/).

= Features =

* Issue and save invoices as PDF
* Add, edit and delete invoices (invoices are stored as custom post types)
* Manage currencies, tax rates and payment methods
* PDF template ready for VAT taxpayers and VAT-exempt entities
* Generate and bulk download invoices by a date range
* Custom numbering by using shortcodes and adding your own prefixes and suffixes
* Set the initial number for invoices if you begin using the plugin throughout the year
* Reports with a summary of all documents issued in a given time range

Check [Flexible Invoices Docs](https://www.wpdesk.net/docs/flexible-invoices-docs/) to see how to use the full potential of the plugin.

= WooCommerce Integration =

Create an invoicing suite for online shops by purchasing a WooCommerce extension: [Flexible Invoices for WooCommerce](https://www.wpdesk.net/products/flexible-invoices-woocommerce/).

= Advanced Reports =

Get better insights about your sales and use reports for accounting purposes [Advanced Invoices Reports](https://www.wpdesk.net/products/flexible-invoices-advanced-reports/).

= Docs =

[View Flexible Invoices Docs](https://www.wpdesk.net/docs/flexible-invoices-docs/)

= Support Policy =

We provide a limited support for the free version in the [plugin Support Forum](https://wordpress.org/support/plugin/flexible-invoices/). Please purchase a WooCommerce extension to get priority e-mail support as well as all e-commerce features. [Upgrade Now &rarr;](https://www.wpdesk.net/products/flexible-invoices-woocommerce/)

> **Get more WooCommerce plugins from WP Desk**<br />
> We provide premium plugins for customizing checkout, shipping, invoicing and more. Check out our [premium WooCommerce plugins here →](https://www.wpdesk.net/products/)

= Flexible Invoices in a nutshell =

Flexible Invoices is a plugin to generate invoices. These are the PDF invoices. You don't need the WooCommerce. It runs well in a standalone WordPress. You can call this plugin either WordPress invoicing plugin or WordPress invoices plugin or even the WordPress billing plugin. The PRO version supports WooCommerce integration so the name goes like this: WooCommerce invoicing plugin, WooCommerce invoices plugin or WooCommerce billing. So many names for such an easy to configure tool :)

== Installation	 ==

You can install this plugin like any other WordPress plugin.

1. Download and unzip the latest release zip file.
2. Upload the entire plugin directory to your /wp-content/plugins/ directory.
3. Activate the plugin through the Plugins menu in WordPress Administration.

You can also use WordPress uploader to upload plugin zip file in menu Plugins -> Add New -> Upload Plugin. Then go directly to point 3.

== Frequently Asked Questions ==

= Can I use the plugin with WooCommerce? =

Yes, but you will have to purchase an extension [Flexible Invoices for WooCommerce](https://www.wpdesk.net/products/flexible-invoices-woocommerce/).

== Screenshots ==

1. Issuing Invoices.
2. All Invoices.
3. General Settings.
4. Invoices Settings.
5. Bulk Generate and Download Invoices.
6. Currency Settings.
7. Tax Settings.
8. Payment Methods.

== Upgrade Notice ==

If you are upgrading from the old WordPress Invoices version (3.2.1, wordpress-invoices) make sure to completely delete the old version first. Your data and invoices will be kept. If you install the new version without deleting the old one you may break your WordPress installation.

== Changelog ==

= 3.8.4 - 2018-10-16 =
* Added support for WooCommerce 3.5
* Dropped support for WooCommerce below 3.0 (the plugin may still work with older versions but we do not declare official support)

= 3.8.3 - 2018-09-19 =
* Fixed roles for bulk delete

= 3.8.2 - 2018-09-18 =
* Added support for roles. Only Administrator and Shop manager roles have access

= 3.8.1 - 2018-06-26 =
* Fixed error with conflict in tracker

= 3.8 - 2018-06-20 =
* Added ability to add invoice notes with flexible_invoices_after_invoice_notes hook
* Tweaked hiding tax columns to include Gross amount
* Tweaked tracker data anonymization 
* Fixed tracker notice

= 3.7.6 - 2018-05-23 =
* Added support for WooCommerce 3.4
* Fixed warnings when issue invoice

= 3.7.5 - 2018-04-03 =
* Tweaked font size in invoice print
* Tweaked SKU column size to wrap around 6 chars

= 3.7.4 - 2018-03-06 =
* Fixed problems with deactivation plugin on multisite

= 3.7.3 - 2018-02-19 =
* Fixed display on invoice the Bank Account Number after editing the invoice

= 3.7.2 - 2018-01-16 =
* Added support for WooCommerce 3.3
* Fixed issue enter saving for company name
* Fixed issue with skipping invoice numbers

= 3.7.1 - 2017-12-22 =
* Fixed minor issue with translations

= 3.7 - 2017-12-21 =
* Added ability to reset invoices and corrections numbering

= 3.6 - 2017-12-14 =
* Added action to change invoice language
* Added filter to change default currency symbol
* Tweaked characters escaping for corrections
* Fixed defeault display currency code instead currency symbol

= 3.5.1 - 2017-11-22 =
* Fixed rows display for corrections when tax is off
* Fixed corrections currency symbol display when currency position is left

= 3.5 - 2017-11-08 =
* Added support for correction invoices introduced in WooCommerce Flexible Invoices 2.5
* Fixed docs links
* Fixed row wrap in SKU column above 10 characters

= 3.4.9 - 2017-10-10 =
* Added compatibility with WooCommerce 3.2
* Dropped compatibility with WooCommerce 2.5.x (the plugin may still work but we do not declare official support)

= 3.4.8 - 2017-06-22 =
* Integrated WP Desk Tracker class to help us understand how you use the plugin (you need to opt in to enable it)
* Fixed some minor notices at WooCommerce 3.0
* Readded translation files to prevent confusion

= 3.4.7 - 2017-03-23 =
* Changed for Polish translation: Data zapłaty to Termin płatności
* Fixed issuing invoices when there is no Flexible Invoices - WooCommerce
* Fixed duplicate numbering invoices for automatic issuing
* Fixed some minor notices

= 3.4.6 - 2017-01-19 =
* Added invoices managment for Shop Manager role if using Flexible Invoices WooCommerce extension
* Fixed some minor notices

= 3.4.5 - 2017-01-09 =
* Removed error logging used for development purposes

= 3.4.4 - 2016-11-23 =
* Fixed templates for child themes
* Tweaked signatures display on PDF invoice

= 3.4.3 - 2016-11-22 =
* Added ability to edit seller's information when editing invoices
* Removed translation files in favor of WordPress Directory translations: https://translate.wordpress.org/projects/wp-plugins/flexible-invoices
* Tweaked separators in reports
* Tweaked seller's name to display name instead of username

= 3.4.2 - 2016-11-16 =
* Added information about Advanced Reports Extension
* Tweaked rounding in reports
* Removed calendar icon from date picker - now the calendar opens on focus

= 3.4.1 - 2016-11-02 =
* Fixed loading CSS on All Invoices page

= 3.4 - 2016-11-02 =
* Added currency to reports
* Upgraded mpdf library to 6.1
* Conditionally loading assets
* Added new lines to invoice notes
* Tweaked decimal separator to use locale settings

= 3.3.4 - 2016-10-07 =
* Fixed a potential CSS conflict

= 3.3.3 - 2016-09-16 =
* Fixed overriding invoice template in themes

= 3.3.2 - 2016-08-26 =
* Fixed capabilities problem when settings were not accessible by admin

= 3.3.1 - 2016-08-24 =
* Fixed issues with multiline textareas
* Fixed issues with deleting currencies

= 3.3 - 2016-08-23 =
* Major Update!
* Released in WordPress Plugin Repository
* Changed name from WordPress Invoices to Flexible Invoices for WordPress
* Added better invoices and customer search
* Added payment methods management
* Added currencies management
* Added tax rates management
* Added custom label for date of sale
* Fixed logo rendering

= 3.2.1 - 2016-05-23 =
* Tweaked to issue invoices based on WordPress time zone settings
* Re-added Dutch (nl_NL) translation

= 3.2 - 2016-04-05 =
* Converted to English
* Added Polish (pl_PL) translation
* Added view/download actions to invoices list and edit pages
* Fixed deleted invoice handling

= 3.1.1 - 2016-03-04 =
* Added Dutch (nl_NL) translation
* Added 21% tax rate

= 3.1 - 2016-01-26 =
* Licensing system and automatic updates

= 3.0.1 - 2015-12-23 =
* Fixed a warning in /wordpress-invoices/class/invoicePostType.php on line 55

= 3.0 - 2015-12-21 =
* Added invoice caching and batch zip downloading

= 2.13 - 2015-11-10 =
* Fixed PHP short tags in generated_invoice.php

= 2.12 - 2015-10-15 =
* Added option for hiding VAT number on PDF invoice

= 2.11 - 2015-09-09 =
* Added option for hiding VAT fields on PDF invoice

= 2.10 - 2015-03-04 =
* Tweaked company name field (allowing quotes)
* Tweaked debug mode
* Upgrade to MPDF 5.7.4

= 2.9 - 2015-02-20 =
* Tweaked debug mode
* Fixed prices refreshing after product removal

= 2.8.1 - 2015-01-05 =
* Tweaked displaying payment method (do not show if unknown)

= 2.8 - 2014-10-17 =
* Tweaked invoice file name

= 2.7 - 2014-10-14 =
* Tweaked reporting based on date of sell

= 2.6 - 2014-10-01 =
* Tweaks in invoice template (prices and payments)
* Fixed invoice preview in WP dashboard

= 2.5 - 2014-09-30 =
* Tweaks in invoice template (order number)

= 2.4 - 2014-09-28 =
* Tweaks after accounting company audit

= 2.3.1 - 2014-09-26 =
* Tweaked decimals in prices and quantities
* Fixed VAT number handling

= 2.2.1 - 2014-09-26 =
* Fixed working with other plugins

= 2.2 - 2014-08-29 =
* Fixed templates handling
* Fixed invoice notes display

= 2.1 - 2014-08-28 =
* Fixed access to invoices for not logged in users

= 2.0 - 2014-08-21 =
* Major update!
* Added invoicing for WordPress (not only WooCommerce)
* Added PDF invoices
* Fixed lots of small issues

= 1.3 - 2014-02-27 =
* Added WooCommerce 2.1 compatibility

= 1.2 - 2013-01-10 =
* Added payment status to the invoice template

= 1.1 - 2013-12-19 =
* Fixed coupon handling

= 1.0 - 2013-11-25 =
* First Release!
