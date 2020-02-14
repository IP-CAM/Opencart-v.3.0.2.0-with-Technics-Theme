<?php
class ControllerProductCategory extends Controller {

// technics
	public function prepareAttr($attribute_groups) {

		$attributes = array();
		$output = array();

		foreach ($attribute_groups as $attribute_group) {
			foreach ($attribute_group['attribute'] as $attribute) {
				$attributes[] = $attribute;
			}
		}

		if (!empty($attributes)) {
			$perColunmCount = 3; 
            $i = 0;
            $k = 0;
            foreach ($attributes as $attribute) {   
              $output[$k][$i] = $attribute;
              $i++;
              if ($i == $perColunmCount && $k != 1) { 
                $k++;
              } 
              if ($i > 5) {
                break;
              }             
            }
		}
		return $output;
	}
// technics end
            
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->load->model('setting/setting');
            

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}


//technics start
		if (isset($this->session->data['change_currency'])) {
			unset($this->session->data['change_currency']);
			unset($this->request->get['min_price']);
			unset($this->request->get['max_price']);
		}

		if (isset($this->request->get['min_price'])) {
			$min_price = $this->request->get['min_price'];
		} else {
			$min_price = '';
		}

		if (isset($this->request->get['max_price'])) {
			$max_price = $this->request->get['max_price'];
		} else {
			$max_price = '';
		}
		$data['language_id'] = $this->config->get('config_language_id');
//technics stop
            
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

//technics start
			if (isset($this->request->get['min_price'])) {
				$url .= '&min_price=' . $this->request->get['min_price'];
			} 

			if (isset($this->request->get['max_price'])) {
				$url .= '&max_price=' . $this->request->get['max_price'];
			} 
			$cat_url = '';
//technics stop
            

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$id = 0;
            

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],

						'breadList' => $this->breadList($id),// technics
						'cat_id' => $id,// technics
            
						
						'href' => $this->url->link('product/category', 'path=' . $path ) // technics
            
					);
				}

				$id = $path_id;
            
			}

//technics start
			$cat_url = $this->url->link('product/category', 'path=' . $path . '_' . $category_id);
//technics stop
            
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['heading_title'] = $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			$this->load->language('extension/theme/technics');
			$data['schema'] = $this->config->get('theme_technics_schema');
			$data['product_detail'] = $this->config->get('theme_technics_product_detail');
			$data['category_time'] = $this->config->get('theme_technics_category_time');
			$data['category_sorts'] = $this->config->get('theme_technics_category_sorts');
			$data['category_limits'] = $this->config->get('theme_technics_category_limits');
			$data['category_categories'] = $this->config->get('theme_technics_category_categories');
			
			$data['button_fastorder_sendorder'] = $this->language->get('button_technics_sendorder');
			$data['text_technics_buy_click'] = $this->language->get('text_technics_buy_click');
			$data['text_product_view_btn'] = $this->language->get('text_product_view_btn');
			$data['time_text_1'] = $this->language->get('text_time_text_1');
			$data['time_text_2'] = $this->language->get('text_time_text_2');
			$data['text_attributes'] = $this->language->get('text_attributes');
			$data['text_description'] = $this->language->get('text_description');
			
			$data['pс_view'] = $this->config->get('theme_technics_pс_view');
			$data['mobile_view'] = $this->config->get('theme_technics_mobile_view');
			if ($this->config->get('theme_technics_buy_click_pdata')) {
				$this->load->language('extension/theme/technics');
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('theme_technics_buy_click_pdata'));

				if ($information_info) {
					$data['text_technics_pdata'] = sprintf($this->language->get('text_technics_pdata'), $this->language->get('button_technics_sendorder'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('theme_technics_buy_click_pdata'), true), $information_info['title'], $information_info['title']);
				} else {
					$data['text_technics_pdata'] = '';
				}
			} else {
				$data['text_technics_pdata'] = '';
			}
			
			$data['buy_click'] = array();
			if($this->config->get('theme_technics_buy_click')){
				$data['buy_click'] = $this->config->get('theme_technics_buy_click');
				if ($this->customer->isLogged()) {
					$this->load->model('account/customer');
					$data['customer_info'] = $this->model_account_customer->getCustomer($this->customer->getId());
				}
			}			
			$this->load->language('checkout/checkout');
			$data['entry_firstname'] = $this->language->get('entry_firstname');
			$data['entry_lastname'] = $this->language->get('entry_lastname');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_telephone'] = $this->language->get('entry_telephone');
			$data['entry_comment'] = $this->language->get('entry_comment');
			$data['lazyload'] = $this->config->get('theme_technics_lazyload');
			// technics end
            

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				
				$data['thumb'] = $this->config->get('theme_technics_image_category_resize') ? $this->model_tool_image->technics_resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')) : $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));// technics
            
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

