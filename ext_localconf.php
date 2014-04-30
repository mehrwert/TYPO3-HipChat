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

// Include classes for TYPO3 4.5
if ( t3lib_div::compat_version('4.5') ) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
		t3lib_extMgm::extPath($_EXTKEY, 'Classes/v45/class.ux_t3lib_beuserauth.php');
}

// Include classes for TYPO3 4.7
if ( t3lib_div::compat_version('4.7') ) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
		t3lib_extMgm::extPath($_EXTKEY, 'Classes/v47/class.ux_t3lib_beuserauth.php');
}

?>