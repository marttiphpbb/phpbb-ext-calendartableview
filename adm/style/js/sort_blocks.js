;(function($, window, document) {
	$('document').ready(function(){
		var list_active = $('ul#list-active')[0];
		var list_inactive = $('ul#list-inactive')[0];

		Sortable.create(list_active, {
			group: "list",
			dragClass: "cursor-move"
		});

		Sortable.create(list_inactive, {
			group: "list",
			dragClass: "cursor-move"
		});

		/*

		$('a[data-o]').click(function(e){
			e.preventDefault();
			var option = $(this).data('o');
			console.log(option);
			var li_el = $(this).parent().parent().parent();
			li_el.find('span.lbl').text($(this).text());
			li_el.data('option', option);
			li_el.attr('data-option', option);
		});

		$('form[method="post"]').submit(function(event) {

			var p_string = '';

			$('ul#list_active > li[data-block]').each(function(){
				var ul_el = $(this);
				p_string += ul_el.attr('data-block') + '.';
				p_string += ul_el.attr('data-option') + ',';
			});

			p_string = p_string.slice(0, -1);
			p_string = '+' + p_string;

			$('#periodic_mail_block_ary').val(p_string);
		});
		*/
	});
})(jQuery, window, document);
