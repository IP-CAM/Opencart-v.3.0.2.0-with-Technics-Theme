<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

    // technics
		$data['yandex_metrika_counter'] = false;
		if ($this->config->get('analytics_yandex_metrika_counter') && $this->config->get('analytics_yandex_metrika_status')) {
			$data['yandex_metrika_counter'] = $this->config->get('analytics_yandex_metrika_counter');
		}
    $this->load->language('extension/theme/technics');
    $data['text_manufacturers'] = $this->language->get('text_manufacturers');
    $data['text_loader'] = $this->language->get('text_loader');
    $data['text_ls_logged'] = sprintf($this->language->get('text_ls_logged'), $this->customer->getFirstName());
    $data['text_register_account'] = $this->language->get('text_register_account');
    $data['text_technics_account'] = $this->language->get('text_technics_account');
    $data['text_technics_email'] = $this->language->get('text_technics_email');
    $data['outdated_browser'] = $this->language->get('outdated_browser');
    $data['text_search'] = $this->language->get('text_search');
    $data['text_forgotten'] = $this->language->get('text_forgotten');
    $data['text_technics_maintenance_phone'] = $this->language->get('text_technics_maintenance_phone');
    $data['text_technics_maintenance_email'] = $this->language->get('text_technics_maintenance_email');
	
    $data['forgotten'] = $this->url->link('account/forgotten', '', true);
    $data['header_type'] = $this->config->get('theme_technics_header_type');
    $data['js_footorhead'] = $this->config->get('theme_technics_js_footorhead');
    $data['open_graph'] = $this->config->get('theme_technics_og');
    $data['preloader'] = $this->config->get('theme_technics_preloader');
    $data['google_site_verification'] = $this->config->get('theme_technics_google_site_verification');
    $data['yandex_verification'] = $this->config->get('theme_technics_yandex_verification');
	$data['shop_email'] = $this->config->get('config_email');
    $data['text_search_placeholder'] = $this->config->get('text_search_placeholder');
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

    $data['bootstrap'] = $this->config->get('theme_technics_bootstrap');
    $data['custom_css'] = html_entity_decode($this->config->get('theme_technics_css'), ENT_QUOTES, 'UTF-8');
	
	$data['theme_color'] = $this->config->get('theme_technics_color');
	$data['fixed_header'] = $this->config->get('theme_technics_fixed_header');

	if ($data['theme_color']) {
		if ($data['theme_color'] == 'custom') {
			$data['theme_color_1'] = $this->config->get('theme_technics_custom_color_1');
			$data['theme_color_2'] = $this->config->get('theme_technics_custom_color_2');
			$data['theme_color_3'] = $this->config->get('theme_technics_custom_color_3');
			list($r, $g, $b) = array_map('hexdec',str_split($this->config->get('theme_technics_custom_color_2'),2));
			$data['theme_color_4'] = $r . ', ' . $g . ', ' . $b . ', 0.5';
			$data['theme_color_5'] = $r . ', ' . $g . ', ' . $b . ', 0.1';
		}else{
			$colors = explode(' ', $data['theme_color']);
			$data['theme_color_1'] = $colors[0];
			$data['theme_color_2'] = $colors[1];
			$data['theme_color_3'] = $colors[2];
			list($r, $g, $b) = array_map('hexdec',str_split($colors[1],2));
			$data['theme_color_4'] = $r . ', ' . $g . ', ' . $b . ', 0.5';
			$data['theme_color_5'] = $r . ', ' . $g . ', ' . $b . ', 0.1';
		}
	}
	$data['theme_fc_color'] = $this->config->get('theme_technics_color_2');
    $data['theme_fc_color_1'] = $this->config->get('theme_technics_custom_fc_color_1');
//	$data['theme_fc_color_1_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_1'));
    $data['theme_fc_color_2'] = $this->config->get('theme_technics_custom_fc_color_2');
//	$data['theme_fc_color_2_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_2'));
    $data['theme_fc_color_3'] = $this->config->get('theme_technics_custom_fc_color_3');
