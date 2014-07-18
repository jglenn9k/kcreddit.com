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
class ControllerPagesCatalogDownload extends AController {
	private $error = array();
	public $data = array();
   
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

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
       		'href'      => $this->html->getSecureURL('catalog/download'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$grid_settings = array(
			'table_id' => 'download_grid',
			'url' => $this->html->getSecureURL('listing_grid/download'),
			'editurl' => $this->html->getSecureURL('listing_grid/download/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/download/update_field'),
			'sortname' => 'name',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('catalog/download/update', '&download_id=%ID%')
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_name'),
			$this->language->get('column_remaining'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 600,
                'align' => 'center',
			),
			array(
				'name' => 'remaining',
				'index' => 'remaining',
				'width' => 200,
                'align' => 'center',
                'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign('help_url', $this->gen_help_url('download_listing') );

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign( 'insert', $this->html->getSecureURL('catalog/download/insert') );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data['content_language_id']);

		$this->processTemplate('pages/catalog/download_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
  	        
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		$this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$data = array();

            $this->request->post['download'] = html_entity_decode($this->request->post['download'], ENT_COMPAT, 'UTF-8');
			$data['download'] = $this->request->post['download'];
			$data['mask'] = $this->request->post['mask'];

			$download_id = $this->model_catalog_download->addDownload(array_merge($this->request->post, $data));
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->html->getSecureURL('catalog/download/update', '&download_id=' . $download_id ));
		}
	
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );		

		$this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
			
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$data = array();

            $this->request->post['download'] = html_entity_decode($this->request->post['download'], ENT_COMPAT, 'UTF-8');
			$data['download'] = $this->request->post['download'];
			$data['mask'] = $this->request->post['mask'];

			$this->model_catalog_download->editDownload($this->request->get['download_id'], array_merge($this->request->post, $data));
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->html->getSecureURL('catalog/download/update', '&download_id=' . $this->request->get['download_id'] ));
		}		
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function _getForm() {
    	if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		$this->data['error'] = $this->error;
 		$this->data['language_id'] = $this->config->get('storefront_language_id');

		$this->document->initBreadcrumb( array(
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array(
       		'href'      => $this->html->getSecureURL('catalog/download'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$this->data['cancel'] = $this->html->getSecureURL('catalog/download');

		if (isset($this->request->get['download_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$download_info = $this->model_catalog_download->getDownload($this->request->get['download_id']);
		}

    	if (isset($download_info['filename']) ) {
            if ( is_file(DIR_RESOURCE.$download_info['filename']) ) {
                $rm = new AResource('archive');
                $id = $rm->getIdFromHexPath( str_replace($rm->getTypeDir(), '', $download_info['filename']) );
                $this->data['download_link'] = $this->html->getSecureURL('common/resource_library/get_resource_preview', '&resource_id='.$id);
    		}
            $this->data['filename'] = $download_info['filename'];
            $this->data['mask'] = $download_info['mask'];
		} else {
			$this->data['filename'] = '';
			$this->data['mask'] = '';
		}
    	  
    	if (isset($this->request->get['download_id'])) {
    		$this->data['show_update'] = TRUE;
		} else {
			$this->data['show_update'] = FALSE;
 		}

		if (isset($this->request->post['download_description'])) {
			$this->data['download_description'] = $this->request->post['download_description'];
		} elseif (isset($this->request->get['download_id'])) {
			$this->data['download_description'] = $this->model_catalog_download->getDownloadDescriptions($this->request->get['download_id']);
		} else {
			$this->data['download_description'] = array();
		}   	
		
		if (isset($this->request->post['remaining'])) {
      		$this->data['remaining'] = $this->request->post['remaining'];
    	} elseif (isset($download_info['remaining'])) {
      		$this->data['remaining'] = $download_info['remaining'];
    	} else {
      		$this->data['remaining'] = 1;
    	}
    	
    	if (isset($this->request->post['update'])) {
      		$this->data['update'] = $this->request->post['update'];
    	} else {
      		$this->data['update'] = FALSE;
    	}

		if (!isset($this->request->get['download_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/download/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_download');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/download/update', '&download_id=' . $this->request->get['download_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_download'). ' - ' . $this->data['download_description'][$this->session->data['content_language_id']]['name'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/download/update_field','&id='.$this->request->get['download_id']);
			$form = new AForm('HS');
		}
		  
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: '
   		 ));  

		$form->setForm(array(
		    'form_name' => 'downloadFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'downloadFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'downloadFrm',
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

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'download_description['.$this->session->data['content_language_id'].'][name]',
		    'value' => $this->data['download_description'][$this->session->data['content_language_id']]['name'],
			'required' => true,
			'attr' => ' maxlength="64" ',
			'help_url' => $this->gen_help_url('name'),
	    ));
		$this->data['form']['fields']['download'] = $form->getFieldHtml(array(
		    'type' => 'hidden',
		    'name' => 'download',
		    'value' => htmlspecialchars($this->data['filename'], ENT_COMPAT, 'UTF-8'),
	    ));
        $this->data['form']['fields']['mask'] = $form->getFieldHtml(array(
		    'type' => 'hidden',
		    'name' => 'mask',
		    'value' => $this->data['mask'],
	    ));
		$this->data['form']['fields']['remaining'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'remaining',
		    'value' => $this->data['remaining']
	    ));
		if ($this->data['show_update']) {
			$this->data['form']['fields']['update'] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'update',
				'value' => $this->data['update'],
				'style' => 'no-save'
			));
		}

		$this->view->assign('help_url', $this->gen_help_url('download_edit') );

		$this->view->batchAssign( $this->data );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data['content_language_id']);

        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            array(
                'object_name' => '',
                'object_id' => '',
                'types' => 'archive',
                'mode' => 'url',
            )
        );
		$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
        $this->view->assign('rl_get_preview', $this->html->getSecureURL('common/resource_library/get_resource_preview'));

        $this->processTemplate('pages/catalog/download_form.tpl' );
  	}

  	private function _validateForm() {
    	if (!$this->user->canModify('catalog/download')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		foreach ($this->request->post['download_description'] as $language_id => $value) {
      		if ((mb_strlen($value['name']) < 2) || mb_strlen($value['name']) > 64) {
        		$this->error['name'] = $this->language->get('error_name');
      		}
    	}



    	if (!$this->request->post['download']) {
        		$this->error['file'] = $this->language->get('error_file');
      	}
		
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

  	private function _validateDelete() {
    	if (!$this->user->canModify('catalog/download')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}	
		
		$this->loadModel('catalog/product');

		foreach ($this->request->post['selected'] as $download_id) {
  			$product_total = $this->model_catalog_product->getTotalProductsByDownloadId($download_id);
    
			if ($product_total) {
	  			$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);	
			}	
		}	
			  	  	 
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		} 
  	}
}
?>