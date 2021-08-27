<div class="js-admin-menu admin-menu col-3 <?= \App\Classes\User::getToggle() ? 'd-block' : 'd-none'; ?>">
	<div class="content admin-content shadow p-4">
		<a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin"><h4>Admin panel</h4></a>

		<hr>
		<h5>Navigation</h5>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/section">Section</a>
		<br>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/page">Page</a>

		<hr>
		<h5>Link</h5>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/section-has-content">SectionHasContent</a>
		<br>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/page-has-content">PageHasContent</a>

		<hr>
		<h5>Content</h5>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/content">Content</a>
		<br>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/media">Media</a>
		<br>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/syntax-highlighting">Syntax Highlighting</a>

		<hr>
		<h5>Config</h5>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/cache">Cache</a>
		<br>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/admin/config">Config</a>

		<hr>
		- <a href="<?= \App\Classes\Config::c('APP_URL'); ?>/logout">Log out</a>
	</div>
</div>
<div class="js-admin-toggle admin-toggle fixed-bottom btn btn-dark">
	<i class="fa fa-bars" aria-hidden="true"></i>
</div>
