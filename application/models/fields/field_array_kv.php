<?php
include_once(APPPATH."models/fields/field_array.php");
class Field_array_kv extends Field_array {
    
    public function __construct($show_name,$name,$is_must_input=false) {
        parent::__construct($show_name,$name,$is_must_input);
        $this->typ = "Field_array_kv";
        
        $this->value = array();
        $this->allKeys = array();
    }

    public function getAllKeys(){
        global $cache_global_kv;
        if (isset($cache_global_kv) && isset($cache_global_kv[$this->typ])){
            $this->allKeys = $cache_global_kv[$this->typ];
            return;
        }

        $this->db->select(array($this->keyField,$this->showField));
        $query = $this->db->get($this->tableName);
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $id = (string)$row[$this->keyField];

                $this->allKeys[$id] = (string)$row[$this->showField];
            }
        }
        $cache_global_kv[$this->typ] = $this->allKeys;
    }

    public function gen_editor($typ=0){
        $this->editor_typ = $typ;
        $this->CI->kvEditorData = $this;
        $editor = $this->CI->load->view('editor/array_kv', '', true);
        return $editor;
    }
    
}
?>