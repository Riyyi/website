<?php
	use App\Classes\Config;
	use App\Classes\User;
?>

<div class="content shadow p-4 mb-4">
	<?= $this->partial('../app/views/partials/message.php'); ?>

<?php if (User::check()) { ?>
	You&apos;re already logged in. Click to <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/logout">log out</a>.

	<?php if ($this->redirectURL) { ?>
	<script type="text/javascript">
		setTimeout(function() {
			window.location.replace("<?= $this->redirectURL; ?>");
		}, 3000);
	</script>
	<?php } ?>
<?php } else { ?>
	<h1>Sign in</h1>
	<?= $this->partial($this->injectView); ?>
	<br>
	<a href="<?= Config::c('APP_URL'); ?>/reset-password">Forgot password</a>?
<?php } ?>
</div>
