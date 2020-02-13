<?php
class ControllerCommonCart extends Controller {
	public function index() {
		$this->load->language('common/cart');

		// Totals
		$this->load->model('setting/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
			
		// Display prices
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}

		$data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));


// technics	

			if ($this->config->get($this->config->get('theme_technics_config_captcha_fo') . '_status')) {
				$this->request->get['route'] = '';
				$data['captcha_fo'] = $this->load->controller('extension/captcha/' . $this->config->get('theme_technics_config_captcha_fo'));
			} else {
				$data['captcha_fo'] = '';
			}
		$this->load->language('extension/theme/technics');	
		$data['text_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
		$data['button_fastorder_sendorder'] = $this->language->get('button_technics_sendorder');
		$data['text_technics_cart_title'] = $this->language->get('text_technics_cart_title');
		$data['text_technics_cart_quantity'] = $this->language->get('text_technics_cart_quantity');
		$data['text_technics_fast_order'] = $this->language->get('text_technics_fast_order');
		$data['text_technics_edit_cart'] = $this->language->get('text_technics_edit_cart');
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
		$data['language_id'] = $this->config->get('config_language_id');
		$data['cart_call'] = $this->config->get('theme_technics_cart_call');
			$this->load->language('checkout/checkout');
			$data['entry_firstname'] = $this->language->get('entry_firstname');
			$data['entry_lastname'] = $this->language->get('entry_lastname');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_telephone'] = $this->language->get('entry_telephone');
			$data['entry_comment'] = $this->language->get('entry_comment');
// technics end
            
		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				
				$image = $this->config->get('theme_technics_image_cart_resize') ? $this->model_tool_image->technics_resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height')) : $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height')); // technics
            
			} else {
				
				$image = $this->config->get('theme_technics_image_cart_resize') ? $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height')) : $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height')); // technics
            
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
				
				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}

			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
				'quantity'  => $product['quantity'],

				'minimum'   => $product['minimum'], //technics add
				'product_id'   => $product['product_id'], //technics add
            
				'price'     => $price,
				'total'     => $total,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
				);
			}
		}

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],

				'code' => $total['code'], //technics	add this
            
				'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
			);
		}

		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);

		
//technics	
			$data['buyclick_form'] = $this->load->view('product/buyclick_form', $data);	
			$data['text_custcart'] = $this->language->get('text_custcart');  	
			$data['text_technics_cart_quantity'] = $this->language->get('text_technics_cart_quantity');  	
			if(isset($this->request->get['custcart'])){
				$this->response->setOutput($this->load->view('common/custcart', $data));
			}else{
				return $this->load->view('common/cart', $data);
			}
//technics	
            
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}