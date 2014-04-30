<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 - 2014 Garret Heaton
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
 ****************************************************************/

/**
 * Library for interacting with the HipChat REST API.
 *
 * @see http://api.hipchat.com/docs/api
 * @see https://github.com/hipchat/hipchat-php
 */
class Tx_HipChat {

	/**
	 * HTTP response codes from API
	 *
	 * @see http://api.hipchat.com/docs/api/response_codes
	 * -1 = Not an HTTP response code
	 */
	const STATUS_BAD_RESPONSE = -1;
	const STATUS_OK = 200;
	const STATUS_BAD_REQUEST = 400;
	const STATUS_UNAUTHORIZED = 401;
	const STATUS_FORBIDDEN = 403;
	const STATUS_NOT_FOUND = 404;
	const STATUS_NOT_ACCEPTABLE = 406;
	const STATUS_INTERNAL_SERVER_ERROR = 500;
	const STATUS_SERVICE_UNAVAILABLE = 503;

	/**
	 * Colors for rooms/message
	 */
	const COLOR_YELLOW = 'yellow';
	const COLOR_RED = 'red';
	const COLOR_GRAY = 'gray';
	const COLOR_GREEN = 'green';
	const COLOR_PURPLE = 'purple';
	const COLOR_RANDOM = 'random';

	/**
	 * Formats for rooms/message
	 */
	const FORMAT_HTML = 'html';
	const FORMAT_TEXT = 'text';

	/**
	 * API versions
	 */
	const VERSION_1 = 'v1';

	/**
	 * @var string
	 */
	private $apiTarget = 'https://api.hipchat.com';

	/**
	 * @var String
	 */
	private $authToken;

	/**
	 * @var bool
	 */
	private $verifySsl = TRUE;

	/**
	 * @var
	 */
	private $proxy;

	/**
	 * @var
	 */
	private $extensionConfiguration = array();

	/**
	 * @var bool
	 */
	private $isConfigured = FALSE;

	/**
	 * @var string
	 */
	private $hipChatDefaultRoomName = '';

	/**
	 * @var string
	 */
	private $hipChatDefaultFromName = '';

