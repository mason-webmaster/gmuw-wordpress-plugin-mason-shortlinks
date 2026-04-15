/* custom admin scripts for site */


jQuery(document).ready(function(){

	// Debug
	//console.log('custom admin js file loaded');

	//Implement datatables

	//datatables on the WP dashboard page specifically (simpler)
	jQuery('.wp-admin.index-php table.data_table').DataTable({
		paging: false,
		dom: 'fti',
	});

	//datatables on non-dashboard WP admin pages
	jQuery('.wp-admin:not(.index-php) table.data_table').DataTable({
		paging: false,
		dom: 'Bfritip',
		buttons: [
		  'copy', 'excel', 'csv', 'print'
		]
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
