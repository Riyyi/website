<div class="row">
	<div class="col-12">
		<div class="content shadow p-4 mb-4">
			<?= $this->partial('../app/views/partials/message.php'); ?>

			<h3>Create</h3>

			<form action="<?= $this->url; ?>" method="post">
<?php foreach($this->attributes as $key => $attribute) { ?>
	<?php
		if ($attribute[3] == 1) { continue; }
		if ($attribute[2] == 1) { $required = 'required'; } else { $required = ''; }

		$name = $attribute[0];
		$title = ucfirst($attribute[0]);
		$title = str_replace('_', ' ', $title);
		$autofocus = $key == 0 ? 'autofocus' : '';
	?>
				<div class="form-group">
					<label for="<?= $name; ?>"><?= $title;?></label>
					<?= ($required && $attribute[1] != 'checkbox') ? ' <span class="text-danger">*</span>' : ''; ?><br>
	<?php if ($attribute[1] == 'text') { ?>

					<input type="text" class="form-control"
						<?= $autofocus; ?>
						<?= $required; ?>
						name="<?= $name; ?>"
						placeholder="<?= $title; ?>">

	<?php } else if ($attribute[1] == 'textarea') { ?>

					<textarea id="summernote" rows="18" cols="1" class="form-control"
						<?= $autofocus; ?>
						<?= $required; ?>
						name="<?= $name; ?>"
						placeholder="<?= $title; ?>"
						></textarea>

	<?php } else if ($attribute[1] == 'checkbox') { ?>

					<input type="hidden" name="<?= $name; ?>" value="0">
					<input type="checkbox" name="<?= $name; ?>" value="1">

	<?php } else if ($attribute[1] == 'dropdown') { ?>

					<select name="<?= $name; ?>" class="custom-select" <?= $required; ?>>
		<?php foreach($this->dropdownData[$key] as $dropdownKey => $value) { ?>
						<option value="<?= $dropdownKey; ?>"><?= $value; ?></option>
		<?php } ?>
					</select>

	<?php } ?>
				</div>
<?php } ?>
				<button type="submit" class="btn btn-dark">Create</button>

				<input type="hidden" name="_token" value="<?= $this->csrfToken; ?>" />
			</form>
		</div>
	</div>
</div>
