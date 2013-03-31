<?php
/**
 * Graphene functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, graphene_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *  
 *     remove_filter('filter_hook', 'callback_function' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.0
 */
 
 /**
 * Retrieve the theme's user settings and default settings. Individual files can access
 * these setting via a global variable call, so database query is only
 * done once.
 *
*/
include('admin/options-defaults.php');
$graphene_settings = get_option('graphene_settings');

/**
 * If there is no theme settings in the database yet (e.g. first install), add the database entry.
*/
if (!function_exists('graphene_db_init')) :
	function graphene_db_init(){
		global $graphene_settings, $graphene_defaults;
		
		/* Run DB updater if $graphene_settings does not exist in db */
		if (!$graphene_settings){
			
			// For first install
			if (get_option('graphene_ga_code') === false) {
				update_option('graphene_settings', $graphene_defaults);
				$graphene_settings = $graphene_defaults;
				
			} else {
			// For updates 
				include('admin/db-updater.php');
				graphene_update_db();
				$graphene_settings = get_option('graphene_settings');
			}
		
		/* $graphene_settings exists, but new options has been added since previous version */	
		} elseif (count($graphene_settings) != count($graphene_defaults)) {
			$initial_settings = array();
			
			// Construct the updated settings
			foreach ($graphene_defaults as $option => $value) :
				$updated_settings[$option] = (array_key_exists($option, $graphene_settings)) ? $graphene_settings[$option] : $value;
			endforeach;
			
			// Add the initial settings to the database
			update_option('graphene_settings', $updated_settings);

			// Update the global $graphene_settings;
			$graphene_settings = $updated_settings;
		}
	}
endif;
add_action('init', 'graphene_db_init');


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if (!isset($content_width)){
	$column_mode = graphene_column_mode();
	if (strpos($graphene_settings['post_date_display'], 'icon') === 0){
		if (strpos($column_mode, 'two-col') === 0){
			$content_width = apply_filters('graphene_content_width_two_columns', 590);
		} else if (strpos($column_mode, 'three-col-center') === 0) {
			$content_width = apply_filters('graphene_content_width_three_columns_center', 360);
		} else if (strpos($column_mode, 'three-col') === 0){
			$content_width = apply_filters('graphene_content_width_three_columns', 375);
		} else {
			$content_width = apply_filters('graphene_content_width_one_columns', 875);	
		}
	} else {
		if (strpos($column_mode, 'two-col') === 0){
			$content_width = apply_filters('graphene_content_width_two_columns_nodate', 645);
		} else if (strpos($column_mode, 'three-col-center') === 0) {
			$content_width = apply_filters('graphene_content_width_three_columns_center_nodate', 415);
		} else if (strpos($column_mode, 'three-col') === 0){
			$content_width = apply_filters('graphene_content_width_three_columns_nodate', 430);
		} else {
			$content_width = apply_filters('graphene_content_width_one_columns_nodate', 930);	
		}
	}
}


/** Tell WordPress to run graphene_setup() when the 'after_setup_theme' hook is run. */
add_action('after_setup_theme', 'graphene_setup' );

if (!function_exists( 'graphene_setup')):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override graphene_setup() in a child theme, add your own graphene_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Graphene 1.0
 */
function graphene_setup() {
	global $graphene_settings;
	
	// Define the theme's database version
	define('GRAPHENE_DBVERSION', 1.0);
	
	// Add the theme's dbversion in the database if none exist
	if (!get_option('graphene_dbversion')) {update_option('graphene_dbversion', GRAPHENE_DBVERSION);}
	
	// Add custom image sizes
	$height = ($graphene_settings['slider_height']) ? $graphene_settings['slider_height'] : 240;
	add_image_size('graphene_slider', apply_filters('graphene_slider_image_width', 660), $height, true);
	add_image_size('graphene_slider_full', apply_filters('graphene_slider_full_image_width', 930), $height, true);
	add_image_size('graphene_slider_small', apply_filters('graphene_slider_small_image_width', 445), $height, true);
	
	// Add support for editor syling
	add_editor_style();
	
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
	
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'graphene', get_template_directory().'/languages' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'Header Menu' => __('Header Menu', 'graphene'),
		'secondary-menu' => __('Secondary Menu', 'graphene'),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define('HEADER_TEXTCOLOR', apply_filters('graphene_header_textcolor', '000000'));
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define('HEADER_IMAGE', apply_filters('graphene_header_image', '%s/images/headers/flow.jpg'));

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to graphene_header_image_width and graphene_header_image_height to change these values.
	define('HEADER_IMAGE_WIDTH', apply_filters('graphene_header_image_width', 960));
	define('HEADER_IMAGE_HEIGHT', apply_filters('graphene_header_image_height', 198));

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size(HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true);

	// Don't support text inside the header image.
	define('NO_HEADER_TEXT', apply_filters('graphene_header_text', false));

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See graphene_admin_header_style(), below.
	add_custom_image_header('', 'graphene_admin_header_style');

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( graphene_get_default_headers() );
        
        do_action('graphene_setup');
}
endif;

function graphene_get_default_headers() {
	return array(
		'Schematic' => array(
			'url' => '%s/images/headers/schematic.jpg',
			'thumbnail_url' => '%s/images/headers/schematic-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image by Syahir Hakim', 'graphene')
		),
		'Flow' => array(
			'url' => '%s/images/headers/flow.jpg',
			'thumbnail_url' => '%s/images/headers/flow-thumb.jpg',
			/* translators: header image description */
			'description' => __('This is the default Graphene theme header image, cropped from image by Quantin Houyoux at sxc.hu', 'graphene')
		),
		'Fluid' => array(
			'url' => '%s/images/headers/fluid.jpg',
			'thumbnail_url' => '%s/images/headers/fluid-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image cropped from image by Ilco at sxc.hu', 'graphene')
		),
		'Techno' => array(
			'url' => '%s/images/headers/techno.jpg',
			'thumbnail_url' => '%s/images/headers/techno-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image cropped from image by Ilco at sxc.hu', 'graphene')
		),
		'Fireworks' => array(
			'url' => '%s/images/headers/fireworks.jpg',
			'thumbnail_url' => '%s/images/headers/fireworks-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image cropped from image by Ilco at sxc.hu', 'graphene')
		),
		'Nebula' => array(
			'url' => '%s/images/headers/nebula.jpg',
			'thumbnail_url' => '%s/images/headers/nebula-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image cropped from image by Ilco at sxc.hu', 'graphene')
		),
		'Sparkle' => array(
			'url' => '%s/images/headers/sparkle.jpg',
			'thumbnail_url' => '%s/images/headers/sparkle-thumb.jpg',
			/* translators: header image description */
			'description' => __('Header image cropped from image by Ilco at sxc.hu', 'graphene')
		),
	);
}


/**
 * Register and print the main theme stylesheet
*/
function graphene_main_stylesheet(){
	wp_register_style('graphene-stylesheet', get_stylesheet_uri(), array(), false, 'screen');
	wp_enqueue_style('graphene-stylesheet');
}
add_action('wp_print_styles', 'graphene_main_stylesheet');


