<div class="row">
	<div class="col-12">
		<div class="content shadow p-4 mb-4">
			<h3><?= _exists([$this->model->title]) ? ($this->escape)($this->model->title) : 'Show'; ?></h3>

			<table class="table table-bordered table-striped">
				<thead>
					<tr class="d-flex">
						<th class="col-4">Column</th>
						<th class="col-8">Value</th>
					</tr>
				</thead>
				<tbody>
<?php foreach ($this->attributes as $attribute) { ?>
	<?php
		// Skip filtered
		if ($attribute[3] == 1) { continue; }

		$title = ucfirst($attribute[0]);
		$title = str_replace('_', ' ', $title);
	?>
					<tr class="d-flex">
						<td class="col-4"><?= $title; ?></td>
						<td class="col-8">
	<?php $value = $this->model->{$attribute[0]}; ?>
	<?php if ($attribute[1] == 'checkbox' && is_numeric($value)) { ?>
							<i class="fa <?= $value ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></i>
	<?php } else { ?>
							<?= ($this->escape)($value); ?>
	<?php } ?>
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>

			<div class="pb-5"></div>
		</div>
	</div>
</div>
