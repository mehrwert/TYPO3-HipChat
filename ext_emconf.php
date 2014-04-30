<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Atlassian HipChat Notification & API Services',
	'description' => 'Send login or login error notifications from TYPO3 to HipCHat and provide a collection of HipChat API methods.',
	'category' => 'Services',
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
	'version' => '1.0.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-4.7.99',
			'php' => '5.2.0-5.4.99'
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