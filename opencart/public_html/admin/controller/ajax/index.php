<?php
class ControllerAjaxIndex extends Controller
{
    public function index()
    {

    }

    public function ajaxGetProductSuboptions()
    {
        $this->load->model('catalog/suboption');

        $result = $this->model_catalog_suboption
            ->getProductSuboptions($this->request->post['product_id'], $this->request->post['option_id'], $this->request->post);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }

}