if (!function_exists('graphene_admin_header_style')) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in graphene_setup().
 *
 * @since graphene 1.0
 */
function graphene_admin_header_style(){ ?>
	<style type="text/css">
    #headimg #name{
    position:relative;
    top:65px;
    left:38px;
    width:852px;
    font:bold 28px "Trebuchet MS";
    text-decoration:none;
    }
    #headimg #desc{
        color:#000;
        border-bottom:none;
        position:relative;
        top:50px;
        width:852px;
        left:38px;
        font:18px arial;
        }
    </style>
    
	<?php
	do_action('graphene_admin_header_style');
}
endif;

/**
 * Sets the various customised styling according to the options set for the theme
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.0.8
*/
function graphene_custom_style(){ 
	global $graphene_settings, $content_width;
	
	$background = get_theme_mod('background_image', false);
	$bgcolor = get_theme_mod('background_color', false);
	$widgetcolumn = (is_front_page() && $graphene_settings['alt_home_footerwidget']) ? $graphene_settings['alt_footerwidget_column'] : $graphene_settings['footerwidget_column'];
?>
	<style type="text/css">		
		<?php /* Disable default background if a custom background colour is defined */ ?>
		<?php if (!$background && $bgcolor) : ?>
		body{background-image:none;}
		<?php endif; ?>
		
		<?php /* Set the width of the bottom widget items if number of columns is specified */ ?>
		<?php if ($widgetcolumn) : $widget_width = floor((960 - (15+25+2)*$widgetcolumn)/$widgetcolumn); ?>
		#sidebar_bottom .sidebar-wrap{width:<?php echo $widget_width; ?>px;}
		<?php endif; ?>
		
		<?php /* Set the width of the nav menu dropdown menu item width if specified */ ?>
		<?php if ($graphene_settings['navmenu_child_width']) : ?>
		.menu li li, .menu li ul{width:<?php echo $graphene_settings['navmenu_child_width']; ?>px;}
			<?php if (!is_rtl()) : ?>
			.menu li ul ul{margin-left:<?php echo $graphene_settings['navmenu_child_width']; ?>px;}
			<?php else : ?>
			.menu li ul ul{margin-left:0;margin-right:<?php echo $graphene_settings['navmenu_child_width']; ?>px;}
			<?php endif; ?>
		.menu ul li a, .menu ul li a:visited{width:<?php echo ($graphene_settings['navmenu_child_width']-10); ?>px;}
		<?php endif; ?>
		
		<?php /* Header title text style */ ?>
		<?php if ($graphene_settings['header_title_font_type'] || $graphene_settings['header_title_font_size'] || $graphene_settings['header_title_font_lineheight'] || $graphene_settings['header_title_font_weight'] || $graphene_settings['header_title_font_style']) : 
			$font_style = '';
			$font_style .= ($graphene_settings['header_title_font_type']) ? 'font-family:'.$graphene_settings['header_title_font_type'].';' : '';
			$font_style .= ($graphene_settings['header_title_font_lineheight']) ? 'line-height:'.$graphene_settings['header_title_font_lineheight'].';' : '';
			$font_style .= ($graphene_settings['header_title_font_size']) ? 'font-size:'.$graphene_settings['header_title_font_size'].';' : '';
			$font_style .= ($graphene_settings['header_title_font_weight']) ? 'font-weight:'.$graphene_settings['header_title_font_weight'].';' : '';
			$font_style .= ($graphene_settings['header_title_font_style']) ? 'font-style:'.$graphene_settings['header_title_font_style'].';' : '';
		?>
		#header h1{<?php echo $font_style; ?>}
		<?php endif; ?>
		
		<?php /* Header description text style */ ?>
		<?php if ($graphene_settings['header_desc_font_type'] || $graphene_settings['header_desc_font_size'] || $graphene_settings['header_desc_font_lineheight'] || $graphene_settings['header_desc_font_weight'] || $graphene_settings['header_desc_font_style']) : 
			$font_style = '';
			$font_style .= ($graphene_settings['header_desc_font_type']) ? 'font-family:'.$graphene_settings['header_desc_font_type'].';' : '';
			$font_style .= ($graphene_settings['header_desc_font_size']) ? 'font-size:'.$graphene_settings['header_desc_font_size'].';' : '';
			$font_style .= ($graphene_settings['header_desc_font_lineheight']) ? 'line-height:'.$graphene_settings['header_desc_font_lineheight'].';' : '';
			$font_style .= ($graphene_settings['header_desc_font_weight']) ? 'font-weight:'.$graphene_settings['header_desc_font_weight'].';' : '';
			$font_style .= ($graphene_settings['header_desc_font_style']) ? 'font-style:'.$graphene_settings['header_desc_font_style'].';' : '';
		?>
		#header h2{<?php echo $font_style; ?>}
		<?php endif; ?>
		
		<?php /* Content text style */ ?>
		<?php if ($graphene_settings['content_font_type'] || $graphene_settings['content_font_size'] || $graphene_settings['content_font_lineheight'] || $graphene_settings['content_font_colour']) : 
			$font_style = '';
			$font_style .= ($graphene_settings['content_font_type']) ? 'font-family:'.$graphene_settings['content_font_type'].';' : '';
			$font_style .= ($graphene_settings['content_font_size']) ? 'font-size:'.$graphene_settings['content_font_size'].';' : '';
			$font_style .= ($graphene_settings['content_font_lineheight']) ? 'line-height:'.$graphene_settings['content_font_lineheight'].';' : '';
			$font_style .= ($graphene_settings['content_font_colour']) ? 'color:'.$graphene_settings['content_font_colour'].';' : '';
		?>
		.entry-content p, .entry-content ul, .entry-content ol, .comment-entry ol{<?php echo $font_style; ?>}
		<?php endif; ?>
	
		<?php /* Adjust post title if author's avatar is shown */ ?>
		<?php if ($graphene_settings['show_post_avatar']) : if (!is_rtl()) : ?>
		.post-title a, .post-title a:visited{display:block;margin-right:45px;padding-bottom:0;}
		<?php else : ?>
		.post-title a, .post-title a:visited{display:block;margin-left:45px;padding-bottom:0;}
		<?php endif; endif; ?>
		
		<?php /* Slider height */ ?>
		<?php if ($graphene_settings['slider_height']) : ?>
		.featured_slider #slider_root{height:<?php echo $graphene_settings['slider_height']; ?>px;}
		<?php endif; ?>
		
		<?php /* Link header image */ ?>
		<?php if ($graphene_settings['link_header_img'] && (HEADER_IMAGE_WIDTH != 900 || HEADER_IMAGE_HEIGHT != 198)) : ?>
		#header_img_link{width:<?php echo HEADER_IMAGE_WIDTH; ?>px;height:<?php echo HEADER_IMAGE_HEIGHT; ?>px;}
		<?php endif;?>
		
		<?php /* Link text */ ?>
		<?php if ($graphene_settings['link_colour_normal']) {echo 'a{color:'.$graphene_settings['link_colour_normal'].';}';} ?>
		<?php if ($graphene_settings['link_colour_visited']) {echo 'a:visited{color:'.$graphene_settings['link_colour_visited'].';}';} ?>
		<?php if ($graphene_settings['link_colour_hover']) {echo 'a:hover{color:'.$graphene_settings['link_colour_hover'].';}';} ?>
		<?php if ($graphene_settings['link_decoration_normal']) {echo 'a{text-decoration:'.$graphene_settings['link_decoration_normal'].';}';} ?>
		<?php if ($graphene_settings['link_decoration_hover']) {echo 'a:hover{text-decoration:'.$graphene_settings['link_decoration_hover'].';}';} ?>
		
		<?php /* Custom css */ ?>
		<?php if ($graphene_settings['custom_css']) {echo $graphene_settings['custom_css'];} ?>
		
		<?php do_action('graphene_custom_style'); ?>
    </style>
    
<?php 
}

