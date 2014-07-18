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
class ControllerResponsesListingGridCategory extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('catalog/category');
		$this->loadModel('catalog/category');
		$this->loadModel('catalog/product');
        $this->loadModel('tool/image');

		//Prepare filter config
		$grid_filter_params = array('name');
	    $filter = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    $filter_data = $filter->getFilterData();
	    //Add custom params
	    $filter_data['parent_id'] = ( isset( $this->request->get['parent_id'] ) ? $this->request->get['parent_id'] : 0 );
	    $new_level = 0;
	    $leafnodes = array();
		//get all leave categories 
		$leafnodes = $this->model_catalog_category->getLeafCategories();
	    if ($this->request->post['nodeid'] ) {
	    	$filter_data = array();
	    	$filter_data['parent_id'] = (integer)$this->request->post['nodeid'];
			$new_level = (integer)$this->request->post["n_level"] + 1;
	    }
	    
	    $total = $this->model_catalog_category->getTotalCategories($filter_data);
	    $response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages( $total );
		$response->records = $total;
	    $response->userdata = (object)array('');
	    $results = $this->model_catalog_category->getCategoriesData($filter_data);

	    $i = 0;

	    $resource = new AResource('image');
	    foreach ($results as $result) {
		    $thumbnail = $resource->getMainThumb('categories',
			                                     $result['category_id'],
			                                     $this->config->get('config_image_grid_width'),
			                                     $this->config->get('config_image_grid_height'),true);

            $response->rows[$i]['id'] = $result['category_id'];
            $cnt = $this->model_catalog_category->getCategoriesData(array('parent_id'=>$result['category_id']),'total_only');
            //treegrid structure
            $name_lable = '';
            if ( $this->config->get('config_show_tree_data') ) {
            	$name_lable = '<label style="white-space: nowrap;">'.$result['basename'].'</label>';
            } else {
            	$name_lable = '<label style="white-space: nowrap;">'.(str_replace($result['basename'],'',$result['name'])).'</label>'
			     .$this->html->buildInput(array(
                    'name'  => 'category_description['.$result['category_id'].']['.$this->session->data['content_language_id'].'][name]',
                    'value' => $result['basename'],
				    'attr' => ' maxlength="32" '
                ));
            }
                   
			$response->rows[$i]['cell'] = array(
                $thumbnail['thumb_html'],
                $name_lable,
                $this->html->buildInput(array(
                    'name'  => 'sort_order['.$result['category_id'].']',
                    'value' => $result['sort_order'],
                )),
				$this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['category_id'].']',
                    'value' => $result['status'],
                    'style'  => 'btn_switch',
                )),
                $this->model_catalog_product->getProductsByCategoryId($result['category_id'], 'total_only'),
                $cnt
                .($cnt ?
                '&nbsp;<a class="btn_action btn_grid grid_action_expand" href="#" rel="parent_id='.$result['category_id'].'" title="'. $this->language->get('text_view') . '">'.
				'<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_expand.png" alt="'. $this->language->get('text_view') . '" /></a>'
                  :''), 
                 'action',
                 $new_level,
                 ( $filter_data['parent_id'] ? $filter_data['parent_id'] : NULL ),
                 ( $result['category_id'] == $leafnodes[$result['category_id']] ? true : false ),
                 false              
			);
			$i++;
		}
		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('catalog/product');
	    $this->loadModel('catalog/category');
		$this->loadLanguage('catalog/category');
		if (!$this->user->canModify('listing_grid/category')) {
			        $error = new AError('');
			        return $error->toJSONResponse('NO_PERMISSIONS_402',
			                                      array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/category'),
			                                             'reset_value' => true
			                                           ) );
		}

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$this->model_catalog_category->deleteCategory($id);
				}
				break;
			case 'save':
				$allowedFields = array('category_description', 'sort_order', 'status',);

				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					foreach ( $allowedFields as $field ) {
						$this->model_catalog_category->editCategory($id, array($field => $this->request->post[$field][$id]) );
					}
				}
				break;

			default:
				//print_r($this->request->post);

		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

    /**
     * update only one field
     *
     * @return void
     */
    public function update_field() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('catalog/category');
        if (!$this->user->canModify('listing_grid/category')) {
	        $error = new AError('');
	        return $error->toJSONResponse('NO_PERMISSIONS_402',
	                                      array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/category'),
	                                             'reset_value' => true
	                                           ) );
		}

        $this->loadModel('catalog/category');

	    if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $field => $value ) {
				$this->model_catalog_category->editCategory($this->request->get['id'], array($field => $value) );
			}
		    return;
	    }
		$language_id = $this->session->data['content_language_id'];
	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value ) {
            foreach ( $value as $k => $v ) {
	             if($field=='category_description'){
				    if ((strlen(utf8_decode($v[$language_id]['name'])) < 2) || (strlen(utf8_decode($v[$language_id]['name'])) > 32)) {
						$err = $this->language->get('error_name');
						$dd = new ADispatcher('responses/error/ajaxerror/validation',array('error_text'=>$err));
						return $dd->dispatch();
					}
			    }
				$this->model_catalog_category->editCategory($k, array($field => $v) );
            }
        }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}
?>