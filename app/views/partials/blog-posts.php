<?php
	use App\Classes\Config;
?>
<?php foreach ($this->posts as $post) { ?>
		<a class="clear" href="<?= Config::c('APP_URL') . '/' . $post['section'] . '/' . $post['page']; ?>">
			<div class="content shadow p-4 mb-4" style="min-height: 0;">
				<div class="row">
					<div class="col-12 <?= _exists($post, 'media_id') ? 'col-md-9' : ''; ?>">
						<div class="d-flex flex-wrap align-items-baseline">
							<h4 class="mr-3"><strong><?= $post['title']; ?></strong></h4>
							<small class="mb-2 text-muted"><?= ($this->prettyTimestamp)($post['created_at']); ?></small>
						</div>
						<p>
							<?= $post['content']; ?>
						</p>
	<?php if (_exists($post, 'tag')) { ?>
						<small>
							<i>
								tags:
		<?php $tags = ($this->tags)($post['tag']); ?>
		<?php foreach ($tags as $key => $tag) { ?>
			<?= $tag . (($key === array_key_last($tags)) ? '' : ', '); ?>
		<?php } ?>
							</i>
						</small>
						<div class="d-md-none mb-3"></div>
	<?php } ?>
					</div>
	<?php if (_exists($post, 'media_id')) { ?>
					<div class="col-12 col-md-3">
						<img src="<?= Config::c('APP_URL'). '/media/' . $post['filename'] . '.' . $post['extension']; ?>"
						     loading="lazy" class="w-100" style="height: 125px; object-fit: cover;">
					</div>
	<?php } ?>
				</div>
			</div>
		</a>
<?php } ?>
