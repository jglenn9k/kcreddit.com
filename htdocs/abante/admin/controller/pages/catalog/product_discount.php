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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesCatalogProductDiscount extends AController {
	private $error = array(); 
	private $data = array(); 
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('catalog/product');
		$promoton = new APromotion();
		
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			if ( !$product_info ) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
    	}

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));


		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach( $results as $r ) {
            $this->data['customer_groups'][ $r['customer_group_id'] ] = $r['name'];
        }
		  
		$this->data['product_discounts'] = $promoton->getProductDiscounts($this->request->get['product_id']);

		$this->data['delete'] = $this->html->getSecureURL('catalog/product_discount/delete', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=%ID%' );
		$this->data['update'] = $this->html->getSecureURL('catalog/product_discount/update', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=%ID%' );
		$this->data['insert'] = $this->html->getSecureURL('catalog/product_discount/insert', '&product_id=' . $this->request->get['product_id'] );

		$this->data['link_general'] = $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_links'] = $this->html->getSecureURL('catalog/product_links', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_options'] = $this->html->getSecureURL('catalog/product_options', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_discount'] = $this->html->getSecureURL('catalog/product_discount', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_special'] = $this->html->getSecureURL('catalog/product_special', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_images'] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] );

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['form_title'] = $this->language->get('text_edit') .'&nbsp;'. $this->data['product_description'][$this->session->data['content_language_id']]['name'];
		$this->data['button_remove'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_remove'),
			'style' => 'button2',
		));
		$this->data['button_edit'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_edit'),
			'style' => 'button2',
		));
		$this->data['button_add_discount'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_add_discount'),
			'style' => 'button1',
		));

		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/catalog/product_discount_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
  
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle($this->language->get('heading_title'));		
		$this->loadModel('catalog/product');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $product_discount_id = $this->model_catalog_product->addProductDiscount($this->request->get['product_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('catalog/product_discount/update', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=' . $product_discount_id ) );
    	}	
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle($this->language->get('heading_title'));		
		$this->loadModel('catalog/product');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_catalog_product->updateProductDiscount($this->request->get['product_discount_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('catalog/product_discount/update', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=' . $this->request->get['product_discount_id'] ) );
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function delete() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->loadModel('catalog/product');
    	$this->model_catalog_product->deleteProductDiscount($this->request->get['product_discount_id']);
		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ));

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function _getForm() {

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] );

		$this->data['link_general'] = $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_images'] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_relations'] = $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_options'] = $this->html->getSecureURL('catalog/product_options', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_promotions'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_extensions'] = $this->html->getSecureURL('catalog/product_extensions', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_layout'] = $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $this->request->get['product_id'] );

		$this->data['active'] = 'promotions';
		$this->view->batchAssign( $this->data );
		$this->data['product_tabs'] = $this->view->fetch('pages/catalog/product_tabs.tpl');

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['heading_title'] = $this->language->get('text_edit') .'&nbsp;'. $this->language->get('text_product') . ' - ' . $this->data['product_description'][$this->session->data['content_language_id']]['name'];

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
			'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->data['heading_title'],
			'separator' => ' :: '
		 ));
		 $this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('tab_promotions'),
			'separator' => ' :: '
		 ));
									
		if (isset($this->request->get['product_discount_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$discount_info = $this->model_catalog_product->getProductDiscount($this->request->get['product_discount_id']);
			if ( $discount_info['date_start'] == '0000-00-00' ) $discount_info['date_start'] = '';
			if ( $discount_info['date_end'] == '0000-00-00' ) $discount_info['date_end'] = '';
    	}

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach( $results as $r ) {
            $this->data['customer_groups'][ $r['customer_group_id'] ] = $r['name'];
        }

        $fields = array('customer_group_id', 'quantity', 'priority', 'price', 'date_start', 'date_end',);
		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
				if(in_array($f,array('date_start','date_end'))){
					$this->data [$f] = dateDisplay2ISO($this->data [$f],$this->language->get('date_format_short'));
				}
			} elseif (isset($discount_info)) {
				$this->data[$f] = $discount_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->request->get['product_discount_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_discount/insert', '&product_id=' . $this->request->get['product_id'] );
			$this->data['form_title'] = $this->language->get('text_insert') .'&nbsp;'. $this->language->get('entry_discount');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_discount/update', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=' . $this->request->get['product_discount_id'] );
			$this->data['form_title'] = $this->language->get('text_edit') .'&nbsp;'. $this->language->get('entry_discount');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_discount_field','&id='.$this->request->get['product_discount_id']);
			$form = new AForm('HS');

		}

		$this->document->addBreadcrumb( array (
			'href'      => $this->data['action'],
			'text'      => $this->data['form_title'],
			'separator' => ' :: '
		 ));

		$form->setForm(array(
		    'form_name' => 'productFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'productFrm',
		    'action' => $this->data['action'],
		    'attr' => 'confirm-exit="true"',
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

        $this->data['form']['fields']['customer_group'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'customer_group_id',
			'value' => $this->data['customer_group_id'],
            'options' => $this->data['customer_groups'],
		));

        $this->data['form']['fields']['quantity'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'quantity',
			'value' => $this->data['quantity'],
	        'style' => 'small-field',
		));
		$this->data['form']['fields']['priority'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'priority',
			'value' => $this->data['priority'],
	        'style' => 'small-field',
		));
        $this->data['form']['fields']['price'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'price',
			'value' => number_format((float)$this->data['price'], 2, $this->language->get('decimal_point'), $this->language->get('thousand_point')),
		));

		$this->data['js_date_format'] = format4Datepicker($this->language->get('date_format_short'));
        $this->data['form']['fields']['date_start'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'date_start',
			'value' => dateISO2Display($this->data['date_start'],$this->language->get('date_format_short')),
            'style' => 'date'
		));
		$this->data['form']['fields']['date_end'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'date_end',
			'value' => dateISO2Display($this->data['date_end'],$this->language->get('date_format_short')),
            'style' => 'date'
		));

		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('product_discount_edit') );
        $this->view->batchAssign( $this->data );
        $this->processTemplate('pages/catalog/product_discount_form.tpl' );
  	} 
	
  	private function _validateForm() {
    	if (!$this->user->canModify('catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

		if ( $this->request->post['date_start'] != '0000-00-00' && $this->request->post['date_end'] != '0000-00-00'
		     &&	dateFromFormat($this->request->post['date_start'],$this->language->get('date_format_short')) > dateFromFormat($this->request->post['date_end'],$this->language->get('date_format_short'))
		) {
			$this->error['date_end'] = $this->language->get('error_date');
		}


    	if (!$this->error) {
			return TRUE;
    	} else {
      		return FALSE;
    	}
  	}
	
}