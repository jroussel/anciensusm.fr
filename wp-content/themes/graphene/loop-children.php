<?php
/**
 * This file lists the child pages of the page currently being displayed,
 * if it has any.
*/
global $post;

/* Get the child pages */
$args = array(
    'child_of' => $post->ID,
    'sort_order' => 'ASC',
    'sort_column' => 'post_title',
    'hierarchical' => 0,
    'parent' => $post->ID,
    'post_type' => 'page',
    'post_status' => 'publish'
);
$pages = get_pages(apply_filters('graphene_child_pages_args', $args));

if ($pages) :

/*
stdClass Object
(
    [ID] => 144
    [post_author] => 3
    [post_date] => 2007-09-04 09:51:50
    [post_date_gmt] => 2007-09-03 23:51:50
    [post_content] => This page has a parent and child.
    [post_title] => Child page 1
    [post_excerpt] => 
    [post_status] => publish
    [comment_status] => closed
    [ping_status] => closed
    [post_password] => 
    [post_name] => child-page-1
    [to_ping] => 
    [pinged] => 
    [post_modified] => 2007-09-04 09:51:50
    [post_modified_gmt] => 2007-09-03 23:51:50
    [post_content_filtered] => 
    [post_parent] => 143
    [guid] => http://wpthemetestdata.wordpress.com/parent-page/child-page-1/
    [menu_order] => 0
    [post_type] => page
    [post_mime_type] => 
    [comment_count] => 0
    [filter] => raw
)
*/
?>
<div class="child-pages-wrap">
	<?php foreach ($pages as $page) : setup_postdata($page); ?>
    <div class="post child-page page">
        <div class="entry">
        	<div class="entry-content">
				<?php /* The post thumbnail */
                if (has_post_thumbnail(get_the_id())) {
                    echo '<div class="excerpt-thumb">';
                    the_post_thumbnail(apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail'));
                    echo '</div>';
                } else {
                    echo graphene_get_post_image(get_the_id(), apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail'), 'excerpt');	
                }
                ?>
                
                <?php /* The title */ ?>
                <h2 class="post-title">
                    <a href="<?php echo get_permalink($page->ID) ?>" rel="bookmark" title="<?php printf(esc_attr__('Permalink to %s', 'graphene'), $page->post_title); ?>"><?php if ($page->post_title == '') {_e('(No title)','graphene');} else {echo $page->post_title;} ?></a>
                </h2>
                
                <?php /* The excerpt */ 
                the_excerpt();
                ?>
                
                <?php /* View page link */ ?>
                <p><a href="<?php echo get_permalink($page->ID) ?>" class="block-button" rel="bookmark" title="<?php printf(esc_attr__('Permalink to %s', 'graphene'), $page->post_title); ?>"><?php _e('View page &raquo;', 'graphene'); ?></a></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>