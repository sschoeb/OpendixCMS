<?php

/**
 * loader.php ist fÃ¼r das Laden sÃ¤mtlicher benÃ¶tigten Klassen verantwortlich
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.1
 */

/**
 * Speichert die Pfade zu sämtlichen, vom System vorgegebenen Klassen
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class ClassList
{
	public static function load()
	{
		//systemklassen
		$list['core'] = array('Blaettern'=> 'system/core/class.Blaettern.php',
		'Auth'=> 'system/core/class.Auth.php',
		'Functions'=> 'system/core/class.Functions.php',
		'Session'=> 'system/core/class.Session.php',
		'Scroller'=> 'system/core/class.Scroller.php',
		'MySmarty'=> 'system/core/class.MySmarty.php',
		'ZipFolder'=> 'system/core/class.ZipFolder.php',
		'Timer'=> 'system/core/class.Timer.php',
		'Watermark'=> 'system/core/class.Watermark.php',
		'Logfile'=> 'system/core/class.Logfile.php',
		'Filebase'=> 'system/core/class.Filebase.php',
		'FileManager'=> 'system/core/class.FileManager.php',
		'Captcha'=> 'system/core/class.Captcha.php',
		'JsImport'=> 'system/core/class.JsImport.php',
		'SqlManager'=> 'system/core/class.SqlManager.php',
		'SqlManagerException'=> 'system/core/class.SqlManager.php',
		'Smarty' => 'system/core/smarty/Smarty.class.php', 
		'Cms' => 'system/core/class.Cms.php', 
		'CMSException' => 'system/core/class.CMSException.php', 
		'Module' => 'system/core/class.Module.php', 
		'Template' => 'system/core/class.Template.php', 
		'User' => 'system/core/class.User.php', 
		'Configuration' => 'system/core/class.Configuration.php', 
		'Law' => 'system/core/class.Law.php', 
		'FormParser' => 'system/core/class.FormParser.php',
		'Validator' => 'system/core/class.Validator.php',
		'Trash'=> 'system/core/class.Trash.php',
		'TrashItem'=> 'system/core/class.TrashItem.php',
		'TrashRecord'=> 'system/core/class.TrashRecord.php',
		'TrashFile'=> 'system/core/class.TrashFile.php',
		'AJAX'=> 'system/core/class.AJAX.php',
		'ModulBase'=> 'system/core/class.ModulBase.php',
		'Action'=> 'system/core/class.Action.php',
		'OpenModulBase'=> 'system/core/class.OpenModulBase.php',
		'HTML'=> 'system/core/class.HTML.php',
		'EXCEL'=> 'system/core/class.EXCEL.php',
		'XML' => 'system/core/class.XML.php',
		'CssImport' => 'system/core/class.CssImport.php',
		'Editor' => 'system/core/class.Editor.php',
		'CKEditor' => 'system/wysiwyg/ckeditor.php',
		'Menue' => 'system/core/class.Menue.php', 
		'Image' => 'system/core/Image/class.Image.php',
		'ImageFormat' => 'system/core/Image/class.ImageFormat.php',
		'EnumBase' => 'system/core/class.EnumBase.php',
		'TimerModulBase' => 'system/core/class.TimerModulBase.php', 
		'SimpleAdminModulBase' => 'system/core/class.SimpleAdminModulBase.php'
		);

		//Standardklassen
		$list['standard'] = array('ModRewriteRuleEditor'=> 'system/components/class.ModRewriteRuleEditor.php',
		'Config'=> 'system/components/class.Config.php',
		'Konfiguration'=> 'system/components/class.Konfiguration.php',
		'Inhaltmenue'=> 'system/components/class.Inhaltmenue.php',
		'Myprofil'=> 'system/components/class.MyProfil.php',
		'MenueAdmin' => 'system/components/class.MenueAdmin.php',
		'Profil'=> 'system/components/class.Profil.php');

		//PEAR-Klassen
		$list['pear'] = array('MIME_Type' => 'system/pear/pear.MIME_Type.php',
		'MIME_Type_Parameter' => 'system/pear/pear.MIME_Type_Parameter.php',
		'Mail_mimePart' => 'system/pear/pear.Mail_mimePart.php',
		'HTTP_Request_Listener ' => 'system/pear/pear.HTTP_Request_Listener .php',
		'Mail_Mime' => 'system/pear/pear.Mail_mime.php',
		'Mail_mime' => 'system/pear/pear.Mail_mime.php',
		'XML_Serializer' => 'system/pear/pear.XML_Serializer.php',
		'XML_Unserializer' => 'system/pear/pear.XML_Unserializer.php',
		'Mail' => 'system/pear/pear.Mail.php',
		'Spreadsheet_Excel_Writer' => 'system/pear/pear.EXCELWriter.php',
		'Spreadsheet_Excel_Writer_Workbook' => 'system/pear/pear.EXCELWorkbook.php',
		'Spreadsheet_Excel_Writer_Worksheet' => 'system/pear/pear.EXCELWorksheet.php',
		'Spreadsheet_Excel_Writer_BIFFwriter' => 'system/pear/pear.EXCELBIFFwriter.php',
		'Spreadsheet_Excel_Writer_Parser' => 'system/pear/pear.EXCELParser.php',
		'Spreadsheet_Excel_Writer_Format' => 'system/pear/pear.EXCELFormat.php',
		'OLE_PPS_File' => 'system/pear/pear.OLEFile.php',
		'OLE_PPS' => 'system/pear/pear.OLEPPS.php',
		'OLE_PPS_Root' => 'system/pear/pear.OLERoot.php',
		'OLE' => 'system/pear/pear.OLE.php',
		'HTTP' => 'system/pear/pear.HTTP.php',
		'HTTP_Header' => 'system/pear/pear.HTTP_Header.php',
		'HTTP_Header_Cache' => 'system/pear/pear.HTTP_Header_Cache.php',
		'HTTP_Request' => 'system/pear/pear.HTTP_Request.php',
		'HTTP_Response' => 'system/pear/pear.HTTP_Request.php',
		'HTTP_Download_PgLOB' => 'system/pear/pear.HTTP_Download_PgLOB.php',
		'Mail_mail' => 'system/pear/pear.Mail_mail.php',
		'XML_Util' => 'system/pear/pear.XML_Util.php',
		'HTTP_Upload' => 'system/pear/pear.HTTP_Upload.php',
		'HTTP_Upload_Error' => 'system/pear/pear.HTTP_Upload.php',
		'HTTP_Upload_File' => 'system/pear/pear.HTTP_Upload.php',
		'HTTP_Client_CookieManager' => 'system/pear/pear.HTTP_Client_CookieManager.php',
		'HTTP_Download' => 'system/pear/pear.HTTP_Download.php',
		'HTTP_Download_Archive' => 'system/pear/pear.HTTP_Download_Archive.php',
		'pear' => 'system/pear/PEAR.php',
		'PEAR' => 'system/pear/PEAR.php',
		'pear_Error' => 'system/pear/pear.pear.php',
		'Console_Getopt' => 'system/pear/pear.Getopt.php',
		'system' => 'system/pear/pear.System.php',
		'System' => 'system/pear/pear.System.php',
		'system_Command' => 'system/pear/pear.Command.php',
		'System_Command' => 'system/pear/pear.Command.php',
		'HTTP_Client' => 'system/pear/pear.HTTP_Client.php',
		'Mail_smtp' => 'system/pear/pear.mail.smtp.php',
		'Net_SMTP'=>'system/pear/pear.net.SMTP.php',
		'Auth_SASL' => 'system/pear/pear.SASL.php',
		'Net_Socket' => 'system/pear/pear.Socket.php',
		'Zip' => 'system/pear/pear.Zip.php',
		'Mail_RFC822' => 'system/pear/pear.RFC822.php'
		);

		$list['interface'] = array('standardLaw' => 'system/core/ifc.standardLaw.php');
		
		return  $list;
	}
}


/**
 * Magische Funktion Autoload
 *
 * @param String $classname Gesuchte Klasse
 */
