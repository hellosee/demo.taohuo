<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	protected $table = '';
    protected $db_select = '';
    protected $join_table = array();

    public function __construct() {
        parent::__construct();
    }
    public function setTable( $table = '' ){
        //$this->table = $table;
        return $this;
    }

    public function setDB($db = ''){
        $this->db_select = $db;
        return $this;
    }

    public function setJoinTable( $tables = array() ){
        $this->join_table = $tables;
        return $this;
    }
    /*
     * 查询一条数据，并返回值
     * @param $field 查询字段
     * @param $where 查询条件
     * return NULL | string
     */
    public function getVal($field = '', $where = array(), $order_by = '', $group_by = '') {
        $data = $this->getData($field, $where, 1, 0, $order_by, $group_by);
        return empty($data) ? '' : current($data[0]);
    }

    public function _sql(){
        return $this->db->last_query();
    }
    /**
     * sql查询
     * @param string $sql
     * @return mixed
     */
    public function sqlQuery( $sql =""){
        return $this->db->query($sql);
    }
    
    /**
     * 字段增加/减少
     * @param string $field
     * @param int $score
     * @param array $where
     * @param int $type
     */
    public function incDer($field='',$score=0,$where=array(),$type = 1 ){
    	$this->db->where($where);
    	if($type){
    		$this->db->set($field,"$field + $score",false);
    	} else {
    		$this->db->set($field,"$field - $score",false);
    	}
    	return $this->db->update($this->table);
    }

    /**
     * 单表插入 bate
     * @param $table
     * @param $fields
     * @param int $type
     * @param int $return_type
     * @return array|string
     */
    public function singleInsert($table,$fields,$type=0,$return_type=0){
        if (!is_array($fields) || count($fields) == 0) return array();
        $sql_field = "";
        $sql_value = "";
        foreach( $fields as $key => $value){
            $sql_field .= ",`$key`";
            $sql_value .= ",`$value`";
        }
        $sql_field = substr($sql_field, 1);
        $sql_value = substr($sql_value, 1);
        if($type == 0){
            $sql = "insert ignore  into $table ($sql_field) values ($sql_value)"; //组合SQL
        } else {
            $sql = "replace into $table ($sql_field) values ($sql_value)"; //组合SQL
        }

        $result = $this->db->query($sql);
        if( $return_type ){
            return $sql;
        }
        return $result;
    }

    /*
     * 查询一条数据
     * @param $field 查询字段
     * @param $where 查询条件
     * @param $order_by 排序字段
     * @param $group_by 分组字段
     * return array
     */

    public function getOne($fields = '', $where = array(), $order_by = '', $group_by = '') {
        $data = $this->getData($fields, $where, 1, 0, $order_by, $group_by);
        if(empty($data)){
            return array();
        }
        return $data[0];
    }

    public function numRows($where = array(), $group_by = '') {
        $field = empty($group_by) ? '*' : 'DISTINCT ' . $group_by;
        return $this->getVal('COUNT(' . $field . ') AS `numrows`', $where);
    }

    public function joinTable() {
        if (empty($this->join_table)) {
            return false;
        }
        foreach ($this->join_table as $type => $tables) {
            if (empty($tables)) {
                continue;
            }
            foreach ($tables as $table1 => $table2) {
                $compopr = '';
                if (!self::_has_operator($table1)) {
                    $compopr = ' = ';
                }
                $cond = $table1 . $compopr . $table2;
                list($table) = explode('.', $table2);
                $this->db->join($table, $cond, $type);
            }
        }
        $this->join_table = array();
    }

    /*
     * 查询数据
     * @param $fields 查询字段
     * @param $where 查询条件
     * @param $group_by 分组字段
     * @param $order_by 排序字段
     * @param $limit 查询条数
     * @param $offset 查询位置
     * return array 如果$limit为1，返回一维数组(row_array)，否则返回二维数组(result_array)
     */

    public function getData($fields = '*', $where = array(), $limit = 0, $offset = 0, $order_by = '', $group_by = '') {
        if (is_array($fields)) {
            $fields = self::_build_fields($fields);
        }
        if(!empty($this->db_select)) {
        	$this->db->db_select($this->db_select);
        }
        $this->db->select($fields);
        $this->db->from($this->table);
        $this->joinTable();
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        if ($query->num_rows() < 1) {
            return array();
        }
        return $query->result_array();
    }
    

    public function setData($data = array(), $where = array(), $check_exist = false) {
        if (empty($this->table)) {
            return 0;
        }
        if(!empty($this->db_select)) {
        	$this->db->db_select($this->db_select);
        }
        if (empty($where)) {
            $data = self::initData($data);
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
        if ($check_exist) {
            $ret = $this->getData('*', $where, 1);
            if (empty($ret)) {
                return $this->setData($data);
            }
        }
        $this->db->where($where);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    
    public function delData($where = array()) {
        if (empty($this->table)) {
            return 0;
        }
        if(!empty($this->db_select)) {
        	$this->db->db_select($this->db_select);
        }
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                if (is_array($val)) {
                    if (count($val) > 1) {
                        $this->db->where_in($key, $val);
                    } else {
                        $this->db->where($key, $val[0]);
                    }
                } else {
                    $this->db->where($key, $val);
                }
            }
        } else {
            $this->db->where($where);
        }
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    
    public function getFields() {
    	$fields = array();
        $fields_data = $this->db->field_data($this->table);
        foreach ($fields_data as $field) {
        	$fields[$field->name] = $field->type;
        }
        return $fields;
    }

    protected function initData($data = array(), $initVal = true) {
        $fields = $this->getFields();
        $newdata = array();
        foreach ($fields as $name => $type) {
            if (!isset($data[$name]) && $initVal) {
                $data[$name] = '';
            }
            if (isset($data[$name])) {
                $type = strtolower($type);
                if (in_array($type, array('tinyint', 'smallint', 'mediumint', 'bigint', 'integer', 'int', 'timestamp', 'real'))) {
                    $data[$name] = $data[$name];
                } elseif (in_array($type, array('float', 'decimal', 'double', 'numeric'))) {
                    $data[$name] = floatval($data[$name]);
                } elseif (empty($data[$name])) {
                    if ($type == 'date') {
                        $data[$name] = '0000-00-00';
                    } elseif ($type == 'time') {
                        $data[$name] = '00:00:00';
                    } elseif ($type == 'datetime') {
                        $data[$name] = '0000-00-00 00:00:00';
                    }
                }
                $newdata[$name] = $data[$name];
            }
        }
        return $newdata;
    }

    static private function _build_fields($field = array()) {
        $select = '';
        foreach ($field as $table => $field) {
            $table = trim($table);
            if (!empty($table)) {
                $field = $table . '.' . $field;
                $field = str_replace(',', ',' . $table . '.', $field);
            }
            $select .= $field . ',';
        }
        $select = rtrim($select, ',');
        return $select;
    }

    static private function _has_operator($string = '') {
        return (bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($string));
    }

}