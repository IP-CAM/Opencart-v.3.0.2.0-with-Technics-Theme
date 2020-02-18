<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);


		// technics
		$this->load->language('extension/theme/technics');
		$data['text_technics_points'] = $this->language->get('text_technics_points');
		$data['schema'] = $this->config->get('theme_technics_schema');
		$data['soc_share_code'] = html_entity_decode($this->config->get('theme_technics_soc_share_code'), ENT_QUOTES, 'UTF-8');
		$data['soc_share_prod'] = $this->config->get('theme_technics_soc_share_prod');
		$data['optMode'] = $this->config->get('theme_technics_product_opt_select');
		$data['opt_price'] = $this->config->get('theme_technics_product_opt_price');
		$data['opt_type'] = $this->config->get('theme_technics_product_opt_type');
		$data['category_time'] = $this->config->get('theme_technics_category_time');
		$data['time_text_1'] = $this->language->get('text_time_text_1');
		$data['time_text_2'] = $this->language->get('text_time_text_2');
		$data['text_review'] = $this->language->get('text_review');
		$data['text_review_num_1'] = $this->language->get('text_review_num_1');
		$data['text_review_num_2'] = $this->language->get('text_review_num_2');
		$data['text_review_num_3'] = $this->language->get('text_review_num_3');
		$data['text_show_more'] = $this->language->get('text_show_more');
		$data['text_review_plus'] = $this->language->get('text_review_plus');
		$data['text_review_minus'] = $this->language->get('text_review_minus');
		$this->load->model('extension/module/technics');
		$data['lazyload'] = $this->config->get('theme_technics_lazyload');
		$isDateTime = false;
		// technics end

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$id = 0;


			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],

						'breadList' => $this->breadList($id),// technics
						'cat_id' => $id,// technics

						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}

				$id = $path_id;

			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

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
					'text' => $category_info['name'],

					'breadList' => $this->breadList($id),// technics
					'cat_id' => $id,// technics

					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

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

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');

			// technics
			$data['href'] = $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']);
			// technics end


			$data['heading_title'] = $product_info['name'];

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];

			// technics
			$data['technics_mpn'] = $product_info['mpn'];
			$data['schema_description'] = str_replace(array("\r\n", "\r", "\n"),' ', strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')));
			// technics

			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

//          Передача в шаблон таба "Полное описание"
            $allAttr = $this->model_catalog_product->getProductAttributes($data['product_id']);
            foreach ($allAttr as $attrGroup) {
                if ($attrGroup['attribute_group_id'] == '8') {
                    foreach ($attrGroup['attribute'] as $attr) {
                        if ($attr['attribute_id'] == '27') {
                            $data['custom_props_and_specifics'] = html_entity_decode($attr['text'], ENT_QUOTES, 'UTF-8');
                        }
                        if ($attr['attribute_id'] == '28') {
                            $data['custom_tech_props'] = html_entity_decode($attr['text'], ENT_QUOTES, 'UTF-8');
                        }
                        if ($attr['attribute_id'] == '29') {
                            $data['custom_additional_options'] = html_entity_decode($attr['text'], ENT_QUOTES, 'UTF-8');
                        }
                    }
                }
            }


			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {

				$data['stock'] = $this->language->get('text_instock') . ': ' . $product_info['quantity'] . ' ' . $this->language->get('text_technics_cart_quantity'); // technics

			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

// technics
			$data['currency'] = 'RUB';
			if (isset($this->session->data['currency'])) {
				$data['currency'] = $this->session->data['currency'];
			}

			$this->load->language('checkout/checkout');
			$data['entry_firstname'] = $this->language->get('entry_firstname');
			$data['entry_lastname'] = $this->language->get('entry_lastname');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_telephone'] = $this->language->get('entry_telephone');
			$data['entry_comment'] = $this->language->get('entry_comment');
			$this->load->language('extension/theme/technics');
			$this->load->model('catalog/information');
			if ($this->config->get('theme_technics_buy_click_pdata')) {
				$click_pdata = $this->model_catalog_information->getInformation($this->config->get('theme_technics_buy_click_pdata'));
				if ($click_pdata) {
					$data['text_click_pdata'] = sprintf($this->language->get('text_technics_pdata'), $this->language->get('button_technics_sendorder'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('theme_technics_buy_click_pdata'), true), $click_pdata['title'], $click_pdata['title']);
				} else {
					$data['text_click_pdata'] = '';
				}
			} else {
				$data['text_click_pdata'] = '';
			}
			if ($this->config->get('theme_technics_review_pdata')) {
				$review_pdata = $this->model_catalog_information->getInformation($this->config->get('theme_technics_review_pdata'));
				if ($review_pdata) {
					$data['text_review_pdata'] = sprintf($this->language->get('text_technics_pdata'), $this->language->get('button_continue'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('theme_technics_review_pdata'), true), $review_pdata['title'], $review_pdata['title']);
				} else {
					$data['text_review_pdata'] = '';
				}
			} else {
				$data['text_review_pdata'] = '';
			}

			$data['button_fastorder_sendorder'] = $this->language->get('button_technics_sendorder');

			$data['text_technics_products_text_more'] = $this->language->get('text_technics_products_text_more');
			$data['text_technics_short_descr'] = $this->language->get('text_technics_short_descr');
			$data['text_technics_products_review'] = $this->language->get('text_technics_products_review');

			$tempDesc = $this->descriptionExtra($data['description']);
			$data['description'] = $tempDesc['fulldesc'];
			if (!$this->config->get('theme_technics_product_short_descr')) {
				$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			}
			$data['short_descr'] = $this->config->get('theme_technics_product_short_descr');
			$data['zoom'] = $this->config->get('theme_technics_product_zoom');
			$data['p_related_view'] = $this->config->get('theme_technics_p_related_view');
			$data['shortdescription'] = $tempDesc['shortdesc'];
			$data['typeOptAtt'] = $this->config->get('theme_technics_product_att_select');
			$data['typeOptSelect'] = $this->config->get('theme_technics_product_opt_select');
			$data['typeOptCheckImg'] = $this->config->get('theme_technics_product_opt_checkbox_img');
			$data['typeOptRadioImg'] = $this->config->get('theme_technics_product_opt_radio_img');
			$data['store'] = $this->config->get('config_name');

			if ($product_info['quantity'] <= 0 && !$this->config->get('config_stock_checkout')) {
				$data['buy_btn'] = $product_info['stock_status'];
			} else {
				$data['buy_btn'] = '';
			}

			if ($this->config->get('theme_technics_image_popup_resize')) {
				if ($product_info['image']) {
					$data['popup'] = $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				} else {
					$data['popup'] = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				}
			} else {


			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {

					$data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));

			}


			}
			if ($this->config->get('theme_technics_image_thumb_resize')) {
				if ($product_info['image']) {
					$data['thumb'] = $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
					if ($this->config->get('theme_technics_og')) { //Technics added this
						$this->document->setOgImage($data['thumb']);
					} //Technics added this
				} else {
					$data['thumb'] = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));;
				}
			} else {

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {

					$data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
					if ($this->config->get('theme_technics_og')) { //Technics added this
						$this->document->setOgImage($data['thumb']);
					} //Technics added this

			}


			}
			if ($this->config->get('theme_technics_image_additional_resize')) {
				if ($product_info['image']) {
					$data['additional'] = $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
					if ($this->config->get('theme_technics_og')) { //Technics added this
						$this->document->setOgImage($data['thumb']);
					} //Technics added this
				} else {
					$data['additional'] = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));;
				}
			} else {
				if ($product_info['image']) {
					$data['additional'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
					if ($this->config->get('theme_technics_og')) { //Technics added this
						$this->document->setOgImage($data['thumb']);
					} //Technics added this
				} else {
					$data['additional'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
					if ($this->config->get('theme_technics_og')) { //Technics added this
						$this->document->setOgImage($data['thumb']);
					} //Technics added this
				}
			}
			$tab_video = array();
			$data['customTabs'] = $this->model_extension_module_technics->getFields4Product($this->request->get['product_id']);
			if (isset($data['customTabs']['video'])) {
				$tab_video = $data['customTabs']['video'];
			}

// technics end

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(

					'popup' =>  $this->config->get('theme_technics_image_popup_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),


					'thumb' => $this->config->get('theme_technics_image_additional_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
					'additional' => $this->config->get('theme_technics_image_additional_resize') ? $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')) : $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))

				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				$data['price_schema'] = number_format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), 0, '', '');// technics

			} else {
				$data['price'] = false;

				$data['price_schema'] = false;// technics

			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				$data['special_schema'] = number_format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), 0, '', '');// technics

			} else {
				$data['special'] = false;

				$data['special_schema'] = false;

			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],

					'date_end' => $discount['date_end'],// technics

					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				$isImage = false; //Lightshop


				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}


