<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_category"><?php echo $heading_title; ?></div>
<?php
if ( !empty($search_form) ) {
    echo '<div class="filter">';
    echo $search_form['form_open'];
    foreach ($search_form['fields'] as $f) echo $f;
	echo '<button type="submit" class="btn_standard">'.$search_form['submit'].'</button>';
	echo '<button type="reset" class="btn_standard">'.$search_form['reset'].'</button>';
    echo '</form>';
    echo '</div>';
}
?>
    <div class="toolbar">
	    <?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	    <?php echo $form_language_switch; ?>
	    <div class="buttons">
		  <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
			<span class="icon_add">&nbsp;</span>
		  </a>
		</div>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<script type="text/javascript">
    $('#category_grid_wrapper a.grid_action_expand').click(function(){
 			        var new_url = '<?php echo $grid_url; ?>&'+$(this).attr('rel');
        			$('#category_grid')
            			.jqGridHistory('setGridParam',{url:new_url})
            			.trigger("reloadGrid");
        			return false;
                });
</script>