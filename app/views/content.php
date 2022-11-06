<div class="row">
<?php $size = $this->sideContent ? '8' : '12'; ?>
	<div class="col-12 col-md-<?= $size; ?> col-lg-<?= $size; ?>">
<?php foreach ($this->contents as $key => $content) { ?>
	<?php if ($content['type'] == '1') { ?>

		<div class="<?= !$content['hide_background'] ? 'content shadow p-4' : ''; ?> mb-4">
		<?php if ($key === array_key_first($this->contents)) { ?>
			<?= $this->partial('../app/views/partials/message.php'); ?>
		<?php } ?>

		<?php if ($content['hide_title'] == 0) { ?>
			<h1><?= ($this->escape)($content['title']); ?></h1>
		<?php } ?>
			<?= $content['content']; ?>

		<?php if ($this->injectView != '') { ?>
			<?= $this->partial($this->injectView); ?>
		<?php } ?>
		</div>

	<?php } ?>
<?php } ?>
	</div>

<?php if ($this->sideContent) { ?>
	<div class="col-8 col-md-4 col-lg-4">
	<?php foreach ($this->contents as $content) { ?>
		<?php if ($content['type'] == '2') { ?>

		<div class="content content-side shadow p-3 mb-4">
			<h3><?= ($this->escape)($content['title']); ?></h3>
			<?= $content['content']; ?>
		</div>

		<?php } ?>
	<?php } ?>
	</div>
<?php } ?>
</div>