//technics start
			if (isset($this->request->get['min_price'])) {
				$url .= '&min_price=' . $this->request->get['min_price'];
			} 

			if (isset($this->request->get['max_price'])) {
				$url .= '&max_price=' . $this->request->get['max_price'];
			} 
//technics stop
            

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);


//technics start
				if(!$this->config->get('theme_technics_subcategory')){
					unset($filter_data['filter_sub_category']);
				}
//technics stop
            
				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),

					// technics
					'thumb' => $this->config->get('theme_technics_image_category_resize') ? $this->model_tool_image->technics_resize(($result['image']=='' ? 'no_image.png' : $result['image']), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'),  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')) : $this->model_tool_image->resize(($result['image']=='' ? 'no_image.png' : $result['image']), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'),  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height')),
					// technics end
            
					
'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id']) //technics
            
				);
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,

				'min_price'    		 => $min_price,//technics
				'max_price'    		 => $max_price,//technics
            
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

//technics start
			if($this->config->get('theme_technics_subcategory')){
				$filter_data['filter_sub_category'] = true;
			}
				if (count($results) == 1) {
					$colmd = 8; 
				}elseif ($this->config->get('theme_technics_category_categories') == 4) {
					$colmd = 4;
				}else{
					$colmd = 6;
				}
				$data['colmd'] = $colmd;
