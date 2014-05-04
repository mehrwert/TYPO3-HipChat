<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hipchat".
 *
 * Auto generated 04-05-2014 14:58
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Atlassian HipChat Notification & API Services',
	'description' => 'Send login or login error notifications from TYPO3 to HipChat and provide a collection of HipChat API methods.',
	'category' => 'services',
	'author' => 'mehrwert',
	'author_company' => 'mehrwert intermediale kommunikation GmbH',
	'author_email' => 'typo3@mehrwert.de',
	'shy' => 0,
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'createDirs' => '',
	'lockType' => '',
	'version' => '1.1.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
			'php' => '5.2.0-5.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:29:{s:12:"ChangeLog.md";s:4:"172e";s:16:"ext_autoload.php";s:4:"a3e7";s:21:"ext_conf_template.txt";s:4:"afee";s:12:"ext_icon.gif";s:4:"a690";s:12:"ext_icon.png";s:4:"395c";s:15:"ext_icon@2x.png";s:4:"6930";s:17:"ext_localconf.php";s:4:"a963";s:14:"ext_tables.php";s:4:"2643";s:14:"ext_tables.sql";s:4:"d55b";s:9:"ReadMe.md";s:4:"6de4";s:19:"Classes/HipChat.php";s:4:"1059";s:46:"Classes/Xclasses/BackendUserAuthentication.php";s:4:"acb5";s:41:"Classes/v45/class.ux_t3lib_beuserauth.php";s:4:"a7ef";s:41:"Classes/v47/class.ux_t3lib_beuserauth.php";s:4:"2db7";s:26:"Documentation/Includes.txt";s:4:"6d5f";s:23:"Documentation/Index.rst";s:4:"79c9";s:26:"Documentation/Settings.yml";s:4:"4090";s:43:"Documentation/AdministratorManual/Index.rst";s:4:"e0e0";s:65:"Documentation/Images/AdministratorManual/HipChatAPIManagement.png";s:4:"1ca9";s:63:"Documentation/Images/AdministratorManual/HipChatCreateToken.png";s:4:"26b6";s:80:"Documentation/Images/AdministratorManual/HipChatExtensionNotificationOptions.png";s:4:"2ba8";s:68:"Documentation/Images/AdministratorManual/HipChatExtensionOptions.png";s:4:"16ff";s:80:"Documentation/Images/AdministratorManual/HipChatScreenshotLoginNotifications.png";s:4:"636d";s:36:"Documentation/Introduction/Index.rst";s:4:"a1aa";s:27:"Documentation/_make/conf.py";s:4:"05a2";s:33:"Documentation/_make/make-html.bat";s:4:"6d1c";s:28:"Documentation/_make/make.bat";s:4:"9464";s:28:"Documentation/_make/Makefile";s:4:"a1d5";s:46:"Documentation/_make/_not_versioned/_.gitignore";s:4:"829c";}',
);

?>