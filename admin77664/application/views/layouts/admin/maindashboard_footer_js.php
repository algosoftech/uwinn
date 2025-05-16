<script src="{ASSET_INCLUDE_URL}js/ripple.js"></script>
<script src="{ASSET_INCLUDE_URL}js/pcoded.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/menu-setting.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/jquery.validate.js"></script>
<!-- notification Js -->
<script src="{ASSET_INCLUDE_URL}js/plugins/bootstrap-notify.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/manoj.js"></script>	
<!-- datatable Js -->
<script src="{ASSET_INCLUDE_URL}js/plugins/jquery.dataTables.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/plugins/dataTables.bootstrap4.min.js"></script>

<script src="{ASSET_INCLUDE_URL}js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src='{ASSET_INCLUDE_URL}js/chosen.jquery.min.js'></script>
<script type="text/javascript">
function create_editor_for_textarea(textareaid)
{	
	if (document.getElementById(textareaid)) {
		// Replace the <textarea id="Description"> with a CKEditor
		// instance, using default configuration.
		CKEDITOR.replace(textareaid, {filebrowserBrowseUrl :'{ASSET_INCLUDE_URL}js/ckeditor/filemanager/browser/default/browser.html?Connector={ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/connector.php',
		filebrowserImageBrowseUrl : '{ASSET_INCLUDE_URL}js/ckeditor/filemanager/browser/default/browser.html?Type=Image&Connector={ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/connector.php',
		filebrowserFlashBrowseUrl :'{ASSET_INCLUDE_URL}js/ckeditor/filemanager/browser/default/browser.html?Type=Flash&Connector={ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/connector.php',
		filebrowserUploadUrl :'{ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/upload.php?Type=File',
		filebrowserImageUploadUrl : '{ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/upload.php?Type=Image',
		filebrowserFlashUploadUrl : '{ASSET_INCLUDE_URL}js/ckeditor/filemanager/connectors/php/upload.php?Type=Flash',
		allowedContent:true,
		height: '200px'/*,
		toolbar: [
				{ name: 'document'},	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
				[ 'Cut', 'Copy', 'Paste',],			// Defines toolbar group without name.
				{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] }
			]*/
		});	
	}
};
</script>