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
class ControllerResponsesListingGridMessageGrid extends AController {
	private $error = array();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/message_manager');
		if (!$this->user->canAccess('tool/message_manager')) {
			$response = new stdClass ();
			$response->userdata->error = sprintf($this->language->get('error_permission_access'), 'tool/message_manager');
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($response));
			return;
		}

		$this->loadModel('tool/message_manager');

		//Prepare filter config
		$grid_filter_params = array( 'title', 'create_date', 'status' );
		$filter = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));

		$total = $this->model_tool_message_manager->getTotalMessages();
		$response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages($total);
		$response->records = $total;
		$sort_array = $filter->getFilterData();
		if ($sort_array[ 'sort' ] == 'sort_order') {
			$sort_array[ 'sort' ] = 'viewed';
		}
		$results = $this->model_tool_message_manager->getMessages($sort_array);

		$i = 0;
		foreach ($results as $result) {

			$response->rows [ $i ] [ 'id' ] = $result [ 'msg_id' ];


			switch ($result[ 'status' ]) {
				case 'E':
					$status = $this->language->get('entry_error');
					$response->userdata->classes[ $result [ 'msg_id' ] ] = 'warning';
					break;
				case 'W':
					$status = $this->language->get('entry_warning');
					$response->userdata->classes[ $result [ 'msg_id' ] ] = 'attension';
					break;
				case 'N':
				default:
					$status = $this->language->get('entry_notice');
					$response->userdata->classes[ $result [ 'msg_id' ] ] = 'success';
					break;
			}


			$style = !$result [ 'viewed' ] ? 'style="font-weight: bold;"' : '';
			$link = '<a href="JavaScript:void(0);" ' . $style . ' onclick="show_popup(' . $result [ 'msg_id' ] . ')">' . $result [ 'title' ] . '</a>';

			$action = '<a class="btn_action" href="JavaScript:void(0);" onclick="show_popup(' . $result [ 'msg_id' ] . ')" title="' . $this->language->get('text_edit') . '">' .
					'<img src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_view.png" alt="' . $this->language->get('text_edit') . '" /></a>';
			$action .= '<a class="btn_action" href="JavaScript:void(0);" onclick="msg_id = ' . $result [ 'msg_id' ] . ';  delete_msg();" title="' . $this->language->get('text_delete') . '">' .
					'<img src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_delete.png" alt="' . $this->language->get('text_delete') . '" /></a>';

			$response->rows [ $i ] [ 'cell' ] = array( $status,
				$link,
				$result [ 'create_date' ],
				$action );

			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));

	}

	public function update() {

		if (!$this->user->canModify('listing_grid/message_grid')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/message_grid'),
					'reset_value' => true
				));
		}

		$this->loadModel('tool/message_manager');

		if ($this->request->post [ 'oper' ] == 'del') {
			$ids = explode(',', $this->request->post [ 'id' ]);
			if ($ids) {
				foreach ($ids as $msg_id) {
					$this->model_tool_message_manager->deleteMessage($msg_id);
				}
			}
		} elseif ($this->request->get [ 'oper' ] == 'show') {
			$msg_id = $this->request->get [ 'id' ];
			if ($msg_id) {
				$message = $this->model_tool_message_manager->getMessage($msg_id);
				if ($message) {
					$this->loadLanguage('tool/message_manager');
					$message [ "message" ] = str_replace("#link-text#", $this->language->get('text_linktext'), $message [ "message" ]);
					switch ($message [ 'status' ]) {
						case 'W' :
							$message [ 'status' ] = $this->language->get('text_warning');
							break;
						case 'E' :
							$message [ 'status' ] = $this->language->get('text_error');
							break;
						default :
							$message [ 'status' ] = $this->language->get('text_notice');
							break;
					}
				} else {
					$message [ "message" ] = $this->language->get('text_not_found');
				}
			}
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($message));
		}

	}

	public function getNotify() {
		$message [ 'msg_cnt' ] = $this->session->data [ 'new_messages' ];
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($message));
	}
}