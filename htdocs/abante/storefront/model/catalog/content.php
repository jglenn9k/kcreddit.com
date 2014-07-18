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
class ModelCatalogContent extends Model {
	public function getContent($content_id) {
		$content_id = (int)$content_id;
		$cache = $this->cache->get('contents.content.'.$content_id, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );

		if(is_null($cache)){
			$cache = array();
			$sql = "SELECT DISTINCT i.content_id, id.*
					FROM " . DB_PREFIX . "contents i
					LEFT JOIN " . DB_PREFIX . "content_descriptions id
						ON (i.content_id = id.content_id
							AND id.language_id = '" . (int)$this->config->get('storefront_language_id') . "')";
			if((int)$this->config->get('config_store_id')){
				$sql .=	" LEFT JOIN " . DB_PREFIX . "contents_to_stores i2s ON (i.content_id = i2s.content_id)";
			}
			$sql .=	" WHERE i.content_id = '" . (int)$content_id . "' ";
			if((int)$this->config->get('config_store_id')){
				$sql .= " AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			}
			$sql .= " AND i.status = '1'";
			$query = $this->db->query($sql);

			if($query->num_rows){
				$cache = $query->row;
			}
			$this->cache->set('contents.content.'.$content_id, $cache, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		}
		return $cache;
	}
	
	public function getContents() {

		$output = $this->cache->get('contents', $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		if(is_null($output)){
			$sql = "SELECT i.*, id.*
					FROM " . DB_PREFIX . "contents i
					LEFT JOIN " . DB_PREFIX . "content_descriptions id
							ON (i.content_id = id.content_id
									AND id.language_id = '" . (int)$this->config->get('storefront_language_id') . "')";

			if((int)$this->config->get('config_store_id')){
				$sql .=	"LEFT JOIN " . DB_PREFIX . "contents_to_stores i2s ON (i.content_id = i2s.content_id)";
			}

			$sql .=	"WHERE i.status = '1' ";

			if((int)$this->config->get('config_store_id')){
				$sql .= " AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			}

			$sql .= "ORDER BY i.sort_order, LCASE(id.title) ASC";
			$query = $this->db->query($sql);

			if($query->num_rows){
				foreach($query->rows as $row){
						$output[$row['content_id']] = $row;
				}
			}
			$this->cache->set('contents',$output, $this->config->get('storefront_language_id'), $this->config->get('config_store_id') );
		}
		return $output;
	}
}
?>