<?php
class ModelCatalogSuboption extends Model {
    public function addSuboption($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "custom_suboptions SET option_value_id = '" . (int)$data['option_value_id'] . "', suboption_name = '" . $data['suboption_name'] . "', suboption_price = '" . $data['suboption_price'] . "'");

        $option_id = $this->db->getLastId();


        return $option_id;
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

}
