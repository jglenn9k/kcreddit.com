<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_country"><?php echo $heading_title; ?></div>
	<div class="heading-tabs">
		<a href="<?php echo $details ?>" <?php echo ( $active == 'details' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_details ?></span></a>
		<?php if (!empty($locations)) { ?>
		  <a href="<?php echo $locations ?>" <?php echo ( $active == 'locations' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_locations ?></span></a>
		<?php } ?>
	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<div class="buttons">
      <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert_location; ?>">
		<span class="icon_add">&nbsp;</span>
	  </a>
    </div></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	  <?php echo $listing_grid; ?>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

