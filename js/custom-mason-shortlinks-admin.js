/* custom admin scripts for site */


jQuery(document).ready(function(){

	// Debug
	//console.log('custom admin js file loaded');

	//Implement datatables
	//regular datatables
	jQuery('table.data_table:not(.dashboardwidget)').DataTable({
		paging: false,
		dom: 'Bfritip',
		buttons: [
		  'copy', 'excel', 'csv', 'print'
		]
	});

	//simple datatables
	jQuery('table.data_table.dashboardwidget').DataTable({
		paging: false,
		dom: 'fti',
		order: [] //no initial sort
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
