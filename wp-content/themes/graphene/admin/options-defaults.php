<?php
/**
 * Set the default values for all the settings. If no user-defined values
 * is available for any setting, these defaults will be used.
 */
$graphene_defaults = array(
	/* Slider options */
	'slider_cat' => '',
	'slider_postcount' => '',
	'slider_img' => 'featured_image',
	'slider_display_style' => 'thumbnail-excerpt',
	'slider_imgurl' => '',
	'slider_height' => '',
	'slider_speed' => '',
	'slider_position' => false,
	'slider_disable' => false,
	
	/* Front page options */
	'frontpage_posts_cats' => array(),
	
	/* Syndication options */
	'custom_feed_url' => '',
	'hide_feed_icon' => false,
	
	/* Adsense Options */
	'show_adsense' => false,
	'adsense_code' => '',
	'adsense_show_frontpage' => false,
	
	/* Social Sharing options */
	'show_addthis' => false,
	'show_addthis_page' => false,
	'addthis_code' => '',
	
	/* Google Analytics options */
	'show_ga' => false,
	'ga_code' => '',
	
	/* Widget Area options */
	'alt_home_sidebar' => false,
	'alt_home_footerwidget' => false,
	'enable_header_widget' => false,
	
	/* Footer options */
	'show_cc' => false,
	'copy_text' => '',
	'hide_copyright' => false,
	
	
	/* Display Options Page */
	
	/* Header options */
	'light_header' => false,
	'link_header_img' => false,
	'featured_img_header' => false,
	'use_random_header_img' => false,
	'hide_top_bar' => false,
	'hide_feed_icon' => false,
	'search_box_location' => 'top_bar',
	
	/* Column options */
	'column_mode' => 'two-col-left', /* two column with the main-content on the left side */	
	
	/* Posts Display options */
	'posts_show_excerpt' => false,
	'hide_post_author' => false,
	'post_date_display' => 'icon_no_year',        
	'hide_post_commentcount' => false,
	'hide_post_cat' => false,
	'hide_post_tags' => false,
	'show_post_avatar' => false,
	'show_post_author' => false,
	'show_excerpt_more' => false,
	
	/* Footer widget options */
	'footerwidget_column' => '',
	'alt_footerwidget_column' => '',
	
	'navmenu_child_width' => '',
	
	/* Header Text options */
	'header_title_font_type' => '',
	'header_title_font_size' => '',
	'header_title_font_lineheight' => '',
	'header_title_font_weight' => '',
	'header_title_font_style' => '',
	
	'header_desc_font_type' => '',
	'header_desc_font_size' => '',
	'header_desc_font_lineheight' => '',
	'header_desc_font_weight' => '',
	'header_desc_font_style' => '',
	
	/* Content Text options */
	'content_font_type' => '',
	'content_font_size' => '',
	'content_font_lineheight' => '',
	'content_font_colour' => '',
	
	'link_colour_normal' => '',
	'link_colour_visited' => '',
	'link_colour_hover' => '',
	'link_decoration_normal' => '',
	'link_decoration_hover' => '',
	
	/* Miscellaneous options */
	'hide_allowedtags' => false,
	'custom_site_title_frontpage' => '',
	'custom_site_title_content' => '',
	'favicon_url' => '',
	'custom_css' => '',
);
?>