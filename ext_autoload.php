<?php

$extensionPath = t3lib_extMgm::extPath( 'hipchat' );

return array(
	'tx_hipchat' => $extensionPath . 'Classes/HipChat.php',
	'mehrwert\HipChat\Xclasses\BackendUserAuthentication' => $extensionPath . 'Classes/Xclasses/BackendUserAuthentication.php',
);

?>