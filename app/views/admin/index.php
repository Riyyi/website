<div class="content shadow p-4 mb-4">
	<div id="home">
		<h3>
			Welcome back,
		</h3>
		<h3>
			<?= $this->user->first_name; ?>
			<?= !empty($this->user->last_name) ? ' ' . $this->user->last_name : ''; ?>
		</h3>
	</div>

	<div class="pb-5"></div>
</div>
