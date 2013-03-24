<?php /* Smarty version 2.6.18, created on 2011-04-06 18:09:11
         compiled from cms_editor.tpl */ ?>

<textarea name="opendixckeditor"><?php echo $this->_tpl_vars['editorvalue']; ?>
</textarea>
<?php echo '
<script type="text/javascript">
				CKEDITOR.replace( \'opendixckeditor\',{ 
					filebrowserBrowseUrl : \'/browser/browse.php\',
			        filebrowserUploadUrl : \'/uploader/upload.php\'
			        } );
			</script>
'; ?>