//technics stop
            

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);

			// technics
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}	

			$currR = $this->currency->getSymbolRight($this->session->data['currency']);
			$currL = $this->currency->getSymbolLeft($this->session->data['currency']);

			if($currR){
				$data['currencydata'] = "R_" . $currR ;
			}else{
				$data['currencydata'] = "L_" . $currL ;
			}
		
			$data['url'] = $url;
			$isDateTime = false;
			$data['path'] = $this->request->get['path'];
			$data['category_id'] = $category_id;
			$data['cat_url'] = $cat_url;
			$data['product_total'] = $product_total;
			$data['minPrice'] = $this->model_catalog_category->getMinPrice($category_id);
			$data['maxPrice'] = $this->model_catalog_category->getMaxPrice($category_id);
			$data['currency'] = $this->session->data['currency'];
            

			// technics
			$this->load->model('extension/module/technics');
			$labelsInfo = array();
			if($this->config->get('theme_technics_label')){
				$labelsInfo = $this->config->get('theme_technics_label');
			}
			$data['labelsinfo'] = $labelsInfo ;
			$data['language_id'] = $this->config->get('config_language_id');
			$newest = array();
			$isDateTime = false;
			$sales = false;
			if(isset($labelsInfo['new']['period']) && $labelsInfo['new']['status']){
				$newest = $this->model_catalog_product->getNewestProducts($labelsInfo['new']['period']);	
			}
			if(isset($labelsInfo['sale']['status']) && $labelsInfo['sale']['status']){
				$sales = true;				
			}
		    if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		       $hits = $this->model_extension_module_technics->getHitProducts($labelsInfo['hit']['period'],$labelsInfo['hit']['qty']);
		    }
			// technics end
            

			foreach ($results as $result) {
				if ($result['image']) {
					
					$image = $this->config->get('theme_technics_image_product_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));// technics
            
				} else {
					
					$image = $this->config->get('theme_technics_image_product_resize') ? $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')) : $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));// technics
            
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}


				// technics
				$extraImages = array();
				if ($this->config->get('theme_technics_images_status')) {
					$images = $this->model_catalog_product->getProductImages($result['product_id']);
					foreach($images as $imageX){
						$extraImages[] = $this->config->get('theme_technics_image_product_resize') ? $this->model_tool_image->technics_resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')) : $this->model_tool_image->resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					}
				}
				
				if (in_array($result['product_id'], $newest)) {
					$isNewest = true;
				} else {
					$isNewest = false;
				}			
								
				if ($result['quantity'] <= 0) {
					$stock = $result['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $this->language->get('text_instock') . ': ' . $result['quantity'] . ' ' . $this->language->get('text_technics_cart_quantity');
				} else {
					$stock = $this->language->get('text_instock');
				}	
				
				if ($result['quantity'] <= 0 && !$this->config->get('config_stock_checkout')) {
					$buy_btn = $result['stock_status'];
				} else {
					$buy_btn = '';
				}
				
				if ($this->config->get('theme_technics_manufacturer') == 1) {
					$manufacturer = $result['model'];
				} elseif ($this->config->get('theme_technics_manufacturer') == 2) {
					$manufacturer = $result['manufacturer'];
				} else {
					$manufacturer = false;
				}

				$discount = '';
				if($sales && $special){
					$special_date_end = false;
					$action = $this->model_catalog_product->getProductActions($result['product_id']);
					if ($action['date_end'] != '0000-00-00') {
						$special_date_end = $action['date_end'];
					}		

					if($labelsInfo['sale']['extra'] == 1){
						$discount = round((($result['price'] - $result['special'])/$result['price'])*100);
						$discount = $discount. ' %';

					}
					if($labelsInfo['sale']['extra'] == 2){
						$discount = $this->currency->format($this->tax->calculate(($result['price'] - $result['special']), $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					}					
				} else {
					$special_date_end = false;
				}
				$catch = false;
				$nocatch = false;
				if (isset($labelsInfo['catch']) && $labelsInfo['catch']['status'] && $result['quantity'] <= $labelsInfo['catch']['qty']) {
					if($result['quantity'] > 0){
						$catch = $labelsInfo['catch']['name'][$this->config->get('config_language_id')];
					}else{
						$catch = $labelsInfo['catch']['name1'][$this->config->get('config_language_id')];
						$nocatch = true;
					}
				}

				$popular = false;
				if (isset($labelsInfo['popular']) && $labelsInfo['popular']['status'] && $result['viewed'] >= $labelsInfo['popular']['views']) {
					$popular = $labelsInfo['popular']['name'][$this->config->get('config_language_id')];
				}

				$hit = false;
				if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
					if (isset($hits[$result['product_id']])) {
						$hit = $labelsInfo['hit']['name'][$this->config->get('config_language_id')];
					}
				}

				$options = array();

				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$priceOp = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$priceOp = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
								'price'                   => $priceOp,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
						$isDateTime = true;
					}
					
					$options[] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}

				$attributes_info = $this->model_catalog_product->getProductAttributes($result['product_id']);

				$attribute_groups = $this->prepareAttr($attributes_info);


				// technics end
            
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],

					'manufacturer'  => $manufacturer,// technics
					'quantity'        => $result['quantity'],// technics
					'stock'        => $stock,// technics
					'images'       => $extraImages,// technics	
					'isnewest'       => $isNewest,// technics
					'sales'       => $sales,// technics
					'discount'       => $discount,// technics
					'catch'       => $catch,// technics
					'nocatch'       => $nocatch,// technics
					'popular'	  => $popular,// technics
					'hit'	 	  => $hit,// technics
					'attribute_groups'	 	  => $attribute_groups,// technics
					'buy_btn'	  => $buy_btn,// technics
					'reward'      => $result['reward'],// technics
					'special_date_end'      => $special_date_end,// technics
            
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}


			// technics
			if ($isDateTime) {
				$data['datepicker'] = $this->language->get('code');
				if ($this->config->get('theme_technics_bootstrap_ver')) { //bootstrap4
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/moment/moment.min.js');
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/moment/moment-with-locales.min.js');
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/js/tempusdominus-bootstrap-4.min.js');
					$this->document->addStyle('catalog/view/javascript/technics/datetimepicker/css/tempusdominus-bootstrap-4.min.css');
					$this->document->addStyle('catalog/view/javascript/technics/datetimepicker/icon/style.css');	
				} else { //bootstrap3
					$this->document->addScript("catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js");
					$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
					$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
					$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
				}				
			}
			// technics end
            
			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

//technics start
			if (isset($this->request->get['min_price'])) {
				$url .= '&min_price=' . $this->request->get['min_price'];
			} 

			if (isset($this->request->get['max_price'])) {
				$url .= '&max_price=' . $this->request->get['max_price'];
			} 
//technics stop
            

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');


// technics
			$data['sort_title'] = $this->language->get('text_default');
			foreach ($data['sorts'] as $value) {
				if ($value['value'] == $sort . '-' . $order) {
					$data['sort_title'] = $value['text'];
				}
			}

			if ($this->config->get($this->config->get('theme_technics_config_captcha_fo') . '_status')) {
				$data['captcha_fo'] = $this->load->controller('extension/captcha/' . $this->config->get('theme_technics_config_captcha_fo'));
			} else {
				$data['captcha_fo'] = '';
			}
			
		$data['viewSub'] = '4';	
		if ($this->config->get('theme_technics_pс_view')) {
			$viewSub = explode('_',$this->config->get('theme_technics_pс_view'));          	
		    if(isset($viewSub[1])){
		        $viewSub = $viewSub[1];
		        $data['viewSub'] = $viewSub; 
		    }
		}
		
		if (isset($this->request->post['view'])) {
			$view = $this->request->post['view'];
			$viewM = $this->request->post['view'];
		}elseif(isset($this->session->data['view'])){
			$view = $this->session->data['view'];
			$viewM = $this->session->data['view'];
		} else {
			$view = $this->config->get('theme_technics_pс_view');
			$viewM = $this->config->get('theme_technics_mobile_view');
		}
			if (isset($_COOKIE["ismobile"]) && $_COOKIE["ismobile"] == 1){ 
				$this->session->data['view'] = $viewM;
			}else{
				$this->session->data['view'] = $view;
			}
		$data['viewLayer'] = $view;
		$data['viewLayerM'] = $viewM;
		$data['view'] = $view;

				if(strpos($view,'grid') !== false){	
					$viewSub = explode('_',$view);




				  	$data['view'] = 'grid';		          	
		          	if(isset($viewSub[1])){
		            	$viewSub = $viewSub[1];
		            	$data['viewSub'] = $viewSub; 
		          	}
				}
				
			if (isset($this->request->post['view'])) {
			
				$this->response->setOutput($this->load->view('product/category_'.$data['view'], $data));
				return ;
			}elseif(isset($this->request->get['popupdetail'])){
				
				$this->response->setOutput($this->load->view('product/category_popup', $data));
				return ;			
			}
			$data['buyclick_form'] = $this->load->view('product/buyclick_form', $data);
			$data['productsview'] = $this->load->view('product/category_'.$data['view'], $data);

// technics end
            
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/category', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/category', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			
// technics
		$this->load->language('extension/theme/technics');
		$data['category_id'] = $category_id;
		if (is_file(DIR_IMAGE . $this->config->get('theme_technics_logo_404'))) {
			$data['logo_404'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . 'image/' . $this->config->get('theme_technics_logo_404');
		} else {
			$data['logo_404'] = '';
		}
		$data['text_404'] = sprintf($this->language->get('text_404'), $this->url->link('information/contact', '', true), $this->url->link('product/search', '', true), $this->url->link('common/home', '', true));
		$this->response->setOutput($this->load->view('error/404', $data));
// technics end					
            
		}
	}

