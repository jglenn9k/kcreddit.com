<span class="select_element">
    <select name="<?php echo $name ?>[]" id="<?php echo $id ?>" <?php echo $attr ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>>
	<?php foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>" <?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?> >
	<?php echo $text ?>
	</option>
	<?php } ?>
	</select>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>

<span class="select_element">
	<select name="<?php echo $name ?>[]" id="<?php echo $id ?>_zones" <?php echo ($style ? 'class="'.$style.'"':''); ?>>

	</select>
</span>

<script>
	$('#<?php echo $id ?>').change( function(){
		$('#<?php echo $id ?>_zones').load('<?php echo $url; ?>&country_name=' + encodeURIComponent($(this).val()) + '&zone_name=<?php echo $zone_name; ?>');
	});
	$('#<?php echo $id ?>_zones').load('<?php echo $url; ?>&country_name='+ encodeURIComponent($('#<?php echo $id ?>').val()) +'&zone_name=<?php echo $zone_name; ?>');
</script>