/* This is for future updates, where hopefully I can make the theme generate the CSS file each time it's changed 
 * and just load that instead. Would be more efficient.
if (!function_exists('graphene_print_style')) :
	function graphene_print_style(){
		wp_register_style('graphene-customised-style', get_template_directory_uri().'/style-custom.php');
		wp_enqueue_style('graphene-customised-style');
	}
endif;

add_action('wp_print_styles', 'graphene_print_style');
*/
add_action('wp_head', 'graphene_custom_style');


/**
 * Register and print the stylesheet for alternate lighter header, if enabled in the options
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.0.8
*/
if ($graphene_settings['light_header']) :
	function graphene_lightheader_style(){
		wp_register_style('graphene-light-header', get_template_directory_uri().'/style-light.css');
		wp_enqueue_style('graphene-light-header');
		
		do_action('graphene_light_header');
		}
	add_action('wp_print_styles', 'graphene_lightheader_style');
endif;


/**
 * Check to see if there's a favicon.ico in wordpress root directory and add
 * appropriate head element for the favicon
*/
function graphene_favicon(){
	global $graphene_settings;
	if ($graphene_settings['favicon_url']) { ?>
		<link rel="icon" href="<?php echo $graphene_settings['favicon_url']; ?>" type="image/x-icon" />
	<?php
    } elseif (is_file(ABSPATH.'favicon.ico')){ ?>
		<link rel="icon" href="<?php echo home_url(); ?>/favicon.ico" type="image/x-icon" />
	<?php }
}
add_action('wp_head', 'graphene_favicon');


/**
 * Define the callback menu, if there is no custom menu.
 * This menu automatically lists all Pages as menu items, including their direct
 * direct descendant, which will only be displayed for the current parent.
*/
if (!function_exists('graphene_default_menu')) :

	function graphene_default_menu(){ ?>
		<ul id="header-menu" class="menu clearfix">
            <?php if (get_option('show_on_front') == 'posts') : ?>
            <li <?php if ( is_single() || is_front_page()) { echo 'class="current_page_item"'; } ?>><a href="<?php echo get_home_url(); ?>"><?php _e('Home','graphene'); ?></a></li>
            <?php endif; ?>
            <?php 
				$args = array(
							'echo' => 1,
							'sort_column' => 'menu_order, post_title',
							'depth' => 5,
							'title_li' => ''
						);
			wp_list_pages(apply_filters('graphene_default_menu_args', $args)); 
			?>
        </ul>
<?php
	do_action('graphene_default_menu');
	} 
	
endif;

/**
 * Defines the callback function for use with wp_list_comments(). This function controls
 * how comments are displayed.
*/

if (!function_exists('graphene_comment')) :

	function graphene_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class('clearfix'); ?>>
				<?php do_action('graphene_before_comment'); ?>
                
                <?php /* Added support for comment numbering using Greg's Threaded Comment Numbering plugin */ ?>
                <?php if (function_exists('gtcn_comment_numbering')) {gtcn_comment_numbering($comment->comment_ID, $args);} ?>
                
				<?php echo get_avatar($comment, apply_filters('graphene_gravatar_size', 40)); ?>
                <?php do_action('graphene_comment_gravatar'); ?>
                
					<div class="comment-wrap clearfix">
						<h5>
                        	<cite><?php comment_author_link(); ?></cite><?php _e(' says:','graphene'); ?>
                        <?php do_action('graphene_comment_author'); ?>
                        </h5>
						<div class="comment-meta">
							<p class="commentmetadata">
                            	<?php /* translators: %1$s is the comment date, %2#s is the comment time */ ?>
								<?php printf(__('%1$s at %2$s', 'graphene'), get_comment_date(), get_comment_time()); ?>
								<?php echo '(UTC '.get_option('gmt_offset').')'; ?>
								<?php edit_comment_link(__('Edit comment','graphene'),' | ',''); ?>
                            	<?php do_action('graphene_comment_metadata'); ?>    
                            </p>
							<p class="comment-reply-link">
								<?php comment_reply_link(array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => __('Reply', 'graphene'))); ?>
                            
                            	<?php do_action('graphene_comment_replylink'); ?>
                            </p>
                            
							<?php do_action('graphene_comment_meta'); ?>
						</div>
						<div class="comment-entry">
                        	<?php do_action('graphene_before_commententry'); ?>
                            
							<?php if ($comment->comment_approved == '0') : ?>
							   <p><em><?php _e('Your comment is awaiting moderation.') ?></em></p>
                               <?php do_action('graphene_comment_moderation'); ?>
							<?php else : ?>
								<?php comment_text(); ?>
                            <?php endif; ?>
                            
                            <?php do_action('graphene_after_commententry'); ?>
						</div>
					</div>
                
                <?php do_action('graphene_after_comment'); ?>
	<?php
	
	do_action('graphene_after_comment');
	}

endif;


		
/**
 * Function to display ads from adsense
*/
$adsense_adcount = 1;
if (!function_exists('graphene_adsense')) :
	function graphene_adsense(){
		global $adsense_adcount, $graphene_settings;
		
		if ($graphene_settings['show_adsense'] && $adsense_adcount <= 3) : ?>
            <div class="post adsense_single">
                <?php echo stripslashes($graphene_settings['adsense_code']); ?>
            </div>
            <?php do_action('graphene_show_adsense'); ?>
		<?php endif;
		
		$adsense_adcount++;
		
		do_action('graphene_adsense');
	}
endif;

/**
 * Function to display the AddThis social sharing button
*/

if (!function_exists('graphene_addthis')) :
	function graphene_addthis($post_id){
		global $graphene_settings;
		
		// Get the local setting
		$show_addthis_local = (get_post_meta($post_id, '_graphene_show_addthis', true)) ? get_post_meta($post_id, '_graphene_show_addthis', true) : 'global';
		$show_addthis_global = $graphene_settings['show_addthis'];
		$show_addthis_page = $graphene_settings['show_addthis_page'];
		
		// Determine whether we should show AddThis or not
		if ($show_addthis_local == 'show')
			$show_addthis = true;
		elseif ($show_addthis_local == 'hide')
			$show_addthis = false;
		elseif ($show_addthis_local == 'global'){
			if (($show_addthis_global && !is_page()) || ($show_addthis_global && $show_addthis_page))
				$show_addthis = true;
			else
				$show_addthis = false;
		}
		
		// Show the AddThis button
		if ($show_addthis) {
			echo '<div class="add-this-right">';
			echo stripslashes($graphene_settings['addthis_code']);
			echo '</div>';
			
			do_action('graphene_show_addthis');
		}
		do_action('graphene_addthis');
	}