//technics
						if ($option_value['image']) {
							$isImage = true;
						}
//technics

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}


//technics
				if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$isDateTime = true;
				}
//technics

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],

					'isimage'              => $isImage,  //technics

					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}


			// technics
			if ($isDateTime) {
				if ($this->config->get('theme_technics_bootstrap_ver')) { //bootstrap4
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/moment/moment.min.js');// technics
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/moment/moment-with-locales.min.js');// technics
					$this->document->addScript('catalog/view/javascript/technics/datetimepicker/js/tempusdominus-bootstrap-4.min.js');// technics
					$this->document->addStyle('catalog/view/javascript/technics/datetimepicker/css/tempusdominus-bootstrap-4.min.css');// technics
					$this->document->addStyle('catalog/view/javascript/technics/datetimepicker/icon/style.css');// technics
				} else { //bootstrap3
					$this->document->addScript("catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js");
					$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
					$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
					$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
				}
			$data['datepicker'] = $this->language->get('code');// technics
			$data['date_format'] = $this->language->get('text_technics_date_format');// technics
			$data['datetime_format'] = $this->language->get('text_technics_datetime_format');// technics
			$data['time_format'] = $this->language->get('text_technics_time_format');// technics
			}

			if (isset($data['customTabs']['video'])) {
				$newImages = array();
				foreach ($data['images'] as $keyImg => $image) {
					if ($keyImg == 2) {
						$video = current($data['customTabs']['video']);
						$newImages[] = array(
							'thumb'		 => $video['description'],
							'popup'		 => $video['description'],
							'additional' => $this->language->get('text_show_video'),
							'isvideo'	 => '1'
						);
					}

					$newImages[] = $image;

				}
				$data['images'] = $newImages;
			}


			$data['prep_options'] = $this->doOptionColumns($data['options']);

			$data['quantity'] = $product_info['quantity'];
			$data['special_date_end'] = $data['discount_date_end'] = 0;

			if ($product_info['discount_date_end'] && $product_info['discount_date_end'] != '0000-00-00' ) {
				$data['discount_date_end'] = $product_info['discount_date_end'];
			}
			if ($data['special']) {
				$action = $this->model_catalog_product->getProductActions($this->request->get['product_id']);
				if ($action['date_end'] != '0000-00-00') {
					$data['special_date_end'] = $action['date_end'];
				}
			}
			$data['reviews_num'] = (int)$product_info['reviews'];


			$sint = array(
				0 => $this->language->get('text_review_num_2'),
				1 => $this->language->get('text_review_num_1'),
				2 => $this->language->get('text_review_num_3'),
				3 => $this->language->get('text_review_num_3'),
				4 => $this->language->get('text_review_num_3'),
				5 => $this->language->get('text_review_num_2')
			);

			$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
			$resultsLs = $this->model_catalog_review->getReviewsStatsByProductId($this->request->get['product_id']);
			for ($x=1; $x<6; $x++){
				$data['reviewsStats'][$x] = array(
					'rating'     => $x,
					'count'     => 0,
					'percent'     => 0,
					'text'     => $this->language->get('text_review_num_2'),
					'link'     => 'product_id=' . $this->request->get['product_id'] . '&rating=' . $x
				);
			}

			foreach ($resultsLs as  $ratingCount) {
				if ($review_total) {
					$percent = round(($ratingCount['totall']*100)/$review_total);
				}else{
					$percent = 0;
				}

				if ($ratingCount['totall'] < 5) {
					$text = $sint[(int)$ratingCount['totall']];
				}else{
					$text = $this->language->get('text_review_num_2');
				}

				$data['reviewsStats'][$ratingCount['rating']] = array(
					'rating'     => $ratingCount['rating'],
					'count'     => $ratingCount['totall'],
					'percent'     => $percent,
					'text'     => $text,
					'link'     => 'product_id=' . $this->request->get['product_id'] . '&rating=' . $ratingCount['rating']
				);
			}
			krsort($data['reviewsStats']);
			// technics end

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);


			// technics


			if ($this->config->get($this->config->get('theme_technics_config_captcha_fo') . '_status')) {
				$data['captcha_fo'] = $this->load->controller('extension/captcha/' . $this->config->get('theme_technics_config_captcha_fo'));
			} else {
				$data['captcha_fo'] = '';
			}

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

			$data['sales'] = $sales;
		    if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		       $hits = $this->model_extension_module_technics->getHitProducts($labelsInfo['hit']['period'],$labelsInfo['hit']['qty']);
		    }
				if (in_array($product_id, $newest)) {
					$data['isnewest'] = true;
				} else {
					$data['isnewest'] = false;
				}

				$data['discount'] = '';
				if($sales && $data['special']){
					if($labelsInfo['sale']['extra'] == 1){
						$discount = round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100);
						$data['discount'] = $discount. ' %';
					}
					if($labelsInfo['sale']['extra'] == 2){
						$data['discount'] = $this->currency->format($this->tax->calculate(($product_info['price'] - $product_info['special']), $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					}
				}

		        $data['catch'] = false;
		        $data['nocatch'] = false;
		        if (isset($labelsInfo['catch']) && $labelsInfo['catch']['status'] && $product_info['quantity'] <= $labelsInfo['catch']['qty']) {
		          if($product_info['quantity'] > 0){
		            $data['catch'] = $labelsInfo['catch']['name'][$this->config->get('config_language_id')];
		          }else{
		            $data['catch'] = $labelsInfo['catch']['name1'][$this->config->get('config_language_id')];
		            $data['nocatch'] = true;
		          }
		        }

		        $data['popular'] = false;
		        if (isset($labelsInfo['popular']) && $labelsInfo['popular']['status'] && $product_info['viewed'] >= $labelsInfo['popular']['views']) {
		          $data['popular'] = $labelsInfo['popular']['name'][$this->config->get('config_language_id')];
		        }

		        $data['hit'] = false;
		        if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
		          if (isset($hits[$product_id])) {
		            $data['hit'] = $labelsInfo['hit']['name'][$this->config->get('config_language_id')];
		          }
		        }

			$data['buy_click'] = array();
			if($this->config->get('theme_technics_buy_click')){
				$data['buy_click'] = $this->config->get('theme_technics_buy_click');

				if ($this->customer->isLogged()) {
					$this->load->model('account/customer');
					$data['customer_info'] = $this->model_account_customer->getCustomer($this->customer->getId());
				}
			}

			$data['text_technics_buy_click'] = $this->language->get('text_technics_buy_click');

			// technics end

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {

				// technics
				if ($this->config->get('theme_technics_image_related_resize')) {

				if ($result['image']) {

						$image = $this->model_tool_image->technics_resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$image = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}
				} else {
					if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}


				}
				// technics end

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

					if ($this->config->get('theme_technics_related_images_status')) {
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


			        $hit = false;
			        if (isset($labelsInfo['hit']) && $labelsInfo['hit']['status']) {
			          if (isset($hits[$result['product_id']])) {
			            $hit = $labelsInfo['hit']['name'][$this->config->get('config_language_id')];
			          }
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

// technics end

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,

					'manufacturer'  => $manufacturer,// technics
					'quantity'        => $result['quantity'],// technics
				//	'stock'        => $stock,// technics
					'images'       => $extraImages,// technics
					'isnewest'       => $isNewest,// technics
					'sales'       => $sales,// technics
					'discount'       => $discount,// technics
					'catch'       => $catch,// technics
					'nocatch'       => $nocatch,// technics
					'popular'	  => $popular,// technics
					'hit'	 	  => $hit,// technics
					'buy_btn'	  => $buy_btn,// technics
					'reward'      => $result['reward'],// technics
					'special_date_end'      => $special_date_end,// technics

					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),

		'href' => str_replace('&amp;', '&', $this->url->link('product/search', 'tag=' . trim($tag)))// technics

					);
				}
			}


