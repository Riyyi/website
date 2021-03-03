<?php
	use App\Classes\Config;
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<?php if ($this->adminSection) { ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/codemirror.min.js" integrity="sha384-v0AyVIspkm1uirQ6Nsmr90ScryXAWf+xv0DlNrNo1BkSY3sqr7tsUKLZyTMHqy2G" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/clike/clike.min.js" integrity="sha384-7LfH34Nu+Rz2rkqh9L9Okzi17vt5/sX9YZvMjHgRhwH94EdtzCaQ5SpIkXfn80/2" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/xml/xml.min.js" integrity="sha384-Fohp26Rl1xXN67RS6nTZ+s+DEgvUoMZGBTzPuAwW/tjDMtaL27W3b4Irtxtf9KPw" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/javascript/javascript.min.js" integrity="sha384-QPVXEBl5e4kOafHL/KBYAhkf9c7iaD6svR+RGt92QYCbdJiKcmfC4weMMPLif77e" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/css/css.min.js" integrity="sha384-3UHVlVGKahbp3CTUk/YC+ICeNAx8Na6vIIrmH18NimXtGR/zF4Ce4F1d+gNPFh9a" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/htmlmixed/htmlmixed.min.js" integrity="sha384-C4oV1iL5nO+MmIRm+JoD2sZ03wIafv758LRO92kyg7AFvpvn8oQAra4g3mrw5+53" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/php/php.min.js" integrity="sha384-WFPjfiKs+lVB1vp9fq9wEhsgi/5Z86jVLyaGMe2wnxa/3o8SEUCySeqglAaRqxnI" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/shell/shell.min.js" integrity="sha384-VzBcr5K83ctjdWufr/bGFmHLB7LbEhdN5dhKWvE3Q/H5Qfvz9dKPJgfHXeaq74da" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/mode/python/python.min.js" integrity="sha384-Wh2d9/yLlLvOQErKI4FDPRJXSklc6GlymSMyq37kJcVmfB73bJ33OqA2TpVHBisf" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.59.4/keymap/vim.min.js" integrity="sha384-MTJMQ129Rzid0TmDEIKZomitzagfahspgvwEWmm1s2ReXw8Evv5NTVf77JlmAOOM" crossorigin="anonymous"></script>

	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.15/dist/summernote-bs4.min.js" integrity="sha384-sH3/pYu1soe3hR5e4aLT8/9wusU2x7Xt10VNfJ6n0VbsUkmkPf+3jI7So6USyROv" crossorigin="anonymous"></script>

	<script src="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/components/prism-core.min.js" data-manual integrity="sha384-BhpFhn+hhQohKi2ygOAJsVKyJu2q+QYydLKC7rqeMZLyEJFQl1DZieRX4/WxKUsJ" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha384-FIz0ezhzXpErvkjCsQ+74Je6cuWUBoVubidnSGt498wjjEBaAV27BAISa0/GaAFq" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/plugins/line-numbers/prism-line-numbers.min.js" integrity="sha384-xktrwc/DkME39VrlkNS1tFEeq/S0JFbc8J9Q8Bjx7Xy16Z3NnmUi+94RuffrOQZR" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/prismjs@1.22.0/plugins/highlight-keywords/prism-highlight-keywords.min.js" integrity="sha384-Rk2xv6YOAfQH8z3ZAK37pgnQihXfgkER8B5EYhoFc+mMNPzf+t7g2J9U74FAvy2T" crossorigin="anonymous"></script>

	<script src="<?= Config::c('APP_URL'); ?>/js/app.js?v=<?= rand(); ?>"></script>
<?php } ?>
<script src="<?= Config::c('APP_URL'); ?>/js/site.js?v=<?= rand(); ?>"></script>