endif;


/**
 * Register widgetized areas
 *
 * To override graphene_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Graphene 1.0
 * @uses register_sidebar
 */
function graphene_widgets_init() {
	if (function_exists('register_sidebar')) {
		global $graphene_settings;
		
		register_sidebar(array(
			'name' => __('Sidebar Widget Area', 'graphene'),
			'id' => 'sidebar-widget-area',
			'description' => __( 'The first sidebar widget area (available in two and three column layouts).', 'graphene' ),
			'before_widget' => '<div class="sidebar-wrap clearfix">',
			'after_widget' => '</div>',
			'before_title' => "<h3>",
			'after_title' => "</h3>",
		));
                
                register_sidebar(array(
                    'name' => __('Sidebar Two Widget Area', 'graphene'),
                    'id' => 'sidebar-two-widget-area',
                    'description' => __( 'The second sidebar widget area (only available in three column layouts).', 'graphene'),
                    'before_widget' => '<div class="sidebar-wrap clearfix">',
                    'after_widget' => '</div>',
                    'before_title' => "<h3>",
                    'after_title' => "</h3>",
                ));
		
		register_sidebar(array(
			'name' => __('Footer Widget Area', 'graphene'),
			'id' => 'footer-widget-area',
			'description' => __( "The footer widget area. Leave empty to disable. Set the number of columns to display at the theme's Display Options page.", 'graphene' ),
			'before_widget' => '<div class="sidebar-wrap clearfix">',
			'after_widget' => '</div>',
			'before_title' => "<h3>",
			'after_title' => "</h3>",
		));
		
		/**
		 * Register alternate widget areas to be displayed on the front page, if enabled
		 *
		 * @package WordPress
		 * @subpackage Graphene
		 * @since Graphene 1.0.8
		*/
		if ($graphene_settings['alt_home_sidebar']) {
			register_sidebar(array(
				'name' => __('Front Page Sidebar Widget Area', 'graphene'),
				'id' => 'home-sidebar-widget-area',
				'description' => __( 'The first sidebar widget area that will only be displayed on the front page.', 'graphene' ),
				'before_widget' => '<div class="sidebar-wrap clearfix">',
				'after_widget' => '</div>',
				'before_title' => "<h3>",
				'after_title' => "</h3>",
			));
			
			register_sidebar(array(
				'name' => __('Front Page Sidebar Two Widget Area', 'graphene'),
				'id' => 'home-sidebar-two-widget-area',
				'description' => __( 'The second sidebar widget area that will only be displayed on the front page.', 'graphene' ),
				'before_widget' => '<div class="sidebar-wrap clearfix">',
				'after_widget' => '</div>',
				'before_title' => "<h3>",
				'after_title' => "</h3>",
			));
		}
		
		if ($graphene_settings['alt_home_footerwidget']) {
			register_sidebar(array(
				'name' => __('Front Page Footer Widget Area', 'graphene'),
				'id' => 'home-footer-widget-area',
				'description' => __( "The footer widget area that will only be displayed on the front page. Leave empty to disable. Set the number of columns to display at the theme's Display Options page.", 'graphene' ),
				'before_widget' => '<div class="sidebar-wrap clearfix">',
				'after_widget' => '</div>',
				'before_title' => "<h3>",
				'after_title' => "</h3>",
			));
		}
		
		/* Header widget area */
		if ($graphene_settings['enable_header_widget']) :
			register_sidebar(array(
				'name' => __('Header Widget Area', 'graphene'),
				'id' => 'header-widget-area',
				'description' => __("The header widget area.", 'graphene'),
				'before_widget' => '<div class="sidebar-wrap clearfix">',
				'after_widget' => '</div>',
				'before_title' => "<h3>",
				'after_title' => "</h3>",
			));
		endif;
	}
	
	do_action('graphene_widgets_init');
}
/** Register sidebars by running graphene_widgets_init() on the widgets_init hook. */
add_action('widgets_init', 'graphene_widgets_init');


/**
 * Register custom Twitter widgets.
*/
global $twitter_username;
global $twitter_tweetcount;
$twitter_username = '';
$twitter_tweetcount = 1;

class Graphene_Widget_Twitter extends WP_Widget{
	
	function Graphene_Widget_Twitter(){
		// Widget settings
		$widget_ops = array('classname' => 'Graphene_Twitter', 'description' => __('Display the latest Twitter status updates.', 'graphene'));
		
		// Widget control settings
		$control_ops = array('id_base' => 'graphene-twitter');
		
		// Create the widget
		$this->WP_Widget('graphene-twitter', 'Graphene Twitter', $widget_ops, $control_ops);
		
	}
	
	function widget($args, $instance){		// This function displays the widget
		extract($args);
		
		// User selected settings
		global $twitter_username;
		global $twitter_tweetcount;
		$twitter_title = $instance['twitter_title'];
		$twitter_username = $instance['twitter_username'];
		$twitter_tweetcount = $instance['twitter_tweetcount'];
		
		echo $args['before_widget'].$args['before_title'].$twitter_title.$args['after_title'];
		?>
        	<ul id="twitter_update_list">
            	<li>&nbsp;</li>
            </ul>
            <p id="tweetfollow" class="sidebar_ablock"><a href="http://twitter.com/<?php echo $twitter_username; ?>"><?php _e('Follow me on Twitter', 'graphene') ?></a></p>
            
            <?php do_action('graphene_twitter_widget'); ?>
        <?php echo $args['after_widget']; ?>
        
        <?php
		add_action('wp_footer', 'graphene_add_twitter_script');
	}
	
	function update($new_instance, $old_instance){	// This function processes and updates the settings
		$instance = $old_instance;
		
		// Strip tags (if needed) and update the widget settings
		$instance['twitter_username'] = strip_tags($new_instance['twitter_username']);
		$instance['twitter_tweetcount'] = strip_tags($new_instance['twitter_tweetcount']);
		$instance['twitter_title'] = strip_tags($new_instance['twitter_title']);
		
		return $instance;
	}
	
	function form($instance){		// This function sets up the settings form
		
		// Set up default widget settings
		$defaults = array(
						'twitter_username' => 'username',
						'twitter_tweetcount' => 5,
						'twitter_title' => __('Latest tweets', 'graphene'),
						);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
        <p>
        	<label for="<?php echo $this->get_field_id('twitter_title'); ?>"><?php _e('Title:', 'graphene'); ?></label>
			<input id="<?php echo $this->get_field_id('twitter_title'); ?>" type="text" name="<?php echo $this->get_field_name('twitter_title'); ?>" value="<?php echo $instance['twitter_title']; ?>" class="widefat" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id('twitter_username'); ?>"><?php _e('Twitter Username:', 'graphene'); ?></label>
			<input id="<?php echo $this->get_field_id('twitter_username'); ?>" type="text" name="<?php echo $this->get_field_name('twitter_username'); ?>" value="<?php echo $instance['twitter_username']; ?>" class="widefat" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id('twitter_tweetcount'); ?>"><?php _e('Number of tweets to display:', 'graphene'); ?></label>
			<input id="<?php echo $this->get_field_id('twitter_tweetcount'); ?>" type="text" name="<?php echo $this->get_field_name('twitter_tweetcount'); ?>" value="<?php echo $instance['twitter_tweetcount']; ?>" size="1" />
        </p>
        <?php
	}
}

