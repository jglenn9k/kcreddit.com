<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerPagesExtensionEncryptionDataManager extends AController {
	private $error = array();
	public $data = array();
	private $fields = array('key_name', 'key_length', 'private_key_type', 'encrypt_key', 'passphrase', 'enc_key', 'enc_test_mode');
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('encryption_data_manager/encryption_data_manager');
		$this->document->setTitle( $this->language->get('encryption_data_manager_name') );
		$this->load->model('setting/setting');
				 
		$enc = new ASSLEncryption(); 
		if ( !$enc->active || !$enc->getKeyPath() ) {
			$this->error['warning'] = $this->language->get('error_openssl_disabled');	
		}		 
		$enc_data = new ADataEncryption(); 
		if ( !$enc_data->active ) {
			$this->error['warning'] = $this->language->get('error_data_encryption_disabled');	
		}		 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->_validate())) {
			
			$this->cache->delete('encryption.keys');
			
			if ( !empty($this->request->post['key_name']) ) {
				$this->request->post['key_name'] = preformatTextID($this->request->post['key_name']);
				$keys = $this->_create_key_pair($this->request->post); 
				if ( $keys['public'] || $keys['private'] ) {
					$this->session->data['success'] = sprintf($this->language->get('text_success_key_get'), $keys['public'], $keys['private']);
				} else {
					$this->error['warning'] = $this->language->get('error_generating_keys_failed');
				}
			} else if ( !empty($this->request->post['enc_key']) ) {
				$enc_result = $this->_encrypt_user_data($this->request->post);
				if ( $this->request->post['enc_test_mode'] ) {
					$this->session->data['success'] = sprintf($this->language->get('text_encryption_test'), implode('<br/>', $enc_result['result']) );
				} else if ( count($enc_result['result'])) {
					$this->session->data['success'] = sprintf(
												$this->language->get('text_success_encrypting'), 
												implode('<br/>', $enc_result['result']),
												$enc_result['key_name']
											);
				} else {
					$this->error['warning'] = $this->language->get('error_encrypting');
				}
			} else {
				$this->error['warning'] = $this->language->get('error_required_data_missing');	
			}
		}
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->data['success'] = $this->session->data['success'];
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('eextension/extensions/extensions'),
       		'text'      => $this->language->get('text_extensions'),
      		'separator' => ' :: '
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/encryption_data_manager'),
       		'text'      => $this->language->get('encryption_data_manager_name'),
      		'separator' => ' :: '
   		 ));
		
		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post[$f];
			} else {
				$this->data [$f] = $this->config->get($f);
			}
		}

		//Build sections for display 		
		$this->data ['action'] = $this->html->getSecureURL ( 'extension/encryption_data_manager' );
		$this->data['cancel'] = $this->html->getSecureURL('extension/encryption_data_manager');
		$this->data ['heading_title'] = $this->language->get ( 'text_additional_settings' );
		$this->data ['update'] = $this->html->getSecureURL ( 'listing_grid/extension/update', '&id=encryption_data_manager' );
		$form = new AForm ( 'HT' );
		$form->setForm ( array ('form_name' => 'keyGenFrm', 'update' => $this->data ['update'] ) );

		$key_gen = array();
		$key_gen['section_id'] = 'key_gen';
		$key_gen['name'] = $this->language->get('key_gen_section_name');
		$key_gen['form_title'] = $key_gen['name'];
		$key_gen['form']['form_open'] = $form->getFieldHtml ( array ('type' => 'form', 'name' => 'keyGenFrm', 'action' => $this->data ['action'] ) );
		$key_gen['form']['submit'] = $form->getFieldHtml(array('type' => 'button', 'name' => 'submit', 'text' => $this->language->get('button_generate_keys'), 'style' => 'button1' ) );
		$key_gen['form']['cancel'] = $form->getFieldHtml( array('type' => 'button', 'name' => 'reset', 'text' => $this->language->get('button_reset'), 'style' => 'button2' ) );
				
		$key_gen['form']['fields']['key_name'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'key_name',
				'value' => $this->data['key_name'],
				'required' => true
			));

		$key_gen['form']['fields']['key_length'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'key_length',
				'value' => !$this->data['key_length'] ? 2048 : $this->data['key_length'],
			));

		$key_gen['form']['fields']['private_key_type'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'private_key_type',
				'options' => array(
					OPENSSL_KEYTYPE_RSA => 'OPENSSL_KEYTYPE_RSA', 
					OPENSSL_KEYTYPE_DSA => 'OPENSSL_KEYTYPE_DSA', 
					OPENSSL_KEYTYPE_DH => 'OPENSSL_KEYTYPE_DH'
				),
				'value' => $this->data['private_key_type'],
			));

		/*
		* Password protected key is not supported 
		$key_gen['form']['fields']['encrypt_key'] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'encrypt_key',
				'value' => $this->data['encrypt_key'],
				'style'  => 'btn_switch',
			));

		$key_gen['form']['fields']['passphrase'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'passphrase',
				'value' => $this->data['passphrase'],
			));

		*/
		$this->data['sections'][] = $key_gen;

		
		$form2 = new AForm ( 'HT' );
		$form2->setForm ( array ('form_name' => 'dataEncFrm', 'update' => $this->data ['update'] ) );

		$data_enc = array();
		$data_enc['section_id'] = 'data_encryption';
		$data_enc['name'] = $this->language->get('data_encryption');	
	
		$data_enc['form_title'] = $data_enc['name'];
		$data_enc['form']['form_open'] = $form2->getFieldHtml ( array ('type' => 'form', 'name' => 'dataEncFrm', 'action' => $this->data ['action'] ) );
		$data_enc['form']['submit'] = $form2->getFieldHtml(array('type' => 'button', 'name' => 'submit', 'text' => $this->language->get('button_encrypt_data'), 'style' => 'button1' ) );
		$data_enc['form']['cancel'] = $form2->getFieldHtml(array('type' => 'button', 'name' => 'reset', 'text' => $this->language->get('button_reset'), 'style' => 'button2' ) );
				
		//load existing keys. 
		$pub_keys_options = $this->_load_keys( $enc );
				
		$data_enc['form']['fields']['enc_key'] = $form2->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'enc_key',
				'options' => $pub_keys_options,
				'value' => $this->data['enc_key'],
			));

		$data_enc['note'] = $this->language->get('post_encrypting_notice');
		
		$enc_tables_options = array();
		$enc_config_tables = $enc_data->getEcryptedTables();
		
		if ( has_value($enc_config_tables) ){
			foreach ($enc_config_tables as $table_name) { $enc_tables_options[$table_name] = $table_name; }
			/*
			//Per table encryption is not suported YET
			$data_enc['form']['fields']['enc_tables'] = $form2->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'enc_tables',
					'options' => $enc_tables_options,
					'value' => $this->data['enc_tables'],
				));
			*/
			$data_enc['form']['fields']['enc_tables'] = implode(', ', $enc_config_tables);
	
			$data_enc['form']['fields']['enc_test_mode'] = $form2->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'enc_test_mode',
					'value' => $this->data['enc_test_mode'],
					'style'  => 'btn_switch',
				));

			$data_enc['form']['fields']['enc_remove_original'] = $form2->getFieldHtml(array(
					'type' => 'checkbox',
					'name' => 'enc_remove_original',
					'value' => (isset($this->data['enc_remove_original'])) ? $this->data['enc_remove_original'] : 1 
				));

		} else {
			$data_enc['note'] = "<b>Enable Data Encryption first!<b>";
		}
		
		$this->data['sections'][] = $data_enc;			

		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/encryption_data_manager.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
		
	private function _validate() {
		if (!$this->user->canModify('extension/encryption_data_manager')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ( !empty($this->request->post['key_name']) ) {
			//validate uniquenes of key name
			$test_row = $this->db->query("SELECT * FROM " . $this->db->table('encryption_keys') . 
						" WHERE `key_name` = '".$this->db->escape($this->request->post['key_name'])."'");
			if ($test_row->num_rows) {
				$this->error['warning'] = $this->language->get('error_duplicate_key');
			}	
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	private function _create_key_pair ( $data ) {
		if ( empty($data['key_name']) ) {
			return array();
		}
		
		$data['encrypt_key'] = $data['encrypt_key'] ? 1 : 0;
		
		//generate keys	and save	
		$enc = new ASSLEncryption ();
		$keys = $enc->generate_ssl_key_pair($data, $data['passphrase']);
		$enc->save_ssl_key_pair($keys, $data['key_name']);
		
		//update record in the database
		$this->db->query("INSERT INTO " . $this->db->table('encryption_keys') . " SET `key_name` = '".$data['key_name']."', `status` = 1;" );
		
		return $keys;
	}

	private function _load_keys ( $enc ) {
		//get key files from the directory
		$files = array_filter(glob($enc->getKeyPath().'/*'), function($file) { return preg_match('/.pub$/', $file ); } );
		$pub_keys = array_map(function($file) { return basename($file, ".pub"); }, $files );
		$pub_keys_options = array();
		//load active keys from db
		$query = $this->db->query("SELECT * FROM " . $this->db->table('encryption_keys') . " WHERE `status` = 1");
		foreach ($query->rows as $key_record) {
			foreach ($pub_keys as $key_name ) {
				if ($key_name == $key_record['key_name']) {
					$pub_keys_options[$key_record['key_id']] = $key_name;
					break;
				}
			}
		}
		return $pub_keys_options;		
	}
	
	private function _encrypt_user_data ( $data ) {
		if ( empty($data['enc_key']) ) {
			return array();
		}
		//load key details 
		$query = $this->db->query("SELECT * FROM " . $this->db->table('encryption_keys') . 
						" WHERE `key_id` = ". (int)$data['enc_key'] );
		if ($query->num_rows != 1 ) {
			return array();		
		}	
		
		$key_name = $query->row['key_name'];
		$key_id = $query->row['key_id'];
		
		$result = array();
								
		//generate keys	and save	
		$enc_data = new ADataEncryption( $key_name ); 
		foreach ($enc_data->getEcryptedTables() as $table_name) {
			$enc_fields = $enc_data->getEcryptedFields($table_name);
			$id_field = $enc_data->getEcryptedTableID($table_name);
			// important to use non-encripted table
			$query_read = $this->db->query("SELECT * FROM " . DB_PREFIX . $table_name );
			$count = 0;
			foreach($query_read->rows as $record) {			
				//if encrypting customers table keep login as email before encrypting email 
				if ($table_name == 'customers' && empty($record['loginname'])) {
					$record['loginname'] = $record['email'];
				}
				//specify key to be used for encryption 		
				$record['key_id'] = $key_id;
				$enc_rec_data = $enc_data->encrypt_data($record, $table_name);
				//check if this is not a test mode and we can write
				$count++;
				if (!$data['enc_test_mode']) {
					$insert_flds = '';
					foreach($enc_rec_data as $col => $val) {
						if ( has_value($val) ) {
							if ( !empty($insert_flds) ) { 
								$insert_flds .= ", ";
							}
							$insert_flds .= "`$col` = '" .$this->db->escape($val) . "'";
						}
					}

					try {
						$this->db->query("INSERT INTO " . $this->db->table($table_name) . " SET $insert_flds;" );
					} catch (AException $e) {
						$result[] = "<div class='error'>Error: Table $table_name record ID: " . $enc_rec_data[$id_field] . " with key name $key_name failed saving! </div>";
						$count--;						
						continue;
					}
					
					//remove orioginal record if requested
					if ($data['enc_remove_original']) {
						$this->db->query("DELETE FROM " . DB_PREFIX . $table_name . " WHERE $id_field=" . $record[$id_field]  );
					}
					
				} else {
					//check if such row exists for test
					$test_row = $this->db->query("SELECT * FROM " . $this->db->table($table_name) . " WHERE $id_field = " . $enc_rec_data[$id_field] );
					if ($test_row->num_rows) {
						$result[] = "<div class='error'>Error: Duplicate record ID: " . $enc_rec_data[$id_field] . " in table ". $this->db->table($table_name) ." !</div>";
						$count--;						
					} 
				}
				
			}			
			$result[] = "<b>Table $table_name has encrypted $count records with key name $key_name</b>";
		}
		return array('key_name' => $key_name, 'result' => $result);
	}	
}