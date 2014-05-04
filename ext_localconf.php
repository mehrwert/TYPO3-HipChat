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

// Define the notification transport
define('HIPCHAT_NOTIFICATION_TRANSPORT_EMAIL', 1);
define('HIPCHAT_NOTIFICATION_TRANSPORT_HIPCHAT_AND_EMAIL', 2);
define('HIPCHAT_NOTIFICATION_TRANSPORT_HIPCHAT', 3);

$_EXTCONF = unserialize($_EXTCONF);

// Include classes for TYPO3 4.5
if ( t3lib_div::int_from_ver(TYPO3_version) < 4007000 ) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
		t3lib_extMgm::extPath($_EXTKEY, 'Classes/v45/class.ux_t3lib_beuserauth.php');
// Include classes for TYPO3 4.7
} elseif ( t3lib_div::int_from_ver(TYPO3_version) < 6002000 ) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
		t3lib_extMgm::extPath($_EXTKEY, 'Classes/v47/class.ux_t3lib_beuserauth.php');
// Include classes for TYPO3 6.2
} elseif ( \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 6001999 ) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication'] = array(
		'className' => 'mehrwert\\HipChat\\Xclasses\\BackendUserAuthentication',
	);
}

?>