// technics
			$data['buyclick_form'] = $this->load->view('product/buyclick_form', $data);
			if (!isset($_COOKIE["productsVieded[" . $product_id . "]"])){
				SetCookie("productsVieded[" . $product_id . "]",time(),time()+3600*24*30*12,"/");
			}
			$data['buyclick_form'] = $this->load->view('product/buyclick_form', $data);

			if ($this->config->get('theme_technics_product_review')) {
				$data['reviewsdata'] = $this->review(1);
			} else {
				$data['reviewsdata'] = false;
			}
// technics end

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
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
		if (is_file(DIR_IMAGE . $this->config->get('theme_technics_logo_404'))) {
			$data['logo_404'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . 'image/' . $this->config->get('theme_technics_logo_404');
		} else {
			$data['logo_404'] = '';
		}
		$data['text_404'] = sprintf($this->language->get('text_404'), $this->url->link('information/contact', '', true), $this->url->link('product/search', '', true), $this->url->link('common/home', '', true));
		$this->response->setOutput($this->load->view('error/404', $data));
// technics end

		}

		// Парсирую табы из описания товара

//        $allProducts = $this->model_catalog_product->getProducts();
//        foreach ($allProducts as $prod) {
//
//            $item[$prod['product_id']] = $prod['description'];
//
//            $descriptionNoTabTech[$prod['product_id']] = preg_replace('#&lt;div class=&quot;tab-content&quot; id=&quot;tab-tech&quot;&gt;(.+?)&lt;/div&gt;#is', '', $item[$prod['product_id']]);
//            $descriptionNoTabOptions[$prod['product_id']] = preg_replace('#&lt;div class=&quot;tab-content&quot; id=&quot;tab-options&quot;&gt;(.+?)&lt;/div&gt;#is', '', $descriptionNoTabTech[$prod['product_id']]);
//        }

