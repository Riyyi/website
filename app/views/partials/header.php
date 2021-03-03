<?php
	use App\Classes\Config;
?>

<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark shadow">
	<a class="navbar-brand" href="<?= Config::c('APP_URL'); ?>/"><i class="fa fa-home"></i> Home</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">

<?php foreach ($this->navigation as $section) { ?>
	<?php if (count($section) < 3) { continue; } ?>
	<?php if (count($section) == 3) { ?>
			<a class="nav-link mx-lg-2"
				href="<?= Config::c('APP_URL'); ?>/<?= $section[0] . '/' . $section[2][0]; ?>"
			><?= ($this->escape)($section[2][1]); ?></a>
		<?php continue; ?>
	<?php } ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle mx-lg-3 purple" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">
					<?= ($this->escape)($section[1]); ?>
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

	<?php foreach ($section as $key => $page) { ?>
		<?php if ($key == 0 || $key == 1) { continue; } ?>
					<a class="dropdown-item purple"
						href="<?= Config::c('APP_URL'); ?>/<?= $section[0] . '/' . $page[0]; ?>"
					><?= ($this->escape)($page[1]); ?></a>
	<?php } ?>

				</div>
			</li>
<?php } ?>

		</ul>

		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a class="nav-link" href="https://git.riyyi.com/riyyi" target="_blank"><i class="fa fa-coffee"></i> Gitea</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://github.com/riyyi" target="_blank"><i class="fa fa-github"></i> GitHub</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://gitlab.com/riyyi" target="_blank"><i class="fa fa-gitlab"></i> GitLab</a>
			</li>
		</ul>

	</div>
</nav>
