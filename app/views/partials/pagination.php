<div class="row">
	<div class="col-12">
		<nav class="pb-2" aria-label="Page navigation">
			<ul class="pagination justify-content-center">
				<li class="page-item <?= $this->page <= 1 ? 'disabled' : ''; ?>">
					<a class="page-link" href="<?= $this->url . '?page=' . ($this->page - 1); ?>" aria-label="Previous">
						<span aria-hidden="true">&laquo;</span>
						<span class="sr-only">Previous</span>
					</a>
				</li>
<?php for($i = 1; $i <= $this->pages; $i++) { ?>
				<li class="page-item <?= $this->page == $i ? 'active' : ''; ?>">
					<a class="page-link" href="<?= $this->url . '?page=' . $i; ?>"><?= $i; ?></a>
				</li>
<?php } ?>
				<li class="page-item <?= $this->page >= $this->pages ? 'disabled' : ''; ?>">
					<a class="page-link" href="<?= $this->url . '?page=' . ($this->page + 1); ?>" aria-label="Next">
						<span aria-hidden="true">&raquo;</span>
						<span class="sr-only">Next</span>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</div>
