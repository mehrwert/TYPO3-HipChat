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
);

?>