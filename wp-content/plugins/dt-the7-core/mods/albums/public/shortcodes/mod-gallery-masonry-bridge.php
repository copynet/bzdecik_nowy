<?php
/**
 * Gallery masonry/grid.
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'weight'            => -1,
	'name'              => __( 'Photos Masonry & Grid', 'the7mk2' ),
	'description'       => __( 'Images from Photo Albums post type', 'dt-the7-core' ),
	'base'              => 'dt_gallery_photos_masonry',
	'class'             => 'dt_vc_sc_gallery_masonry',
	'icon'              => 'dt_vc_ico_photos',
	'category'          => __( 'by Dream-Theme', 'the7mk2' ),
	'admin_enqueue_css' => array(
		PRESSCORE_THEME_URI . '/fonts/icomoon-the7-gallery-32x32/style.css',
	),
	'params'            => array(
		// General group.

		array(
			'heading' => __('Show', 'the7mk2'),
			'param_name' => 'post_type',
			'type' => 'dropdown',
			'std' => 'category',
			'value' => array(
				'Images from all albums' => 'posts',
				'Images from albums in categories' => 'category',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Choose albums', 'the7mk2' ),
			'param_name' => 'posts',
			'settings' => array(
				'multiple' => true,
				'min_length' => 0,
			),
			'save_always' => true,
			'description' => __( 'Field accept album ID, title. Leave empty to show images from all albums.', 'the7mk2' ),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'post_type',
				'value' => 'posts',
			),
		),
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Choose albums categories', 'the7mk2' ),
			'param_name' => 'category',
			'settings' => array(
				'multiple' => true,
				'min_length' => 0,
			),
			'save_always' => true,
			'description' => __( 'Field accept album category ID, title, slug. Leave empty to show images from all albums.', 'the7mk2' ),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'post_type',
				'value' => 'category',
			),
		),
		array(
			'heading' => __('Order', 'the7mk2'),
			'param_name' => 'order',
			'type' => 'dropdown',
			'std' => 'desc',
			'value' => array(
				'Ascending' => 'asc',
				'Descending' => 'desc',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading' => __('Order by', 'the7mk2'),
			'param_name' => 'orderby',
			'type' => 'dropdown',
			'value' => array(
				'Date' => 'date',
				'Name' => 'title',
				'Rand' => 'rand',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		// - Layout Settings.
		array(
			'heading'    => __( 'Layout, Columns & Responsiveness', 'the7mk2' ),
			'param_name' => 'dt_title',
			'type'       => 'dt_title',
			'value'      => '',
		),
		array(
			'heading'          => __( 'Mode', 'the7mk2' ),
			'param_name'       => 'mode',
			'type'             => 'dropdown',
			'value'            => array(
				'Masonry' => 'masonry',
				'Grid'    => 'grid',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		// - Columns & Responsiveness.
		array(
			'heading'          => __( 'Responsiveness mode', 'the7mk2' ),
			'param_name'       => 'responsiveness',
			'type'             => 'dropdown',
			'value'            => array(
				'Browser width based' => 'browser_width_based',
				'Post width based'    => 'post_width_based',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		// -- Browser width based.
		array(
			'heading'    => __( 'Number of columns', 'the7mk2' ),
			'param_name' => 'bwb_columns',
			'type'       => 'dt_responsive_columns',
			'value'      => 'desktop:6|h_tablet:4|v_tablet:3|phone:2',
			'dependency' => array(
				'element' => 'responsiveness',
				'value'   => 'browser_width_based',
			),
		),
		// -- Post width based.
		array(
			'heading'          => __( 'Column minimum width', 'the7mk2' ),
			'param_name'       => 'pwb_column_min_width',
			'type'             => 'dt_number',
			'value'            => '',
			'units'            => 'px',
			'dependency'       => array(
				'element' => 'responsiveness',
				'value'   => 'post_width_based',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'heading'          => __( 'Desired columns number', 'the7mk2' ),
			'param_name'       => 'pwb_columns',
			'type'             => 'dt_number',
			'value'            => '',
			'units'            => '',
			'max'              => 12,
			'description'      => __( 'Affects only masonry layout', 'the7mk2' ),
			'dependency'       => array( 'element' => 'responsiveness', 'value' => 'post_width_based' ),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'heading'          => __( 'Gap between columns', 'the7mk2' ),
			'param_name'       => 'gap_between_posts',
			'type'             => 'dt_number',
			'value'            => '5px',
			'units'            => 'px',
			'description'      => __( 'Please note that this setting affects post paddings. So, for example: a value 10px will give you 20px gaps between posts)', 'the7mk2' ),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'heading'          => __( 'Loading effect', 'the7mk2' ),
			'param_name'       => 'loading_effect',
			'type'             => 'dropdown',
			'value'            => array(
				'None'             => 'none',
				'Fade in'          => 'fade_in',
				'Move up'          => 'move_up',
				'Scale up'         => 'scale_up',
				'Fall perspective' => 'fall_perspective',
				'Fly'              => 'fly',
				'Flip'             => 'flip',
				'Helix'            => 'helix',
				'Scale'            => 'scale',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),


		// - Image Settings.
		array(
			'heading'    => __( 'Image Settings', 'the7mk2' ),
			'param_name' => 'dt_title',
			'type'       => 'dt_title',
			'value'      => '',
		),
		array(
			'heading'          => __( 'Image sizing', 'the7mk2' ),
			'param_name'       => 'image_sizing',
			'type'             => 'dropdown',
			'std'              => 'proportional',
			'value'            => array(
				'Preserve images proportions' => 'proportional',
				'Resize images'               => 'resize',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'headings'    => array( __( 'Width', 'the7mk2' ), __( 'Height', 'the7mk2' ) ),
			'param_name'  => 'resized_image_dimensions',
			'type'        => 'dt_dimensions',
			'value'       => '1x1',
			'dependency'  => array(
				'element' => 'image_sizing',
				'value'   => 'resize',
			),
			'description' => __( 'Set image proportions, for example: 4x3, 3x2.', 'the7mk2' ),
		),
		array(
			'heading'          => __( 'Image border radius', 'the7mk2' ),
			'param_name'       => 'image_border_radius',
			'type'             => 'dt_number',
			'value'            => '0',
			'units'            => 'px',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			"type" => "dropdown",
			"heading" => __("Image decoration", 'the7mk2'),
			"param_name" => "image_decoration",
			"value" => array(
				"None" => "none",
				"Shadow" => "shadow",
			),
			"edit_field_class" => "vc_col-xs-12 vc_column dt_row-6",
		),
		array(
			'heading' => __('Horizontal length', 'the7mk2'),
			'param_name' => 'shadow_h_length',
			'type' => 'dt_number',
			'value' => '0px',
			'units' => 'px',
			'dependency' => array(
				'element' => 'image_decoration',
				'value' => 'shadow',
			),
		),
		array(
			'heading' => __('Vertical length', 'the7mk2'),
			'param_name' => 'shadow_v_length',
			'type' => 'dt_number',
			'value' => '4px',
			'units' => 'px',
			'dependency' => array(
				'element' => 'image_decoration',
				'value' => 'shadow',
			),
		),
		array(
			'heading' => __('Blur radius', 'the7mk2'),
			'param_name' => 'shadow_blur_radius',
			'type' => 'dt_number',
			'value' => '12px',
			'units' => 'px',
			'dependency' => array(
				'element' => 'image_decoration',
				'value' => 'shadow',
			),
		),
		array(
			'heading' => __('Spread', 'the7mk2'),
			'param_name' => 'shadow_spread',
			'type' => 'dt_number',
			'value' => '3px',
			'units' => 'px',
			'dependency' => array(
				'element' => 'image_decoration',
				'value' => 'shadow',
			),
		),
		array(
			"heading"		=> __("Shadow color", 'the7mk2'),
			"type"			=> "colorpicker",
			"param_name"	=> "shadow_color",
			"value"			=> 'rgba(0,0,0,.25)',
			'dependency' => array(
				'element' => 'image_decoration',
				'value' => 'shadow',
			),
		),
		array(
			'heading'          => __( 'Scale animation on hover', 'the7mk2' ),
			'param_name'       => 'image_scale_animation_on_hover',
			'type'             => 'dropdown',
			'std'              => 'quick_scale',
			'value'            => array(
				'Disabled'    => 'disabled',
				'Quick scale' => 'quick_scale',
				'Slow scale'  => 'slow_scale',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),

		array(
			'heading'          => __( 'Hover background color', 'the7mk2' ),
			'param_name'       => 'image_hover_bg_color',
			'type'             => 'dropdown',
			'std'              => 'default',
			'value'            => array(
				'Disabled'    => 'disabled',
				'Default'     => 'default',
				'Mono color' => 'solid_rollover_bg',
				'Gradient'    => 'gradient_rollover_bg',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
		),
		array(
			'heading'     => __( 'Background color', 'the7mk2' ),
			'param_name'  => 'custom_rollover_bg_color',
			'type'        => 'colorpicker',
			'value'       => 'rgba(0,0,0,0.5)',
			'dependency'  => array(
				'element' => 'image_hover_bg_color',
				'value'   =>  'solid_rollover_bg',
			),
		),
		array(
			'heading'    => __( 'Gradient', 'the7mk2' ),
			'param_name' => 'custom_rollover_bg_gradient',
			'type'       => 'dt_gradient_picker',
			'value'      => '45deg|rgba(12,239,154,0.8) 0%|rgba(0,108,220,0.8) 50%|rgba(184,38,220,0.8) 100%',
			'dependency' => array(
				'element' => 'image_hover_bg_color',
				'value'   => 'gradient_rollover_bg',
			),
		),
		array(
			'heading'          => __( 'Hover background animation', 'dt-the7-core' ),
			'param_name'       => 'hover_animation',
			'type'             => 'dropdown',
			'value'            => array(
				'Fade'                    => 'fade',
				'Direction aware'         => 'direction_aware',
				'Reverse direction aware' => 'redirection_aware',
				'Scale in'                => 'scale_in',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency'       => array(
				'element' => 'image_hover_bg_color',
				'value'   => 'gradient_overlay',
				'value'   => array( 'solid_rollover_bg', 'gradient_rollover_bg' ),
			),
		),
		//Icons

		array(
			'group'      => __( 'Hover Icon', 'the7mk2' ),
			'heading'    => __( 'Show icon on image hover', 'the7mk2' ),
			'param_name' => 'show_zoom',
			'type'       => 'dt_switch',
			'value'      => 'y',
			'options'    => array(
				'Yes' => 'y',
				'No'  => 'n',
			),
		),
		array(
			'group'      => __( 'Hover Icon', 'the7mk2' ),
			'heading'    => __( 'Choose image zoom icon', 'the7mk2' ),
			'param_name' => 'gallery_image_zoom_icon',
			'type'       => 'dt_navigation',
			'value'      => 'icon-im-hover-001',
			'dependency' => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Icon Size & Background', 'the7mk2' ),
			'param_name'       => 'dt_project_icon_title',
			'type'             => 'dt_title',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Icon size', 'the7mk2' ),
			'param_name'       => 'project_icon_size',
			'type'             => 'dt_number',
			'value'            => '32px',
			'units'            => 'px',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Icon color', 'the7mk2' ),
			'description'      => __( 'Live empty to use accent color.', 'the7mk2' ),
			'param_name'       => 'project_icon_color',
			'type'             => 'colorpicker',
			'value'            => 'rgba(255,255,255,1)',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),

		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Background size', 'the7mk2' ),
			'param_name'       => 'project_icon_bg_size',
			'type'             => 'dt_number',
			'value'            => '44px',
			'units'            => 'px',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Paint background', 'the7mk2' ),
			'param_name'       => 'project_icon_bg',
			'type'             => 'dt_switch',
			'value'            => 'n',
			'options'          => array(
				'Yes' => 'y',
				'No'  => 'n',
			),
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Background color', 'the7mk2' ),
			'param_name'       => 'project_icon_bg_color',
			'type'             => 'colorpicker',
			'value'            => 'rgba(255,255,255,0.3)',
			'dependency'       => array(
				'element' => 'project_icon_bg',
				'value'   => 'y',
			),
			'description'      => __( 'Live empty to use accent color.', 'the7mk2' ),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Border radius', 'the7mk2' ),
			'param_name'       => 'project_icon_border_radius',
			'type'             => 'dt_number',
			'value'            => '100px',
			'units'            => 'px',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Border width', 'the7mk2' ),
			'param_name'       => 'project_icon_border_width',
			'type'             => 'dt_number',
			'value'            => '0',
			'units'            => 'px',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		array(
			'group'            => __( 'Hover Icon', 'the7mk2' ),
			'heading'          => __( 'Border color', 'the7mk2' ),
			'description'      => __( 'Live empty to use accent color.', 'the7mk2' ),
			'param_name'       => 'project_icon_border_color',
			'type'             => 'colorpicker',
			'value'            => '',
			'dependency'       => array(
				'element' => 'show_zoom',
				'value'   => 'y',
			),
			'edit_field_class' => 'the7-icons-dependent vc_col-xs-12',
		),
		// - Pagination group.
		array(
			'heading'    => __( 'Pagination', 'the7mk2' ),
			'param_name' => 'dt_title',
			'type'       => 'dt_title',
			'value'      => '',
			'group'      => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'          => __( 'Pagination mode', 'the7mk2' ),
			'param_name'       => 'loading_mode',
			'type'             => 'dropdown',
			'std'              => 'disabled',
			'value'            => array(
				'Disabled'           => 'disabled',
				'Standard' => 'standard',
				'JavaScript pages'   => 'js_pagination',
				'"Load more" button' => 'js_more',
				'Infinite scroll'    => 'js_lazy_loading',
			),
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'group'            => __( 'Pagination', 'the7mk2' ),
		),
		// -- Disabled.
		array(
			'heading' => __('Total number of images', 'the7mk2'),
			'param_name' => 'dis_posts_total',
			'type' => 'dt_number',
			'value' => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'disabled',
			),
			'description' => __('Leave empty to display all posts.', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		// -- Standard.
		array(
			'heading' => __('Number of posts to display on one page', 'the7mk2'),
			'param_name' => 'st_posts_per_page',
			'type' => 'dt_number',
			'value' => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'standard',
			),
			'description' => __('Leave empty to use number from wp settings.', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading' => __('Show all pages in paginator', 'the7mk2'),
			'param_name' => 'st_show_all_pages',
			'type' => 'dt_switch',
			'value' => 'n',
			'options' => array(
				'Yes' => 'y',
				'No' => 'n',
			),
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'standard',
			),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading' => __('Gap before pagination', 'the7mk2'),
			'param_name' => 'st_gap_before_pagination',
			'type' => 'dt_number',
			'value' => '',
			'units' => 'px',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'standard',
			),
			'description' => __('Leave empty to use default gap', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		// -- JavaScript pages.
		array(
			'heading' => __('Total number of posts', 'the7mk2'),
			'param_name' => 'jsp_posts_total',
			'type' => 'dt_number',
			'value' => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'js_pagination',
			),
			'description' => __('Leave empty to display all posts.', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'          => __( 'Number of images to display on one page', 'the7mk2' ),
			'param_name'       => 'jsp_posts_per_page',
			'type'             => 'dt_number',
			'value'            => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency'       => array(
				'element' => 'loading_mode',
				'value'   => 'js_pagination',
			),
			'description'      => __( 'Leave empty to use number from wp settings.', 'the7mk2' ),
			'group'            => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'    => __( 'Show all pages in paginator', 'the7mk2' ),
			'param_name' => 'jsp_show_all_pages',
			'type'       => 'dt_switch',
			'value'      => 'n',
			'options'    => array(
				'Yes' => 'y',
				'No'  => 'n',
			),
			'dependency' => array(
				'element' => 'loading_mode',
				'value'   => 'js_pagination',
			),
			'group'      => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'     => __( 'Gap before pagination', 'the7mk2' ),
			'param_name'  => 'jsp_gap_before_pagination',
			'type'        => 'dt_number',
			'value'       => '',
			'units'       => 'px',
			'dependency'  => array(
				'element' => 'loading_mode',
				'value'   => 'js_pagination',
			),
			'description' => __( 'Leave empty to use default gap', 'the7mk2' ),
			'group'       => __( 'Pagination', 'the7mk2' ),
		),
		// -- js Load more.
		array(
			'heading' => __('Total number of posts', 'the7mk2'),
			'param_name' => 'jsm_posts_total',
			'type' => 'dt_number',
			'value' => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'js_more',
			),
			'description' => __('Leave empty to display all posts.', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'          => __( 'Number of images to display on one page', 'the7mk2' ),
			'param_name'       => 'jsm_posts_per_page',
			'type'             => 'dt_number',
			'value'            => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency'       => array(
				'element' => 'loading_mode',
				'value'   => 'js_more',
			),
			'description'      => __( 'Leave empty to use number from wp settings.', 'the7mk2' ),
			'group'            => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'     => __( 'Gap before pagination', 'the7mk2' ),
			'param_name'  => 'jsm_gap_before_pagination',
			'type'        => 'dt_number',
			'value'       => '',
			'units'       => 'px',
			'dependency'  => array(
				'element' => 'loading_mode',
				'value'   => 'js_more',
			),
			'description' => __( 'Leave empty to use default gap', 'the7mk2' ),
			'group'       => __( 'Pagination', 'the7mk2' ),
		),
		// -- js Infinite scroll.
		array(
			'heading' => __('Total number of posts', 'the7mk2'),
			'param_name' => 'jsl_posts_total',
			'type' => 'dt_number',
			'value' => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency' => array(
				'element' => 'loading_mode',
				'value'	=> 'js_lazy_loading',
			),
			'description' => __('Leave empty to display all posts.', 'the7mk2'),
			'group' => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'          => __( 'Number of images to display on one page', 'the7mk2' ),
			'param_name'       => 'jsl_posts_per_page',
			'type'             => 'dt_number',
			'value'            => '',
			'edit_field_class' => 'vc_col-xs-12 vc_column dt_row-6',
			'dependency'       => array(
				'element' => 'loading_mode',
				'value'   => 'js_lazy_loading',
			),
			'description'      => __( 'Leave empty to use number from wp settings.', 'the7mk2' ),
			'group'            => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'    => __( 'Color Settings', 'the7mk2' ),
			'param_name' => 'dt_title',
			'type'       => 'dt_title',
			'group'      => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'     => __( 'Font color', 'the7mk2' ),
			'param_name'  => 'navigation_font_color',
			'type'        => 'colorpicker',
			'value'       => '',
			'description' => __( 'Leave empty to use headers color.', 'the7mk2' ),
			'group'       => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'heading'     => __( 'Accent color', 'the7mk2' ),
			'param_name'  => 'navigation_accent_color',
			'type'        => 'colorpicker',
			'value'       => '',
			'description' => __( 'Leave empty to use accent color.', 'the7mk2' ),
			'group'       => __( 'Pagination', 'the7mk2' ),
		),
		array(
			'type'       => 'css_editor',
			'heading'    => __( 'CSS box', 'the7mk2' ),
			'param_name' => 'css_dt_gallery',
			'group'      => __( 'Design Options', 'the7mk2' ),
		),
	),
);