//	$data['theme_fc_color_3_db'] = $this->getbrightness($this->config->get('theme_technics_custom_fc_color_3'));

    $data['custom_js'] = html_entity_decode($this->config->get('theme_technics_js'), ENT_QUOTES, 'UTF-8');
    $data['fontawesome'] = $this->config->get('theme_technics_fontawesome');
    $data['bootstrap_ver'] = $this->config->get('theme_technics_bootstrap_ver');
    $data['type3_logo'] = $this->config->get('theme_technics_header_type3_logo');
    $data['type3_menu'] = $this->config->get('theme_technics_header_type3_menu');
    $data['container_width'] = $this->config->get('theme_technics_container_width');
    $data['fonts'] = $this->config->get('theme_technics_fonts');
    $data['header_text_logo'] = html_entity_decode($this->config->get('theme_technics_header_text_logo'), ENT_QUOTES, 'UTF-8');
    $data['text_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
    $data['text_islogged'] = sprintf($this->language->get('text_islogged'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('account/wishlist'));
    $data['text_empty_wish'] = $this->language->get('text_empty_wish');
    $data['text_technics_wish_head'] = $this->language->get('text_technics_wish_head');
    $data['text_technics_comp_head'] = $this->language->get('text_technics_comp_head');
    $data['text_empty_compare'] = $this->language->get('text_empty_compare');
    $data['text_compare_href'] = $this->url->link('product/compare');
    $data['text_wish_href'] = $this->url->link('account/wishlist');
    $data['text_technics_show_all'] = $this->language->get('text_technics_show_all');
    $data['text_header_category'] = $this->language->get('text_header_category');
    $data['text_technics_information'] = $this->language->get('text_technics_information');
    $data['text_technics_menu'] = $this->language->get('text_technics_menu');
    $data['text_show_more'] = $this->language->get('text_show_more');
    $data['text_nav_more'] = $this->language->get('text_nav_more');
    $data['text_account_title'] = $this->language->get('text_account_title');
    $data['text_account_login'] = $this->language->get('text_account_login');
    $data['text_account_register'] = $this->language->get('text_account_register');
    $data['text_account_check'] = $this->language->get('text_account_check');
    $data['text_account_submit'] = $this->language->get('text_account_submit');
    $data['text_account_password'] = $this->language->get('text_account_password');
    $data['text_header_callback'] = $this->language->get('text_header_callback');
    $data['address'] = nl2br($this->config->get('config_address'));
    $data['version'] = $this->config->get('theme_technics_version');
    $data['language_id'] = $this->config->get('config_language_id');

    $data['top_links'] = array();
    if($this->config->get('theme_technicslinks_array')){
      $data['top_links'] = $this->config->get('theme_technicslinks_array');
      $data['top_links'] = $data['top_links'][$this->config->get('config_language_id')];
    }
    
    $this->load->model('extension/module/technics');
//    $catLinks = $this->model_extension_module_technics->createCatLinks($this->config->get('config_language_id'));
//    $data['top_links'] = array_merge ($data['top_links'],$catLinks);

    foreach($data['top_links'] as $key => $link){
      if(strpos(current($link),':') !== false ){ continue;}
      $data['top_links'][$key][key($link)] = $this->url->link(current($link));  
    } 

    $data['header_navs'] = array();
    $header_navs = $this->config->get('theme_technics_header_nav');

    //Основное меню
    $data['main_navs'] = array();
    $main_navs = $this->config->get('theme_technics_main_nav');
    $data['main_navs_v'] = array();
    $main_navs_v = $this->config->get('theme_technics_main_nav_v');

    require_once(DIR_APPLICATION . 'controller/extension/module/technics/header_add.php');


    //ПРАВЫЙ ЭЛЕМЕНТ ПОДМЕНЮ КАТЕГОРИИ

    $data['manufacturerlinks'] = $this->url->link('product/manufacturer');

    $this->load->language('product/compare');
    $data['productscomp'] = $this->getCompareData();
    $data['countcomp'] = count($data['productscomp']);
    $data['islogged'] = false;
    $data['productswish'] = array();    
    if ($this->customer->isLogged() || $this->config->get('theme_technics_wishlist')) {
      $data['islogged'] = true;
    }
    $data['productswish'] = $this->getWishData(); 
    $data['count'] = count($data['productswish']);
    $data['counTotall'] =$data['countcomp']+$data['count']; 

    if (is_file(DIR_IMAGE . $this->config->get('theme_technics_header_logo'))) {
      $data['header_logo'] = $server . 'image/' . $this->config->get('theme_technics_header_logo');
    } else {
      $data['header_logo'] = '';
    }
    if (is_file(DIR_IMAGE . $this->config->get('theme_technics_fav_16'))) {
      $data['fav_16'] = $server . 'image/' . $this->config->get('theme_technics_fav_16');
    } else {
      $data['fav_16'] = '';
    }
    if (is_file(DIR_IMAGE . $this->config->get('theme_technics_fav_32'))) {
      $data['fav_32'] = $server . 'image/' . $this->config->get('theme_technics_fav_32');
    } else {
      $data['fav_32'] = '';
    }
    if (is_file(DIR_IMAGE . $this->config->get('theme_technics_fav_180'))) {
      $data['fav_180'] = $server . 'image/' . $this->config->get('theme_technics_fav_180');
    } else {
      $data['fav_180'] = '';
    }
    $data['callback_status'] = $this->config->get('theme_technics_callback_status');
    $data['schema'] = $this->config->get('theme_technics_schema');
    
    $data['max_subcat'] = $this->config->get('theme_technics_max_subcat');

    // Menu
    $this->load->model('catalog/category');

    $this->load->model('catalog/product');

    $data['topcat'] = array();

    $data['categories'] = array();

    if ($this->config->get('developer_theme')) {
      $data['categories'] = $this->cache->get('category.categoriesdata.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.0');
    }

    $data['topcat'] = $this->cache->get('category.categoriestopcat.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.0');
//    $data['categories'] = array();
  if (!$data['categories']) {
    $categoriesLs = array();
    $categories = $this->model_catalog_category->getCategories(0);
      
  

    foreach ($categories as $category) {//Level 1
      if ($category['top']) {
        // Level 2
        $children_data = array();

        $children = $this->model_catalog_category->getCategories($category['category_id']);
        $L3_chid = false;// <--technics add this
        foreach ($children as $child) {  // Level 2       
          $children2_data = array();
          $children2 = $this->model_catalog_category->getCategories($child['category_id']);
          foreach ($children2 as $child2) {    // Level 3 
//Level 4 start
            $children3_data = array();
            $children3 = $this->model_catalog_category->getCategories($child2['category_id']);               
              foreach ($children3 as $child3) { 
                $filter_data = array(
                  'filter_category_id'  => $child3['category_id'],
                  'filter_sub_category' => true
                );

                $children3_data[$child3['category_id']] = array(
                  'name'  => $child3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                  'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child2['category_id'] . '_' . $child3['category_id'])
                );
                $categoriesLs[$child3['category_id']] = $children3_data[$child3['category_id']];
                $categoriesLs[$child3['category_id']]['childrencount'] = 0;
//                $categoriesLs[$child['category_id']]['have_L3'] = false;
              }
//Level 4 end
            $filter_data = array(
              'filter_category_id'  => $child2['category_id'],
              'filter_sub_category' => true
            );
            // Level 3
            $children2_data[$child2['category_id']] = array(
              'name'  => $child2['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
              'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child2['category_id']),
              'children' => $children3_data
            );
            $categoriesLs[$child2['category_id']] = $children2_data[$child2['category_id']];
            $categoriesLs[$child2['category_id']]['childrencount'] = count($children3_data);
            $categoriesLs[$child['category_id']]['have_L3'] = false;
          }         
                  
          $filter_data = array(
            'filter_category_id'  => $child['category_id'],
            'filter_sub_category' => true
          );
          // Level 2
          if($children2_data){$L3_chid = true;}// <--technics add this
          
          $children_data[$child['category_id']] = array(// <--technics change this
            'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
            'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
            'children' => $children2_data // <--technics add this
          );

          $categoriesLs[$child['category_id']] = $children_data[$child['category_id']];
          $categoriesLs[$child['category_id']]['childrencount'] = count($children2_data);
          $categoriesLs[$child['category_id']]['have_L3'] = false;          
        }

        // Level 1
        $data['categories'][$category['category_id']] = array(// <--technics change this
          'name'     => $category['name'],
          'column'     => $category['column'],
          'children' => $children_data,
          'have_L3' => $L3_chid,// <--technics add this
          'column'   => $category['column'] ? $category['column'] : 1,
          'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
        );
        $tid = 'c'.$category['category_id'];
        $data['topcat'][$tid] = $this->url->link('product/category', 'path=' . $category['category_id']);// <--technics change this
        $categoriesLs[$category['category_id']] = $data['categories'][$category['category_id']];
        $categoriesLs[$category['category_id']]['childrencount'] = count($children_data);
      }
    }

    $data['categories']['categoriesls'] = $categoriesLs;

  $this->cache->set('category.categoriesdata.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.0', $data['categories']);
  $this->cache->set('category.categoriestopcat.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.0', $data['topcat']);
    }



    $data['wish_head'] = $this->load->view('account/wish_head', $data);
    $data['compare_head'] = $this->load->view('product/compare_head', $data); 

    $data['mobiview'] = $this->createNavMobiView($data);

    $viewNav = $this->createNavView($data);

    $data['main_navs'] = $viewNav['main_navs'];

    $data['main_navs_v'] = $viewNav['main_navs_v'];

    $data['header_navs'] = $viewNav['header_navs'];

  

//technics end  
            
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		
// technics
    $data['callback'] = $this->load->controller('extension/module/callback');  

	$data['header_view'] = $this->load->view('common/header_' . $data['header_type'], $data);

	if ($this->config->get('config_maintenance')) {
		$this->user = new Cart\User($this->registry);
		if (!$this->user->isLogged()) {
			$data['header_view'] = $this->load->view('common/header_maintenance', $data);
		}
	}
    if (isset($this->request->get['mobiheader'])){
      $this->response->setOutput($data['mobiview']);
    }else{
      return $this->load->view('common/header', $data);
    }
// technics end 
            
	}

// technics
  public function getCompareData() {
    $this->load->language('product/compare');
    $this->load->model('tool/image');
    $this->load->language('common/header');
    $this->load->language('extension/theme/technics');
    $json = array();

    

    if (!isset($this->session->data['compare'])) {
      $this->session->data['compare'] = array();
    }

    $this->load->model('catalog/product');

    $data = array();

    foreach ($this->session->data['compare'] as $key => $product_id) {
      $product_info = $this->model_catalog_product->getProduct($product_id);

      if ($product_info) {
        if ($product_info['image']) {
          $image = $this->config->get('theme_technics_image_compare_resize') ? $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_height')) : $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_height')); // technics
        } else {
          $image = false;
        }

        $data[$product_id] = array(
          'product_id'   => $product_info['product_id'],
          'name'         => $product_info['name'],
          'thumb'        => $image,
          'href'         => $this->url->link('product/product', 'product_id=' . $product_id)
        );
      }
    }
    return $data;
  }
  
  public function getWishData() {
    $data = array();

    $this->load->language('account/wishlist');

    $this->load->model('account/wishlist');

    $this->load->model('catalog/product');

    $this->load->model('tool/image');
    $this->load->language('extension/theme/technics');

    $results = $this->model_account_wishlist->getWishlistLb();

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

        $data[$product_info['product_id']] = array(
          'product_id' => $product_info['product_id'],
          'thumb'      => $image,
          'name'       => $product_info['name'],
          'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
        );
      } 
    }
    return $data;
  }
  public function getwish() {
    $this->load->language('extension/theme/technics');
    $data['text_islogged'] = sprintf($this->language->get('text_islogged'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('account/wishlist'));
    $data['text_empty_wish'] = $this->language->get('text_empty_wish');
    $data['text_empty_compare'] = $this->language->get('text_empty_compare');
    $data['text_technics_wish_head'] = $this->language->get('text_technics_wish_head');
	$data['header_type'] = $this->config->get('theme_technics_header_type');
    $data['text_compare_href'] = $this->url->link('product/compare');
    $data['text_wish_href'] = $this->url->link('account/wishlist');
    $data['islogged'] = false;
    $data['productswish'] = array();
    
    if ($this->customer->isLogged() || $this->config->get('theme_technics_wishlist')) {
      $data['islogged'] = true;
    }

    $data['productswish'] = $this->getWishData();
    $data['count'] = count($data['productswish']);
    $this->response->setOutput($this->load->view('account/wish_head', $data));
  }
  
  public function getcompare() {
    $this->load->language('extension/theme/technics');
    $data['text_islogged'] = sprintf($this->language->get('text_islogged'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('account/wishlist'));
    $data['text_empty_wish'] = $this->language->get('text_empty_wish');
    $data['text_empty_compare'] = $this->language->get('text_empty_compare');
    $data['text_technics_comp_head'] = $this->language->get('text_technics_comp_head');
	$data['header_type'] = $this->config->get('theme_technics_header_type');
    $data['text_compare_href'] = $this->url->link('product/compare');
    $data['text_wish_href'] = $this->url->link('account/wishlist');
    $data['productscomp'] = $this->getCompareData();
    $data['countcomp'] = count($data['productscomp']);
    $this->response->setOutput($this->load->view('product/compare_head', $data));
  }
  
  public function getwishcompare() {
    $json = array();
    $productscomp = $this->getCompareData();
    $countcomp = count($productscomp);

    $productswish = $this->getWishData(); 
    $count = count($productswish);
    $json['counTotall'] = $countcomp+$count;
    $this->response->setOutput(json_encode($json)); 
  }
  
  public function getbrightness($color) {
    $tone = false;
    list($r, $g, $b) = array_map('hexdec',str_split($color,2)); 
    $Y = (0.3*$r) + (0.59*$g) + (0.11*$b);
    if ($Y > 128) {
      $tone = true;
    }
    return $tone ;
  }

  public function createNavMobiView($data) {
    $view = $this->cache->get('navviewmobi.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

    if (!$view) {
        $orientTypes = array(
              'main_navs_v' => 'header_mobi',  // FOR VERTICAL MENU
              'main_navs' => 'header_mobi',     // FOR HORISONTAL MENU
              'header_navs' => 'header_mobi'     // FOR HORISONTAL MENU
        );
        $data['orientTypes'] = $orientTypes;
        foreach ($orientTypes as $index => $type) { 

            foreach($data[$index] as $key => $main_nav){  
                  if(!isset($main_nav['type'][0]['links'])){
                    $data[$index][$key]['type'][0]['links'] = $data['topcat'];
                  }

                  if ($main_nav['settype'] == 1 || $main_nav['settype'] == 2) {
                    $data[$index][$key]['type'][$main_nav['settype']] = $this->prepCustLinks($data,$main_nav['type'][$main_nav['settype']],$key);
                  }
            }        
        }

        $view = $this->load->view('common/header_mobi', $data);
        $this->cache->set('navviewmobi.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $view);       
    }


    return $view;
  }

  public function createNavView($data) {

    $view = $this->cache->get('navview.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id')); 
//$view = false; // Remove this in production!!! Disable caching.     
    if (!$view) { 
        $view['main_navs'] = array();
        $view['main_navs_v'] = array();
        $view['header_navs'] = array();

        $orientTypes = array(
              'main_navs' => 'header_nav_',     // FOR HORISONTAL MENU
              'main_navs_v' => 'header_nav_v_',  // FOR VERTICAL MENU
              'header_navs' => 'header_top_'     // FOR HEADER MENU
        );

        foreach ($orientTypes as $index => $type) {

            foreach($data[$index] as $key => $main_nav){  
                  if(!isset($main_nav['type'][0]['links'])){
                    $data[$index][$key]['type'][0]['links'] = $data['topcat'];
                  }
                  $data['number'] = $key;

                switch ($main_nav['settype']) {
                  case '0': // Category (Mega menu)

                    

                    $data[$index][$key]['type'][0]['cattoview'] = $this->doColumns($data,$main_nav,$key); 
                    if (isset($main_nav['addelem']['settype'])) {
                      $adElSetType = $main_nav['addelem']['settype']; 
                      if ($adElSetType == 2) {
                          $adElSetType = 0;
                      }
                      if (isset($main_nav['addelem']['type'][$adElSetType]) && !empty($main_nav['addelem']['type'][$adElSetType])) {

                        $data[$index][$key]['addelem']['content'] = $this->righElemPrepare($main_nav['addelem']);

                      }
                    }


                    $view[$index][$key]['view'] = $this->load->view('common/' .  $type . 'mega', $data);          
                    break;

                  case '1': // DropDown. Custom list

                    $data[$index][$key]['type'][1] = $this->prepCustLinks($data,$main_nav['type'][1],$key);

                    $view[$index][$key]['view'] = $this->load->view('common/' .  $type . 'cust_drop', $data);

                    break;

                  case '2': // Simple custom list

                    $data[$index][$key]['type'][2] = $this->prepCustLinks($data,$main_nav['type'][2],$key);

                    $view[$index][$key]['view'] = $this->load->view('common/' .  $type . 'cust', $data);

                    break;

                  case '3': // Category. DropDown.
                            
                    $view[$index][$key]['view'] = $this->load->view('common/' .  $type . 'drop', $data);

                    break;

                }

            }
            if ($index == 'main_navs_v' && !empty($view['main_navs_v'])) {
              $n = $this->config->get('theme_technics_main_nav_v_header');
              $view['main_navs_v']['header'] = $n[$this->config->get('config_language_id')];
            }

        }

        $this->cache->set('navview.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $view);
    }
 
    return $view;
  }

  private function prepCustLinks($data,$main_nav,$key) {

          if(!isset($main_nav['links'])) { 
            $main_nav['links'] = array();
          }
         
          if (isset($main_nav['language']) && !$main_nav['language'][$data['language_id']]['href']) {
            $main_nav['language'][$data['language_id']]['href'] = '#';
          }
          foreach ($main_nav['links'] as $id => $link) {
            if (isset($data['top_links'][$id]['target'])) {
              $main_nav['target'][$id] = $data['top_links'][$id]['target'];
            }else{
              $main_nav['target'][$id] = '';
            }

            $main_nav['name'][$id] = $this->model_extension_module_technics->getName4MenuItem($id,$data);
          }

          return $main_nav; 
  }

  private function doColumns($data,$main_nav,$key) {
          if(!isset($main_nav['type'][0]['links'])){
            $main_nav['type'][0]['links'] = $data['topcat'];
          }
          
          $catToView = array();
          foreach ($main_nav['type'][0]['links'] as $cat => $link) {
            $cat_id = substr($cat, 1); 
            $subCatsInfo = $data['categories']['categoriesls'][$cat_id];
             
            if (isset($subCatsInfo['column']) && $subCatsInfo['column']) {
              $columnNumb = $subCatsInfo['column']; 
            }else{
              $columnNumb = 1;  //Default columnNumb
            }

            $subCatNumb = $subCatsInfo['childrencount']; 
            $subCatInColumnNumb = (int)($subCatNumb/$columnNumb); 
            $i = 0;
            $k = 0;

            foreach ($subCatsInfo['children'] as $subCat) {
              $catToView[$cat_id]['columns'][$k][$i] = $subCat;
              $i++;
              if ($i == $subCatInColumnNumb && $k != $columnNumb-1) { 
                $i = 0;
                $k++;
              }              
            }

            $catToView[$cat_id]['width'] = 100/$columnNumb;
          }

      return $catToView;
  }

  private function righElemPrepare($elemData){ 

    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('catalog/manufacturer');
    $this->load->model('tool/image');   
    $data['main_recs'] = array();
    $data['main_prods'] = array();
    $data['main_manf'] = array();

            if(!$elemData['settype']){

                $this->load->model('extension/module/technics');
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

                foreach($elemData['type'][0]['links'] as $prodId => $product){ 
                  $product_info = $this->model_catalog_product->getProduct($prodId);

                  if ($product_info['image']) { 
                    $thumb = $this->config->get('theme_technics_image_main_rec_resize') ? $this->model_tool_image->technics_resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_main_rec_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_main_rec_height')) : $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_main_rec_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_main_rec_height')); 
                  } else {
                    $thumb = '';
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


                if (in_array($product_info['product_id'], $newest)) {
                    $isNewest = true;
                } else {
                    $isNewest = false;
                }           
                                
                if ($product_info['quantity'] <= 0) {
                    $stock = $product_info['stock_status'];
                } elseif ($this->config->get('config_stock_display')) {
                    $stock = $this->language->get('text_instock') . ': ' . $product_info['quantity'] . ' ' . $this->language->get('text_technics_cart_quantity');
                } else {
                    $stock = $this->language->get('text_instock');
                }   
                
                if (in_array($product_info['product_id'], $newest)) {
                    $isNewest = true;
                } else {
                    $isNewest = false;
                }           
                                
                if ($product_info['quantity'] <= 0) {
                    $stock = $product_info['stock_status'];
                } elseif ($this->config->get('config_stock_display')) {
                    $stock = $this->language->get('text_instock') . ': ' . $product_info['quantity'] . ' ' . $this->language->get('text_technics_cart_quantity');
                } else {
                    $stock = $this->language->get('text_instock');
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


                  $prodCatInfo = array();
                  $prodCats = $this->model_catalog_product->getCategories($prodId); 
                  if ($prodCats) {
                    $prodCatid = array_pop($prodCats); 
                    $prodCatid = $prodCatid['category_id']; 
                    $prodCat = $this->model_catalog_category->getCategory($prodCatid); 
                    $prodCatInfo['name'] = $prodCat['name'];
                    $prodCatInfo['href'] = $this->url->link('product/category', 'category_id=' . $prodCatid);
                  }
                  $data['main_prods'][] = array(
                                'thumb'       => $thumb,
                                'name'        => $product_info['name'], 
                                'category'        => $prodCat, 
                                'price'       => $price,
                                'special'     => $special,
                                'isnewest'       => $isNewest,// technics
                                'sales'       => $sales,// technics
                                'discount'       => $discount,// technics
                                'catch'       => $catch,// technics
                                'nocatch'       => $nocatch,// technics
                                'popular'     => $popular,// technics
                                'hit'         => $hit,// technics
                                'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])             
                  );
                }
            }elseif($elemData['settype'] == 2){
                if(isset($elemData['type'][0]['manf'])){
                    foreach($elemData['type'][0]['manf'] as $manfId => $product){
                      $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manfId);

                        if ($this->config->get('theme_technics_image_manufacturer_resize')) {
                            if ($manufacturer_info['image']) {
                                $image = $this->model_tool_image->technics_resize($manufacturer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'));
                            } else {
                                $image = $this->model_tool_image->technics_resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'));
                            }
                        } else {
                            if ($manufacturer_info['image']) {
                                $image = $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'));
                            } else {
                                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_manufacturer_height'));
                            }
                        }

                      $data['main_manf'][] = array(
                                    'name'        => $manufacturer_info['name'],
                                    'href'        => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id']),
                                    'image'        => $image              
                      );
                    }             
                }             
            }else{
                $data['main_html'] = html_entity_decode($elemData['type'][1]['html'], ENT_QUOTES, 'UTF-8');
            }   
      return $data;
  }

// technics end
            
}
