<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
get_header(); ?>

	<?php
	
	/* Check if the user selects specific category for the front page */
	if (is_home() && $graphene_settings['frontpage_posts_cats']) {
		global $wp_query;
		$cats = implode(',', $graphene_settings['frontpage_posts_cats']);
		$args = wp_parse_args(array('cat' => $cats, 'paged' => get_query_var('paged')), $query_string);
		// $args = $query_string.'&paged='.get_query_var('paged').'&cat='.$cats;
		query_posts($args);
		$wp_query->is_home = true;
	}
	
    /* Run the loop to output the posts.
     * If you want to overload this in a child theme then include a file
     * called loop-index.php and that will be used instead.
     */
     get_template_part('loop', 'index');
    ?>
            
<?php get_footer(); ?>