<?php

use \App\Classes\Config;
?>
<div class="content shadow p-4 mb-4">
	<h3 class="mb-4">Cache</h3>

	<?php if (Config::c('CLOUDFLARE_ENABLED') != '1') { ?>
		To enable the Cloudflare cache options,
		make sure to set the following option in the <p>config.php</p> file:
		<pre class="line-numbers mb-4 language-php"><p class="language-php"><span class="token single-quoted-string string">'CLOUDFLARE_ENABLED'</span> <span class="token operator">=</span><span class="token operator">&gt;</span> <span class="token single-quoted-string string">'1'</span><span class="token punctuation">,</span><span aria-hidden="true" class="line-numbers-rows"><span></span></span></p></pre>
		</p><?php } else { ?>
		<div>
			<p>
				Cloudflare cache options:
			</p>
		</div>
		<hr>
		<div class="row align-items-center">
			<div class="col-9 col-md-10 col-lg-11">
				<h5>Purge CSS/JavaScript</h5>
				<p class="mb-0">Granuarly remove .css and .js files from Cloudflare&apos;s cache.</p>
			</div>
			<div class="col-3 col-md-2 col-lg-1">
				<div class="d-flex justify-content-end">
					<button id="" class="btn btn-dark">Purge</button>
				</div>
			</div>
		</div>
		<hr>
		<div class="row align-items-center">
			<div class="col-9 col-md-10 col-lg-11">
				<h5>Purge Fonts/Images</h5>
				<p class="mb-0">Granuarly remove font and images files from Cloudflare&apos;s cache.</p>
			</div>
			<div class="col-3 col-md-2 col-lg-1">
				<div class="d-flex justify-content-end">
					<button id="" class="btn btn-danger">Purge</button>
				</div>
			</div>
		</div>
		<hr>
		<div class="row align-items-center">
			<div class="col-9 col-md-10 col-lg-11">
				<h5>Purge All Files</h5>
				<p class="mb-0">Remove ALL files from Cloudflare&apos;s cache.</p>
			</div>
			<div class="col-3 col-md-2 col-lg-1">
				<div class="d-flex justify-content-end">
					<button id="" class="btn btn-danger">Purge</button>
				</div>
			</div>
		</div>
		<hr>
		<div class="row align-items-center">
			<div class="col-9 col-mlg-10 col-xl-11">
				<h5>Enable Development Mode</h5>
				<p class="mb-0">
					This will bypass Cloudflare&apos;s accelerated cache and slow down your site,
					but is useful if you are making changes to cacheable content
					(like images, CSS, or JavaScript) and would like to see those changes right away.
					Once entered, development mode will last for 3 hours and then automatically toggle off.
		<?php $state = $this->config['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED']; ?>
					<span id="develop-enabled" style="visibility: <?= $state == '1' ? 'visible' : 'hidden'; ?>;">
						Enabled for another
						<code id="develop-remaining">
							<?= $state == '1' ? $this->config['enabled-remaining'] : ''; ?>
						</code> hours.
					</span>
				</p>
			</div>
			<div class="col-3 col-lg-2 col-xl-1">
				<div class="d-flex justify-content-start">
					<input type="checkbox" id="development-mode"
						<?= $this->config['CLOUDFLARE_DEVELOPMENT_MODE_ENABLED'] ? 'checked' : ''; ?>>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="pb-5"></div>
</div>
