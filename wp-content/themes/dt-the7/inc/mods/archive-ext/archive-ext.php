<?php
/**
 * Arhive templates module.
 * @package the7
 * @since   3.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Presscore_Modules_ArchiveExtModule', false ) ) :

	class Presscore_Modules_ArchiveExtModule {

		const ARCHIVE_OPTIONS_MENU_SLUG = 'of-archives-templates-menu';

		/**
		 * @var The7_Archive_Shortcodes_Manager
		 */
		public static $archive_manager;

		/**
		 * Execute module.
		 */
		public static function execute() {
			require_once dirname( __FILE__ ) . '/class-the7-archive-shortcodes-handler.php';
			require_once dirname( __FILE__ ) . '/class-the7-archive-shortcodes-manager.php';
			require_once dirname( __FILE__ ) . '/class-the7-archive-shortcodes-map.php';

			$shortcodes_manager = self::get_archive_shortcodes_manager();
			add_action( 'admin_init', array( $shortcodes_manager, 'add_cache_invalidation_hooks' ) );

//			add_action( 'the7_archive_populate_config', array( __CLASS__, 'populate_template_config' ) );
			$supported_post_types = array(
				'post',
				'dt_portfolio',
				'dt_gallery',
			);
			foreach ( $supported_post_types as $post_type ) {
				add_filter( "the7_{$post_type}_archive_loop", array( __CLASS__, 'loop' ) );
			}
			add_filter( 'the7_search_loop', array( __CLASS__, 'loop' ) );

			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts_filter' ) );
			}

			add_action( 'presscore_config_base_init', array( __CLASS__, 'archive_config_action' ) );
			add_filter( 'presscore_config_post_id_filter', array( __CLASS__, 'config_page_id_filter' ) );
			add_filter( 'presscore_options_files_list', array( __CLASS__, 'add_theme_options_filter' ) );
		}

		/**
		 * Alter current page value with archive template id in the theme config.
		 *
		 * @param int|null $page_id
		 *
		 * @return int|null
		 */
		public static function config_page_id_filter( $page_id = null ) {
			if ( $page_id ) {
				return $page_id;
			}

			if ( is_single() || is_page() ) {
				return get_the_ID();
			}

			$archive_template_id = self::get_archive_template_id();
			if ( $archive_template_id ) {
				return $archive_template_id;
			}

			return $page_id;
		}

		/**
		 * Match template shortcode and alter main query.
		 *
		 * @param WP_Query $query
		 */
		public static function pre_get_posts_filter( $query ) {
			if ( ! $query->is_main_query() || ! ( $query->is_archive() || $query->is_search() ) ) {
				return;
			}

			$template_id = self::get_archive_template_id();
			if ( ! $template_id ) {
				return;
			}

			$page = get_post( $template_id );

			if ( ! is_object( $page ) ) {
				return;
			}

			$shortcodes_manager = self::get_archive_shortcodes_manager();

			if ( ! $shortcodes_manager->match_shortcode( $page->post_content ) ) {
				return;
			}

			$posts_per_page = get_option( 'posts_per_page', 10 );
			$atts = $shortcodes_manager->get_shortcode_atts();
			switch ( $shortcodes_manager->get_shortcode_tag() ) {
				case 'dt_blog_list':
				case 'dt_blog_masonry':
				case 'dt_portfolio_masonry':
				case 'dt_team_masonry':
				case 'dt_testimonials_masonry':
					if ( isset( $atts['loading_mode'] ) ) {
						$mode = $atts['loading_mode'];
						$ppp_map = array(
							'standard'        => 'st_posts_per_page',
							'js_pagination'   => 'jsp_posts_per_page',
							'js_more'         => 'jsm_posts_per_page',
							'js_lazy_loading' => 'jsl_posts_per_page',
						);
						if ( array_key_exists( $mode, $ppp_map ) && ! empty( $atts[ $ppp_map[ $mode ] ] ) ) {
							$posts_per_page = (int) $atts[ $ppp_map[ $mode ] ];
						}
					}
					break;
				case 'dt_portfolio_jgrid':
				case 'dt_albums':
				case 'dt_albums_jgrid':
					if ( ! empty( $atts['posts_per_page'] ) ) {
						$posts_per_page = $atts['posts_per_page'];
					}
					break;
				default:
					return;
			}

			$query->set( 'posts_per_page', $posts_per_page );
		}

		/**
		 * Return archive template id.
		 *
		 * @return int
		 */
		public static function get_archive_template_id() {
			$page_id = 0;
			if ( is_search() ) {
				$page_id = of_get_option( 'template_page_id_search', null );
			} else if ( is_category() ) {
				$page_id = of_get_option( 'template_page_id_blog_category', null );
			} else if ( is_tag() ) {
				$page_id = of_get_option( 'template_page_id_blog_tags', null );
			} else if ( is_author() ) {
				$page_id = of_get_option( 'template_page_id_author', null );
			} else if ( is_date() || is_day() || is_month() || is_year() ) {
				$page_id = of_get_option( 'template_page_id_date', null );
			}

			$page_id = apply_filters( 'the7_archive_page_template_id', $page_id );

			return (int) $page_id;
		}

		/**
		 * Factory method for The7_Archive_Shortcodes_Manager.
		 *
		 * @return The7_Archive_Shortcodes_Manager
		 */
		public static function get_archive_shortcodes_manager() {
			if ( ! self::$archive_manager ) {
				$shortcodes_map = new The7_Archive_Shortcodes_Map();
				$shortcodes_handler = new The7_Archive_Shortcodes_Handler();
				self::$archive_manager = new The7_Archive_Shortcodes_Manager( $shortcodes_map, $shortcodes_handler );
			}

			return self::$archive_manager;
		}

		/**
		 * Provide archive basic configuration.
		 */
		public static function archive_config_action() {
			if ( ! ( is_archive() || is_search() ) ) {
				return;
			}

			/**
			 * Bail if shortcode is used as a template.
			 */
			$archive_manager = self::get_archive_shortcodes_manager();
			if ( $archive_manager->get_shortcode_tag() ) {
				return;
			}

			$config = presscore_config();

			$config->set( 'show_titles', true );
			$config->set( 'show_excerpts', true );

			$config->set( 'show_links', true );
			$config->set( 'show_details', true );
			$config->set( 'show_zoom', true );

			$config->set( 'post.meta.fields.date', true );
			$config->set( 'post.meta.fields.categories', true );
			$config->set( 'post.meta.fields.comments', true );
			$config->set( 'post.meta.fields.author', true );
			$config->set( 'post.meta.fields.media_number', true );

			$config->set( 'post.preview.width.min', 320 );
			$config->set( 'post.preview.mini_images.enabled', true );
			$config->set( 'post.preview.load.effect', 'fade_in' );
			$config->set( 'post.preview.background.enabled', true );
			$config->set( 'post.preview.background.style', 'fullwidth' );
			$config->set( 'post.preview.description.alignment', 'left' );
			$config->set( 'post.preview.description.style', 'under_image' );

			$config->set( 'post.preview.hover.animation', 'fade' );
			$config->set( 'post.preview.hover.color', 'accent' );
			$config->set( 'post.preview.hover.content.visibility', 'on_hoover' );

			$config->set( 'post.fancy_date.enabled', false );

			$config->set( 'template.columns.number', 3 );
			$config->set( 'load_style', 'default' );
			$config->set( 'image_layout', 'original' );
			$config->set( 'all_the_same_width', true );
			$config->set( 'item_padding', 10 );

			$config->set( 'layout', 'masonry' );
			$config->set( 'template.layout.type', 'masonry' );

			do_action( 'the7_archive_populate_config' );
		}

		/**
		 * Archive main loop.
		 *
		 * @param bool $res
		 *
		 * @return bool
		 */
		public static function loop( $res = false ) {
			if ( $res ) {
				return $res;
			}

			$config = presscore_config();
			$page_id = (int) $config->get( 'page_id' );

			// Check page content.
			$page = get_post( $page_id ? $page_id : - 1 );

			// On invalid page display generic archive.
			if ( ! is_object( $page ) ) {
				return false;
			}

			$page_template_layout = false;
//			$page_template_layout = self::get_page_template_layout( $page_id );
			$shortcodes_manager = self::get_archive_shortcodes_manager();

			if ( ! $shortcodes_manager->get_shortcode_tag() ) {
				return false;
			}

			do_action( 'presscore_before_loop' );

			if ( $page_template_layout ) {
//				presscore_get_template_part( 'theme', "archive/blog-{$page_template_layout}" );
			} else {
				$atts = $shortcodes_manager->get_shortcode_atts();

				// Disable pagination and filters.
				$atts['loading_mode'] = 'disabled';
				$atts['show_categories_filter'] = 'n';
				$atts['show_orderby_filter'] = 'n';
				$atts['show_order_filter'] = 'n';
				$atts['show_filter'] = '';
				$atts['show_orderby'] = '';
				$atts['show_order'] = '';
				$atts['posts_per_page'] = '-1';

				$shortcodes_manager->set_shortcode_atts( $atts );
				$shortcodes_manager->display_shortcode();
			}

			dt_paginator();
			do_action( 'presscore_after_loop' );

			return true;
		}

		/**
		 * Populate config for posts archive.
		 */
		public static function populate_template_config() {
			if ( ! ( is_category() || is_tag() ) ) {
				return;
			}

			$config = presscore_config();
			$post_id = $config->get( 'post_id' );

			// In archive use page_id, NOT post_id.
			$config->set( 'page_id', $post_id );

			$layout = self::get_page_template_layout( $post_id );
			if ( ! $layout ) {
				return;
			}

			$config->set( 'template', 'blog' );
			$config->set( 'template.layout.type', $layout );
			presscore_congif_populate_blog_vars();
		}

		/**
		 * Retrieve blog template name.
		 *
		 * @param string|int $post_id
		 *
		 * @return string
		 */
		protected static function get_page_template_layout( $post_id ) {
			$page_template = dt_get_template_name( $post_id );
			switch ( $page_template ) {
				case 'template-blog-list.php':
					return 'list';
				case 'template-blog-masonry.php':
					return 'masonry';
				default:
					return '';
			}
		}

		/**
		 * Add archives theme options.
		 *
		 * @param array $files_list
		 *
		 * @return array
		 */
		public static function add_theme_options_filter( $files_list ) {
			if ( ! array_key_exists( self::ARCHIVE_OPTIONS_MENU_SLUG, $files_list ) ) {
				$files_list[ self::ARCHIVE_OPTIONS_MENU_SLUG ] = plugin_dir_path( __FILE__ ) . 'options-archive.php';
			}

			return $files_list;
		}
	}

	Presscore_Modules_ArchiveExtModule::execute();

endif;

if ( ! function_exists( 'presscore_module_archive_get_menu_slug' ) ) :

	/**
	 * Retrieve archive theme options menu slug.
	 *
	 * @return string
	 */
	function presscore_module_archive_get_menu_slug() {
		return Presscore_Modules_ArchiveExtModule::ARCHIVE_OPTIONS_MENU_SLUG;
	}

endif;
