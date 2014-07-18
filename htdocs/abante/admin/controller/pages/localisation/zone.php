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
class ControllerPagesLocalisationZone extends AController {
	public $data = array();
	private $error = array(); 
	private $fields = array('status', 'name', 'code', 'country_id');
 
	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

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
       		'href'      => $this->html->getSecureURL('localisation/zone'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$grid_settings = array(
			'table_id' => 'zone_grid',
			'url' => $this->html->getSecureURL('listing_grid/zone'),
			'editurl' => $this->html->getSecureURL('listing_grid/zone/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/zone/update_field'),
			'sortname' => 'country_id',
			'sortorder' => 'asc',
			'columns_search' => false,
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/zone/update', '&zone_id=%ID%')
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
	            'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_country'),
	        $this->language->get('column_name'),
            $this->language->get('column_code'),
            $this->language->get('column_status'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'country_id',
				'index' => 'country_id',
				'width' => 120,
                'align' => 'center',
			),
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 250,
                'align' => 'center',
			),
			array(
				'name' => 'code',
				'index' => 'code',
				'width' => 120,
                'align' => 'center',
			),
			array(
				'name' => 'status',
				'index' => 'status',
				'width' => 130,
                'align' => 'center',
				'search' => false,
			),
		);

		$this->loadModel('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$countries = array('' => $this->language->get('text_select_country'));
		foreach ( $this->data['countries'] as $c ) {
			$countries[ $c['country_id'] ] = $c['name'];
		}

		$form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'zone_grid_search',
	    ));

	    $grid_search_form = array();
        $grid_search_form['id'] = 'zone_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'zone_grid_search',
		    'action' => '',
	    ));
        $grid_search_form['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_go'),
		    'style' => 'button1',
	    ));
		$grid_search_form['reset'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'reset',
		    'text' => $this->language->get('button_reset'),
		    'style' => 'button2',
	    ));

	    $grid_search_form['fields']['country_id'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'country_id',
            'options' => $countries,
	    ));

		$grid_settings['search_form'] = true;

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );

		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/zone/insert') );
		$this->view->assign('help_url', $this->gen_help_url('zone_listing') );

		$this->processTemplate('pages/localisation/zone_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$zone_id = $this->model_localisation_zone->addZone($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/zone/update', '&zone_id=' . $zone_id ));
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_localisation_zone->editZone($this->request->get['zone_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/zone/update', '&zone_id=' . $this->request->get['zone_id'] ));
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {
		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/zone');

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('localisation/zone'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$this->loadModel('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$countries = array();
		foreach ( $this->data['countries'] as $c ) {
			$countries[ $c['country_id'] ] = $c['name'];
		}

		if (isset($this->request->get['zone_id']) ) {
			$zone_info = $this->model_localisation_zone->getZone($this->request->get['zone_id']);
		}

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($zone_info)) {
				$this->data[$f] = $zone_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->request->get['zone_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/zone/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_zone');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/zone/update', '&zone_id=' . $this->request->get['zone_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_zone');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/zone/update_field','&id='.$this->request->get['zone_id']);
			$form = new AForm('HS');
		}
		
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: '
   		 ));  

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
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
			'name' => 'name',
			'value' => $this->data['name'],
			'required' => true,
			'help_url' => $this->gen_help_url('name'),
		));
		$this->data['form']['fields']['code'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'code',
			'value' => $this->data['code'],
			'help_url' => $this->gen_help_url('code'),
		));
		$this->data['form']['fields']['country'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'country_id',
			'value' => $this->data['country_id'],
			'options' => $countries,
		));
		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'status',
		    'value' => $this->data['status'],
			'style'  => 'btn_switch',
	    ));

		$this->view->batchAssign( $this->data );
		$this->view->assign('help_url', $this->gen_help_url('zone_edit') );
        $this->processTemplate('pages/localisation/zone_form.tpl' );
	}

	private function _validateForm() {
		if(!isset($this->request->post['status'])){
			$this->request->post['status'] =0;
		}

		if (!$this->user->canModify('localisation/zone')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((strlen(utf8_decode($this->request->post['name'])) < 2) || (strlen(utf8_decode($this->request->post['name'])) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>