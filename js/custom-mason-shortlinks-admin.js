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


});
