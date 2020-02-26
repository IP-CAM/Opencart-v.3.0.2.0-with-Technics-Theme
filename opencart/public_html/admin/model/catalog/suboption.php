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

    public function getSuboption($suboption_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_suboptions WHERE suboption_id = '" . (int)$suboption_id . "'");

        return $query->row;
    }

    public function getSuboptions($data)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "custom_suboptions WHERE option_value_id = '" . $data['option_value_id'] ."'";

        $query = $this->db->query($sql);

        return $query->rows;
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