/* The function that prints the Twitter script to the footer */
if (!function_exists('graphene_add_twitter_script')) :
	function graphene_add_twitter_script(){
		global $twitter_username;
		global $twitter_tweetcount;
		echo '
		<!-- BEGIN Twitter Updates script -->
		<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
		<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/'.$twitter_username.'.json?callback=twitterCallback2&amp;count='.$twitter_tweetcount.'"></script>
		<!-- END Twitter Updates script -->
		';
	}
endif;


/**
 * Register the custom widget by passing the graphene_load_widgets() function to widgets_init
 * action hook.
 * To override in a child theme, remove the action hook and add your own
*/ 
function graphene_load_widgets(){
	register_widget('Graphene_Widget_Twitter');
}
add_action('widgets_init', 'graphene_load_widgets');


/**
 * Enqueue style for admin page
*/
if (!function_exists('graphene_admin_options_style')) :
	function graphene_admin_options_style() {
		wp_enqueue_style('graphene-admin-style');
		if (is_rtl()) {wp_enqueue_style('graphene-admin-style-rtl');}
	}
endif;


/** 
 * Adds the theme options page
*/
function graphene_options_init() {
	$graphene_options = add_theme_page(__('Graphene Options', 'graphene'), __('Graphene Options', 'graphene'), 'edit_theme_options', 'graphene_options', 'graphene_options');	
	/*
	$graphene_display = add_theme_page(__('Graphene Display', 'graphene'), __('Graphene Display', 'graphene'), 'edit_theme_options', 'graphene_options_display', 'graphene_options_display');
	*/
	$graphene_faq = add_theme_page(__('Graphene FAQs', 'graphene'), __('Graphene FAQs', 'graphene'), 'edit_theme_options', 'graphene_faq', 'graphene_faq');
	
	wp_register_style('graphene-admin-style', get_template_directory_uri().'/admin/admin.css');
	if (is_rtl()) {wp_register_style('graphene-admin-style-rtl', get_template_directory_uri().'/admin/admin-rtl.css');}
	
	add_action('admin_print_styles-'.$graphene_options, 'graphene_admin_options_style');
	/*
	add_action('admin_print_styles-'.$graphene_display, 'graphene_admin_options_style');
	*/
	
	do_action('graphene_options_init');
}
add_action('admin_menu', 'graphene_options_init');

// Includes the files where our theme options are defined
include('admin/options.php');
// include('admin/display.php');
include('admin/faq.php');


/**
 * Function that generate the tabs in the theme's options page
*/
if (!function_exists('graphene_options_tabs')) :
	function graphene_options_tabs($current = 'general', $tabs = array('general' => 'General')){
		$links = array();
		foreach( $tabs as $tab => $name) :
			if ( $tab == $current ) :
				$links[] = "<a class='nav-tab nav-tab-active' href='?page=graphene_options&amp;tab=$tab'>$name</a>";
			else :
				$links[] = "<a class='nav-tab' href='?page=graphene_options&amp;tab=$tab'>$name</a>";
			endif;
		endforeach;
		
		echo '<h3 class="options-tab">';
		foreach ($links as $link)
			echo $link;
		echo '</h3>';
	}
endif;


/**
 * Include the file for additional user fields
 * 
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.1
*/
include('admin/user.php');

/**
 * Include the file for additional custom fields in posts and pages editing screens
 * 
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.1
*/
include('admin/custom-fields.php');



/**
 * Customise the comment form
*/

// Starting with the default fields
function graphene_comment_form_fields(){
	$fields =  array(
		'author' => '<p class="comment-form-author"><label for="author" class="graphene_form_label">'.__('Name:','graphene').'</label><input id="author" name="author" type="text" /></p>',
		'email'  => '<p class="comment-form-email"><label for="email" class="graphene_form_label">' . __('Email:','graphene').'</label><input id="email" name="email" type="text" /></p>',
		'url'    => '<p class="comment-form-url"><label for="url" class="graphene_form_label">'.__('Website:','graphene').'</label><input id="url" name="url" type="text" /></p>',
	);
	
	do_action('graphene_comment_form_fields');
	
	return $fields;
}

// The comment field textarea
function graphene_comment_textarea(){
	echo '<p class="clearfix"><label class="graphene_form_label">'.__('Message:','graphene').'</label><textarea name="comment" id="comment" cols="40" rows="10" tabindex="4"></textarea></p><div class="graphene_wrap">';
	
	do_action('graphene_comment_textarea');
}

// The submit button
function graphene_comment_submit_button(){
	echo '<p class="graphene-form-submit"><button type="submit" id="graphene_submit" class="block-button" name="graphene_submit">'.__('Submit Comment', 'graphene').'</button></p></div>';
	
	do_action('graphene_comment_submit_button');
	}

// Add all the filters we defined
add_filter('comment_form_default_fields', 'graphene_comment_form_fields');
add_filter('comment_form_field_comment', 'graphene_comment_textarea');
add_filter('comment_form', 'graphene_comment_submit_button');


/**
 * Returns a "Continue Reading" link for excerpts
 * Based on the function from the Twenty Ten theme
 *
 * @since Graphene 1.0.8
 * @return string "Continue Reading" link
 */
if (!function_exists('graphene_continue_reading_link')) :
	function graphene_continue_reading_link() {
		if (!is_page()) {
			$more_link_text = __('Continue reading &raquo;', 'graphene');
			return '</p><p><a class="more-link block-button" href="'.get_permalink().'">'.$more_link_text.'</a>';
		}
	}
endif;


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and graphene_continue_reading_link().
 * Based on the function from Twenty Ten theme.
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Graphene 1.0.8
 * @return string An ellipsis
 */
function graphene_auto_excerpt_more($more) {
	return apply_filters('graphene_auto_excerpt_more', ' &hellip; '.graphene_continue_reading_link());
}
add_filter('excerpt_more', 'graphene_auto_excerpt_more' );


/**
 * Add the Read More link to manual excerpts
 *
 * @since Graphene 1.1.3
*/
function graphene_manual_except_more($text){
	if (has_excerpt()){
		$text = explode('</p>', $text);
		$text[count($text)-2] .= graphene_auto_excerpt_more('');
		$text = implode('</p>', $text);
	}
	return $text;
}
if ($graphene_settings['show_excerpt_more']) {
	add_filter('the_excerpt', 'graphene_manual_except_more');
}


