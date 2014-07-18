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

class ModelExtensionDefaultFlatRateShipping extends Model {
	function getQuote($address) {
		$this->load->language('default_flat_rate_shipping/default_flat_rate_shipping');
		$location_id = (int)$this->config->get('default_flat_rate_shipping_location_id');

		if ($this->config->get('default_flat_rate_shipping_status')) {

			$taxes = $this->tax->getTaxes((int)$address['country_id'], (int)$address['zone_id']);
		
      		if (!$location_id) {
        		$status = TRUE;
      		} elseif ($taxes) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}

		$method_data = array();
	
		if ($status) {
			$quote_data = array();

			//Process all products shipped together with not special shipping settings on a product level
			if ( count($this->cart->basicShippingProducts()) > 0 ) {
	      		$quote_data['default_flat_rate_shipping'] = array(
	        		'id'           => 'default_flat_rate_shipping.default_flat_rate_shipping',
	        		'title'        => $this->language->get('text_description'),
	        		'cost'         => $this->config->get('default_flat_rate_shipping_cost'),
	        		'tax_class_id' => $this->config->get('default_flat_rate_shipping_tax'),
					'text'         => $this->currency->format($this->tax->calculate($this->config->get('default_flat_rate_shipping_cost'),
					                                                                $this->config->get('default_flat_rate_shipping_tax'),
					                                                                $this->config->get('config_tax')))
	      		);
			} 
			 
			$special_ship_products = $this->cart->specialShippingProducts();
			foreach ($special_ship_products as $product) {
				//check if free or fixed shipping
				$fixed_cost = 0;
				if ($product['free_shipping']) {
				    $fixed_cost = 0;
				} else if($product['shipping_price'] > 0) {
				    $fixed_cost = $product['shipping_price'];
				    //If ship individually count every quintaty 
				    if ($product['ship_individually']) {
				    	$fixed_cost = $fixed_cost * $product['quantity'];	
				    }
				    
				   					
				} else {
				    $fixed_cost = $this->config->get('default_flat_rate_shipping_cost');			
				}
				//merge data and accumulate shipping cost
				if ( isset( $quote_data['default_flat_rate_shipping'] ) ) {
			        	$quote_data['default_flat_rate_shipping']['cost'] = $quote_data['default_flat_rate_shipping']['cost'] + $fixed_cost;
			        	$quote_data['default_flat_rate_shipping']['text'] = 
			        				$this->currency->format($this->tax->calculate($quote_data['default_flat_rate_shipping']['cost'],
						                                                          $this->config->get('default_flat_rate_shipping_tax'),
						                                                          $this->config->get('config_tax')));
			    } else {
		      		$quote_data['default_flat_rate_shipping'] = array(
		        		'id'           => 'default_flat_rate_shipping.default_flat_rate_shipping',
		        		'title'        => $this->language->get('text_description'),
		        		'cost'         => $fixed_cost,
		        		'tax_class_id' => $this->config->get('default_flat_rate_shipping_tax'),
						'text'         => $this->currency->format($this->tax->calculate($fixed_cost,
						                                                                $this->config->get('default_flat_rate_shipping_tax'),
						                                                                $this->config->get('config_tax')))
		      		);
			    }						
			}

      		$method_data = array(
        		'id'         => 'default_flat_rate_shipping',
        		'title'      => $this->language->get('text_title'),
        		'quote'      => $quote_data,
				'sort_order' => $this->config->get('default_flat_rate_shipping_sort_order'),
        		'error'      => FALSE
      		);
		}
	
		return $method_data;
	}
}
?>