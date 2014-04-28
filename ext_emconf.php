<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Connector for Atlassian HipChat',
	'description' => 'Using the HipChat API',
	'category' => 'Services',
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
	'createDirs' => 'typo3temp/tx_hipchat/',
	'lockType' => '',
	'version' => '1.0.1-dev',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
			'php' => '5.3.0-0.0.0'
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