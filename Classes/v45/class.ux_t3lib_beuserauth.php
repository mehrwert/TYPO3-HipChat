<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
 *  (c) 2014 mehrwert <typo3@mehrwert.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * XCLASS for T3lib BE User Auth for tx_hipchat Extension
 *
 * @author		mehrwert <typo3@mehrwert.de>
 * @package		TYPO3
 * @subpackage	tx_hipchat
 * @license		GPL
 */
class ux_t3lib_beUserAuth extends t3lib_beUserAuth {

	/**
	 * Create a HipChat notification and send to HipChat API
	 *
	 * @return void
	 */
	public function hipChatLoginNotification() {
		/** @var Tx_HipChat $hipChat */
		$hipChat = t3lib_div::makeInstance('Tx_HipChat');
		$hipChat->hipChatNotification(
			$this->loginSessionStarted,
			$this->user['username'],
			$this->loginFailure,
			$this->formfield_uname
		);
	}

	/**
	 * Compile the list of erroneous logins and send to HipChat API
	 *
	 * @param $message
	 * @return void
	 */
	public function hipChatLoginFailureNotification($message) {
		/** @var Tx_HipChat $hipChat */
		$hipChat = t3lib_div::makeInstance('Tx_HipChat');
		$hipChat->hipChatLoginFailureNotification(
			$message
		);
	}

	/**
	 * Check if user is logged in and if so, call ->fetchGroupData() to load
	 * group information and access lists of all kind, further check IP, set
	 * the ->uc array and send login-notification email if required.
	 * If no user is logged in the default behaviour is to exit with an error
	 * message, but this will happen ONLY if the constant TYPO3_PROCEED_IF_NO_USER
	 * is set TRUE. This function is called right after ->start() in fx. init.php
	 *
	 * @return void
	 * @throws RuntimeException
	 * @throws RuntimeException
	 */
	public function backendCheckLogin() {

		$message = '';

		if (!$this->user['uid']) {
			if ($this->loginFailure) {
				$this->hipChatLoginFailureNotification($message);
			}
			if (!defined('TYPO3_PROCEED_IF_NO_USER') || !TYPO3_PROCEED_IF_NO_USER) {
				t3lib_utility_Http::redirect($GLOBALS['BACK_PATH']);
			}
			// ...and if that's the case, call these functions
		} else {
			// The groups are fetched and ready for permission checking in this
			// initialization. Tables.php must be read before this because stuff
			// like the modules has impact in this
			$this->fetchGroupData();
			if ($this->checkLockToIP()) {
				if ($this->isUserAllowedToLogin()) {
					// Setting the UC array. It's needed with fetchGroupData first,
					// due to default/overriding of values.
					$this->backendSetUC();
					// email at login - if option set.
					$this->emailAtLogin();
					// HipChat Notification
					$this->hipChatLoginNotification();
				} else {
					throw new RuntimeException(
						'Login Error: TYPO3 is in maintenance mode at the moment. Only administrators are allowed access.',
						1294585860
					);
				}
			} else {
				throw new RuntimeException(
					'Login Error: IP locking prevented you from being authorized. Can\'t proceed, sorry.',
					1294585861
				);
			}
		}
	}

	/**
	 * Sends a warning to $email if there has been a certain amount of failed
	 * logins during a period. If a login fails, this function is called. It
	 * will look up the sys_log to see if there has been more than $max failed
	 * logins the last $secondsBack seconds (default 3600). If so, an email
	 * with a warning is sent to $email.
	 *
	 * @param String $email Email address
	 * @param Integer $secondsBack	Number of sections back in time to check.
	 * 								This is a kind of limit for how many failures
	 * 								an hour for instance.
	 * @param Integer $max Max allowed failures before a warning mail is sent
	 * @return	void
	 */
	public function checkLogFailures($email, $secondsBack = 3600, $max = 3) {

		$hipChatMsg = '';

		// get last flag set in the log for sending
		$theTimeBack = $GLOBALS['EXEC_TIME'] - $secondsBack;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tstamp',
			'sys_log',
			'type=255 AND action=4 AND tstamp>' . intval($theTimeBack),
			'',
			'tstamp DESC',
			'1'
		);
		if ($testRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$theTimeBack = $testRow['tstamp'];
		}

		// Check for more than $max number of error failures with the last period.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'sys_log',
			'type=255 AND action=3 AND error<>0 AND tstamp>' . intval($theTimeBack),
			'',
			'tstamp'
		);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > $max) {
			// OK, so there were more than the max allowed number of login
			// failures - so we will send an email then.
			$subject = 'TYPO3 Login Failure Warning (at ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] . ')';
			$emailBody = 'There have been some attempts (' . $GLOBALS['TYPO3_DB']->sql_num_rows($res) .
				') to login at the TYPO3 site "' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] . '" (' .
				t3lib_div::getIndpEnv('HTTP_HOST') . ').' . LF .
				'This is a dump of the failures:' .  LF;
			$hipChatMsg = nl2br($emailBody);
			$hipChatMsg .= '<ul>';

			while ($testRows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$theData = unserialize($testRows['log_data']);
				$emailBody .= date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' .
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $testRows['tstamp']) . ':  ' .
					@sprintf($testRows['details'], '' . $theData[0], '' . $theData[1], '' . $theData[2]);
				$emailBody .= LF;

				$hipChatMsg .= '<li>' . date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' .
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $testRows['tstamp']) . ':  ' .
					@sprintf($testRows['details'], '' . $theData[0], '' . $theData[1], '' . $theData[2]) . '</li>';
			}
			$hipChatMsg .= '</ul>';

			if ($email) {
				$from = t3lib_utility_Mail::getSystemFrom();
				/** @var $mail t3lib_mail_Message */
				$mail = t3lib_div::makeInstance('t3lib_mail_Message');
				$mail->setTo($email)
					->setFrom($from)
					->setSubject($subject)
					->setBody($emailBody);
				$mail->send();
				// Logout written to log
				$this->writelog(
					255,
					4,
					0,
					3,
					'Failure warning (%s failures within %s seconds) sent by email to %s',
					array(
						$GLOBALS['TYPO3_DB']->sql_num_rows($res),
						$secondsBack,
						$email
					)
				);
			}
		}

		if ( trim($hipChatMsg) != '' ) {
			$this->hipChatLoginFailureNotification($hipChatMsg);
		}

	}
}

?>