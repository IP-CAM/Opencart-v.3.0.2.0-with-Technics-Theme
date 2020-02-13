<?php
class ControllerCommonFooter extends Controller {

	public function getbrightness($color) {
		$tone = false;
		list($r, $g, $b) = array_map('hexdec',str_split($color,2)); 
		$Y = (0.3*$r) + (0.59*$g) + (0.11*$b);
		if ($Y > 128) {
			$tone = true;
		}
		return $tone ;
	}
            
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('setting/setting');
		$this->load->model('extension/module/technicsnews');//!!!
            

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');

		// technics
			$data['buy_click'] = array();
			if($this->config->get('theme_technics_buy_click')){
				$this->load->language('extension/theme/technics');
				$this->load->language('checkout/checkout');
				$data['entry_firstname'] = $this->language->get('entry_firstname');
				$data['entry_lastname'] = $this->language->get('entry_lastname');
				$data['entry_email'] = $this->language->get('entry_email');
				$data['entry_telephone'] = $this->language->get('entry_telephone');
				$data['entry_comment'] = $this->language->get('entry_comment');
				$data['text_technics_buy_click'] = $this->language->get('text_technics_buy_click');
				$data['button_fastorder_sendorder'] = $this->language->get('button_technics_sendorder');
				$data['buy_click'] = $this->config->get('theme_technics_buy_click');
				if ($this->customer->isLogged()) {
					$this->load->model('account/customer');
					$data['customer_info'] = $this->model_account_customer->getCustomer($this->customer->getId());
				}
				$data['buyclick_form'] = $this->load->view('product/buyclick_form', $data);
			}	

		$this->load->language('extension/theme/technics');
		$data['scripts'] = $this->document->getScripts();
		$data['js_codes'] = $this->document->getScripts('js_code'); 
		$data['styles'] = $this->document->getStyles();
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['version'] = $this->config->get('theme_technics_version');

		$data['language_id'] = $this->config->get('config_language_id');	
		$data['top_links'] = array();
		if($this->config->get('theme_technicslinks_array')){
			$data['top_links'] = $this->config->get('theme_technicslinks_array');
			$data['top_links'] = $data['top_links'][$this->config->get('config_language_id')];
			foreach($data['top_links'] as $key => $link){ 
				if(strpos(current($link),':') !== false ){ continue;}
				$data['top_links'][$key][key($link)] = $this->url->link(current($link));	
			}
		}

		$this->load->model('extension/module/technics');

					
		$data['footer_navs'] = array();
		$footer_navs = $this->config->get('theme_technics_footer_nav');
		if(isset($footer_navs)){
			foreach($footer_navs as $footer_nav){
				
				if(isset($footer_nav['type'][0]['links'])){
					foreach($footer_nav['type'][0]['links'] as $key => $link){
						if(strpos($link,':') !== false ){$footer_nav['type'][0]['links'][$key] = current($data['top_links'][$key]); continue;}
						$footer_nav['type'][0]['links'][$key] = $this->url->link($link);
						$footer_nav['type'][0]['names'][$key] = $this->model_extension_module_technics->getName4MenuItem($key,$data);
					}					
				}
				if(isset($footer_nav['type'][1]['links']['html'])){
					if ($footer_nav['type'][1]['links']['html']) {
						$footer_nav['type'][1]['links']['html'] = html_entity_decode($footer_nav['type'][1]['links']['html']);
					}
				}

				$data['footer_navs'][$footer_nav['sort']] = $footer_nav;
			}
		}
		ksort($data['footer_navs']);

		//Социальные сети
		$data['social_navs'] = array();
		$social_links = $this->model_setting_setting->getSetting('theme_technicssoclinks');
		$data['social_links'] = $social_links['theme_technicssoclinks_array'];
		$data['social_navs'] = $this->config->get('theme_technics_social_nav');	

		$data['messenger_navs'] = array();
		$messenger_links = $this->model_setting_setting->getSetting('theme_technicsmeslinks');
		$data['messenger_links'] = $messenger_links['theme_technicsmeslinks_array'];
		$data['messenger_navs'] = $this->config->get('theme_technics_messenger_nav');		
		$data['messenger_status'] = $this->config->get('theme_technics_messenger_status');
		$data['messenger_pos'] = $this->config->get('theme_technics_messenger_pos');

		$data['payment_icons'] = array();
		if($this->config->get('theme_technics_vidg_payicons')){
			$payment_icons = $this->config->get('theme_technics_vidg_payicons');
			foreach ($payment_icons as $key => $payment_icon) {
				if (!is_file(DIR_IMAGE . $payment_icon['image'])) {
					$payment_icon['image'] = 'no_image.png';
				}
				if ($this->request->server['HTTPS']) {
					$payment_icons[$key]['thumb'] = $this->config->get('config_ssl') . 'image/' . $payment_icon['image'];
				} else {
					$payment_icons[$key]['thumb'] = $this->config->get('config_url') . 'image/' . $payment_icon['image'];
				}
			}
			$paysort  = array_column($payment_icons, 'sort');
			array_multisort($paysort, SORT_ASC, $payment_icons);
			$data['payment_icons'] = $payment_icons;
		}


		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_footer_subscribe_email'] = $this->language->get('text_footer_subscribe_email');
		$data['text_header_callback'] = $this->language->get('text_header_callback');
		$data['text_social_navs'] = $this->language->get('text_social_navs');
		$data['callback_status'] = $this->config->get('theme_technics_callback_status');
		$data['soc_stat'] = $this->config->get('theme_technics_footer_soc_stat');
    $data['technics_phones'] = array();
    $data['technics_phones_main'] = array();
    $technics_phones = $this->config->get('theme_technics_phones');



