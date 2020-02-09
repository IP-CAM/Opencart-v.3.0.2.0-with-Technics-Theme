<?php
class ControllerCustomProductsMain extends Controller {
	public function index() {
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}


        $layout_id = $this->model_design_layout->getLayout($route);


		$data['products_main'] = array(
		    0 => 123,
            1 => 1231512,
            2 => 'tratata',
            3 => 'atafaf'
            );

		return $this->load->view('custom/products_main', $data);
	}
}
