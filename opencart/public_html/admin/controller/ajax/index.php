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
            ->getSuboptions($this->request->post);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }

}
