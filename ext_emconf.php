<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hipchat".
 *
 * Auto generated 02-05-2014 15:34
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
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'createDirs' => 'typo3temp/tx_hipchat/',
	'lockType' => '',
	'version' => '1.1.0',
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
	'_md5_values_when_last_written' => 'a:12:{s:13:"ChangeLog.txt";s:4:"6715";s:16:"ext_autoload.php";s:4:"f5c0";s:21:"ext_conf_template.txt";s:4:"921d";s:12:"ext_icon.gif";s:4:"a690";s:17:"ext_localconf.php";s:4:"d118";s:14:"ext_tables.php";s:4:"2643";s:14:"ext_tables.sql";s:4:"d55b";s:9:"ReadMe.md";s:4:"f871";s:10:"ReadMe.rst";s:4:"e4d1";s:19:"Classes/HipChat.php";s:4:"1059";s:41:"Classes/v45/class.ux_t3lib_beuserauth.php";s:4:"95dc";s:41:"Classes/v47/class.ux_t3lib_beuserauth.php";s:4:"7c90";}',
);

?>