<form action="<?= $this->url; ?>" method="post">

	<?php $count = 0; ?>
	<?php foreach ($this->form->getFields() as $name => $field) { ?>

		<?php if ($field[1] == 'comment') { ?>

			<div class="form-group">
				<?= $field[0]; ?>
			</div>

		<?php } else if ($field[1] == 'radio') { ?>

			<div class="form-group">
			<?php $radioCount = 0; ?>

			<?php if ($field[0] != '') { ?>
				<label><?= $field[0]; ?></label><br>
			<?php } ?>

			<input type="hidden" name="<?= $name; ?>" value="">
			<?php foreach ($field[2] as $value => $label) { ?>

				<input type="radio" name="<?= $name; ?>" id="<?= $value; ?>"
					<?= strstr($field[3], 'required') ? 'required' : ''; ?>
					<?= $this->{$name} == $value || (!isset($this->{$name}) && $radioCount == 0 && $field[0] == '') ? 'checked' : ''; ?>
					<?= $count == 0 && $radioCount == 0 ? 'autofocus' : ''; ?>
					value="<?= $value; ?>">
				<label for="<?= $value; ?>"><?= $label; ?>&nbsp;&nbsp;</label>
				<?= $field[0] != '' ? '<br>' : ''; ?>

				<?php $radioCount++; ?>
			<?php } ?>
			</div>

		<?php } else if ($field[1] == 'text' || $field[1] == 'email' ||
						 $field[1] == 'tel' || $field[1] == 'password') { ?>

				<div class="form-group">
					<label for="<?= $name; ?>"><?= $field[0]; ?></label>
					<input type="<?= $field[1]; ?>" name="<?= $name; ?>" id="<?= $name; ?>" class="form-control"
						<?= strstr($field[3], 'required') ? 'required' : ''; ?>
						<?= _exists($field, 5) ? "pattern='$field[5]'" : ''; ?>
						<?= _exists($field, 6) ? "title='$field[6]'" : ''; ?>
						<?= strstr($field[3], 'captcha') ? 'autocomplete="off"' : '' ?>
						<?= $count == 0 ? 'autofocus' : ''; ?>
						value="<?= $this->{$name}; ?>">

			<?php if (strstr($field[3], 'captcha')) { ?>
					<img src="/img/captcha.jpg" class="img-fluid pt-2">
			<?php } ?>
				</div>

		<?php } else if ($field[1] == 'textarea') { ?>

			<div class="form-group">
				<label for="<?= $name; ?>"><?= $field[0]; ?></label>
				<textarea name="<?= $name; ?>" cols="1" rows="5" id="<?= $name; ?>" class="form-control"
					<?= strstr($field[3], 'required') ? 'required' : ''; ?>
					<?= $count == 0 ? 'autofocus' : ''; ?>
					><?= $this->{$name}; ?></textarea>
			</div>

		<?php } else if ($field[1] == 'checkbox') { ?>

			<div class="form-group form-check">

			<?php $checkboxCount = 0; ?>
				<input name="<?= $name; ?>" type="hidden" value="0">
			<?php foreach ($field[2] as $value => $label) { ?>

				<input type="checkbox" name="<?= $name; ?>" id="<?= $value; ?>" class="form-check-input"
					<?= strstr($field[3], 'required') ? 'required' : ''; ?>
					<?= $this->{$name} == $value ? 'checked' : ''; ?>
					<?= $count == 0 && $checkboxCount == 0 ? 'autofocus' : ''; ?>
					value="<?= $value; ?>">
				<label for="<?= $value; ?>" class="form-check-label"><?= $label; ?></label><br>

				<?php $checkboxCount++; ?>
			<?php } ?>
			</div>

		<?php } ?>

		<?php $count++; ?>
	<?php } ?>

	<p class="mb-0">
	<?php if (_exists([$this->form->getReset()])) { ?>
		<button type="reset" class="btn btn-dark"><?= $this->form->getReset(); ?></button>
	<?php } ?>
		<button type="submit" class="btn btn-dark"><?= $this->form->getSubmit(); ?></button>
	</p>

	<input type="hidden" name="_token" value="<?= $this->csrfToken; ?>" />
</form>