/**
 * Generates the posts navigation links
*/
if (!function_exists('graphene_posts_nav')) :
	function graphene_posts_nav(){ ?>
		<div class="post-nav clearfix">
        <?php if (!is_search()) : ?>
			<p id="previous"><?php next_posts_link(__('Older posts &laquo;', 'graphene')) ?></p>
			<p id="next-post"><?php previous_posts_link(__('&raquo; Newer posts', 'graphene')) ?></p>
        <?php else : ?>
            <p id="next-post"><?php next_posts_link(__('Next page &raquo;', 'graphene')) ?></p>
			<p id="previous"><?php previous_posts_link(__('&laquo; Previous page', 'graphene')) ?></p>
        <?php endif; ?>
		</div>
        
        
        <?php do_action('graphene_posts_nav'); ?>
	<?php
	}
endif;


/**
 * Prints out the scripts required for the featured posts slider
*/

/* jQuery Scrollable */ 
if (!function_exists('graphene_scrollable')) :
	function graphene_scrollable() { 
		global $graphene_settings;
		
		$interval = ($graphene_settings['slider_speed']) ? $graphene_settings['slider_speed'] : 7000;
		?>
            <!-- Scrollable -->
            <script type="text/javascript">
				//<![CDATA[
                jQuery(document).ready(function($){
					$(function() {
                        // initialize scrollable
						$("#slider_root").scrollable({circular: true}).navigator({	  
								navi: ".slider_nav",
								naviItem: 'a',
								activeClass: 'active'
							}).autoscroll({interval: <?php echo $interval; ?>});
                    });
                });
				//]]>
            </script>
            <!-- #Scrollable -->
		<?php 
	}
endif;

/* Load jQuery Tools script */
function graphene_scrollable_js() {
	wp_enqueue_script('graphene-jquery-tools', 'http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js', array('jquery'), '', true);
}
// Print both the js and the script
add_action('template_redirect', 'graphene_scrollable_js');




/**
 * Control the excerpt length
*/
function graphene_excerpt_length($length) {
	global $graphene_settings;
	$column_mode = graphene_column_mode();
	if ($graphene_settings['slider_display_style'] == 'bgimage-excerpt'){
		if (strpos($column_mode, 'three-col') === 0)
			return 24;
		if (strpos($column_mode, 'two-col') === 0)
			return 40;
		if ($column_mode == 'one-column')
			return 55;
	}
	
	return 55;
}
add_filter('excerpt_length', 'graphene_excerpt_length');



