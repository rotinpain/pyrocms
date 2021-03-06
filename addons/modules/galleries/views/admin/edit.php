<h3><?php echo lang('gallery_images.edit_image_label'); ?></h3>

<?php echo form_open($this->uri->uri_string(), 'class="crud"'); ?>
	<ul>
		<li class="even">
			<label for="current_thumbnail"><?php echo lang('gallery_images.thumbnail_label'); ?></label>
			<img id="current_thumbnail" src="<?php echo site_url('uploads/galleries/' . $gallery_image->slug . '/thumbs/' . $gallery_image->filename . '_thumb.' . $gallery_image->extension); ?>" alt="<?php echo $gallery_image->title; ?>" />
			<input type="hidden" id="thumb_width" name="thumb_width" />
			<input type="hidden" id="thumb_height" name="thumb_height" />
			<input type="hidden" id="thumb_x" name="thumb_x" />
			<input type="hidden" id="thumb_y" name="thumb_y" />
		</li>
		<li>
			<label for="thumbnail_actions"><?php echo lang('gallery_images.action_label'); ?></label>
			<select id="thumbnail_actions" name="thumbnail_actions">
				<option value="NONE" selected="selected"><?php echo lang('gallery_images.none_label'); ?></option>
				<option value="crop"><?php echo lang('gallery_images.crop_label'); ?></option>
				<option value="new"><?php echo lang('gallery_images.recreate_label'); ?></option>
				<option value="delete"><?php echo lang('gallery_images.delete_label'); ?></option>
			</select>
		</li>
		<li class="even">
			<label for="title"><?php echo lang('gallery_images.title_label'); ?></label>
			<input type="text" id="title" name="title" maxlength="255" value="<?php echo $gallery_image->title; ?>" />
		</li>
		<li>
			<label for="description"><?php echo lang('gallery_images.description_label'); ?></label>
			<textarea id="description" name="description" rows="3" col="20"><?php echo $gallery_image->description; ?></textarea>
		</li>
	</ul>

	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
<?php echo form_close(); ?>
