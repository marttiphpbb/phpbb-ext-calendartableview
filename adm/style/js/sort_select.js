;(function($, window, document) {
	$('document').ready(function(){
		$('div.info-container').each(function(){
			var $container = $(this);
			var $input = $container.find('input[type="hidden"]').eq(0);
			var name = $input.attr('name');

			var $list_active = $container.find('.info-active ul').eq(0);
			var $list_inactive = $container.find('.info-inactive ul').eq(0);

			Sortable.create($list_active[0], {
				group: name,
			});

			Sortable.create($list_inactive[0], {
				group: name,
			});

			$('form[method="post"]').submit(function(event) {

				var p_string = '';

				$list_active.find('li[data-id]').each(function(){
					var $li = $(this);
					p_string += $li.attr('data-id');
					p_string += ',';
				});

				if (p_string){
					p_string = p_string.slice(0, -1);
				}

				$input.val(p_string);
			});
		});
	});
})(jQuery, window, document);
