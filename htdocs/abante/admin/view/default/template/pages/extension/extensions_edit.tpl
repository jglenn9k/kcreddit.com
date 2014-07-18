<div id="aPopup">
	<div class="message_body">
		<div class="aform">
			<div class="afield mask2">
				<div class="tl">
					<div class="tr">
						<div class="tc"></div>
					</div>
				</div>
				<div class="cl">
					<div class="cr">
						<div class="cc">
							<div class="message_text" id="msg_body"></div>
						</div>
					</div>
				</div>
				<div class="bl">
					<div class="br">
						<div class="bc"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $resources_scripts ?>
<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_information"><?php echo $heading_title; ?></div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
							src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
					<?php echo $form_language_switch; ?>
				</div>
				<div class="tools">
					<a class="btn_standard" href="<?php echo $back; ?>"><?php echo $button_back; ?></a>
					<a class="btn_standard" href="<?php echo $reload; ?>"><?php echo $button_reload ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">

				<div class="extension_info">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td><img src="<?php echo $extension['icon'] ?>" alt="" border="0"/></td>
							<td><?php echo $extension['name'] ?></td>
							<td><?php echo ($extension['version'] ? $text_version . ': ' . $extension['version'] : ''); ?></td>
							<td><?php echo ($extension['installed'] ? $text_installed_on . ' ' . $extension['installed'] : ''); ?></td>
							<td><?php echo ($extension['create_date'] ? $text_date_added . ' ' . $extension['create_date'] : ''); ?></td>
							<td><?php echo ($extension['license'] ? $text_license . ': ' . $extension['license'] : ''); ?></td>
							<?php if ($add_sett) { ?>
							<td><a class="btn_standard" href="<?php echo $add_sett['link']; ?>"
								   target="_blank"><?php echo $add_sett['text']; ?></a></td>
							<?php
						}
							if ($extension['upgrade']['text']) {
								?>
								<td><a class="btn_standard"
									   href="<?php echo $extension['upgrade']['link'] ?>"><?php echo $extension['upgrade']['text'] ?></a>
								</td>
								<?php } ?>
							<?php if ($extension['help']['file']): ?>
							<td><a class="btn_standard" href="javascript:void(0);"
								   onClick="show_help();"><?php echo $extension['help']['text'] ?></a></td>
							<?php elseif ($extension['help']['ext_link']): ?>
							<td><a class="btn_standard" href="<?php echo $extension['help']['ext_link'] ?>"
								   target="_help"><?php echo $extension['help']['text'] ?></a></td>
							<?php endif; ?>
						</tr>
					</table>
				</div>

				<?php  echo $form['form_open']; ?>
				<table class="form">
					<?php foreach ($settings as $key => $value) : ?>
					<?php if (is_integer($value['note'])) {
						echo $value['value'];
						continue;
					} ?>
					<tr>
						<td><?php echo $value['note']; ?></td>
						<td class="ml_field">
							<?php
							if (in_array($key, array_keys($resource_field_list))) {
								echo '<div id="' . $key . '">' . $resource_field_list[$key]['value'] . '</div>';
								//echo $text_click_to_change;
							}
							echo $value['value']; ?>
						</td>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td align="right"></td>
						<td>
							<div align="center" style="margin-left:-220px;">
								<a class="btn_standard"
								   href="<?php echo $reload ?>&restore=1"><?php echo $button_restore_defaults; ?></a>&nbsp;
								<button class="btn_standard" type="submit"><?php echo $button_save; ?></button>
								&nbsp;
								<?php if ($add_sett) { ?>
								<a class="btn_standard" <?php echo $add_sett['onclick']; ?>
								   href="<?php echo $add_sett['link']; ?>"
								   target="_blank"><?php echo $add_sett['text']; ?></a>
								<?php } ?>
							</div>
						</td>
					</tr>
				</table>
				</form>

				<?php if ($extension['note']) { ?>
				<div class="note"><?php echo $extension['note']; ?></div>
				<?php } ?>

				<?php if ($extension['preview']) { ?>
				<div class="product_images">
					<div class="main_image">
						<a href="<?php echo $extension['preview'][0]; ?>" title="<?php echo $heading_title; ?>"
						   class="thickbox" rel="gallery">
							<img width="150" src="<?php echo $extension['preview'][0]; ?>"
								 title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image"/>
						</a>
					</div>
					<?php if (count($extension['preview']) > 1) { ?>
					<div class="additional_images">
						<?php for ($i = 1; $i < count($extension['preview']); $i++) { ?>
						<div>
							<a href="<?php echo $extension['preview'][$i]; ?>" title="<?php echo $heading_title; ?>"
							   class="thickbox" rel="gallery">
								<img width="50" src="<?php echo $extension['preview'][$i]; ?>"
									 title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>"/>
							</a>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<br class="clr_both"/>

					<div class="enlarge"><span><?php echo $text_enlarge; ?></span></div>
				</div>
				<?php } ?>

				<?php if (!empty($extension['dependencies'])) { ?>
				<h2><?php echo $text_dependencies; ?></h2>
				<table class="list">
					<thead>
					<tr>
						<td class="left"><b><?php echo $column_id; ?></b></td>
						<td class="left"><b><?php echo $column_required; ?></b></td>
						<td class="left"><b><?php echo $column_status; ?></b></td>
						<td class="left"><b><?php echo $column_action; ?></b></td>
					</tr>
					</thead>
					<?php foreach ($extension['dependencies'] as $item) { ?>
					<tbody>
					<tr <?php echo ($item['class'] ? 'class="' . $item['class'] . '"' : ''); ?>>
						<td class="left"><?php echo $item['id']; ?></td>
						<td class="left"><?php echo ($item['required'] ? $text_required : $text_optional); ?></td>
						<td class="left"><?php echo $item['status']; ?></td>
						<td class="left"><?php echo $item['action']; ?></td>
					</tr>
					</tbody>
					<?php } ?>
				</table>
				<br/><br/>
				<?php } ?>

			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>
<div id="confirm_dialog"></div>
<script type="text/javascript">
	<!--

	function show_help(){
		$aPopup = $('#aPopup').dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			width: 550,
			minWidth: 550,
			buttons:{
			<?php if ( $extension['help']['ext_link'] ) { ?>
			"<?php echo $text_more_help; ?>": function() {
				window.open(
					'<?php echo $extension['help']['ext_link']; ?>',
					'_blank'
				)
			},
			<?php } ?>
			"close": function(event, ui) {
				$(this).dialog('destroy');
			}
		},
		open: function() {
		},

		resize: function(event, ui){
		},
		close: function(event, ui) {
			$(this).dialog('destroy');
			$("#message_grid").trigger("reloadGrid");
		}
	});

	$.ajax({
		url: '<?php echo $extension['help']['file_link']; ?>',
		type: 'GET',
		dataType: 'json',
		success: function(data) {

			$aPopup.dialog( "option", "title", data.title );
			$('#msg_body').html(data.content);

			$aPopup.dialog('open');
		}
	});
}

