<?php
/**
 * @package WordPress
 * @subpackage Constructor
 */
?>
<article <?php post_class(); ?>>
    <h1 class="opacity box center"><a href="#" title="<?php _e( 'Nothing Found', 'constructor' ); ?>"><?php _e( 'Nothing Found', 'constructor' ); ?></a></h1>
    <p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'constructor'); ?></p>
    <?php get_search_form() ?>
</article>
<script type="text/javascript">
    // focus on search field after it has loaded
    document.getElementById('s') && document.getElementById('s').focus();
</script>