//       ***ЗДЕСЬ СКРИПТ ЗАНОСИТ ЗАПИСИ В БД - СЛЕДУЮЩИЕ СТРОЧКИ НЕ РАСКОМЕНТИРОВАТЬ***
//        foreach ($descriptionNoTabOptions as $key => $text) {
//            $this->model_catalog_product->updateProductDescription($key, $text);
//        }
//                                  ******
	}


// technics
	public function like() {

		$this->load->model('extension/module/technics');

		$json = array();
		$data = array();
		$likes = 0;

		$data['review_id'] = $this->request->get['review_id'];

		$likesInfo = $this->model_extension_module_technics->getLikes4review($this->request->get['review_id']);

		$data['count_good'] = $likesInfo['count_good'];
		$data['count_bad'] = $likesInfo['count_bad'];



		if (!isset($_COOKIE["likesls"][(int)$data['review_id']] )){
			if ($this->request->get['islike']) {
				$likes = (int)$likesInfo['count_good'];
				$likes++;
				$data['count_good'] = $likes;
			}else{
				$likes = (int)$likesInfo['count_bad'];
				$likes++;
				$data['count_bad'] = $likes;
			}

			SetCookie("likesls[" . $data['review_id'] . "]",time(),time()+3600*24*30*12,"/");
			$this->model_extension_module_technics->setLikes4review($data);
			$json['success']['likes'] = $likes;
		}




		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
// technics end

	public function review($type = false) {	// technics

		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}


		// technics
		$data['schema'] = $this->config->get('theme_technics_schema');
		$data['reviewsStats'] = array();
		if (isset($this->request->get['rating'])) {
			$rating = $this->request->get['rating'];
		}else{
			$rating = 0;
		}
		// technics end

		$data['reviews'] = array();


		// technics
		$results = $this->model_catalog_review->getReviewsByProductIdLs($this->request->get['product_id'], ($page - 1) * 5, 5, $rating); //technics add this
		$review_total = $this->model_catalog_review->getTotalReviewsByProductIdLs($this->request->get['product_id'],$rating);
		// technics end

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],

				'review_id'     => (int)$result['review_id'], // technics
				'count_good'     => (int)$result['count_good'], // technics
				'count_bad'     => (int)$result['count_bad'], // technics
				'text_plus'     => nl2br($result['text_plus']), // technics
				'text_minus'     => nl2br($result['text_minus']), // technics
				'date_added_schema' => date('Y-m-d', strtotime($result['date_added'])), // technics

				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));


		//technics