	/**
	 * Creates a new API interaction object.
	 */
	public function __construct() {
		$this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['hipchat']);
		$this->apiTarget = trim($this->extensionConfiguration['hipChatApiEndpoint']);
		$this->authToken = trim($this->extensionConfiguration['hipChatDefaultApiToken']);
		$this->apiVersion = trim($this->extensionConfiguration['hipChatApiVersion']);
		$this->hipChatDefaultRoomName = trim($this->extensionConfiguration['hipChatDefaultRoomName']);
		$this->hipChatDefaultFromName = trim($this->extensionConfiguration['hipChatDefaultFromName']);
		$this->checkConfiguration();
	}

	/**
	 * Check required params
	 * @return void
	 */
	private function checkConfiguration() {
		if ( t3lib_div::isValidUrl($this->apiTarget) === TRUE
			&& trim($this->authToken) != ''
				&& $this->apiVersion != ''
					&& $this->hipChatDefaultRoomName != ''
						&& $this->hipChatDefaultFromName != '') {
			$this->isConfigured = TRUE;
		}
	}

	/**
	 * @param String $message The message to post to HipChat
	 * @return void
	 */
	public function hipChatLoginFailureNotification($message) {
		try {
			if (!$this->messageRoom(
				$this->hipChatDefaultRoomName,
				$this->hipChatDefaultFromName,
				$message,
				TRUE,
				self::COLOR_RED,
				self::FORMAT_HTML
			)
			) {
				throw new RuntimeException(
					'Could not send notification to HipChat Room ' . $this->hipChatDefaultRoomName,
					1398703010
				);
			}
		} catch (RuntimeException $e) {
			t3lib_div::sysLog('HipChat API error: ' . $e->getMessage(), 'hipchat', 3);
		}
	}

	/**
	 * @param Boolean $loginSessionStarted
	 * @param String $userName
	 * @param Boolean $loginFailure
	 * @param String $formFieldUsername
	 * @throws Exception
	 * @throws Tx_HipChat_Exception
	 * @return void
	 */
	public function hipChatNotification($loginSessionStarted, $userName, $loginFailure, $formFieldUsername) {

		try {
			if ($loginSessionStarted) {
				$msg = sprintf('User "%s" logged in from %s (%s) at "%s" (%s)',
					$userName,
					t3lib_div::getIndpEnv('REMOTE_ADDR'),
					t3lib_div::getIndpEnv('REMOTE_HOST'),
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
					t3lib_div::getIndpEnv('HTTP_HOST')
				);
				if ( self::VERSION_1 == 'v1' ) {
					if (!$this->messageRoom(
							$this->hipChatDefaultRoomName,
							$this->hipChatDefaultFromName,
							$msg,
							TRUE,
							self::COLOR_GREEN,
							self::FORMAT_HTML
						)
					) {
						throw new RuntimeException(
							'Could not send notification to HipChat Room ' . $this->hipChatDefaultRoomName,
							1398703010
						);
					}
				} else {
					if ($this->roomExists($this->hipChatDefaultRoomName) === TRUE) {
						if (!$this->messageRoom(
								$this->hipChatDefaultRoomName,
								$this->hipChatDefaultFromName,
								$msg,
								TRUE,
								self::COLOR_GREEN,
								self::FORMAT_HTML
							)
						) {
							throw new RuntimeException(
								'Could not send notification to HipChat Room ' . $this->hipChatDefaultRoomName,
								1398703010
							);
						}
					} else {
						throw new RuntimeException(
							'HipChat Room ' . $this->hipChatDefaultRoomName .
							' Does not exist. Can\'t proceed, sorry.',
							1398703000
						);
					}
				}
			} elseif ($loginFailure) {
				$msg = sprintf('Failed user login for "%s" from %s (%s) at "%s" (%s)',
					t3lib_div::_GP($formFieldUsername),
					t3lib_div::getIndpEnv('REMOTE_ADDR'),
					t3lib_div::getIndpEnv('REMOTE_HOST'),
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
					t3lib_div::getIndpEnv('HTTP_HOST')
				);
				if ( !$this->messageRoom(
						$this->hipChatDefaultRoomName,
						$this->hipChatDefaultFromName,
						$msg,
						TRUE,
						self::COLOR_YELLOW,
						self::FORMAT_HTML
					)
				) {
					throw new RuntimeException(
						'Could not send notification to HipChat Room ' . $this->hipChatDefaultRoomName,
						1398703010
					);
				}
			}
		} catch (RuntimeException $e) {
			t3lib_div::sysLog('HipChat API error: ' . $e->getMessage(), 'hipchat', 3);
		}
	}

	/////////////////////////////////////////////////////////////////////////////
	// Room functions
	/////////////////////////////////////////////////////////////////////////////

	/**
	 * Get information about a room
	 *
	 * @see http://api.hipchat.com/docs/api/method/rooms/show
	 */
	public function getRoom($roomId) {
		$response = $this->makeRequest(
			'rooms/show',
			array(
				'room_id' => $roomId
			)
		);
		return $response->room;
	}

	/**
	 * Determine if the given room name or room id already exists.
	 *
	 * @param mixed $roomId
	 * @return boolean
	 * @throws Tx_HipChat_Exception
	 */
	public function roomExists($roomId) {
		try {
			$this->getRoom($roomId);
		} catch (Tx_HipChat_Exception $e) {
			if ($e->code === self::STATUS_NOT_FOUND) {
				return FALSE;
			}
			throw $e;
		}
		return TRUE;
	}

	/**
	 * Get list of rooms
	 *
	 * @see http://api.hipchat.com/docs/api/method/rooms/list
	 */
	public function getRooms() {
		$response = $this->makeRequest('rooms/list');
		return $response->rooms;
	}

	/**
	 * Send a message to a room
	 *
	 * @param String $roomId
	 * @param String $from
	 * @param String $message
	 * @param bool $notify
	 * @param string $color
	 * @param string $messageFormat
	 * @return bool
	 * @throws Tx_HipChat_Exception
	 * @see http://api.hipchat.com/docs/api/method/rooms/message
	 */
	public function messageRoom($roomId, $from, $message, $notify = FALSE,
								$color = self::COLOR_YELLOW,
								$messageFormat = self::FORMAT_HTML) {
		$args = array(
			'room_id' => $roomId,
			'from' => $from,
			'message' => utf8_encode($message),
			'notify' => (int)$notify,
			'color' => $color,
			'message_format' => $messageFormat
		);
		$response = $this->makeRequest('rooms/message', $args, 'POST');
		return ($response->status == 'sent');
	}

	/**
	 * Get chat history for a room
	 *
	 * @param $roomId
	 * @param string $date
	 * @return mixed
	 * @throws Tx_HipChat_Exception
	 * @see https://www.hipchat.com/docs/api/method/rooms/history
	 */
	public function getRoomsHistory($roomId, $date = 'recent') {
		$response = $this->makeRequest(
			'rooms/history',
			array(
				'room_id' => $roomId,
				'date' => $date
			)
		);
		return $response->messages;
	}

	/**
	 * Set a room's topic
	 *
	 * @param $roomId
	 * @param $topic
	 * @param null $from
	 * @return bool
	 * @throws Tx_HipChat_Exception
	 * @see http://api.hipchat.com/docs/api/method/rooms/topic
	 */
	public function setRoomTopic($roomId, $topic, $from = NULL) {
		$args = array(
			'room_id' => $roomId,
			'topic' => utf8_encode($topic),
		);

		if ($from) {
			$args['from'] = utf8_encode($from);
		}

		$response = $this->makeRequest('rooms/topic', $args, 'POST');
		return ($response->status == 'ok');
	}

	/**
	 * Create a room
	 *
	 * @param $name
	 * @param null $ownerUserId
	 * @param null $privacy
	 * @param null $topic
	 * @param null $guestAccess
	 * @return mixed
	 * @throws Tx_HipChat_Exception
	 * @see http://api.hipchat.com/docs/api/method/rooms/create
	 */
	public function createRoom($name, $ownerUserId = NULL, $privacy = NULL, $topic = NULL, $guestAccess = NULL) {

		$args = array(
			'name' => $name
		);

		if ($ownerUserId) {
			$args['owner_user_id'] = $ownerUserId;
		}

		if ($privacy) {
			$args['privacy'] = $privacy;
		}

		if ($topic) {
			$args['topic'] = utf8_encode($topic);
		}

		if ($guestAccess) {
			$args['guest_access'] = (int) $guestAccess;
		}

		// Return the std object
		return $this->makeRequest('rooms/create', $args, 'POST');
	}

	/**
	 * Delete a room
	 *
	 * @param String $roomId
	 * @return Boolean
	 * @see http://api.hipchat.com/docs/api/method/rooms/delete
	 */
	public function deleteRoom($roomId) {
		$args = array(
			'room_id' => $roomId
		);

		$response = $this->makeRequest('rooms/delete', $args, 'POST');

		return ($response->deleted == 'true');
	}

	/////////////////////////////////////////////////////////////////////////////
	// User functions
	/////////////////////////////////////////////////////////////////////////////

	/**
	 * Get information about a user
	 *
	 * @param String $userId
	 * @see http://api.hipchat.com/docs/api/method/users/show
	 */
	public function getUser($userId) {
		$response = $this->makeRequest('users/show', array(
			'user_id' => $userId
		));
		return $response->user;
	}

	/**
	 * Get list of users
	 *
	 * @see http://api.hipchat.com/docs/api/method/users/list
	 */
	public function getUsers() {
		$response = $this->makeRequest('users/list');
		return $response->users;
	}


	/////////////////////////////////////////////////////////////////////////////
	// Helper functions
	/////////////////////////////////////////////////////////////////////////////

	/**
	 * Performs a curl request
	 *
	 * @param String $url URL to hit.
	 * @param Array $postData Data to send via POST. Leave null for GET request.
	 * @throws Tx_HipChat_Exception
	 * @return string
	 */
	public function curlRequest($url, $postData = NULL) {

		if (is_array($postData)) {
			$postData = array_map(array($this, 'sanitizeCurlParameter'), $postData);
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
		if (isset($this->proxy)) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		}
		if (is_array($postData)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		$response = curl_exec($ch);

		// make sure we got a real response
		if (strlen($response) == 0) {
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			throw new Tx_HipChat_Exception(self::STATUS_BAD_RESPONSE,
				'CURL error: ' . $errno . '-' . $error, NULL, $url);
		}

		// make sure we got a 200
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code != self::STATUS_OK) {
			throw new Tx_HipChat_Exception($code,
				'HTTP status code: ' . $code . ', response=' . $response, NULL, $url);
		}

		curl_close($ch);

		return $response;
	}

	/**
	 * Sanitizes the given value as cURL parameter.
	 *
	 * The first value may not be a "@". PHP would treat this as a file upload
	 *
	 * @link http://www.php.net/manual/en/function.curl-setopt.php CURLOPT_POSTFIELDS
	 *
	 * @param string $value
	 * @return string
	 */
	private function sanitizeCurlParameter($value) {

		if ((strlen($value) > 0) && ($value[0] === '@')) {
			return substr_replace($value, '&#64;', 0, 1);
		}

		return $value;
	}

	/**
	 * Make an API request using curl
	 *
	 * @param string $apiMethod  Which API method to hit, like 'rooms/show'.
	 * @param array  $args        Data to send.
	 * @param string $httpMethod HTTP method (GET or POST).
	 *
	 * @throws Tx_HipChat_Exception
	 * @return mixed
	 */
	public function makeRequest($apiMethod, $args = array(), $httpMethod = 'GET') {
		$response = FALSE;
		if ($this->isConfigured === TRUE) {
			$args['format'] = 'json';
			$args['auth_token'] = $this->authToken;
			$url = $this->apiTarget . '/' . $this->apiVersion . '/' . $apiMethod;
			$postData = NULL;

			// add args to url for GET
			if ($httpMethod == 'GET') {
				$url .= '?' . http_build_query($args);
			} else {
				$postData = $args;
			}

			try {
				$response = $this->curlRequest($url, $postData);

				// make sure response is valid json
				$response = json_decode($response);
				if (!$response) {
					throw new Tx_HipChat_Exception(
						self::STATUS_BAD_RESPONSE,
						'Invalid JSON received: ' . $response,
						NULL,
						$url
					);
				}
			} catch (Tx_HipChat_Exception $e) {
				t3lib_div::sysLog('HipChat API error: ' . $e->getMessage(), 'hipchat', 3);
			}
		}
		return $response;
	}

	/**
	 * Enable/disable verifySsl.
	 * This is useful when curl spits back ssl verification errors, most likely
	 * due to outdated SSL CA bundle file on server. If you are able to, update
	 * that CA bundle. If not, call this method with false for $bool param before
	 * interacting with the API.
	 *
	 * @param bool $bool
	 * @return bool
	 * @link http://davidwalsh.name/php-ssl-curl-error
	 */
	public function setVerifySsl($bool = TRUE) {
		$this->verifySsl = (bool)$bool;
		return $this->verifySsl;
	}

	/**
	 * Set an outbound proxy to use as a curl option
	 * To disable proxy usage, set $proxy to null
	 *
	 * @param string $proxy
	 * @return void
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

}

/**
 * Class Tx_HipChat_Exception
 */
class Tx_HipChat_Exception extends \Exception {

	/**
	 * @var
	 */
	public $code;

	/**
	 * @param string $code
	 * @param int $info
	 * @param Exception $exception
	 * @param String $url
	 */
	public function __construct($code, $info, $exception, $url) {
		$message = 'HipChat API error: code=' . $code . ', info=' . $info . ', url=' . $url;
		parent::__construct($message, (int)$code);
	}
}

?>