// technics
	public function breadList($category_id) {
		$this->load->model('catalog/category');
		$data = array();
		$categories = $this->model_catalog_category->getCategories($category_id);
		foreach($categories as $category){
			$data[] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/category', 'path=' . $category['category_id'])
			);
		}
		return $data;
	}
	public function breadlistcr() {

		$this->load->model('catalog/category');
		$category_id = $this->request->get['cat_id'];
		$data['breadLists'] = array();
		$categories = $this->model_catalog_category->getCategories($category_id);
		foreach($categories as $category){
			$data['breadLists'][] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/category', 'path=' . $category['category_id'])
			);
		}
		$this->response->setOutput($this->load->view('product/bread_popup',$data));
	}
	
	public function popupdetail() {
		$this->load->language('product/category');
		
		$this->load->language('extension/theme/technics');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->load->model('setting/setting');

		$this->load->model('extension/module/technics');
		
		$data['review_status'] = $this->config->get('config_review_status');
		
		$data['category_time'] = $this->config->get('theme_technics_category_time');
		$data['time_text_1'] = $this->language->get('text_time_text_1');
		$data['time_text_2'] = $this->language->get('text_time_text_2');

		$filter_ocfilter = '';
		
		$prodkey = false;

			$data['button_fastorder_sendorder'] = $this->language->get('button_technics_sendorder');
			$data['text_technics_buy_click'] = $this->language->get('text_technics_buy_click');
			
		$data['text_technics_popup_link'] = $this->language->get('text_technics_popup_link');
		$data['text_technics_popup_link_more'] = $this->language->get('text_technics_popup_link_more');
		$data['text_technics_popup_upload'] = $this->language->get('text_technics_popup_upload');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['opt_type'] = $this->config->get('theme_technics_product_opt_type');

			$data['buy_click'] = array();
			if($this->config->get('theme_technics_buy_click')){
				$data['buy_click'] = $this->config->get('theme_technics_buy_click');
			}


		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		if (isset($this->request->get['min_price'])) {
			$min_price = $this->request->get['min_price'];
		} else {
			$min_price = '';
		}

		if (isset($this->request->get['max_price'])) {
			$max_price = $this->request->get['max_price'];
		} else {
			$max_price = '';
		}

		
			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			if (isset($this->request->get['popuptype'])) {
				$url .= '&popuptype=' . $this->request->get['popuptype'];
			}
			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}
			
			
		if (isset($this->request->get['prod_id'])) {
			$product_id = $this->request->get['prod_id'];
		}		
		
		$data['language'] = $this->session->data['language'];

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}
		
		if (isset($this->request->get['popuppath'])) {

			$path = '';

			$parts = explode('_', (string)$this->request->get['popuppath']);

			$category_id = (int)array_pop($parts);

		} else {
			$category_id = 0;
		}
		
		$data['text_select'] = $this->language->get('text_select');		
