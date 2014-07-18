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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelAccountAddress extends Model {
	public function addAddress($data) {
		//encrypt customer data
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'addresses');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}
		
		$this->db->query("INSERT INTO " . $this->db->table("addresses") . " SET customer_id = '" . (int)$this->customer->getId() . "', company = '" . $this->db->escape($data['company']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'" . $key_sql);
		
		$address_id = $this->db->getLastId();
		
		if (isset($data['default']) && $data['default'] == '1') {
			$this->db->query("UPDATE " . $this->db->table("customers") . " SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
		
		return $address_id;
	}
	
	public function editAddress($address_id, $data) {
		//encrypt customer data
		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'addresses');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}

		$this->db->query("UPDATE " . $this->db->table("addresses") . " SET company = '" . $this->db->escape($data['company']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "'". $key_sql . " WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	
		if (isset($data['default'])) {
			$this->db->query("UPDATE " . $this->db->table("customers") . " SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
	}
	
	public function deleteAddress($address_id) {
		$this->db->query("DELETE FROM " . $this->db->table("addresses") . " WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}	
	
	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . $this->db->table("addresses") . " WHERE address_id = '" . (int)$address_id . "' and customer_id = '" . (int)$this->customer->getId() . "'");
		
		if ($address_query->num_rows) {
			$addr_row = $this->dcrypt->decrypt_data($address_query->row, 'addresses');

			$country_query = $this->db->query("SELECT * FROM `" . $this->db->table("countries") . "` WHERE country_id = '" . (int)$addr_row['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . $this->db->table("zones") . "` WHERE zone_id = '" . (int)$addr_row['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
			
			$address_data = array(
				'firstname'      => $addr_row['firstname'],
				'lastname'       => $addr_row['lastname'],
				'company'        => $addr_row['company'],
				'address_1'      => $addr_row['address_1'],
				'address_2'      => $addr_row['address_2'],
				'postcode'       => $addr_row['postcode'],
				'city'           => $addr_row['city'],
				'zone_id'        => $addr_row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $addr_row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
			
			return $address_data;
		} else {
			return FALSE;	
		}
	}
	
	public function getAddresses() {
		$address_data = array();
		
		$query = $this->db->query("SELECT * FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		foreach ($query->rows as $result) {
			$result = $this->dcrypt->decrypt_data($result, 'addresses');
			$country_query = $this->db->query("SELECT * FROM `" . $this->db->table("countries") . "` WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->db->query("SELECT * FROM `" . $this->db->table("zones") . "` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
		
			$address_data[] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $result['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}		
		
		return $address_data;
	}	
	
	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("addresses") . " WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		return $query->row['total'];
	}
	
	public function validateAddressData( $data ) {
		$error = array();
    	if ((strlen(utf8_decode($data['firstname'])) < 1) || (strlen(utf8_decode($data['firstname'])) > 32)) {
      		$error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((strlen(utf8_decode($data['lastname'])) < 1) || (strlen(utf8_decode($data['lastname'])) > 32)) {
      		$error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((strlen(utf8_decode($data['address_1'])) < 3) || (strlen(utf8_decode($data['address_1'])) > 64)) {
      		$error['address_1'] = $this->language->get('error_address_1');
    	}

    	if ((strlen(utf8_decode($data['city'])) < 3) || (strlen(utf8_decode($data['city'])) > 32)) {
      		$error['city'] = $this->language->get('error_city');
    	}

    	if ((strlen(utf8_decode($data['postcode'])) < 3) || (strlen(utf8_decode($data['postcode'])) > 10)) {
      		$error['postcode'] = $this->language->get('error_postcode');
    	}
    	
		if ($data['country_id'] == 'FALSE') {
      		$error['country'] = $this->language->get('error_country');
    	}
		
    	if ($data['zone_id'] == 'FALSE') {
      		$error['zone'] = $this->language->get('error_zone');
    	}
    	return $error;	
	}
}
?>