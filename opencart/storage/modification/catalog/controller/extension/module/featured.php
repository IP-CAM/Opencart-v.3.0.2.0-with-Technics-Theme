<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		// technics
		$this->load->language('extension/theme/technics');
		$data['lazyload'] = $this->config->get('theme_technics_lazyload');
		$data['category_time'] = $this->config->get('theme_technics_category_time');
		// labels
			$this->load->model('extension/module/technics');
			$labelsInfo = array();
			if($this->config->get('theme_technics_label')){
				$labelsInfo = $this->config->get('theme_technics_label');
			}
			$data['language_id'] = $this->config->get('config_language_id');
			$newest = array();
			$sales = false;
			if(isset($labelsInfo['new']['period']) && $labelsInfo['new']['status']){
				$newest = $this->model_catalog_product->getNewestProducts($labelsInfo['new']['period']);			
			}
			if(isset($labelsInfo['sale']['status']) && $labelsInfo['sale']['status']){
				$sales = true;				
			}	
			$data['labelsinfo'] = $labelsInfo;		
		      if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		        $hits = $this->model_extension_module_technics->getHitProducts($labelsInfo['hit']['period'],$labelsInfo['hit']['qty']);
		      }	
		// labels	
		// technics end				
            

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}


					// technics
					
					$extraImages = array();				
					$images = $this->model_catalog_product->getProductImages($product_info['product_id']);
					foreach($images as $imageX){
						$extraImages[] = $this->model_tool_image->resize($imageX['image'], $setting['width'], $setting['height']);
					}
					
					if (in_array($product_info['product_id'], $newest)) {
						$isNewest = true;
					} else {
						$isNewest = false;
					}
					
					$discount = '';
					if($sales && $special){
						$special_date_end = false;
						$action = $this->model_catalog_product->getProductActions($product_info['product_id']);
						if ($action['date_end'] != '0000-00-00') {
							$special_date_end = $action['date_end'];
						}		

						if($labelsInfo['sale']['extra'] == 1){
							$discount = round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100);
							$discount = $discount. ' %';

						}
						if($labelsInfo['sale']['extra'] == 2){
							$discount = $this->currency->format($this->tax->calculate(($product_info['price'] - $product_info['special']), $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						}					
					} else {
						$special_date_end = false;
					}
				
					$catch = false;
					$nocatch = false;
					if (isset($labelsInfo['catch']) && $labelsInfo['catch']['status'] && $product_info['quantity'] <= $labelsInfo['catch']['qty']) {
						if($product_info['quantity'] > 0){
							$catch = $labelsInfo['catch']['name'][$this->config->get('config_language_id')];
						}else{
							$catch = $labelsInfo['catch']['name1'][$this->config->get('config_language_id')];
							$nocatch = true;
						}
					}

					$popular = false;
					if (isset($labelsInfo['popular']) && $labelsInfo['popular']['status'] && $product_info['viewed'] >= $labelsInfo['popular']['views']) {
						$popular = $labelsInfo['popular']['name'][$this->config->get('config_language_id')];
					}

					$hit = false;
					if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
						if (isset($hits[$product_info['product_id']])) {
							$hit = $labelsInfo['hit']['name'][$this->config->get('config_language_id')];
						}
					}			
					
					if ($this->config->get('theme_technics_manufacturer') == 1) {
						$manufacturer = $product_info['model'];
					} elseif ($this->config->get('theme_technics_manufacturer') == 2) {
						$manufacturer = $product_info['manufacturer'];
					} else {
						$manufacturer = false;
					}
					
					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$stock = $product_info['quantity'];
					} else {
						$stock = $this->language->get('text_instock');
					}	
					
					if ($product_info['quantity'] <= 0 && !$this->config->get('config_stock_checkout')) {
						$buy_btn = $product_info['stock_status'];
					} else {
						$buy_btn = '';
					}
				
					// technics end
            
					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

						'manufacturer'  => $manufacturer,// technics
						'quantity'        => $product_info['quantity'],// technics
						'stock'        => $stock,// technics
						'images'       => $extraImages,// technics	
						'isnewest'       => $isNewest,// technics
						'sales'       => $sales,// technics
						'discount'       => $discount,// technics
						'catch'       => $catch,// technics
						'nocatch'       => $nocatch,// technics
						'popular'	  => $popular,// technics
						'hit'	 	  => $hit,// technics
						'buy_btn'	  => $buy_btn,// technics
						'reward'      => $product_info['reward'],// technics
						'special_date_end'      => $special_date_end,// technics
            
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			
					// technics
			if(isset($setting['layout']) && strpos($setting['layout'],'column_') !== false){
				return $this->load->view('extension/module/featured_column', $data);
			}else{
				return $this->load->view('extension/module/featured', $data);
			}
					// technics end

            
		}
	}
}