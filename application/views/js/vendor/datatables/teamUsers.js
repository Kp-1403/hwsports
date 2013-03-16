	var editor; // use a global for the submit and return data rendering in the examples
	
	$(document).ready(function() {
		editor = new $.fn.dataTable.Editor( {
			"ajaxUrl": "/datatables/teamUsers/"+$('#teamID').html(),
			"domTable": "#teamUsers",
			"fields": [ {
					"label": "User ID",
					"name": "userID",
					"type": "hidden"
				}, {
					"label": "First Name",
					"name": "firstName"
				}, {
					"label": "Last Name",
					"name": "lastName"
				}, {
					"label": "Email",
					"name": "email"
				}, {
					"label": "Phone",
					"name": "phone"
				}, {
					"label": "Address",
					"name": "address"
				}, {
					"label": "Bio",
					"name": "aboutMe"
				}
			],
			"events": {
				"onCreate": function (json, data) {
				},
				"onEdit": function (json, data) {
				},
				"onOpen": function ( settings, json ) {
				},
				"onInitRemove": function() {
					$.fancybox({
						href : '/datatables/predelete/'+$('.DTTT_selected').attr('id'),
						type : 'ajax'
					});
				}
			}
		} );

		$('#teamUsers').dataTable( {
			"sDom": 'TC<"clear">Rlfrtip',
			"sAjaxSource": "/datatables/teamUsers/"+$('#teamID').html(),
			"aoColumns": [
				{ "mData": "userID" },
				{ "mData": "firstName" },
				{ "mData": "email" },
				{ "mData": "phone" },
				{ "mData": "address" },
				{ "mData": "aboutMe" },
				{ "mData": "detailsLink" }
			],
			"aoColumnDefs": [
				{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] }
            ],
			"oTableTools": {
				"sSwfPath": "/swf/copy_csv_xls_pdf.swf",
				"sRowSelect": "single",
				"aButtons": [
					{ "sExtends": "editor_create", "editor": editor },
					{ "sExtends": "editor_edit",   "editor": editor },
					{ "sExtends": "editor_remove", "editor": editor },
					"select_all", 
					"select_none",
					{
						"sExtends":    "collection",
						"sButtonText": "Export",
						"aButtons":    [
							
							{
								"sExtends": "csv"
							},
							{
								"sExtends": "xls"
							},
							{
								"sExtends": "pdf",
								"mColumns": "visible"
							}
						]
					}
				]
			},
			"fnInitComplete": function ( settings, json ) {
				//editor.field('sportCategoryID').update( json.sportCategories );
			}
		} );
		
	} );