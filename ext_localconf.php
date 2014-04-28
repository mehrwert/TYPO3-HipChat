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

// Include classes for TYPO3 4.7 or 6.2
if ( t3lib_div::compat_version('4.7') ) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
		t3lib_extMgm::extPath($_EXTKEY, 'Classes/v47/class.ux_t3lib_beuserauth.php');
}
/* elseif ( \TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('6.2') ) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\ContentObject\\FluidTemplateContentObject'] = array(
		'className' => 'Enet\\FxLibrary\\Xclass\\FluidTemplateContentObject',
	);
}
*/

?>