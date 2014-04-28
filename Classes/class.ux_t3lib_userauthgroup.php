<?php

class ux_t3lib_userAuthGroup extends t3lib_userAuthGroup {

	/**
	 * @return void
	 */
	public function hipChatNotification($msg) {

		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['hipchat']);
		/** @var Tx_HipChat $hipChat */
		$hipChat = t3lib_div::makeInstance(
			'Tx_HipChat',
			trim($extConf['hipChatDefaultToken'])
		);

		if ( $hipChat->message_room(
			trim($extConf['hipChatDefaulRoomName']),
			trim($extConf['hipChatDefaulFromName']),
			$msg,
			TRUE,
			$hipChat::COLOR_RED,
			$hipChat::FORMAT_HTML
		)
		) {
		} else {
			throw new RuntimeException(
				'Could not send notification to HipChat Room ' . trim($extConf['hipChatDefaulRoomName']),
				1398703010
			);
		}
	}

	/**
	 * Sends a warning to $email if there has been a certain amount of failed logins during a period.
	 * If a login fails, this function is called. It will look up the sys_log to see if there has been more than $max failed logins the last $secondsBack seconds (default 3600). If so, an email with a warning is sent to $email.
	 *
	 * @param	string		Email address
	 * @param	integer		Number of sections back in time to check. This is a kind of limit for how many failures an hour for instance.
	 * @param	integer		Max allowed failures before a warning mail is sent
	 * @return	void
	 * @access private
	 */
	public function checkLogFailures($email, $secondsBack = 3600, $max = 3) {

		if ($email) {

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
					// OK, so there were more than the max allowed number of login failures - so we will send an email then.
				$subject = 'TYPO3 Login Failure Warning (at ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] . ')';
				$email_body = 'There have been some attempts (' . $GLOBALS['TYPO3_DB']->sql_num_rows($res) . ') to login at the TYPO3
site "' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] . '" (' . t3lib_div::getIndpEnv('HTTP_HOST') . ').

This is a dump of the failures:

';
				while ($testRows = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$theData = unserialize($testRows['log_data']);
					$email_body .= date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $testRows['tstamp']) . ':  ' . @sprintf($testRows['details'], '' . $theData[0], '' . $theData[1], '' . $theData[2]);
					$email_body .= LF;
				}
				$from = t3lib_utility_Mail::getSystemFrom();
				/** @var $mail t3lib_mail_Message */
				$mail = t3lib_div::makeInstance('t3lib_mail_Message');
				$mail->setTo($email)
						->setFrom($from)
						->setSubject($subject)
						->setBody($email_body);
				$mail->send();
				$this->writelog(255, 4, 0, 3, 'Failure warning (%s failures within %s seconds) sent by email to %s', array($GLOBALS['TYPO3_DB']->sql_num_rows($res), $secondsBack, $email)); // Logout written to log
			}
		}
		$this->hipChatNotification($email_body);
die('HIER');

	}
}

?>