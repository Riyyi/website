<div class="row">
	<div class="col-12">
		<div class="content shadow p-4 mb-4">
			<?= $this->partial('../app/views/partials/message.php'); ?>

			<h3>Media file manager</h3>

			<table class="table table-bordered table-striped">
				<thead>
					<th scope="col">Thumbnail</th>
					<th scope="col">URL</th>
					<th scope="col">Uploaded by</th>
					<th scope="col">Modifier</th>
				</thead>
				<tbody>

<?php foreach($this->media as $media) { ?>
	<?php
		$filename = $media['filename'] . '.' . $media['extension'];
	?>
					<tr>
						<td>
	<?php if (in_array($media['extension'], ['jpg', 'jpeg', 'png', 'gif'])) { ?>
							<img src="<?= $this->fileUrl . $filename; ?>"
								title="<?= $filename; ?>" alt="<?= $filename; ?>"
								class="img-thumbnail" width="100px">
	<?php } else { ?>
							No thumbnail available.
	<?php } ?>
						</td>
						<td>
							<a href="<?= $this->fileUrl . $filename; ?>" target="_blank">
								<?= $this->fileUrl . $filename; ?>
							</a>
						</td>
						<td>
	<?php
		$idx = array_search(           $media['log_id'], array_column($this->log, 'id'));
		$idx = array_search($this->log[$idx]['user_id'], array_column($this->user, 'id'));
		echo $this->user[$idx]['username'];
	?>
						</td>
						<td>
							<a class="js-delete" href="<?= $this->url . '/' . $media['id']; ?>">
								<button type="button" class="btn btn-danger">
									Delete
								</button>
							</a>
						</td>
					</tr>
<?php } ?>
					<tr>
						<form method="POST" action="<?= $this->url; ?>" enctype="multipart/form-data">
							<td colspan="2" class="js-upload p-0 middle">
								<input type="file" id="file" class="d-none" name="file[]" required multiple>

								<table class="table mb-0">
									 <thead></thead>
									 <tbody>
										<tr class="bg-transparent">
											<td class="border-0 middle text-center">
												<i class="fa fa-cloud-upload"></i>
												<span class="">Drag files here</span>
											</td>
											<td class="border-0 middle text-center">
												<label for="file" class="btn btn-light border px-3 py-1 mb-0">Select files</label>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td>
								<input type="hidden" name="overwrite" value="0">
								<input type="checkbox" name="overwrite" value="1"> Overwrite
							 </td>
							<td>
								<button type="submit" class="btn btn-success">Upload</button>
							</td>
						</form>
					</tr>

				</tbody>
			</table>

			<?= $this->partial('../app/views/partials/pagination.php'); ?>

		</div>
	</div>
</div>
