<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

final class AUser {
	private $user_id;
	private $username;
	private $last_login;
	private $permission = array();

	/**
	 * @param $registry Registry
	 */
	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['user_id'])) {
			$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "users WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];
				$this->last_login = $this->session->data['user_last_login'];
				$this->user_group_id = (int)$user_query->row['user_group_id'];

				$this->db->query("UPDATE " . DB_PREFIX . "users
      			                  SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
      			                  WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

				$user_group_query = $this->db->query("SELECT permission
      			                                      FROM " . DB_PREFIX . "user_groups
      			                                      WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
				if (unserialize($user_group_query->row['permission'])) {
					foreach (unserialize($user_group_query->row['permission']) as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		} else {
			unset($this->session->data['token']);
		}
	}

	public function login($username, $password) {
		$user_query = $this->db->query("SELECT *
    	                                FROM " . DB_PREFIX . "users
    	                                WHERE username = '" . $this->db->escape($username) . "'
    	                                AND password = '" . $this->db->escape(AEncryption::getHash($password)) . "'");

		if ($user_query->num_rows) {
			$this->session->data['user_id'] = $user_query->row['user_id'];
			$this->session->data['user_last_login'] = $user_query->row['last_login'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];
			$this->last_login = $user_query->row['last_login'];
			if ($this->last_login == '0000-00-00 00:00:00') {
				$this->session->data['user_last_login'] = $this->last_login = '-------';
			}

			$this->db->query("UPDATE " . DB_PREFIX . "users
							  SET last_login = NOW()
							  WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

			$user_group_query = $this->db->query("SELECT permission
      		                                      FROM " . DB_PREFIX . "user_groups
      		                                      WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

			if ($user_group_query->row['permission']) {
				foreach (unserialize($user_group_query->row['permission']) as $key => $value) {
					$this->permission[$key] = $value;
				}
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) {
		//If top_admin allow all permisson. Make sure Top Admin Group is set to ID 1
		if ($this->user_group_id == 1) {
			return TRUE;
		} else if (isset($this->permission[$key])) {
			return $this->permission[$key][$value] == 1 ? true : false;
		} else {
			return FALSE;
		}
	}

	public function canAccess($value) {
		return $this->hasPermission('access', $value);
	}

	public function canModify($value) {
		return $this->hasPermission('modify', $value);
	}

	public function isLoggedWithToken( $token ) {
		if ( (isset($this->session->data['token']) && !isset( $token ))
			|| ( (isset( $token ) && (isset($this->session->data['token']) && ( $token != $this->session->data['token'])))) ) {
			return FALSE;
		} else {
			return $this->user_id;
		}
	}

	public function isLogged() {
		if (IS_ADMIN && $this->request->get['token'] != $this->session->data['token']) {
			return false;
		}
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}

	public function getLastLogin() {
		return $this->last_login;
	}

	public function validate($username, $email) {
		$user_query = $this->db->query(
			"SELECT * FROM " . DB_PREFIX . "users
			WHERE username = '" . $this->db->escape($username) . "'
				AND email = '" . $this->db->escape($email) . "'");

		if ($user_query->num_rows)
			return true;
		else
			return false;
	}

	static function generatePassword($length = 8) {
		$chars = "1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$i = 0;
		$password = "";
		while ($i <= $length) {
			$password .= $chars{mt_rand(0, strlen($chars))};
			$i++;
		}
		return $password;
	}
}

?>