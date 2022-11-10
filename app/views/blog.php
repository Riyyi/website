<div class="row mt-4">
<?php $size = $this->sideContent ? '8' : '12'; ?>
	<div class="col-12 col-md-<?= $size; ?> col-lg-<?= $size; ?>">

		<div class="input-group mb-4">
			<input type="text" name="blog-search" id="js-blog-search" class="form-control"
				autofocus="" placeholder="Search" value="<?= $this->search; ?>" onfocus="this.select();" data-url="<?= $this->url; ?>">
			<div class="input-group-append">
				<button type="button" id="js-blog-search-button" class="btn btn-dark"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>

		<div id="blog-posts">
			<?= $this->partial($this->injectView); ?>
		</div>
	</div>
</div>
