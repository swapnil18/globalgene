<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!class_exists('CI_Model')) { class CI_Model extends Model {} }
class Common_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}
	public function getByField($table, $fieldName, $value) {
		$this->db->where($fieldName, $value);
		$query = $this->db->get($table);
		$result = $query->result_array();
		if(sizeof($result) > 0) {
			return $result[0];
		} else {
			return array();
		}
			
	}
	public function getAll($table, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	public function getJournalistAll($table, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$this->db->where('is_inactive', 0);
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	public function getAllFor($table, $fieldName, $value, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$this->db->where($fieldName, $value);
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	public function getAllOrderBy($table, $orderBy, $order, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$this->db->order_by($orderBy, $order);
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	public function getAllForOrderBy($table, $fieldName, $value, $orderBy, $order, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$this->db->where($fieldName, $value);
		$this->db->order_by($orderBy, $order);
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	public function searchFor($table, $criteria, $limit=false, $offset=0) {
		if($limit !== false) {
			$this->db->limit($limit, $offset);
		}
		if(is_array($criteria) && count($criteria) > 0) {
			foreach ($criteria as $key=>$val) {
				if(is_numeric($key)) {
					$this->db->where($val);
				} else {
					$this->db->where($key, $val);
				}
			}
		}
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
		
	}
	public function searchForOrderBy($table, $criteria, $orderBy, $order, $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		foreach ($criteria as $key=>$val) {
			if(is_numeric($key)) {
				$this->db->where($val);
			} else {
				$this->db->where($key, $val);
			}
		}
		$this->db->order_by($orderBy, $order);
		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	public function insert($table, $data) {
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}
	public function update($table, $data, $condition) {
		$this->db->update($table, $data, $condition);
	}
	public function update_batch($table, $data, $field_name, $conn = false) {
		$this->db->update_batch($table, $data, $field_name);
	}
	public function last_query() {
		return $this->db->last_query();
	}
	
	public function delete($table, $where=NULL) {
		if(is_null($where)) {
			throw Exception("Cannot delete all rows... need to specify where clause / condition");
		}
		$this->db->delete($table, $where);
	}
	
	public function delete_where_in($table, $column, $where_in=NULL) {
		if(is_null($column) || empty($column) || is_null($where_in) || empty($where_in)) {
			throw Exception("Cannot delete all rows... need to specify where clause / condition");
		}
		$this->db->where_in($column, $where_in);
		$this->db->delete($table);
	}
	public function getPrimaryKey($table) {
		$fields = $this->db->field_data($table);
		foreach ($fields as $field)
		{
			if($field->primary_key == 1) {
				return $field->name;
			}
		}
	}
	
	public function updateField($table, $field, $incrementBy, $condition) {
		$this->db->set($field, $field . $incrementBy, FALSE);
		$this->db->where($condition);
		$this->db->update($table);
	}
	
	public function countSearchFor($table, $criteria) {
		if(is_array($criteria) && count($criteria) > 0)
		foreach ($criteria as $key=>$val) {
			if(is_numeric($key)) {
				$this->db->where($val);
			} else {
				$this->db->where($key, $val);
			}
		}
		$result = $this->db->count_all_results($table);
        //echo $this->db->last_query();
        //echo "<br/>";
		return $result;
	}
	
	public function countAllFor($table, $fieldName, $value) {
		$this->db->where($fieldName, $value);
		$result = $this->db->count_all_results($table);
		return $result;
	}
	
	public function countAll($table) {
		$result = $this->db->count_all_results($table);
		return $result;
	}

	public function getDistinct($table, $fieldName) {
		$this->db->select($fieldName);
		$this->db->where("$fieldName != ''");
		$this->db->distinct();
		$query = $this->db->get($table);
		$result = $query->result_array();
		$final = array();
		foreach($result as $row) {
			array_push($final, $row[$fieldName]);
		}
		return $final;
	}
	
	 public function getByQuery($query) {
		$query = $this->db->query($query);
		if ($query->num_rows() > 0)
		{
		 $result = $query->result_array();
		 return $result;
	    }else{
	    	return array();
	    }
	}	

	 public function execQuery($query) {
		$query = $this->db->query($query);
		return $query;
		 
	}	
	public function getProcedureData($query) {
		$query = $this->db->query($query);
		$result = $query->result_array();
		$query->next_result();
        $query->free_result();
		return $result;
		if ($query->num_rows() > 0)
		{
			
	    }
	}

	public function getByWhere($table, $fieldName, $value, $columns = '', $criteria = false) {
		$this->db->where($fieldName, $value);
		if(is_array($criteria) && count($criteria) > 0) {
			foreach ($criteria as $key=>$val) {
				if(is_numeric($key)) {
					$this->db->where($val);
				} else {
					$this->db->where($key, $val);
				}
			}
		}
		if($columns != "")
		$this->db->select($columns);
		$query  = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	
	public function getByWhereOrderBy($table, $fieldName, $value, $columns = '', $criteria = false, $orderBy = '', $order= '') {
		$this->db->where($fieldName, $value);
		if(is_array($criteria) && count($criteria) > 0) {
			foreach ($criteria as $key=>$val) {
				if(is_numeric($key)) {
					$this->db->where($val);
				} else {
					$this->db->where($key, $val);
				}
			}
		}
		
		if($orderBy != "" && $order != "" )
			$this->db->order_by($orderBy, $order);
		
		if($columns != "")
		$this->db->select($columns);
		$query  = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}
	
	
	public function getByWhere_IN_OrderBy($table, $fieldName, $value, $columns = '', $orderBy = '', $order= '', $limit=false, $offset=0) {
		if($limit != false) {
			$this->db->limit($limit, $offset);
		}
		$this->db->where_in($fieldName, $value);
		
		if($orderBy != "" && $order != "" )
		$this->db->order_by($orderBy, $order);

		if($columns != "")
		$this->db->select($columns);

		$query = $this->db->get($table);
		$result = $query->result_array();
		return $result;
	}

	public function insert_batch($table, $data) {
		return $this->db->insert_batch($table, $data);		
	}
        
        public function getMultiResultSet($SqlCommand) {
            $k = 0;
            $arr_results_sets = array();
            /* execute multi query */
            if (mysqli_multi_query($this->db->conn_id, $SqlCommand)) {
                do {
                    $result = mysqli_store_result($this->db->conn_id);
                    if ($result) {
                        $l = 0;
                        while ($row = $result->fetch_assoc()) {
                            $arr_results_sets[$k][$l] = $row;                        
                            $l++;
                        }
                    }
                    $k++;
                } while (mysqli_next_result($this->db->conn_id));

                return $arr_results_sets;
            }
        }

    public function getMultiProcedureMultiResultSet($SqlCommands,$conn=false) {
        
        $arr_results_sets = array();
        $results = array();
        foreach ($SqlCommands as $SqlCommand) {
        	$arr_results_sets[] = $this->getMultiResultSet($SqlCommand);
        }
        foreach ($arr_results_sets as $arr_results_set) {
        	foreach($arr_results_set[0] as $re) {
        		$results[0][] = $re;
        	}
        	foreach($arr_results_set[1] as $re2) {
        		foreach ($re2 as $re2key=>$re) {
        			$results[1][$re2key] = $results[1][$re2key] + $re;
        		}        		
        	}
        }
        
        return $results;
        
    }


    public function getMultiProcedureResultSet($SqlCommands,$conn=false) {
        
        $arr_results_sets = array();
        $results = array();
        foreach ($SqlCommands as $SqlCommand) {
        	$arr_results_sets[] = $this->getProcedureData($SqlCommand);
        }
        foreach ($arr_results_sets as $arr_results_set) {
        	foreach($arr_results_set as $re) {
        		$results[] = $re;
        	}
        }
        
        return $results;
        
    }
}
?>