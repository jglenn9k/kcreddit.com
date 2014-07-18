<?php
/*
Template Name: Authors
*/
/**
 * @package WordPress
 * @subpackage Constructor
 */

get_header(); ?>
<div id="content" class="box shadow opacity <?php the_constructor_layout_class() ?>">
    <div id="container" class="container-authors">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?> id="post-<?php the_ID() ?>">
                <header class="opacity box">
                    <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'constructor'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h1>
                </header>
                <div class="entry">
                    <?php the_content(__('Read the rest of this entry &raquo;', 'constructor')) ?>
                    <ul>
                        <?php wp_list_authors('optioncount=1&exclude_admin=0&show_fullname=1') ?>
                    </ul>
                </div>
                <footer>
                    <?php if($post->post_parent) : $parent_link = get_permalink($post->post_parent); ?>
                    <a href="<?php echo $parent_link; ?>"><?php _e('Back to Parent Page', 'constructor');?></a> |
                    <?php endif; ?>
                    <?php edit_post_link(__('Edit', 'constructor'), '', ' | '); ?>
                </footer>
            </article>
        <?php endwhile; ?>
    </div><!-- id='container' -->
    <?php get_constructor_sidebar(); ?>
</div><!-- id='content' -->
<?php get_footer(); ?>