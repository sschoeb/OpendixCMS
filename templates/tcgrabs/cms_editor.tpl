
<textarea name="opendixckeditor">{$editorvalue}</textarea>
{literal}
<script type="text/javascript">
				CKEDITOR.replace( 'opendixckeditor',{ 
					filebrowserBrowseUrl : '/browser/browse.php',
			        filebrowserUploadUrl : '/uploader/upload.php'
			        } );
			</script>
{/literal}


