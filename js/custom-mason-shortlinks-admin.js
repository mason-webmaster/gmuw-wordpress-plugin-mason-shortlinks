/* custom admin scripts for site */


jQuery(document).ready(function(){

	// Debug
	//console.log('custom admin js file loaded');

	//Implement datatables

	//datatables on the WP dashboard page specifically (simpler)
	jQuery('.wp-admin.index-php table.data_table').DataTable({
		paging: false,
		dom: 'fti',
		order: [],
		width: '100%',
		autoWidth: false
	});

	//datatables on non-dashboard WP admin pages
	jQuery('.wp-admin:not(.index-php) table.data_table').DataTable({
		paging: false,
		dom: 'Bfritip',
		order: [],
		buttons: [
		  'copy', 'excel', 'csv', 'print'
		]
	});

	//handle datatables in the dashboard meta boxes.
	//listen for the WordPress 'postbox-toggled' event and resize the datatable appropriately
	jQuery(document).on('postbox-toggled', function(event, postbox) {

		//find if there is a DataTable inside the box that was just opened
		var $table = jQuery(postbox).find('table.data_table');

		//if there was, wait a bit and then recalculate the table size
		if ($table.length > 0) {
			setTimeout(function() {
				$table.DataTable().columns.adjust().responsive.recalc();
				$table.DataTable().draw();
			}, 200);
		}
	});

	//shortlink copy to clipboard
	jQuery(document).on('click', '.admin-copy', function(e) {
		e.preventDefault();

		const $btn = jQuery(this);
		const urlToCopy = $btn.data('url');

		if (!urlToCopy) return;

		// Use the Clipboard API
		navigator.clipboard.writeText(urlToCopy).then(() => {

			// Add the class to trigger CSS animation and tooltip
			$btn.addClass('copy-success');

			// Remove it after 1 second
			setTimeout(() => {
				$btn.removeClass('copy-success');
			}, 1000);

		}).catch(err => {
			console.error('Could not copy text: ', err);
		});
	});

});