/**
 * Creates the functions that output the slider
*/
function graphene_slider(){
	global $graphene_settings;
	
	do_action('graphene_before_slider'); ?>
    <?php $class = ($graphene_settings['slider_display_style'] == 'bgimage-excerpt') ? ' full-sized' : ''; ?>
    <div class="featured_slider<?php echo $class; ?>">
        <?php do_action('graphene_before_slideritems'); ?>
        <div id="slider_root">
            <div class="slider_items">
    <?php 
        /**
         * Get the featured posts to be displayed on the slider
        */
        global $post;
        
        /**
         * Get the category whose posts should be displayed here. If no 
         * category is defined, the 5 latest posts will be displayed
        */
        $slidercat = ($graphene_settings['slider_cat'] != '') ? $graphene_settings['slider_cat'] : false;
        
		/* Set the post types to be displayed */
		$slider_post_type = apply_filters('graphene_slider_post_type', array('post'));
		
        /* Get the posts to display in the slider */					
			
		// Get the number of posts to show
		$postcount = ($graphene_settings['slider_postcount']) ? $graphene_settings['slider_postcount'] : 5 ;
			
		$args = array(
					'numberposts' => $postcount,
					'orderby' => 'date',
					'order' => 'DESC',
					'suppress_filters' => 0,
					'post_type' => $slider_post_type
					 );
		
		if ($slidercat && $slidercat != 'random') {
			$args = array_merge($args, array('category' => $slidercat));
		}
		
		if ($slidercat && $slidercat == 'random') {
			$args = array_merge($args, array('orderby' => 'rand'));
		}

		$sliderposts = get_posts(apply_filters('graphene_slider_args', $args));
            
        /* Display each post in the slider */	
        foreach ($sliderposts as $post){
            setup_postdata($post);
			
			$style = '';
			/* Slider background image*/
			if ($graphene_settings['slider_display_style'] == 'bgimage-excerpt') {
				$column_mode = graphene_column_mode();
				if ($column_mode == 'one-column'){
					$image_size = 'graphene_slider_full';
				} elseif ( strpos($column_mode, 'two-col') === 0){
					$image_size = 'graphene_slider';
				} else if ( strpos($column_mode, 'three-col') === 0 ){
					$image_size = 'graphene_slider_small';
				}
				$image = graphene_get_slider_image($post->ID, $image_size, true);
				if ($image){
					$style .= 'style="background-image:url(';
					$style .= (is_array($image)) ? $image[0] : $image;
					$style .= ');"';
				}
			}
			?>
            
            <div class="slider_post clearfix" <?php echo $style; ?>>
                <?php do_action('graphene_before_sliderpost'); ?>
                
                <?php if ($graphene_settings['slider_display_style'] == 'thumbnail-excerpt') : ?>
					<?php /* The slider post's featured image */ ?>
                    <?php 
                    if (get_post_meta($post->ID, '_graphene_slider_img', true) != 'disabled' && !((get_post_meta($post->ID, '_graphene_slider_img', true) == 'global' || get_post_meta($post->ID, '_graphene_slider_img', true) == '') && $graphene_settings['slider_img'] == 'disabled')) : 
					$image = graphene_get_slider_image($post->ID, apply_filters('graphene_slider_image_size', 'thumbnail'));
					if ($image) :
					?>
                    
                    <div class="sliderpost_featured_image">
                        <?php echo $image;	?>
                    </div>
                    
                    <?php endif; endif; ?>
                <?php endif; ?>
                
                <div class="slider-entry-wrap">
                	<div class="slider-content-wrap">
						<?php /* The slider post's title */ ?>
                        <h2 class="slider_post_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        
                        <?php /* The slider post's excerpt */ ?>
                        <div class="slider_post_entry">
                        	<?php 
							if ($graphene_settings['slider_display_style'] != 'full-post'){
								the_excerpt(); 
							?>
                            <a class="block-button" href="<?php the_permalink(); ?>"><?php _e('View full post', 'graphene'); ?></a>
                            <?php } else {the_content();}?>
                            
                            <?php do_action('graphene_slider_postentry'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php	
        }
        
    ?>
            </div>
        </div>
        
        <?php /* The slider navigation */ ?>
        <div class="slider_nav">
            <?php $i = 0; foreach ($sliderposts as $post) : ?>
            <a href="#" <?php if ($i == 0) {echo ' class="active"';} ?>><span><?php the_title(); ?></span></a>
            <?php $i++; endforeach; ?>
            
            <?php do_action('graphene_slider_nav'); ?>
        </div>
        
    </div>
    <?php
}
/* Create an intermediate function that controls where the slider should be displayed */
if (!function_exists('graphene_display_slider')) :
	function graphene_display_slider(){
		if (is_front_page()){
			graphene_slider();
			add_action('wp_footer', 'graphene_scrollable');
		}
	}
endif;
/* Hook the slider to the appropriate action hook */
if (!$graphene_settings['slider_disable']){
	if (!$graphene_settings['slider_position'])
		add_action('graphene_top_content', 'graphene_display_slider');
	else
		add_action('graphene_bottom_content', 'graphene_display_slider');
}


/**
 * This function determines which image to be used as the slider image based on user
 * settings, and returns the <img> tag of the the slider image.
 *
 * It requires the post's ID to be passed in as argument so that the user settings in
 * individual post / page can be retrieved.
*/
if (!function_exists('graphene_get_slider_image')) :
	function graphene_get_slider_image($post_id = NULL, $size = 'thumbnail', $urlonly = false){
		global $graphene_settings;
		
		// Throw an error message if no post ID supplied
		if ($post_id == NULL){
			echo '<strong>ERROR:</strong> Post ID must be passed as an input argument to call the function <code>graphene_get_slider_image()</code>.';
			return;
		}
		
		// First get the settings
		$global_setting = ($graphene_settings['slider_img']) ? $graphene_settings['slider_img'] : 'featured_image';
		$local_setting = (get_post_meta($post_id, '_graphene_slider_img', true)) ? get_post_meta($post_id, '_graphene_slider_img', true) : 'global';
		
		// Determine which image should be displayed
		$final_setting = ($local_setting == 'global') ? $global_setting : $local_setting;
		
		// Build the html based on the final setting
		$html = '';
		if ($final_setting == 'disabled'){					// image disabled
		
			return;
			
		} elseif ($final_setting == 'featured_image'){		// Featured Image
		
			if (has_post_thumbnail($post_id)) :
				if ($urlonly)
					$html = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
				else
					$html .= get_the_post_thumbnail($post_id, $size);
			/* Disabling the default image. Seems like it's more trouble than worth it
			elseif (!$urlonly) :
				$html .= apply_filters('graphene_generic_slider_img', '<img alt="" src="'.get_template_directory_uri().'/images/img_slider_generic.png" />');
			else :
				$html .= apply_filters('graphene_generic_slider_img', get_template_directory_uri().'/images/img_slider_generic.png');
			*/
			endif;
			
		} elseif ($final_setting == 'post_image'){			// First image in post
			
				$html = graphene_get_post_image($post_id, $size, '', $urlonly);
			
		} elseif ($final_setting == 'custom_url'){			// Custom URL
			
			if (!$urlonly){
				$html .= '<a href="'.get_permalink($post_id).'">';
				if ($local_setting != 'global') :
					$html .= '<img src="'.get_post_meta($post_id, '_graphene_slider_imgurl', true).'" alt="" />';
				else :
					$html .= '<img src="'.$graphene_settings['slider_imgurl'].'" alt="" />';
				endif;
				$html .= '</a>';
			} else {
				if ($local_setting != 'global') :
					$html .= get_post_meta($post_id, '_graphene_slider_imgurl', true);
				else :
					$html .= $graphene_settings['slider_imgurl'];
				endif;
			}
			
		}
		
		// Returns the html
		return $html;
		
	}
endif;


/**
 * This function gets the first image (as ordered in the post's media gallery) attached to
 * the current post. It outputs the complete <img> tag, with height and width attributes.
 * The function returns the thumbnail of the original image, linked to the post's 
 * permalink. Returns FALSE if the current post has no image.
 *
 * This function requires the post ID to get the image from to be supplied as the
 * argument. If no post ID is supplied, it outputs an error message. An optional argument
 * size can be used to determine the size of the image to be used.
 *
 * Based on code snippets by John Crenshaw 
 * (http://www.rlmseo.com/blog/get-images-attached-to-post/)
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.1
*/
if (!function_exists('graphene_get_post_image')) :
	function graphene_get_post_image($post_id = NULL, $size = 'thumbnail', $context = '', $urlonly = false){
		
		/* Display error message if no post ID is supplied */
		if ($post_id == NULL){
			_e('<strong>ERROR: You must supply the post ID to get the image from as an argument when calling the graphene_get_post_image() function.</strong>', 'graphene');
			return;
		}
		
		/* Get the images */
		$images = get_children(array(
								'post_type' 		=> 'attachment',
								'post_mime_type' 	=> 'image',
								'post_parent' 	 	=> $post_id,
								'orderby'			=> 'menu_order',
								'order'				=> 'ASC',
								'numberposts'		=> 1,
									 ), ARRAY_A);
		
		$html = '';
		
		/* Returns FALSE if there is no image */
		if (empty($images) && $context != 'excerpt' && !$urlonly) {
			$html .= apply_filters('graphene_generic_slider_img', '<img alt="" src="'.get_template_directory_uri().'/images/img_slider_generic.png" />');
		}
		
		/* Build the <img> tag if there is an image */
		foreach ($images as $image){
			if (!$urlonly) {
				if ($context == 'excerpt') {$html .= '<div class="excerpt-thumb">';};
				$html .= '<a href="'.get_permalink($post_id).'">';
				$html .= wp_get_attachment_image($image['ID'], $size);
				$html .= '</a>';
				if ($context == 'excerpt') {$html .= '</div>';};
			} else {
				$html = wp_get_attachment_image_src($image['ID'], $size);
			}
		}
		
		/* Returns the image HTMl */
		return $html;
}
endif;


/**
 * This function retrieves the header image for the theme
*/
if (!function_exists('graphene_get_header_image')) :
	function graphene_get_header_image($post_id = NULL){
		global $graphene_settings;
		
		if ( is_singular() && has_post_thumbnail( $post_id ) && ( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'post-thumbnail' ) ) &&  $image[1] >= HEADER_IMAGE_WIDTH && !$graphene_settings['featured_img_header']) {
			// Houston, we have a new header image!
			// Gets only the image url. It's a pain, I know! Wish Wordpress has better options on this one
			$header_img = get_the_post_thumbnail( $post_id, 'post-thumbnail' );
			$header_img = explode('" class="', $header_img);
			$header_img = $header_img[0];
			$header_img = explode('src="', $header_img);
			$header_img = $header_img[1]; // only the url
		}
		else if ($graphene_settings['use_random_header_img']){
			$default_header_images = graphene_get_default_headers();
			$randomkey = array_rand($default_header_images);
			$header_img = str_replace('%s', get_template_directory_uri(), $default_header_images[$randomkey]['url']);
		} else {
			$header_img = get_header_image();
		}
	return $header_img;
}
add_action('graphene_get_header_image', 'graphene_get_header_image');
endif;


/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.1.3
*/
function graphene_comment_count($type = 'comments', $noneText = '', $oneText = '', $moreText = ''){

	if($type == 'comments') :
		$typeSql = 'comment_type = ""';
	elseif($type == 'pings') :
		$typeSql = 'comment_type != ""';
	elseif($type == 'trackbacks') :
		$typeSql = 'comment_type = "trackback"';
	elseif($type == 'pingbacks') :
		$typeSql = 'comment_type = "pingback"';
	endif;

	global $wpdb;

    $result = $wpdb->get_var('
        SELECT
            COUNT(comment_ID)
        FROM
            '.$wpdb->comments.'
        WHERE
            '.$typeSql.' AND
            comment_approved="1" AND
            comment_post_ID= '.get_the_ID()
    );

	if($result == 0):
		echo str_replace('%', $result, $noneText);
	elseif($result == 1): 
		echo str_replace('%', $result, $oneText);
	elseif($result > 1): 
		echo str_replace('%', $result, $moreText);
	endif;
}

