<?php

/**
 * Extension configuration
 *
 * @author		mehrwert <typo3@mehrwert.de>
 * @package		TYPO3
 * @subpackage	tx_hipchat
 * @license		GPL
 */

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$_EXTCONF = unserialize($_EXTCONF);

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
	t3lib_extMgm::extPath($_EXTKEY, 'Classes/class.ux_t3lib_beuserauth.php');

?>