//		if ($this->config->get('theme_technics_product_review') ) {
			if ($type) {
				return $this->load->view('product/review', $data);
			}else{
			    $this->load->language('extension/theme/technics');
			    $data['text_review_plus'] = $this->language->get('text_review_plus');
			    $data['text_review_minus'] = $this->language->get('text_review_minus');
				$this->response->setOutput($this->load->view('product/review', $data));
			}
//		}
		// technics end


	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


// technics

	public function custtabload() {
		$this->load->model('extension/module/technics');

		$customTabs = $this->model_extension_module_technics->getFields4Product($this->request->get['product_ids']);
		$data['customTab'] = $customTabs["popup"][$this->request->get['tab']];

		$this->response->setOutput($this->load->view('product/custtab_popup',$data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
//			$this->load->model('catalog/option');
			$this->load->model('tool/image');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_tag'   => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();
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


				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'image'      => $image,
					'option'     => $option_data,
					'price'      => $price,
					'special'    => $special,
					'href' 		 => str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $result['product_id']))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
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
	public function descriptionExtra($description) {
		$data['fulldesc'] = $description;
		$data['shortdesc'] = '';
		$tag = html_entity_decode($this->config->get('theme_technics_product_short_tag'), ENT_QUOTES, 'UTF-8');
		$temp = explode($tag,$description);
		if(isset($temp[0])){
			$data['shortdesc'] = $temp[0];
			$data['fulldesc'] = str_replace($data['shortdesc'].$tag, "", $description);
		}
		return $data;
	}
	public function analystdataorder() {
		$json = array();

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('extension/module/technics');
		$this->load->model('checkout/order');

		if (isset($this->request->post['order_id'])) {
			$order_id = (int)$this->request->post['order_id'];
			$products = $this->model_extension_module_technics->getOrderProducts($order_id);
			$order_info = $this->model_checkout_order->getOrder($order_id);
			$json['value'] = $order_info['total'];
			$json['transaction_id'] = $order_id;
		}else{
			$products = $this->cart->getProducts();
		}

			$cartSubTotal = $this->cart->getSubTotal();

			foreach ($products as $product) {

				$product_categories = $this->model_catalog_product->getCategories($product['product_id']);
				if($product_categories){
					$category_info = $this->model_catalog_category->getCategory($product_categories[0]['category_id']);
					$category_name = $category_info['name'];
				}else{
					$category_name = "";
				}

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);

				$json['items'][] = array(
					'id' => $product['product_id'],
					'name'       => strip_tags(html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8')),
					'price' => $product['price'],
					'quantity' => $product['quantity'],
					'category'       => strip_tags(html_entity_decode($category_name, ENT_QUOTES, 'UTF-8')),
					'brand' => strip_tags(html_entity_decode($product_info['manufacturer'], ENT_QUOTES, 'UTF-8'))
				);
			}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function analystdata() {
		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['qty'])) {
			$quantity = $this->request->post['qty'];
		} else {
			$quantity = 1;
		}



		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$price = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				}

		$product_categories = $this->model_catalog_product->getCategories($product_id);
		if($product_categories){
			$category_info = $this->model_catalog_category->getCategory($product_categories[0]['category_id']);
			$category_name = $category_info['name'];
		}else{
			$category_name = "";
		}

				$json['items'][] = array(
					'id' => $product_info['product_id'],
					'name'       => strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')),
					'price' => $price,
					'quantity' => $quantity,
					'category'       => strip_tags(html_entity_decode($category_name, ENT_QUOTES, 'UTF-8')),
					'brand' => strip_tags(html_entity_decode($product_info['manufacturer'], ENT_QUOTES, 'UTF-8'))
				);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function doOptionColumns($options){
		$optionColumns = array();
		$column = $this->config->get('theme_technics_product_opt_select');
		if (!$column) {
			$column = 2;
		}


//		if ($column == 2) {
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

//		}else{
//			$optionColumns[] = $options;
//		}

		return $optionColumns;
	}


// technics end

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
