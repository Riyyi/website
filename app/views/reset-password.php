<div class="content shadow p-4 mb-4">
	<?= $this->partial('../app/views/partials/message.php'); ?>

<?php if (!$this->newPassword) { ?>
			<h1>Reset password</h1>
			<p>Please fill in one of the fields.</p>
<?php } else { ?>
			<h1>Set new password</h1>
<?php } ?>

	<?= $this->partial($this->injectView); ?>

	<div class="pb-5"></div>
</div>
