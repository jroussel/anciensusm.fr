<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to graphene_comment which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
?>

<?php if (post_password_required() && (comments_open() || have_comments())) : ?>
			<div id="comments">
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'graphene' ); ?></p>
                
                <?php do_action('graphene_protected_comment'); ?>
			</div><!-- #comments -->
<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php /* Lists all the comments for the current post */ ?>
<?php if ( have_comments() ) : ?>

<div id="comments" class="clearfix">
    <h4><a href="#"><?php graphene_comment_count('comments', __('No comment yet','graphene'), __('1 comment','graphene'), __("% comments", 'graphene') );?></a></h4>
    <h4 class="pings"><a href="#"><?php graphene_comment_count('pings', __('No ping yet','graphene'), __('1 ping','graphene'), __("% pings", 'graphene') );?></a></h4>

	<?php do_action('graphene_before_comments'); ?>

    <ol class="clearfix" id="comments_list">
        <?php
        /* Loop through and list the comments. Tell wp_list_comments()
         * to use graphene_comment() to format the comments.
         * If you want to overload this in a child theme then you can
         * define graphene_comment() and that will be used instead.
         * See graphene_comment() in functions.php for more.
         */
		 wp_list_comments(array('callback' => 'graphene_comment', 'style' => 'ol', 'type' => 'comment')); ?>
    </ol>
    <ol class="clearfix" id="pings_list">
        <?php
        /* Loop through and list the pings. Use the same callback function as
		 * listing comments above, graphene_comment() to format the pings.
         */
		 wp_list_comments(array('callback' => 'graphene_comment', 'style' => 'ol', 'type' => 'pings')); ?>
    </ol>
                    
		<?php // Are there comments to navigate through? ?>
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
        <div class="comment-nav clearfix">
            <p><?php paginate_comments_links(); ?>&nbsp;</p>
            <?php do_action('graphene_comments_pagination'); ?>
        </div>
        <?php endif; // Ends the comment navigation ?>
    
    <?php do_action('graphene_after_comments'); ?>
</div>
<?php endif; // Ends the comment listing ?>


<?php /* Display comments disabled message if there's already comments, but commenting is disabled */ ?>
<?php if (!comments_open() && have_comments()) : ?>
	<div id="respond">
		<h3 id="reply-title"><?php _e('Comments have been disabled.', 'graphene'); ?></h3>
        <?php do_action('graphene_comments_disabled'); ?>
    </div>
<?php endif; ?>


<?php /* Display the comment form if comment is open */ ?>
<?php if (comments_open()) : ?>
	<?php do_action('graphene_before_commentform'); 
	
	/**
	 * Get the comment form.
	*/ 
	
	if (!$graphene_settings['hide_allowedtags'])
		$allowedtags = '<p class="form-allowed-tags">'.sprintf(__('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'graphene'),'<code>'.allowed_tags().'</code>').'</p>';
	else
		$allowedtags = '';
	
	$args = array(
				'comment_notes_before' => '<p class="comment-notes">'.__('Your email address will not be published.', 'graphene').'</p>',
				'comment_notes_after'  => $allowedtags,
				'id_form'              => 'commentform',
				'label_submit'         => __('Submit Comment', 'graphene'),
				 );
	comment_form(apply_filters('graphene_comment_form_args', $args)); 

	do_action('graphene_after_commentform'); 

endif; // Ends the comment status ?>