;(function($, window, document) {
	$('document').ready(function(){
		$calendar_div = $('div.calendartableview');
		var $ev = $calendar_div.find('tr td[data-topic]');

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

		var $overlay = $calendar_div.find('table.calendar-navigation-overlay');

		var select_count = $overlay.data('select-count');
		var select_start = $overlay.data('select-start');
		var nav_offset = Math.floor(select_count / 2);

		$overlay_tr = $overlay.find('tr');
		$overlay_td = $overlay_tr.find('td');

		$overlay_tr.on('set_nav_class', function(event, index_start){

			var index_end = index_start + select_count - 1;

			$(this).find('td').each(function(){
				var index = $(this).index();

				if (index < index_start || index > index_end){
					$(this).removeClass('select select-left select-right');
					return;
				}

				$(this).addClass('select');

				if (index === index_start){
					$(this).addClass('select-left');
					return;
				}

				if (index === index_end){
					$(this).addClass('select-right');
				}
			});
		});

		$overlay_td.hover(function(e){

			var eq_index = $(this).index();
			var td_length = $(this).siblings('td').length + 1;
			var nav_index = Math.max(0, eq_index - nav_offset);
			var nav_index = Math.min(nav_index, td_length - select_count);

			$(this).parent().trigger('set_nav_class', nav_index);

		}, function(e){
			$(this).parent().trigger('set_nav_class', select_start);
		});

		$overlay_tr.trigger('set_nav_class', select_start);
	});
})(jQuery, window, document);
