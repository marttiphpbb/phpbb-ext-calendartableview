;(function($, window, document) {
	$('document').ready(function(){
		var $ev = $('div.calendartableview tr td[data-topic]');
		$ev.hover(function(){
			var t = $(this).data('topic');
			$ev.filter('[data-topic="' + t + '"]').each(function(){
				$(this).addClass('hover');
			});
		}, function(){
			t = $(this).data('topic');
			$ev.filter('[data-topic="' + t + '"]').each(function(){
				$(this).removeClass('hover');
			});
		});
	});
})(jQuery, window, document);