    if ($technics_phones) {
      foreach ($technics_phones as $key => $technics_phone) {
        $data['technics_phones'][$technics_phone['sort']] = $technics_phone;
      }
      ksort($data['technics_phones']);
      $data['technics_phones_main'] = current($data['technics_phones']);
    }

		$data['theme_color'] = $this->config->get('theme_technics_color');
		$data['theme_fc_color'] = $this->config->get('theme_technics_color_2');
		$data['fontawesome'] = $this->config->get('theme_technics_fontawesome');
		$data['bootstrap_ver'] = $this->config->get('theme_technics_bootstrap_ver');
		$data['theme_color_1'] = $this->config->get('theme_technics_custom_color_1');
		$data['theme_color_2'] = $this->config->get('theme_technics_custom_color_2');
		list($r, $g, $b) = array_map('hexdec',str_split($this->config->get('theme_technics_custom_color_2'),2));
		$data['theme_color_3'] = $r . ', ' . $g . ', ' . $b . ', 0.3';
		$data['theme_fc_color_1'] = $this->config->get('theme_technics_custom_fc_color_1');
//		$data['theme_fc_color_1_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_1'));
		$data['theme_fc_color_2'] = $this->config->get('theme_technics_custom_fc_color_2');
//		$data['theme_fc_color_2_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_2'));
		$data['theme_fc_color_3'] = $this->config->get('theme_technics_custom_fc_color_3');
//		$data['theme_fc_color_3_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_3'));
		$data['footer_type'] = $this->config->get('theme_technics_footer_type');
		if ($this->config->get('config_maintenance')) {
			$this->user = new Cart\User($this->registry);
			if (!$this->user->isLogged()) {
				$data['footer_type'] = 'maintenance';
			}
		}
		$data['subscribe_status'] = $this->config->get('theme_technics_subscribe_status');
		$data['subscribe_title'] = $this->config->get('theme_technics_subscribe_title' . $this->config->get('config_language_id'));
		$data['subscribe_subtitle'] = $this->config->get('theme_technics_subscribe_subtitle' . $this->config->get('config_language_id'));
		$data['js_footorhead'] = $this->config->get('theme_technics_js_footorhead');
		$data['footer_copyright'] = html_entity_decode($this->config->get('theme_technics_footer_copyright' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
		$data['footer_text'] = html_entity_decode($this->config->get('theme_technics_footer_text' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
		$data['footer_t_logo'] = $this->config->get('theme_technics_footer_t_logo');
		$data['text_logo'] = html_entity_decode($this->config->get('theme_technics_footer_text_logo'), ENT_QUOTES, 'UTF-8');
		$data['shop_email'] = $this->config->get('config_email');
		$data['scroll_to_top'] = $this->config->get('theme_technics_scrolltt_status');
		$data['scroll_to_top_pos'] = $this->config->get('theme_technics_scrolltt_pos');
		$data['footer_categories'] = $this->config->get('theme_technics_footer_categories');
		$data['text_technics_subscribe_btn'] = $this->language->get('text_technics_subscribe_btn');
		$data['text_footer_questions'] = $this->language->get('text_footer_questions');
		$data['text_footer_link'] = $this->language->get('text_footer_link');
		$data['text_footer_pay'] = $this->language->get('text_footer_pay');
		$data['text_technics_maintenance_phone'] = $this->language->get('text_technics_maintenance_phone');
		$data['text_technics_maintenance_email'] = $this->language->get('text_technics_maintenance_email');
		$data['text_technics_con_soc2'] = $this->language->get('text_technics_con_soc2');
		$data['lazyload'] = $this->config->get('theme_technics_lazyload');
		
			if ($this->config->get('theme_technics_subscribe_pdata')) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('theme_technics_subscribe_pdata'));

				if ($information_info) {
					$data['text_technics_pdata'] = sprintf($this->language->get('text_technics_pdata'), $this->language->get('text_technics_subscribe_btn'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('theme_technics_subscribe_pdata'), true), $information_info['title'], $information_info['title']);
				} else {
					$data['text_technics_pdata'] = '';
				}
			} else {
				$data['text_technics_pdata'] = '';
			}
		
		if (is_file(DIR_IMAGE . $this->config->get('theme_technics_footer_logo'))) {
			$data['footer_logo'] = $server . 'image/' . $this->config->get('theme_technics_footer_logo');
		} else {
			$data['footer_logo'] = '';
		}
		$data['custom_css'] = $this->config->get('theme_technics_css');
		$data['custom_js'] = html_entity_decode($this->config->get('theme_technics_js'), ENT_QUOTES, 'UTF-8');
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
		  $data['og_url'] = $this->url->link('common/home');
		} else {
		  $data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}
		$data['name'] = $this->config->get('config_name');
		$data['callback'] = $this->load->controller('extension/module/callback');

		$data['islogged'] = $this->customer->isLogged();
		$data['cookieagry'] = 0;
		if (isset($_COOKIE["cookieagry"])){
			$data['cookieagry'] = 1;
		}
		
		if ($this->config->get('theme_technics_cookies_pdata')) {
			$this->load->model('catalog/information');

			$information_cookies = $this->model_catalog_information->getInformation($this->config->get('theme_technics_cookies_pdata'));

			if ($information_cookies) {
				$data['text_technics_cookieagry_btn'] = sprintf($this->language->get('text_technics_cookieagry_btn'), html_entity_decode($information_cookies['description'], ENT_QUOTES, 'UTF-8'));
			} else {
				$data['text_technics_cookieagry_btn'] = '';
			}
		} else {
			$data['text_technics_cookieagry_btn'] = '';
		}
		$data['home'] = $this->url->link('common/home');
		// technics end
            
		
		return $this->load->view('common/footer', $data);
	}
}
