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
class ModelCheckoutOrder extends Model {
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . $this->db->table("countries") . "` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . $this->db->table("zones") . "` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . $this->db->table("countries") . "` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . $this->db->table("zones") . "` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$order_data = $this->dcrypt->decrypt_data($order_query->row, 'orders');

			$order_data['shipping_zone_code'] = $shipping_zone_code;
			$order_data['shipping_iso_code_2'] = $shipping_iso_code_2;
			$order_data['shipping_iso_code_3'] = $shipping_iso_code_3;
			$order_data['payment_zone_code'] = $payment_zone_code;
			$order_data['payment_iso_code_2'] = $payment_iso_code_2;
			$order_data['payment_iso_code_3'] = $payment_iso_code_3;

			return $order_data;
		} else {
			return FALSE;
		}
	}

	public function create($data, $old_order_id = '') {
		//reuse same order_id or unused one order_status_id = 0
		if ($old_order_id) {
			$query = $this->db->query("SELECT order_id FROM `" . $this->db->table("orders") . "` WHERE order_id = " . $old_order_id . " AND order_status_id = '0'");

			if (!$query->num_rows) { // for already processed orders do redirect
				$query = $this->db->query("SELECT order_id FROM `" . $this->db->table("orders") . "` WHERE order_id = " . $old_order_id . " AND order_status_id > '0'");
				if ($query->num_rows) {
					return false;
				}
			}
		} else {
			$query = $this->db->query("SELECT order_id FROM `" . $this->db->table("orders") . "` WHERE date_added < '" . date('Y-m-d', strtotime('-1 month')) . "' AND order_status_id = '0'");
		}

		foreach ($query->rows as $result) {
			$this->db->query("DELETE FROM `" . $this->db->table("orders") . "` WHERE order_id = '" . (int)$result['order_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("order_history") . " WHERE order_id = '" . (int)$result['order_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("order_products") . " WHERE order_id = '" . (int)$result['order_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("order_options") . " WHERE order_id = '" . (int)$result['order_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("order_downloads") . " WHERE order_id = '" . (int)$result['order_id'] . "'");
			$this->db->query("DELETE FROM " . $this->db->table("order_totals") . " WHERE order_id = '" . (int)$result['order_id'] . "'");
		}

		if ($old_order_id) {
			$old_order_id = "order_id = '" . $this->db->escape($old_order_id) . "', ";
		}

		$key_sql = '';
		if ( $this->dcrypt->active ) {
			$data = $this->dcrypt->encrypt_data($data, 'orders');
			$key_sql = ", key_id = '" . (int)$data['key_id'] . "'";
		}

		$this->db->query("INSERT INTO `" . $this->db->table("orders") . "`
							SET " . $old_order_id . " store_id = '" . (int)$data['store_id'] . "',
								store_name = '" . $this->db->escape($data['store_name']) . "',
								store_url = '" . $this->db->escape($data['store_url']) . "',
								customer_id = '" . (int)$data['customer_id'] . "',
								customer_group_id = '" . (int)$data['customer_group_id'] . "',
								firstname = '" . $this->db->escape($data['firstname']) . "',
								lastname = '" . $this->db->escape($data['lastname']) . "',
								email = '" . $this->db->escape($data['email']) . "',
								telephone = '" . $this->db->escape($data['telephone']) . "',
								fax = '" . $this->db->escape($data['fax']) . "',
								total = '" . (float)$data['total'] . "',
								language_id = '" . (int)$data['language_id'] . "',
								currency = '" . $this->db->escape($data['currency']) . "',
								currency_id = '" . (int)$data['currency_id'] . "',
								value = '" . (float)$data['value'] . "',
								coupon_id = '" . (int)$data['coupon_id'] . "',
								ip = '" . $this->db->escape($data['ip']) . "',
								shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "',
								shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',
								shipping_company = '" . $this->db->escape($data['shipping_company']) . "',
								shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "',
								shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "',
								shipping_city = '" . $this->db->escape($data['shipping_city']) . "',
								shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "',
								shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "',
								shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "',
								shipping_country = '" . $this->db->escape($data['shipping_country']) . "',
								shipping_country_id = '" . (int)$data['shipping_country_id'] . "',
								shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "',
								shipping_method = '" . $this->db->escape($data['shipping_method']) . "',
								payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "',
								payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "',
								payment_company = '" . $this->db->escape($data['payment_company']) . "',
								payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "',
								payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "',
								payment_city = '" . $this->db->escape($data['payment_city']) . "',
								payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "',
								payment_zone = '" . $this->db->escape($data['payment_zone']) . "',
								payment_zone_id = '" . (int)$data['payment_zone_id'] . "',
								payment_country = '" . $this->db->escape($data['payment_country']) . "',
								payment_country_id = '" . (int)$data['payment_country_id'] . "',
								payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "',
								payment_method = '" . $this->db->escape($data['payment_method']) . "',
								comment = '" . $this->db->escape($data['comment']) . "'"
								. $key_sql . ",
								date_modified = NOW(),
								date_added = NOW()");

		$order_id = $this->db->getLastId();

		foreach ($data['products'] as $product) {
			$this->db->query("INSERT INTO " . $this->db->table("order_products") . "
								SET order_id = '" . (int)$order_id . "',
								product_id = '" . (int)$product['product_id'] . "',
								name = '" . $this->db->escape($product['name']) . "',
								model = '" . $this->db->escape($product['model']) . "',
								price = '" . (float)$product['price'] . "',
								total = '" . (float)$product['total'] . "',
								tax = '" . (float)$product['tax'] . "',
								quantity = '" . (int)$product['quantity'] . "',
								subtract = '" . (int)$product['stock'] . "'");

			$order_product_id = $this->db->getLastId();

			foreach ($product['option'] as $option) {
				$this->db->query("INSERT INTO " . $this->db->table("order_options") . "
									SET order_id = '" . (int)$order_id . "',
										order_product_id = '" . (int)$order_product_id . "',
										product_option_value_id = '" . (int)$option['product_option_value_id'] . "',
										name = '" . $this->db->escape($option['name']) . "',
										`value` = '" . $this->db->escape($option['value']) . "',
										price = '" . (float)$product['price'] . "',
										prefix = '" . $this->db->escape($option['prefix']) . "'");
			}

			foreach ($product['download'] as $download) {
				$this->db->query("INSERT INTO " . $this->db->table("order_downloads") . " SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int)($download['remaining'] * $product['quantity']) . "'");
			}
		}

		foreach ($data['totals'] as $total) {
			$this->db->query("INSERT INTO " . $this->db->table("order_totals") . "
								SET order_id = '" . (int)$order_id . "',
									title = '" . $this->db->escape($total['title']) . "',
									text = '" . $this->db->escape($total['text']) . "',
									`value` = '" . (float)$total['value'] . "',
									sort_order = '" . (int)$total['sort_order'] . "',
									type = '" . $this->db->escape($total['total_type']) . "'");
		}

		return $order_id;
	}

	public function confirm($order_id, $order_status_id, $comment = '') {
		$this->extensions->hk_confirm($this, $order_id, $order_status_id, $comment);
	}

	public function _confirm($order_id, $order_status_id, $comment = '') {
		$order_query = $this->db->query("SELECT *,
												l.filename AS filename,
												l.directory AS directory
										 FROM `" . $this->db->table("orders") . "` o
										 LEFT JOIN " . $this->db->table("languages") . " l ON (o.language_id = l.language_id)
										 WHERE o.order_id = '" . (int)$order_id . "'
										        AND o.order_status_id = '0'");

		if ($order_query->num_rows) {
			$order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');
			//if promotions applied - updates total and add row to order_total for promo type
			$update = array();

			$update[] = "order_status_id = '" . (int)$order_status_id . "'";
			$sql = "UPDATE `" . $this->db->table("orders") . "`
				    SET " . implode(", ", $update) . "
					WHERE order_id = '" . (int)$order_id . "'";
			$this->db->query($sql);


			$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
							   SET order_id = '" . (int)$order_id . "',
							        order_status_id = '" . (int)$order_status_id . "',
							        notify = '1',
							        comment = '" . $this->db->escape($comment) . "',
							        date_added = NOW()");

			$order_product_query = $this->db->query("SELECT *
													 FROM " . $this->db->table("order_products") . "
													 WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $product) {
				if ($product['subtract']) {
					$this->db->query("UPDATE " . $this->db->table("products") . "
									  SET quantity = (quantity - " . (int)$product['quantity'] . ")
									  WHERE product_id = '" . (int)$product['product_id'] . "'");
				}

				$order_option_query = $this->db->query("SELECT *
														FROM " . $this->db->table("order_options") . "
														WHERE order_id = '" . (int)$order_id . "'
																AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($order_option_query->rows as $option) {
					$this->db->query("UPDATE " . $this->db->table("product_option_values") . "
									  SET quantity = (quantity - " . (int)$product['quantity'] . ")
									  WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
									        AND subtract = 1");
				}

				$this->cache->delete('product');

			}

			$language = new ALanguage($this->registry);
			$language->load($order_row['filename']);
			$language->load('mail/order_confirm');

			$this->load->model('localisation/currency');

			$order_status_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_statuses") . "
													WHERE order_status_id = '" . (int)$order_status_id . "'
															AND language_id = '" . (int)$order_row['language_id'] . "'");
			$order_product_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_products") . "
													WHERE order_id = '" . (int)$order_id . "'");
			$order_total_query = $this->db->query("SELECT *
													FROM " . $this->db->table("order_totals") . "
													WHERE order_id = '" . (int)$order_id . "'
													ORDER BY sort_order ASC");
			$order_download_query = $this->db->query("SELECT *
														FROM " . $this->db->table("order_downloads") . "
														WHERE order_id = '" . (int)$order_id . "'");

			$subject = sprintf($language->get('text_subject'), $order_row['store_name'], $order_id);

			// HTML Mail
			$template = new ATemplate();

			$template->data['title'] = sprintf($language->get('text_subject'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

			$template->data['text_greeting'] = sprintf($language->get('text_greeting'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'));
			$template->data['text_order_detail'] = $language->get('text_order_detail');
			$template->data['text_order_id'] = $language->get('text_order_id');
			$template->data['text_invoice'] = $language->get('text_invoice');
			$template->data['text_date_added'] = $language->get('text_date_added');
			$template->data['text_telephone'] = $language->get('text_telephone');
			$template->data['text_email'] = $language->get('text_email');
			$template->data['text_ip'] = $language->get('text_ip');
			$template->data['text_fax'] = $language->get('text_fax');
			$template->data['text_shipping_address'] = $language->get('text_shipping_address');
			$template->data['text_payment_address'] = $language->get('text_payment_address');
			$template->data['text_shipping_method'] = $language->get('text_shipping_method');
			$template->data['text_payment_method'] = $language->get('text_payment_method');
			$template->data['text_comment'] = $language->get('text_comment');
			$template->data['text_powered_by'] = $language->get('text_powered_by');

			$template->data['column_product'] = $language->get('column_product');
			$template->data['column_model'] = $language->get('column_model');
			$template->data['column_quantity'] = $language->get('column_quantity');
			$template->data['column_price'] = $language->get('column_price');
			$template->data['column_total'] = $language->get('column_total');

			$template->data['order_id'] = $order_id;
			$template->data['customer_id'] = $order_row['customer_id'];
			$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_row['date_added']));
			$template->data['logo'] = 'cid:' . md5(pathinfo($this->config->get('config_logo'), PATHINFO_FILENAME)) . '.' . pathinfo($this->config->get('config_logo'), PATHINFO_EXTENSION);
			$template->data['store_name'] = $order_row['store_name'];
			$template->data['address'] = nl2br($this->config->get('config_address'));
			$template->data['telephone'] = $this->config->get('config_telephone');
			$template->data['fax'] = $this->config->get('config_fax');
			$template->data['email'] = $this->config->get('store_main_email');
			$template->data['store_url'] = $order_row['store_url'];
			$template->data['invoice'] = $order_row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id;
			$template->data['firstname'] = $order_row['firstname'];
			$template->data['lastname'] = $order_row['lastname'];
			$template->data['shipping_method'] = $order_row['shipping_method'];
			$template->data['payment_method'] = $order_row['payment_method'];
			$template->data['customer_email'] = $order_row['email'];
			$template->data['customer_telephone'] = $order_row['telephone'];
			$template->data['customer_ip'] = $order_row['ip'];
			$template->data['comment'] = $order_row['comment'];

			$zone_query = $this->db->query("SELECT *
											FROM `" . $this->db->table("zones") . "`
											WHERE zone_id = '" . (int)$order_row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone_code = $zone_query->row['code'];
			} else {
				$zone_code = '';
			}

			$shipping_data = array(
				'firstname' => $order_row['shipping_firstname'],
				'lastname' => $order_row['shipping_lastname'],
				'company' => $order_row['shipping_company'],
				'address_1' => $order_row['shipping_address_1'],
				'address_2' => $order_row['shipping_address_2'],
				'city' => $order_row['shipping_city'],
				'postcode' => $order_row['shipping_postcode'],
				'zone' => $order_row['shipping_zone'],
				'zone_code' => $zone_code,
				'country' => $order_row['shipping_country']
			);

			$template->data['shipping_address'] = $this->customer->getFormatedAdress($shipping_data, $order_row['shipping_address_format']);
			$zone_query = $this->db->query("SELECT * FROM `" . $this->db->table("zones") . "` WHERE zone_id = '" . (int)$order_row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone_code = $zone_query->row['code'];
			} else {
				$zone_code = '';
			}

			$payment_data = array(
				'firstname' => $order_row['payment_firstname'],
				'lastname' => $order_row['payment_lastname'],
				'company' => $order_row['payment_company'],
				'address_1' => $order_row['payment_address_1'],
				'address_2' => $order_row['payment_address_2'],
				'city' => $order_row['payment_city'],
				'postcode' => $order_row['payment_postcode'],
				'zone' => $order_row['payment_zone'],
				'zone_code' => $zone_code,
				'country' => $order_row['payment_country']
			);

			$template->data['payment_address'] = $this->customer->getFormatedAdress($payment_data, $order_row['payment_address_format']);
			$template->data['products'] = array();

			foreach ($order_product_query->rows as $product) {
				$option_data = array();

				$order_option_query = $this->db->query("SELECT * FROM " . $this->db->table("order_options") . " WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

				foreach ($order_option_query->rows as $option) {
					$option_data[] = array(
						'name' => $option['name'],
						'value' => $option['value']
					);
				}

				$template->data['products'][] = array(
					'name' => $product['name'],
					'model' => $product['model'],
					'option' => $option_data,
					'quantity' => $product['quantity'],
					'price' => $this->currency->format($product['price'], $order_row['currency'], $order_row['value']),
					'total' => $this->currency->format($product['total'], $order_row['currency'], $order_row['value'])
				);
			}

			$template->data['totals'] = $order_total_query->rows;

			$html = $template->fetch('mail/order_confirm.tpl');

			// Text Mail
			$text = sprintf($language->get('text_greeting'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			$text .= $language->get('text_order_id') . ' ' . $order_id . "\n";
			$text .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_row['date_added'])) . "\n";
			$text .= $language->get('text_order_status') . ' ' . $order_status_query->row['name'] . "\n\n";
			$text .= $language->get('text_product') . "\n";

			foreach ($order_product_query->rows as $result) {
				$text .= $result['quantity'] . 'x ' . $result['name'] . ' (' . $result['model'] . ') ' . html_entity_decode($this->currency->format($result['total'], $order_row['currency'], $order_row['value']), ENT_NOQUOTES, 'UTF-8') . "\n";
				$order_option_query = $this->db->query("SELECT * FROM " . $this->db->table("order_options") . " WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $result['order_product_id'] . "'");
				foreach ($order_option_query->rows as $option) {
					$text .= chr(9) . '-' . $option['name'] . ' ' . $option['value'] . "\n";
				}
			}

			$text .= "\n";

			$text .= $language->get('text_total') . "\n";

			foreach ($order_total_query->rows as $result) {
				$text .= $result['title'] . ' ' . html_entity_decode($result['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
			}

			$order_total = $result['text'];

			$text .= "\n";

			if ($order_row['customer_id']) {
				$text .= $language->get('text_invoice') . "\n";
				$text .= $order_row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id . "\n\n";
			}

			if ($order_download_query->num_rows) {
				$text .= $language->get('text_download') . "\n";
				$text .= $order_row['store_url'] . 'index.php?rt=account/download' . "\n\n";
			}

			if ($order_row['comment'] != '') {
				$comment = ($order_row['comment'] . "\n\n" . $comment);
			}

			if ($comment) {
				$text .= $language->get('text_comment') . "\n\n";
				$text .= $comment . "\n\n";
			}

			$text .= $language->get('text_footer');

			$mail = new AMail($this->config);
			$mail->setTo($order_row['email']);
			$mail->setFrom($this->config->get('store_main_email'));
			$mail->setSender($order_row['store_name']);
			$mail->setSubject($subject);
			$mail->setHtml($html);
			$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$mail->addAttachment(DIR_RESOURCE . $this->config->get('config_logo'),
					md5(pathinfo($this->config->get('config_logo'), PATHINFO_FILENAME)) . '.' . pathinfo($this->config->get('config_logo'), PATHINFO_EXTENSION));

			$mail->send();

			if ($this->config->get('config_alert_mail')) {

				// HTML
				$template->data['text_greeting'] = $language->get('text_received') . "\n\n";
				$template->data['invoice'] = '';
				$template->data['text_invoice'] = '';

				if (file_exists(DIR_TEMPLATE . $this->config->get('config_storefront_template') . '/template/mail/order_confirm.tpl')) {
					$html = $template->fetch('mail/order_confirm.tpl');
				} else {
					$html = $template->fetch('mail/order_confirm.tpl');
				}

				$subject = sprintf($language->get('text_subject'), html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8'), $order_id . ' (' . $order_total . ')');

				$mail->setSubject($subject);
				$mail->setTo($this->config->get('store_main_email'));
				$mail->setHtml($html);
				$mail->send();

				// Send to additional alert emails
				$emails = explode(',', $this->config->get('config_alert_emails'));
				foreach ($emails as $email) {
					if (trim($email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}

			}

			$msg_text = sprintf($language->get('text_new_order_text'), $order_row['firstname'] . ' ' . $order_row['lastname']);
			$msg_text .= "<br/><br/>";
			foreach ($template->data['totals'] as $total) {
				$msg_text .= $total['title'] . ' - ' . $total['text'] . "<br/>";
			}
			$msg = new AMessage();
			$msg->saveNotice($language->get('text_new_order') . $order_id, $msg_text);
		}
	}

	public function update($order_id, $order_status_id, $comment = '', $notify = FALSE) {
		$order_query = $this->db->query("SELECT *
										 FROM `" . $this->db->table("orders") . "` o
										 LEFT JOIN " . $this->db->table("languages") . " l ON (o.language_id = l.language_id)
										 WHERE o.order_id = '" . (int)$order_id . "' AND o.order_status_id > '0'");

		if ($order_query->num_rows) {
			$order_row = $this->dcrypt->decrypt_data($order_query->row, 'orders');
			
			$this->db->query("UPDATE `" . $this->db->table("orders") . "`
								SET order_status_id = '" . (int)$order_status_id . "',
									date_modified = NOW()
								WHERE order_id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO " . $this->db->table("order_history") . "
								SET order_id = '" . (int)$order_id . "',
									order_status_id = '" . (int)$order_status_id . "',
									notify = '" . (int)$notify . "',
									comment = '" . $this->db->escape($comment) . "',
									date_added = NOW()");

			if ($notify) {
				$language = new ALanguage($order_row['directory']);
				$language->load($order_row['filename']);
				$language->load('mail/order_update');

				$subject = sprintf($language->get('text_subject'), html_entity_decode($order_row['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

				$message = $language->get('text_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_row['date_added'])) . "\n\n";

				$order_status_query = $this->db->query("SELECT *
														FROM " . $this->db->table("order_statuses") . "
														WHERE order_status_id = '" . (int)$order_status_id . "'
															AND language_id = '" . (int)$order_row['language_id'] . "'");

				if ($order_status_query->num_rows) {
					$message .= $language->get('text_order_status') . "\n\n";
					$message .= $order_status_query->row['name'] . "\n\n";
				}

				$message .= $language->get('text_invoice') . "\n";
				$message .= $order_row['store_url'] . 'index.php?rt=account/invoice&order_id=' . $order_id . "\n\n";

				if ($comment) {
					$message .= $language->get('text_comment') . "\n\n";
					$message .= $comment . "\n\n";
				}

				$message .= $language->get('text_footer');

				$mail = new AMail($this->config);
				$mail->setTo($order_row['email']);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($order_row['store_name']);
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}
}

