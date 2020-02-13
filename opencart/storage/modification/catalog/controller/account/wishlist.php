<?php
class ControllerAccountWishList extends Controller {
	public function index() {
		
		if (!$this->customer->isLogged() && !$this->config->get('theme_technics_wishlist')) {
            
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/wishlist');

		$this->load->language('extension/theme/technics'); // technics
            

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			// Remove Wishlist
			
			$this->model_account_wishlist->deleteWishlistLb($this->request->get['remove']);
            

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/wishlist'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/wishlist')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}


		$data['islogged'] = $this->customer->isLogged();//technics
            
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
            

		
		$results = $this->model_account_wishlist->getWishlistLb();// <--technics change this
		$data['button_compare'] = $this->language->get('button_compare');				
            

		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);

			if ($product_info) {

				// technics
				if ($this->config->get('theme_technics_image_wishlist_resize')) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
					$image = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				}
				} else {
            
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
					
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				}
            
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
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


					// technics
					
					$extraImages = array();				
					$images = $this->model_catalog_product->getProductImages($product_info['product_id']);
					foreach($images as $imageX){
						
						$extraImages[] = $this->config->get('theme_technics_image_product_resize') ? $this->model_tool_image->technics_resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')) : $this->model_tool_image->resize($imageX['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            
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
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'stock'      => $stock,

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
            
					'price'      => $price,
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
				);
			} else {
				
				$this->model_account_wishlist->deleteWishlistLb($result['product_id']);
            
			}
		}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}

	public function add() {
		$this->load->language('account/wishlist');

		$this->load->language('extension/theme/technics'); // technics
            

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			
			if ($this->customer->isLogged() || $this->config->get('theme_technics_wishlist')) {
            
				// Edit customers cart
				$this->load->model('account/wishlist');

				
				$this->model_account_wishlist->addWishlistLb($this->request->post['product_id']);//technics change this
            

				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				
				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlistLb());
            
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$this->session->data['wishlist'][] = $this->request->post['product_id'];

				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);

				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function getwish() {
		
		$data['islogged'] = false;
		
		if ($this->customer->isLogged()) {
			$data['islogged'] = true;
//			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', true);

//			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		

		$this->load->language('account/wishlist');
		$this->load->language('extension/theme/technics'); // technics

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');
		$data['text_islogged'] = sprintf($this->language->get('text_islogged'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('account/wishlist'));

		$json = array();

		$data['products'] = array();

		$results = $this->model_account_wishlist->getWishlistLb();//technics change this

		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);

			if ($product_info) {

				// technics
				if ($this->config->get('theme_technics_image_wishlist_resize')) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
					} else {
						$image = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
					}
					} else {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
					}
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

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
				);
				$data['count'] = count($data['products']);
			} 
		}

		$this->response->setOutput($this->load->view('account/wish_head', $data));
	}
            
}
