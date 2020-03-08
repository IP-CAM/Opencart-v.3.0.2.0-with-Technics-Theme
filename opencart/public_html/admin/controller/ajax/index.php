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

        if (empty($result)) {
            $result = $this->model_catalog_suboption->getSuboptions($this->request->post);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }

    public function ajaxGetOptionGroup()
    {
        $result = $this->db->query("SELECT option_id FROM oc_option_description WHERE name='" . $this->request->post['option_group_name'] . "'")->row;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));

    }

    public function writeLog()
    {
        $logFileName = realpath($_SERVER["DOCUMENT_ROOT"] . "/../..") . "/logs/stanok.log";
        $backtrace = debug_backtrace();
        $backtracePath = array();
        foreach($backtrace as $k => $bt)
        {
            if($k > 15)
                break;
            $backtracePath[] = substr($bt['file'], strlen($_SERVER['DOCUMENT_ROOT'])) . ':' . $bt['line'];
        }

        $data = func_get_args();
        if(count($data) == 0)
            return;
        elseif(count($data) == 1)
            $data = current($data);

        if(!is_string($data) && !is_numeric($data))
            $data = var_export($data, 1);
        $fp = fopen($logFileName, 'at+');
        fwrite($fp, "\n--------------------------" . date('Y-m-d H:i:s ') . microtime() . "-----------------------\n Backtrace: " . implode(' > ', $backtracePath) . "\n" . $data);
        fflush($fp);
        fclose($fp);
    }

}