function __autoload($classname)
{
	static $classListCore;
	static $classListPear;
	static $classListStandard;
	static $classListInterface;

	if($classListCore == '')
	{
		$list = ClassList::load();
		$classListCore = $list['core'];
		$classListPear = $list['pear'];
		$classListStandard = $list['standard'];
		$classListInterface = $list['interface'];
	}

	//PrÃ¼fen ob die Klasse in der Core erfasst ist (Hier sind nur die systemeigenen Klassen erfasst)
	if(isset($classListCore[$classname]))
	{
		include_once($classListCore[$classname]);
		return;
	}

	//PrÃ¼fen ob die Klasse in der Standard erfasst ist (Hier sind nur die systemeigenen Klassen erfasst)
	if(isset($classListStandard[$classname]))
	{
		include_once($classListStandard[$classname]);
		return;
	}

	//PrÃ¼fen ob die Klasse in der PEAR erfasst ist (Hier sind nur die systemeigenen Klassen erfasst)
	if(isset($classListPear[$classname]))
	{
		include_once($classListPear[$classname]);
		return;
	}
	

	//PrÃ¼fen ob es sich um ein Interface handelt
	if(isset($classListInterface[$classname]))
	{
		include_once($classListInterface[$classname]);
		return;
	}


	//Ansonsten noch die MÃ¶glichkeit, dass die Klase in der Datenbank registriert ist
	$query = "SELECT s.name, file, moduleId FROM sysclasses c, sysmodul s WHERE c.name ='$classname' AND s.id = moduleId LIMIT 1";
	$insert = mysql_query($query) OR die('FATAL ERROR!');
	if(mysql_num_rows($insert) == 0)
	{
		die('FATAL ERROR - KLASSE NICHT GEFUNDEN ' . $classname);
	}

	$libPath = Cms::GetInstance() -> GetConfiguration() -> __get('Path/moduleFolder');
	$fileInfo = mysql_fetch_assoc($insert);
	//Da es sich um
	if(!file_exists($libPath . $fileInfo['moduleId'] . '_' . strtolower($fileInfo['name']) . '/' . $fileInfo['file']))
	{
		
		die('FILE NOT FOUND: ' . $libPath . $fileInfo['moduleId'] . '_' . strtolower($fileInfo['name']) . '/' . $fileInfo['file']);
	}

	include_once($libPath . $fileInfo['moduleId'] . '_' . strtolower($fileInfo['name']) . '/' . $fileInfo['file']);
}
