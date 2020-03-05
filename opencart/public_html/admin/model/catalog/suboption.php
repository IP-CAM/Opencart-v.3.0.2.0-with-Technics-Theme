<?php
class ModelCatalogSuboption extends Model {

    public function setSuboptions($option_id, $data) {

        $this->db->query("TRUNCATE " . DB_PREFIX . "custom_suboptions");

        if (!empty($data['suboptions'])) {
            foreach ($data['suboptions'] as $option_value_id => $suboptions) {
                foreach ($suboptions as $suboption_id => $suboption_name) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "custom_suboptions SET suboption_id = '" . $suboption_id . "', option_value_id = '" . (int)$option_value_id . "', suboption_name = '" . $suboption_name . "', option_id = '" . $option_id . "'");
                }
            }
        }

    }

    public function setProductSuboptions($product_id, $data)
    {

        foreach ($data['prod_suboption'] as $suboption_id => $suboption_properties) {
            $suboptionRow = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_product_suboptions WHERE product_id = '" . $product_id . "' AND option_id = '" . $suboption_properties['option_id'] . "' AND option_value_id = '" . $suboption_properties['option_value_id'] . "' AND suboption_id = '" . $suboption_id . "'")->row;
            if ($suboptionRow) {
                if (array_key_exists('status', $suboption_properties)) {
                    $this->db->query("UPDATE " . DB_PREFIX . "custom_product_suboptions SET status = '" . $suboption_properties['status'] . "', suboption_price = '" . $suboption_properties['prod_suboption_price'] . "' WHERE product_id = '" . $product_id . "' AND option_id = '" . $suboption_properties['option_id'] . "' AND option_value_id = '" . $suboption_properties['option_value_id'] . "' AND suboption_id = '" . $suboption_id . "' AND product_option_id = '" . $suboption_properties['product_option_id'] . "'");
                } else {
                    $this->db->query("UPDATE " . DB_PREFIX . "custom_product_suboptions SET status = 0, suboption_price = '" . $suboption_properties['prod_suboption_price'] . "' WHERE product_id = '" . $product_id . "' AND option_id = '" . $suboption_properties['option_id'] . "' AND option_value_id = '" . $suboption_properties['option_value_id'] . "' AND suboption_id = '" . $suboption_id . "' AND product_option_id = '" . $suboption_properties['product_option_id'] . "'");
                }
            } else {
                if (array_key_exists('status', $suboption_properties)) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "custom_product_suboptions SET product_id = '" . $product_id . "', option_value_id = '" . $suboption_properties['option_value_id'] . "', option_id = '" . $suboption_properties['option_id'] . "', suboption_name = '" . $suboption_properties['suboption_name'] . "', suboption_id = '" . $suboption_id . "', status = '" . $suboption_properties['status'] . "', suboption_price = '" . $suboption_properties['prod_suboption_price'] . "', product_option_id = '" . $suboption_properties['product_option_id'] . "'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "custom_product_suboptions SET product_id = '" . $product_id . "', option_value_id = '" . $suboption_properties['option_value_id'] . "', option_id = '" . $suboption_properties['option_id'] . "', suboption_name = '" . $suboption_properties['suboption_name'] . "', suboption_id = '" . $suboption_id . "', status = 0, suboption_price = '" . $suboption_properties['prod_suboption_price'] . "', product_option_id = '" . $suboption_properties['product_option_id'] . "'");
                }
            }
        }


    }

    public function getSuboption($suboption_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_suboptions WHERE suboption_id = '" . (int)$suboption_id . "'");

        return $query->row;
    }

    public function getSuboptions($data)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "custom_suboptions WHERE option_value_id = '" . $data['option_value_id'] ."' AND option_id = '" . $data['option_id'] . "'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getProductSuboptions($product_id, $option_group_id, $data)
    {
        $productSuboptions = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_product_suboptions WHERE product_id = '" . intval($product_id) . "' AND option_value_id = '" . intval($data['option_value_id']) ."' AND option_id = '" . intval($option_group_id) ."'")->rows;

        return $productSuboptions;
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
