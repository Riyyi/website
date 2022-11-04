<?php
	use App\Classes\Config;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha384-i61gTtaoovXtAbKjo903+O55Jkn2+RtzHtvNez+yI49HAASvznhe9sZyjaSHTau9" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<?php if ($this->adminSection) { ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/codemirror.min.js" integrity="sha384-CYXtjHe/lDQEYXvQVyIBaQsJ/mSdH193+qSBy4GHEQJX4qN2LAHI/vrze0AC73We" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/clike/clike.min.js" integrity="sha384-PgWELdtHCInKQO7E0Iuj74Ih+ksuRESaj8vjQhxDVdAhhqdnfmhOsQUy/cBFASN/" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/xml/xml.min.js" integrity="sha384-xPpkMo5nDgD98fIcuRVYhxkZV6/9Y4L8s3p0J5c4MxgJkyKJ8BJr+xfRkq7kn6Tw" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/javascript/javascript.min.js" integrity="sha384-kmQrbJf09Uo1WRLMDVGoVG3nM6F48frIhcj7f3FDUjeRzsiHwyBWDjMUIttnIeAf" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/css/css.min.js" integrity="sha384-fpeIC2FZuPmw7mIsTvgB5BNc8QVxQC/nWg2W+CgPYOAiBiYVuHe2E8HiTWHBMIJQ" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/htmlmixed/htmlmixed.min.js" integrity="sha384-xYIbc5F55vPi7pb/lUnFj3wu24HlpAMZdtBHkNrb2YhPzJV3pX7+eqXT2PXSNMrw" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/php/php.min.js" integrity="sha384-1FUwPY2kaZKXw258/9CYBSS+zcc3CPggxE1zLjmYYiOdkcOw3KcXH5VNJWWbjw2U" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/shell/shell.min.js" integrity="sha384-dmPRq54XNtYnlnAo7QrwJnZNa5r0JP5gU8z4H0686Cgz37qrR2jmNaDHUXyMI4wn" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/mode/python/python.min.js" integrity="sha384-anASZwLFwg9fpwvqs21eRCiI1Jxce0cbRF4W0KPN+4qrqlFGnByppUAMd6GeZb+A" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/keymap/vim.min.js" integrity="sha384-DlidtUdSavEUghGXpIXr2aM++HC3RMLDux675TC5dNunOL7pRBxKkrrkfarvcL8z" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js" integrity="sha384-tG37zJAk+EcUTn0PLQkIE5Bbmkuna7/Spxvd1GU2kyY8kKGX5V2K+qGitOvd5Sm1" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" data-manual integrity="sha384-MXybTpajaBV0AkcBaCPT4KIvo0FzoCiWXgcihYsw4FUkEz0Pv3JGV6tk2G8vJtDc" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha384-Uq05+JLko69eOiPr39ta9bh7kld5PKZoU+fF7g0EXTAriEollhZ+DrN8Q/Oi8J2Q" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js" integrity="sha384-6QJu8apxMmB9TiPVWzYKF5pRgKcz7snO0/QU+MrWmgBLECQjoa6erxX2VQ5t41Jd" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/highlight-keywords/prism-highlight-keywords.min.js" integrity="sha384-Wchy6vgUSGGGXWUpUsb15Y8wXgk4snlp96dF6c2GvaSQ3LeYP7778HuJUDJXg/so" crossorigin="anonymous"></script>

	<script src="<?= Config::c('APP_URL'); ?>/js/app.js"></script>
<?php } ?>
<script src="<?= Config::c('APP_URL'); ?>/js/site.js"></script>
