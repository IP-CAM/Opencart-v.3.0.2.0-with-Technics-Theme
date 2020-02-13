<?php
class ControllerCommonMaintenance extends Controller {
	public function index() {
		$this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_maintenance'),
			'href' => $this->url->link('common/maintenance')
		);

		$data['message'] = $this->language->get('text_message');

		// technics
		$this->load->language('extension/theme/technics');
		$data['text_technics_maintenance_message1'] = $this->language->get('text_technics_maintenance_message1');
		$data['text_technics_maintenance_message2'] = $this->language->get('text_technics_maintenance_message2');
		$data['text_technics_con_soc2'] = $this->language->get('text_technics_con_soc2');
		
		$this->load->model('setting/setting');
		$this->load->model('extension/module/technics');
		$data['social_navs'] = array();
		$social_links = $this->model_setting_setting->getSetting('theme_technicssoclinks');
		$data['social_links'] = $social_links['theme_technicssoclinks_array'];
		$data['social_navs'] = $this->config->get('theme_technics_social_nav');	
	    // technics end													
            

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/maintenance', $data));
	}
}