//search
		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['search'])) {
			$tag = $this->request->get['search'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}
		
		if (isset($this->request->get['popuptype'])) {
			$popuptype = $this->request->get['popuptype'];
		} elseif(isset($this->request->get['popuppath'])) {
			$popuptype = 'main';
		}else{
			$popuptype = 'modules';
		}

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}

		if (isset($this->request->get['sub_category'])) {
			$sub_category = $this->request->get['sub_category']; 
		} else {
			$sub_category = '';
		}

		if (isset($this->request->get['popuppath'])) {
			$popuppath = $this->request->get['popuppath'];
		} else {
			$popuppath = '';
		}
		
		$data['page'] = $page;
//search
			$data['products'] = array();

			$filter_data = array(
				'filter_name'         => $search,
				'filter_tag'          => $tag,
				'filter_description'  => $description,
				'filter_sub_category' => $sub_category,
				'filter_category_id' => $category_id,
				'filter_manufacturer_id' => $manufacturer_id,
				'min_price'    		 => $min_price,
				'max_price'    		 => $max_price,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

//technics start
			if($this->config->get('theme_technics_subcategory')){
				$filter_data['filter_sub_category'] = true;
			}
//technics stop

//			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

//			$results = $this->model_catalog_product->getProducts($filter_data);

		
		switch ($popuptype) { 
		case 'main':
			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
			$results = $this->model_catalog_product->getProducts($filter_data);
			break;
		case 'special':
			$product_total = $this->model_catalog_product->getTotalProductSpecials();
			$results = $this->model_catalog_product->getProductSpecials($filter_data);
			break;
		case 'modules':
			$product_total = 1;
			$results[] = $this->model_catalog_product->getProduct($product_id);
			break;
		}

		$data['popuptype'] = $popuptype;

			$data['products'] = array();
			$labelsInfo = array();
			if($this->config->get('theme_technics_label')){
				$labelsInfo = $this->config->get('theme_technics_label');
			}
			$data['labelsinfo'] = $labelsInfo ;
			$data['language_id'] = $this->config->get('config_language_id');
			$newest = array();
			$sales = false;
			if(isset($labelsInfo['new']['period']) && $labelsInfo['new']['status']){
				$newest = $this->model_catalog_product->getNewestProducts($labelsInfo['new']['period']);	
			}
			if(isset($labelsInfo['sale']['status']) && $labelsInfo['sale']['status']){
				$sales = true;				
			}
		    if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		       $hits = $this->model_extension_module_technics->getHitProducts($labelsInfo['hit']['period'],$labelsInfo['hit']['qty']);
		    }
			$k = 0;	
			$data['activElem'] = 0;

			foreach ($results as $result) {


				$productOut = array(
					'product_id'  => $result['product_id'],
					'thumb'       => '',
					'popup'       => '',
					'additional'  => '',
					'name'        => $result['name'],
					'manufacturer' => $result['manufacturer'],
					'model' 	  => $result['model'],
					'quantity'     => $result['quantity'],
					'stock'        => '',
					'images'       => array(),
					'isnewest'       => '',
					'options'       => array(),
					'sales'       => '',
			        'catch'      	=> '',
			        'nocatch'       => '',
			        'popular'   	=> '',
			        'hit'     		=> '',
			        'buy_btn'   	=> '',
					'discount'    => '',
					'customTabs'    => array(),
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'special_date_end' => false,
					'price'       => '',
					'special'     => '',
					'tax'         => '',
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => 0,
					'href'        => $this->url->link('product/category/popupdetail',  'popuppath=' . $popuppath . '&prod_id=' . $result['product_id'] . $url)
				);



				if ($result['image']) {
					$productOut['additional'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));

					$productOut['thumb'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height'));

					$productOut['popup'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				} else {
					$productOut['additional'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')) : $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));

					$productOut['thumb'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height')) : $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height'));

					$productOut['popup'] = $this->config->get('theme_technics_image_product_detail_resize') ? $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) : $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				}
			  if ($result['product_id'] == $product_id) {
			
				$extraImages = array();				
				$images = $this->model_catalog_product->getProductImages($result['product_id']);
				foreach($images as $imageX){
					$extraImages[] = array(
						'popup' =>  $this->config->get('theme_technics_image_popup_resize') ? $this->model_tool_image->technics_resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) : $this->model_tool_image->resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),	            
						
						'thumb' => $this->config->get('theme_technics_image_additional_resize') ? $this->model_tool_image->technics_resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height')) : $this->model_tool_image->resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_detail_height')),

						'additional' => $this->config->get('theme_technics_image_additional_resize') ? $this->model_tool_image->technics_resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')) : $this->model_tool_image->resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
					);
				}			

				if ($result['quantity'] <= 0) {
					$stock = $result['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $this->language->get('text_instock') . ': ' . $result['quantity'] . ' ' . $this->language->get('text_technics_cart_quantity');
				} else {
					$stock = $this->language->get('text_instock');
				}				
				if (in_array($result['product_id'], $newest)) {
					$isNewest = true;
				} else {
					$isNewest = false;
				}			
		        $catch = false;
		        if (isset($labelsInfo['catch']) && $labelsInfo['catch']['status'] && $result['quantity'] <= $labelsInfo['catch']['qty']) {
		          if($result['quantity'] > 0){
		            $catch = $labelsInfo['catch']['name'][$this->config->get('config_language_id')];
		          }else{
		            $catch = $labelsInfo['catch']['name1'][$this->config->get('config_language_id')];
		          }
		        }

				$catch = false;
				$nocatch = false;
				if (isset($labelsInfo['catch']) && $labelsInfo['catch']['status'] && $result['quantity'] <= $labelsInfo['catch']['qty']) {
					if($result['quantity'] > 0){
						$catch = $labelsInfo['catch']['name'][$this->config->get('config_language_id')];
					}else{
						$catch = $labelsInfo['catch']['name1'][$this->config->get('config_language_id')];
						$nocatch = true;
					}
				}

		        $popular = false;
		        if (isset($labelsInfo['popular']) && $labelsInfo['popular']['status'] && $result['viewed'] >= $labelsInfo['popular']['views']) {
		          $popular = $labelsInfo['popular']['name'][$this->config->get('config_language_id')];
		        }

		        $hit = false;
		        if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		          if (isset($hits[$result['product_id']])) {
		            $hit = $labelsInfo['hit']['name'][$this->config->get('config_language_id')];
		          }
		        }			


				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$href = $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url );
					
				if ($result['quantity'] <= 0 && !$this->config->get('config_stock_checkout')) {
					$buy_btn = $result['stock_status'];
				} else {
					$buy_btn = '';
				}
				
				$manufacturer = $result['manufacturer'];
				
				$model = $result['model'];
				
				$manufacturer_url = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']); 

				$discount = '';
				$special_date_end = false;
				if($sales && $special){
					$special_date_end = false;
					$action = $this->model_catalog_product->getProductActions($result['product_id']);
					if ($action['date_end'] != '0000-00-00') {
						$special_date_end = $action['date_end'];
					}	

					if($labelsInfo['sale']['extra'] == 1){
						$discount = round((($result['price'] - $result['special'])/$result['price'])*100);
						$discount = $discount. ' %';
					}
					if($labelsInfo['sale']['extra'] == 2){
						$discount = $this->currency->format($this->tax->calculate(($result['price'] - $result['special']), $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					}					
				}	

				$customTabs = $this->model_extension_module_technics->getFields4Product($result['product_id']);
				
				$options = array();

				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();
					$isImage = false;
					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$priceOp = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
							} else {
								$priceOp = false;
							}

							if ($option_value['image']) {
								$isImage = true;
							}
						

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
								'price'                   => $priceOp,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}
					
					if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
						$isDateTime = true;
					}
					
					$options[] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'isimage'              => $isImage, 						
						'required'             => $option['required']
					);
				}

				if (isset($customTabs['video']) && $result['product_id'] == $product_id) {
					$newImages = array();
					foreach ($extraImages as $keyImg => $eximage) {
						if ($keyImg == 2) {
							$video = current($customTabs['video']);
							$newImages[] = array(
								'thumb'		 => $video['description'],
								'popup'		 => $video['description'],
								'additional' => $this->language->get('text_show_video'),
								'isvideo'	 => '1'
							);
						}

						$newImages[] = $eximage;

					}
					$extraImages = $newImages;
				}

				$productOut['images'] = $extraImages;
				$productOut['isnewest'] = $isNewest;
				$productOut['manufacturer'] = $manufacturer;
				$productOut['manufacturer_url'] = $manufacturer_url;
				$productOut['model'] = $model;
				$productOut['options'] = $options;
				$productOut['sales'] = $sales;
				$productOut['catch'] = $catch;
				$productOut['nocatch'] = $nocatch;
				$productOut['popular'] = $popular;
				$productOut['stock'] = $stock;
				$productOut['hit'] = $hit;
				$productOut['buy_btn'] = $buy_btn;
				$productOut['discount'] = $discount;
				$productOut['customTabs'] = $customTabs;
				$productOut['price'] = $price;
				$productOut['special'] = $special;
				$productOut['special_date_end'] = $special_date_end; 
				$productOut['tax'] = $tax;
				$productOut['href'] = $href;
				$productOut['optMode'] = $this->config->get('theme_lightshop_product_opt_select');
				$productOut['prep_options'] = $this->doOptionColumns($options);

			  }
				$data['products'][] = $productOut; 

				if(isset($product_id) && $result['product_id'] == $product_id){
//					$prodkey = count($data['products'])-1;
					$data['activElem'] = $k;
				}
				$k++;
			}
