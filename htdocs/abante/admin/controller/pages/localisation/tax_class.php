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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerPagesLocalisationTaxClass extends AController {
    public $data = array();
    private $error = array();

    public function main() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));

        $grid_settings = array(
            'table_id' => 'tax_grid',
            'url' => $this->html->getSecureURL('listing_grid/tax_class'),
            'editurl' => $this->html->getSecureURL('listing_grid/tax_class/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/tax_class/update_field'),
            'sortname' => 'title',
            'sortorder' => 'asc',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=%ID%')
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
            $this->language->get('column_title'),
        );
        $grid_settings['colModel'] = array(
            array(
                'name' => 'title',
                'index' => 'title',
                'width' => 600,
                'align' => 'center',
            ),
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->view->assign('insert', $this->html->getSecureURL('localisation/tax_class/insert'));
        $this->view->assign('help_url', $this->gen_help_url('tax_class_listing'));

        $this->processTemplate('pages/localisation/tax_class_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $tax_class_id = $this->model_localisation_tax_class->addTaxClass($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('localisation/tax_class/insert_rates', '&tax_class_id=' . $tax_class_id));
        }
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $this->model_localisation_tax_class->editTaxClass($this->request->get['tax_class_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']));
        }
        $this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function rates() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $tax_class_info = $this->model_localisation_tax_class->getTaxClass($this->request->get['tax_class_id']);

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']),
            'text' => $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $tax_class_info['title'],
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']),
            'text' => $this->language->get('tab_rates'),
            'separator' => ' :: '
        ));


        $this->data = array();
        $this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $tax_class_info['title'];
        $this->data['error'] = $this->error;
        $this->data['tax_rates'] = $this->model_localisation_tax_class->getTaxRates($this->request->get['tax_class_id']);
        $this->data['insert_rate'] = $this->html->getSecureURL('localisation/tax_class/insert_rates', '&tax_class_id=' . $this->request->get['tax_class_id']);
        $this->data['delete_rate'] = $this->html->getSecureURL('localisation/tax_class/delete_rates', '&tax_class_id=' . $this->request->get['tax_class_id'] . '&tax_rate_id=%ID%');
        $this->data['update_rate'] = $this->html->getSecureURL('localisation/tax_class/update_rates', '&tax_class_id=' . $this->request->get['tax_class_id'] . '&tax_rate_id=%ID%');

        $this->data['rates'] = $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']);
        $this->data['action'] = $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']);
        $this->data['active'] = 'rates';

        $this->loadModel('localisation/location');
        $this->loadModel('localisation/zone');
        $results = $this->model_localisation_location->getLocations();

        $rates = $this->data['zones'] = $this->data['locations'] = array();
        $this->data['zones'][0] = $this->language->get('text_tax_all_zones');
        foreach ($this->data['tax_rates'] as $rate) {
            $rates[] = $rate['location_id'];
        }

        foreach ($results as $c) {
            if (in_array($c['location_id'], $rates)) {
                $this->data['locations'][$c['location_id']] = $c['name'];
                $tmp = $this->model_localisation_zone->getZonesByLocationId($c['location_id']);
                foreach ($tmp as $zone) {
                    $this->data['zones'][$zone['zone_id']] = $zone['name'];
                }
            }
        }
        unset($results, $tmp);

        $this->view->assign('help_url', $this->gen_help_url('rates_listing'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/localisation/tax_class_data_list.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function insert_rates() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateRateForm()) {
            $tax_rate_id = $this->model_localisation_tax_class->addTaxRate($this->request->get['tax_class_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id'] . '&tax_rate_id=' . $tax_rate_id));
        }
        $this->_getRatesForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update_rates() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateRateForm()) {
            $this->model_localisation_tax_class->editTaxRate($this->request->get['tax_rate_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id'] . '&tax_rate_id=' . $this->request->get['tax_rate_id']));
        }
        $this->_getRatesForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete_rates() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->model_localisation_tax_class->deleteTaxRate($this->request->get['tax_rate_id']);
        $this->session->data['success'] = $this->language->get('text_success');
        $this->redirect($this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']));

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _getRatesForm() {

        $tax_class_info = $this->model_localisation_tax_class->getTaxClass($this->request->get['tax_class_id']);

        $this->data = array();
        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']);


	    if (isset($this->session->data['success'])) {
		    $this->data['success'] = $this->session->data['success'];
	        unset($this->session->data['success']);
	    }

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']),
            'text' => $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $tax_class_info['title'],
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']),
            'text' => $this->language->get('tab_rates'),
            'separator' => ' :: '
        ));


        if (isset($this->request->get['tax_rate_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $rate_info = $this->model_localisation_tax_class->getTaxRate($this->request->get['tax_rate_id']);
        }

        $fields = array('location_id', 'zone_id', 'description', 'rate', 'priority', 'rate_prefix', 'threshold_condition', 'threshold');
        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($rate_info)) {
                $this->data[$f] = $rate_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        $this->loadModel('localisation/location');
        $results = $this->model_localisation_location->getLocations();
        //$this->data['locations'] = array('' => $this->language->get('text_select'));
        foreach ($results as $c) {
            $this->data['locations'][$c['location_id']] = $c['name'];
        }

        $zone_to_locations = $this->model_localisation_location->getZoneToLocations(array('location_id' => $this->data['location_id']));

        foreach ($zone_to_locations as $value) {
            $zones[] = $value['zone_id'];
        }

        if ($zones) {
            $this->loadModel('localisation/zone');
            $results = $this->model_localisation_zone->getZones(array('search' => ' z.zone_id IN (' . implode(',', $zones) . ')'));
            $this->data['zones'] = array();
            foreach ($results as $c) {
                $this->data['zones'][$c['zone_id']] = $c['name'];
            }
        }
        $this->loadLanguage('localisation/zone');
        $this->data['active'] = 'rates';
        $this->data['rates'] = $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']);

        if (!isset($this->request->get['tax_rate_id'])) {
            $form_action = $this->html->getSecureURL('localisation/tax_class/insert_rates', '&tax_class_id=' . $this->request->get['tax_class_id']);
            $this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $tax_class_info['title'];
            $this->data['form_title'] = $this->language->get('text_insert') . $this->language->get('text_rate');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $form_action = $this->html->getSecureURL('localisation/tax_class/update_rates', '&tax_class_id=' . $this->request->get['tax_class_id'] . '&tax_rate_id=' . $this->request->get['tax_rate_id']);
            $this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $tax_class_info['title'];
            $this->data['form_title'] = $this->language->get('text_edit') . $this->language->get('text_rate');
            $this->data['update'] = $this->html->getSecureURL('listing_grid/tax_class/update_rate_field', '&id=' . $this->request->get['tax_rate_id']);
            $form = new AForm('ST');
        }
        $this->data['action'] = $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']);
        $this->document->addBreadcrumb(array(
            'href' => $this->data['action'],
            'text' => $this->data['form_title'],
            'separator' => ' :: '
        ));
        $this->data['common_zone'] = $this->html->getSecureURL('common/zone');
        $form->setForm(array(
            'form_name' => 'cgFrm',
            'update' => $this->data['update'],
        ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'cgFrm',
            'action' => $form_action,
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

        $this->data['form']['fields']['location'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'location_id',
            'value' => $this->data['location_id'],
            'options' => $this->data['locations'],
        ));
        $this->data['form']['fields']['zone'] = $form->getFieldHtml(array(
            'name' => 'all_zones',
            'type' => 'checkbox',
            'checked' => ($this->data['zone_id'] ? 0 : 1),
            'value' => 1,
            'label_text' => $this->language->get('text_tax_all_zones'),
            'style' => 'no-save',
        )) . '<br>';

        $this->data['form']['fields']['zone'] .= $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'zone_id',
            'value' => '',
            'options' => $this->data['zones'],
            'attr' => ' style=" display: ' . ($this->data['zone_id'] ? 'block' : 'none') . '; min-width: 130px !important;" ',
        ));

        $this->data['form']['fields']['description'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'description',
            'value' => $this->data['description'],
            'style' => 'large-field',
        ));
        $this->data['form']['fields']['rate'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'rate',
            'value' => $this->data['rate'],
            'required' => true,
        ));
        
		$this->data[ 'rate_prefix' ] = trim($this->data[ 'rate_prefix' ]);
		$currency_symbol = $this->currency->getCurrency($this->config->get('config_currency'));
		$currency_symbol = $currency_symbol[ 'symbol_left' ] . $currency_symbol[ 'symbol_right' ];
		if (!$this->data[ 'rate_prefix' ]) {
			$this->data[ 'rate_prefix' ] = $currency_symbol;
		}

        $this->data['form']['fields']['rate_prefix'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
            'name' => 'rate_prefix',
            'value' => $this->data['rate_prefix'],
			'options' => array(
				'%' => $this->language->get('text_percent') . " (%)",
				'$' => $this->language->get('text_absolute') . " (". $currency_symbol . ")",
			),
        ));

        $this->data['form']['fields']['threshold_condition'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
            'name' => 'threshold_condition',
            'value' => $this->data['threshold_condition'],
			'options' => array(
				'' => '',
				'gt' => '>',
				'ge' => '>=',
				'lt' => '<',
				'le' => '<=',
				'ne' => '<>',
				'eq' => '=',
			),
        ));

        $this->data['form']['fields']['threshold'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'threshold',
            'value' => $this->data['threshold'],
            'required' => false,
        ));


        $this->data['form']['fields']['priority'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'priority',
            'value' => (int)$this->data['priority']
        ));
        $this->view->assign('help_url', $this->gen_help_url('rate_edit'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/localisation/tax_class_rate_form.tpl');
    }


    private function _getForm() {
        $this->data = array();
        $this->data['error'] = $this->error;
        $this->data['cancel'] = $this->html->getSecureURL('localisation/tax_class');

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('localisation/tax_class'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));

        if (isset($this->request->get['tax_class_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $tax_class_info = $this->model_localisation_tax_class->getTaxClass($this->request->get['tax_class_id']);
        }

        $fields = array('title', 'description');
        foreach ($fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data [$f] = $this->request->post [$f];
            } elseif (isset($tax_class_info)) {
                $this->data[$f] = $tax_class_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

        $this->data['active'] = 'details';
        if (!isset($this->request->get['tax_class_id'])) {
            $this->data['action'] = $this->html->getSecureURL('localisation/tax_class/insert');
            $this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_class');
            $this->data['update'] = '';
            $form = new AForm('ST');
        } else {
            $this->data['rates'] = $this->html->getSecureURL('localisation/tax_class/rates', '&tax_class_id=' . $this->request->get['tax_class_id']);
            $this->data['action'] = $this->html->getSecureURL('localisation/tax_class/update', '&tax_class_id=' . $this->request->get['tax_class_id']);
            $this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_class') . ' - ' . $this->data['title'];
            $this->data['update'] = $this->html->getSecureURL('listing_grid/tax_class/update_field', '&id=' . $this->request->get['tax_class_id']);
            $form = new AForm('HS');
        }

        $this->document->addBreadcrumb(array(
            'href' => $this->data['action'],
            'text' => $this->data['heading_title'],
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

        $this->data['form']['fields']['title'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'title',
            'value' => $this->data['title'],
            'required' => true,
        ));
        $this->data['form']['fields']['description'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'description',
            'value' => $this->data['description'],
            'style' => 'large-field',
        ));
        $this->view->assign('help_url', $this->gen_help_url('tax_class_edit'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/localisation/tax_class_form.tpl');
    }

    private function _validateForm() {
        if (!$this->user->canModify('localisation/tax_class')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((strlen(utf8_decode($this->request->post['title'])) < 2) || (strlen(utf8_decode($this->request->post['title'])) > 32)) {
            $this->error['title'] = $this->language->get('error_title');
        }

        if (isset($this->request->post['tax_rate'])) {
            foreach ($this->request->post['tax_rate'] as $value) {
                if (!$value['rate']) {
                    $this->error['warning'] = $this->language->get('error_rate');
                }
            }
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function _validateRateForm() {
        if (!$this->user->canModify('localisation/tax_class')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['rate']) {
            $this->error['rate'] = $this->language->get('error_rate');
        }

        $this->request->post['zone_id'] = (int)$this->request->post['zone_id'];
        if ($this->request->post['all_zones'] == 1) {
            $this->request->post['zone_id'] = 0;
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>