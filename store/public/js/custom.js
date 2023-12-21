$(document).ready(function() {

	$('[data-toggle="tooltip"]').tooltip();

	$('#toTop').on('click',function (e) {
		e.preventDefault();

		var target = this.hash;
		var $target = $(target);

		$('html, body').stop().animate({
			'scrollTop': 0
		}, 900, 'swing');
	});

	$('.custom-nav .expand').on('click', function(e) {
		e.preventDefault();
		let parent = $(this).closest('.custom-nav');
		parent.toggleClass("custom-nav-extended");
	});

});