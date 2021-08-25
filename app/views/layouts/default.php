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

		<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<?php if ($this->adminSection) { ?>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/codemirror.min.css" rel="stylesheet" integrity="sha384-K/FfhVUneW5TdId1iTRDHsOHhLGHoJekcX6UThyJhMRctwRxlL3XmSnTeWX2k3Qe" crossorigin="anonymous">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/theme/tomorrow-night-eighties.min.css" rel="stylesheet" integrity="sha384-zTCWZYMg963D68otcZCn2SQ2SBwih+lCwYxWqvx6xH8/Wt6+NN+giHIvcMpN4cPD" crossorigin="anonymous">

		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote-bs4.min.css" rel="stylesheet" integrity="sha384-JNFvp1YkK/DsvVg1KxCYX/jfLcrqFkwUE1+4kt+Zpkhvfeetb13H+j2ZZhrTJwRy" crossorigin="anonymous">
<?php } ?>

		<link href="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/themes/prism-tomorrow.min.css" rel="stylesheet" integrity="sha384-rG0ypOerdVJPawfZS6juq8t8GVE9oCCPJbOXV/bF+e61zYW9Ib6u9WwSbTOK6CKA" crossorigin="anonymous">
		<link href="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" integrity="sha384-n3/UuPVL3caytud/opHXuyFoezGp2oAUB0foYaCAIs2QwGv/nV0kULHS2WAaJuxR" crossorigin="anonymous">

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