/*			
		if(isset($prodkey) && ($prodkey >= count($data['products'])-3)){			
			$data['isLast'] = 1;
		}
		$data['datepicker'] = $this->language->get('code');	

		if(!isset($product_id)){
			$prodkey = 0;
			$product_id  = current(reset($data['products']));
		}	
		$data['prodkey'] = $prodkey;		
		$data['minVisKey'] = $prodkey - 3;	

		if($page > 1){
			$data['prevLink'] = HTTP_SERVER . '?' .$url . '&page=' . ($page-1);
		}		

		if($page < ceil($product_total/$limit)){
			$data['nextLink'] = HTTP_SERVER . '?' .$url . '&page=' . ($page+1);
		}		
*/		


		$data['product_id'] = $product_id;
		
		$data['popup_link'] = $this->url->link('product/product', 'product_id=' . $product_id);
		
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = HTTPS_SERVER . '?' .$url . '&page={page}';

			$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('product/category_popup', $data));
			


	}	

	public function doOptionColumns($options){
		$optionColumns = array();
		$column = $this->config->get('theme_technics_product_opt_select');
		if (!$column) {
			$column = 2;
		}

		$qtyPerColumn = (int)(count($options)/$column);
        $i = 0;
        $k = 0;
 
		foreach ($options as $key => $option) {
			$optionColumns[$k][$i] = $option;
	        $i++;
	        if ($i == $column) {  
	            $i = 0;
	            $k++;
	        }    				
		}

		return $optionColumns;
	}

	public function totalproducts() {
		$this->load->language('extension/theme/technics');
		$this->load->model('catalog/product');
		$json = array();


		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['min_price'])) {
			$min_price = $this->request->get['min_price'];
		} else {
			$min_price = '';
		}

		if (isset($this->request->get['max_price'])) {
			$max_price = $this->request->get['max_price'];
		} else {
			$max_price = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$category_id = (int)$this->request->get['filter_category_id'];
		} else {
			$category_id = 0;
		}

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_sub_category' => $this->config->get('theme_technics_subcategory'),//technics
				'min_price'    		 => $min_price,
				'max_price'    		 => $max_price,
				'filter_filter'      => $filter,
				'limit'     		 => '10000'
			);
			
//technics start
			if($this->config->get('theme_technics_subcategory')){
				$filter_data['filter_sub_category'] = true;
			}
//technics stop

		$json['total'] = $this->model_catalog_product->getTotalProducts($filter_data);
		$json['text_show'] = $this->language->get('text_technics_show');
		$json['text_products'] = $this->language->get('text_technics_products');
		$json['id'] = rand();

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

// technics end
            
}
