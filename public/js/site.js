$(document).ready(function() {

	//------------------------------------------//

	$.fn.inViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();

		var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();

		return elementBottom > viewportTop && elementTop < viewportBottom;
	}

	//------------------------------------------//
	// Blog search

	function blogSearch(input)
	{
		var url = input.data("url");
		var search = input.val();
		window.location.href = url + '?search=' + search;
	}

	$("#js-blog-search").keydown(function(e) {
		if (e.key == 'Enter') {
			e.preventDefault();
			blogSearch($(this));
		}
	});

	$("#js-blog-search-button").click(function() {
		blogSearch($("#js-blog-search"));
	});

	$("#js-blog-search").on("input", function() {
		var url = $(this).data("url");
		var search = $(this).val();
		if (search.length == 0 || search.length >= 3) {
			fetch(url + '/search?query=' + search)
				.then(response => response.text())
				.then(data => {
					$("#blog-posts").empty().append(data);
				});
		}
	});

	//------------------------------------------//

	// Image hover mouseenter
	$(".js-img-hover").mouseenter(function(event) {
		var width = $(window).width() - event.clientX - 1 + "px";
		if (!$("#isMobile").is(":hidden")) {
			width = "100%";
		}
		var url = $(this).data("img-hover");
		var divImg = $("<div id='img-hover' style='z-index: 9999; position: fixed; top: 0px; right: 0px;'>" +
			"<img style='max-width: " + width + "; height auto;' src=" + url + "></div>");
		divImg.appendTo("body");
	});

	// Image hover mouseleave
	$(".js-img-hover").mouseleave(function() {
		$("#img-hover").remove();
	});

	// Image hover on click
	$(".js-img-hover").click(function() {
		var url = $(this).data("img-hover");
		window.open(url, '_blank');
	});

	// Lazy load video's
	$(window).on('resize scroll', function() {
		$('.js-video').each(function(index) {
			if (!$(this).inViewport()) {
				return true;
			}

			$(this).find('source').each(function() {
				var source = $(this).attr('data-src');
				if (source === undefined) {
					return true;
				}

				$(this).attr("src", source);
				var video = this.parentElement;
				video.load();
				video.play();
				$(this).removeAttr('data-src');
			});
		});
	});
	$(window).trigger('scroll');

	// Only 1 field is required
	var $inputs = $('input[name=reset-password-username],input[name=reset-password-email]');
	$inputs.on('input', function() {
		// Set the required property of the other input to false if this input is not empty.
		$inputs.not(this).prop('required', !$(this).val().length);
	});

	// List toggle
	$('.js-toggle').click(function() {
		$(this).next("ul").toggle(400);
	});

	// Admin panel toggle
	$('.js-admin-toggle').click(function() {
		$.get('/admin/toggle').done(function(data) {
			if (data == '1') {
				$('.js-admin-menu').removeClass('d-none').addClass('d-block');
				$('.js-main-content').removeClass('col-12').addClass('col-9');
			}
			else if (data == '0') {
				$('.js-admin-menu').removeClass('d-block').addClass('d-none');
				$('.js-main-content').removeClass('col-9').addClass('col-12');
			}
		});
	});

});