$(function(){
	$("input, textarea, select, .scrollbox", '.contentBox #editSettings').not('.no-save').aform({
		triggerChanged: true,
        buttons: {
            save: '<?php echo str_replace("\r\n", "", $button_save_green); ?>',
            reset: '<?php echo str_replace("\r\n", "", $button_reset); ?>'
        },
        save_url: '<?php echo $update; ?>'
	});

	$("#store_id").change(function(){
		location = '<?php echo $target_url;?>&store_id='+ $(this).val();
	});
<?php  if ($resource_field_list) {
		foreach ($resource_field_list as $name => $resource_field) {
			?>
		$('#<?php echo $name; ?>').click(function(){
        selectDialog('<?php echo $resource_field['resource_type'] ?>', $(this).attr('id'));
        return false;
    });
	<?php } ?>

<?php } ?>

	if($('#btn_upgrade')){
		$('#btn_upgrade').click(function(){
			window.open($(this).parent('a').attr('href'),'','width=700,height=700,resizable=yes,scrollbars=yes');
			return false;
		});
	}
});

$("#<?php echo $extension['id']; ?>_status").parents('.aswitcher').click(
	function(){
		var switcher = $("#<?php echo $extension['id']; ?>_status");
		var value = switcher.val();
		if(value==1){
			$aPopup = $('#confirm_dialog').dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				height: 'auto',
				minWidth: 100,
				buttons: {
							"<?php echo $button_agree;?>": function() {
								$( this ).dialog( "destroy" );
							},
							"<?php echo $button_cancel;?>": function() {
								$("#<?php echo $extension['id']; ?>_status").parents('.aform').find('.abuttons_grp').find('a:eq(1)').click();
								$( this ).dialog( "destroy" );
						}
				},
				close: function(event, ui) {
							$("#<?php echo $extension['id']; ?>_status").parents('.aform').find('.abuttons_grp').find('a:eq(1)').click();
							$(this).dialog('destroy');
						}

			});

			$.ajax({
						url: '<?php echo $dependants_url; ?>',
						type: 'GET',
						data: 'extension=<?php echo $extension['id']; ?>',
						dataType: 'json',
						success: function(data) {
							if(data=='' || data==null){
								return null;
							}else{
								if(data.text_confirm){
									$('#confirm_dialog').html(data.text_confirm);
								}
								$aPopup.dialog('open');
							}
						}
					});
		}

});
-->
</script>
