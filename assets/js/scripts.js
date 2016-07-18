(function($) {

	/**
	 * Change export fields when export class is changed.
	 * 
	 * @param {object} e
	 */
	$('#wc-export-class').on('change', function (e) {
		e.preventDefault();

		var $this = $(this);

		$.post(ajaxurl, {
			action: 'wc_export',
			wc_export_class: $this.val()
		}, function (res) {
			var $target = $this.closest('.form-table').find('#wc-export-include-fields');
			var tmpl = _.template($('#tmpl-wc-export-include-fields').html());

			$target.replaceWith(tmpl({
				fields: $.parseJSON(res)
			}));
		});
	});

	/**
	 * Enable datepicker on start date and end date field.
	 */
	$('#wc-export-start-date, #wc-export-end-date').datepicker({
		dateFormat: 'yy-mm-dd'
	});

})(window.jQuery);
