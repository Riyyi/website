<div class="row">
	<div class="col-12">
		<div class="content shadow p-4 mb-4">
			<?= $this->partial('../app/views/partials/message.php'); ?>

			<h3><?= $this->title; ?></h3>

			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
<?php foreach($this->attributes as $attribute) { ?>
	<?php
		if ($attribute[3] == 1) { continue; }

		$title = ucfirst($attribute[0]);
		$title = str_replace('_', ' ', $title);
	?>
						<th><?= $title; ?></th>
<?php } ?>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php foreach($this->rows as $key => $row) { ?>
					<tr>
						<td>
							<a href="<?= $this->url . '/' . $row['id']; ?>">
								<?= $key + 1; ?>
							</a>
						</td>
	<?php foreach($this->attributes as $attribute) { ?>
		<?php
			// Skip filtered
			if ($attribute[3] == 1) { continue; }
		?>
						<td>
		<?php $value = $row[$attribute[0]]; ?>
		<?php if ($attribute[1] == 'checkbox' && is_numeric($value)) { ?>
							<i class="fa <?= $value ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></i>
		<?php } else { ?>
							<?= ($this->escape)(substr($value, 0, 47)); ?>
							<?= strlen($value) > 47 ? '...' : ''; ?>
		<?php } ?>
						</td>
	<?php } ?>
						<td>
							<a href="<?= $this->url . '/' . $row['id']; ?>/edit">
								<i class="fa fa-pencil" aria-hidden="true"></i>
							</a>
							<a class="js-delete" href="<?= $this->url . '/' . $row['id']; ?>" data-token="<?= $this->csrfToken; ?>">
								<i class="fa fa-trash text-danger" aria-hidden="true"></i>
							</a>
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>

			<?= $this->partial('../app/views/partials/pagination.php'); ?>

			<div class="row">
				<div class="col-12">
					<a class="btn btn-dark" href="<?= $this->url ?>/create">New <?= $this->title; ?></a>
				</div>
			</div>

			<div class="pb-5"></div>
		</div>
	</div>
</div>
