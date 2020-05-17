<?php
class ControllerInformationCompany extends Controller {
    public function index() {
        $this->load->model('catalog/information');

        // Optional. Set the title of your web page
        $this->document->setTitle($this->language->get('heading_title'));
        // Breadcrumbs for the page
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/company')
        );

        $information_info = $this->model_catalog_information->getInformation(4);
        $data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

        // Get "heading_title" from language file
        $data['heading_title'] = $this->language->get('heading_title');

        // All the necessary page elements
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header_company');

        // Load the template file and show output
        $this->response->setOutput($this->load->view('information/company', $data));
    }
}