/**
 * Enqueue the jQuery Tools Tabs JS and the necessary script for comments/pings tabs
*/
function graphene_tabs_js(){ ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function($){
			$(function(){
				$("div#comments").tabs("div#comments > ol", {tabs: 'h4', effect: 'fade'});
			});
		});
		//]]>
	</script>
<?php
}
add_action('wp_footer', 'graphene_tabs_js');


/**
 * Register and enqueue the TinyDropdown menu script
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.1.4
*/
/*
function graphene_tinydropdown_js(){
	wp_enqueue_script('tiny-dropdown', get_template_directory_uri().'/js/tinydropdown.js', array(), '', true);
}
add_action('template_redirect', 'graphene_tinydropdown_js');
*/


/**
 * Add the TinyDropdown initialisation script
*/
/*
function apis_tinydropdown_script(){ ?>
	<script type="text/javascript">
	<!--
	var menu1 = new menu.dd('menu1');
	menu1.init('header-menu');
	//-->
	</script>
<?php
}
add_action('wp_footer', 'apis_tinydropdown_script');
*/



/**
 * Add JavaScript to hide and show the options panel
*/
function graphene_options_js(){ ?>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function($){
        $('.meta-box-sortables .head-wrap').click(function(){
            $(this).next().toggle(400);
            return false;
        }).next().hide();
    });
	//]]>
	</script>
<?php
}
add_action('admin_footer', 'graphene_options_js');


/**
 * This functions adds additional classes to the <body> element. The additional classes
 * are added by filtering the Wordpress body_class() function.
*/
function graphene_body_class($classes){
    
    $column_mode = graphene_column_mode();
    $classes[] = $column_mode;
    // for easier CSS
    if ( strpos($column_mode, 'two-col') === 0){
        $classes[] = 'two-columns';
    } else if ( strpos($column_mode, 'three-col') === 0 ){
        $classes[] = 'three-columns';
    }
    
    // Prints the body class
    return $classes;
}
add_filter('body_class', 'graphene_body_class');


/**
 * This functions adds additional classes to the post element. The additional classes
 * are added by filtering the WordPress post_class() function.
*/
function graphene_post_class($classes){
    global $graphene_settings;
    
	if (in_array($graphene_settings['post_date_display'], array('hidden', 'text'))) {
		$classes[] = 'nodate';
	}
	
    // Prints the body class
    return $classes;
}
add_filter('post_class', 'graphene_post_class');


function graphene_column_mode(){
    global $graphene_settings;
    
    // first check the template
    if (is_page_template('template-onecolumn.php'))
        return 'one-column';
    elseif (is_page_template('template-twocolumnsleft.php'))
        return 'two-col-left';
    elseif (is_page_template('template-twocolumnsright.php'))
        return 'two-col-right';
    elseif (is_page_template('template-threecolumnsleft.php'))
        return 'three-col-left';
    elseif (is_page_template('template-threecolumnsright.php'))
        return 'three-col-right';
    elseif (is_page_template('template-threecolumnscenter.php'))
        return 'three-col-center';
    else // now get the column mode        
        return $graphene_settings['column_mode']; 
}

/**
 * Add the .htc file for partial CSS3 support in Internet Explorer
*/
function graphene_ie_css3(){ ?>
	<!--[if lte IE 8]>
      <style type="text/css" media="screen">
      	#footer, div.sidebar-wrap, .block-button, .featured_slider, #slider_root, #comments li.bypostauthor{behavior: url(<?php echo get_template_directory_uri(); ?>/js/PIE.php);}
        .featured_slider{margin-top:0 !important;}
      </style>
    <![endif]-->
    <?php
}
add_action('wp_head', 'graphene_ie_css3');


/**
 * Add Google Analytics code if tracking is enabled 
 */ 
function graphene_google_analytics(){
	global $graphene_settings;
    if ($graphene_settings['show_ga']) : ?>
    <!-- BEGIN Google Analytics script -->
    	<?php echo stripslashes($graphene_settings['ga_code']); ?>
    <!-- END Google Analytics script -->
    <?php endif; 
}
add_action('wp_head', 'graphene_google_analytics', 1000);


/**
 * This function prints out the title for the website.
 * If present, the theme will display customised site title structure.
*/
if (!function_exists('graphene_title')) :
	function graphene_title(){
		global $graphene_settings;
		
		if (is_front_page()) { 
			if ($graphene_settings['custom_site_title_frontpage']) {
				$title = $graphene_settings['custom_site_title_frontpage'];
				$title = str_replace('#site-name', get_bloginfo('name'), $title);
				$title = str_replace('#site-desc', get_bloginfo('description'), $title);
			} else {
				$title = get_bloginfo('name') . " &raquo; " . get_bloginfo('description');
			}
			
		} else {
			if ($graphene_settings['custom_site_title_content']) {
				$title = $graphene_settings['custom_site_title_content'];
				$title = str_replace('#site-name', get_bloginfo('name'), $title);
				$title = str_replace('#site-desc', get_bloginfo('description'), $title);
				$title = str_replace('#post-title', wp_title('', false), $title);
			} else {
				$title = wp_title('', false)." &raquo; ".get_bloginfo('name');
			}
		}
		
		echo $title;
	}
endif;


/*
 * Adds a menu-item-ancestor class to menu items with children for styling.
 * Code taken from the Menu-item-ancestor plugin by Valentinas Bakaitis
*/
function graphene_add_ancestor_class($classlist, $item){
	global $wp_query, $wpdb;
	//get the ID of the object, to which menu item points
	$id = get_post_meta($item->ID, '_menu_item_object_id', true);
	//get first menu item that is a child of that object
	$children = $wpdb->get_var('SELECT post_id FROM '.$wpdb->postmeta.' WHERE meta_key like "_menu_item_menu_item_parent" AND meta_value='.$item->ID.' LIMIT 1');
	//if there is at least one item, then $children variable will contain it's ID (which is of course more than 0)
	if($children > 0)
		//in that case - add the CSS class
		$classlist[] = 'menu-item-ancestor';
	//return class list
	return $classlist;
}

//add filter to nav_menu_css_class list
add_filter('nav_menu_css_class', 'graphene_add_ancestor_class', 2, 10);


/**
 * Prints out the content of a variable wrapped in <pre> elements.
 * For development and debugging use
*/
function disect_it($var = NULL, $exit = true, $comment = false){
	if ($var !== NULL){
		if ($comment) {echo '<!--';}
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		if ($comment) {echo '-->';}
		if ($exit) {exit();}
	} else {
		echo '<strong>ERROR:</strong> You must pass a variable as argument to the <code>disect_it()</code> function.';	
	}
}
?>