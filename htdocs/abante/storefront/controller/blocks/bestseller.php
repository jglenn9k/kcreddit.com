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
class ControllerBlocksBestSeller extends AController {

	public $data;

	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

      	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->loadModel('catalog/product');
		$this->loadModel('catalog/review');
		$this->loadModel('tool/image');

		$this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
		
		$this->data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($this->config->get('config_bestseller_limit'));
		foreach($results as $result){
			$product_ids[] = (int)$result['product_id'];
		}

		$products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);

		$resource = new AResource('image');

		foreach ($results as $result) {
			$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_product_width'),
			                                     $this->config->get('config_image_product_height'),true);
			
			$rating = $products_info[$result['product_id']]['rating'];

			$special = FALSE;
			

			$discount = $products_info[$result['product_id']]['discount'];

			if ($discount) {
				$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				$special = $products_info[$result['product_id']]['special'];
				if ($special) {
					$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
				}						
			}

			$options = $products_info[$result['product_id']]['options'];
			
			if ($options) {
				$add = $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode');
			} else {
                if($this->config->get('config_cart_ajax')){
                    $add = '#';
                }else{
                    $add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
                }
			}
			
			$this->data['products'][] = array(
				'product_id'    => $result['product_id'],
				'name'    		=> $result['name'],
				'model'   		=> $result['model'],
				'rating'  		=> $rating,
				'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
				'price'   		=> $price,
				'options'   	=> $options,
				'special' 		=> $special,
				'thumb'   		=> $thumbnail,
				'href'    		=> $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode'),
				'add'    		=> $add
			);
		}

		if ($this->config->get('config_customer_price')) {
			$this->data['display_price'] = TRUE;
		} elseif ($this->customer->isLogged()) {
			$this->data['display_price'] = TRUE;
		} else {
			$this->data['display_price'] = FALSE;
		}
		// framed needs to show frames for generic block.
		//If tpl used by listing block framed was set by listing block settings
		$this->data['block_framed'] = true;
        $this->view->batchAssign($this->data);
        $this->processTemplate();
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>