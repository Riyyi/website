<?php
	use App\Classes\Config;
	use App\Classes\User;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="<?= $this->metaDescription; ?>">

		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<?php if ($this->adminSection) { ?>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/codemirror.min.css" rel="stylesheet" integrity="sha384-zaeBlB/vwYsDRSlFajnDd7OydJ0cWk+c2OWybl3eSUf6hW2EbhlCsQPqKr3gkznT" crossorigin="anonymous">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/theme/tomorrow-night-eighties.min.css" rel="stylesheet" integrity="sha384-zTCWZYMg963D68otcZCn2SQ2SBwih+lCwYxWqvx6xH8/Wt6+NN+giHIvcMpN4cPD" crossorigin="anonymous">

		<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet" integrity="sha384-hqv27sxmxAI2L4eughLkUpjS75/Z3/hg9DOWIl0PJWE4B6GJqM2Kx72ZPoQzsUpF" crossorigin="anonymous">
<?php } ?>

		<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" integrity="sha384-wFjoQjtV1y5jVHbt0p35Ui8aV8GVpEZkyF99OXWqP/eNJDU93D3Ugxkoyh6Y2I4A" crossorigin="anonymous">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" integrity="sha384-nUkTNLI8COlMCRJ0FHIdX76If83145OTCLUx4gQyfnO0gGeO/sD9czGEUBxtkcUv" crossorigin="anonymous">

		<link href="<?= Config::c('APP_URL'); ?>/css/style.css" rel="stylesheet">

		<title><?= ($this->escape)($this->pageTitle); ?><?= $this->pageTitle != '' ? ' - ' : '' ?>Rick van Vonderen</title>
		<link rel="icon" type="image/png" href="<?= Config::c('APP_URL'); ?>/img/favicon.png">
	</head>
	<body>
		<?= $this->partial('../app/views/partials/header.php'); ?>

		<div class="container">
			<div class="row">
				<div class="js-main-content col-<?= User::getToggle() ? '9' : '12'; ?>">
					<?= $this->yieldView(); ?>
					<?= $this->partial('../app/views/partials/footer.php'); ?>
				</div>
				<?php if ($this->loggedIn) { ?>
					<?= $this->partial('../app/views/partials/admin.php'); ?>
				<?php } ?>
			</div>

			<div id="isMobile" class="d-md-none d-lg-none d-xl-none"></div>
		</div>

		<?= $this->partial('../app/views/partials/script.php'); ?>
	</body>
</html>
