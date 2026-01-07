<?php
/*
 * 2021-03-08 : Allow Length Diabled : Todsaporn S.
 * 
 */
class datastore extends dbc{
	private $length = 30;
	private $start = 0;
	
	private $table = null;
	private $dataset = null;
	private $columns = null;
	private $order = null;
	private $search = null;
	
	private $output = array();
	
	function SetParam($table,$dataset,$order,$columns,$search){
		$this->table = $table;
		$this->dataset = $dataset;
		$this->search = $search;
		$this->order = $order;
		$this->columns = $columns;
	}
	
	function SetLimit($length,$start){
		$this->length = $length;
		$this->start = $start;
	}
	
	function GetResult(){
		return $this->output;
	}
	
	function Processing(){
		$sql = $this->getSQLSelect($this->table,$this->dataset);
		$sql .= $this->getSQLWhere($this->dataset,$this->columns,$this->search);
		$sql .= $this->getSQLGroup($this->table);
		$sql .= $this->getSQLOrder($this->dataset,$this->columns,$this->order);
		$sql .= $this->getSQLLimit($this->start,$this->length);
		$rResult = $this->Query($sql);
		$rResultFilterTotal = $this->Query("SELECT FOUND_ROWS()");
		$iFilteredTotal = $this->Fetch($rResultFilterTotal)[0];
		$rResultTotal = $this->Query($this->createSQLTotal($this->table));
		$iTotal = $this->Fetch($rResultTotal)[0];
		$output = array(
			"sql" => $sql,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		while ($aRow = $this->Fetch( $rResult )){
			$row = $aRow;
			$row["DT_RowId"] = $row[$this->table['index']];
			$output['aaData'][] = $row;
		}
		
		$this->output = $output;
	}
	
	function getSQLSelect($table,$columns){
		$sql = "";
		$sql .= "SELECT SQL_CALC_FOUND_ROWS ";
		
		foreach($columns as $field => $column){
			if(!is_integer($field)){
				$sql .= $column." AS ".$field;
			}else{
				$sql .= $column;
			}
			$sql .= ",";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " FROM ";
		$sql .= $table['name'];
		if(isset($table['join'])){
			foreach($table['join'] as $join){
				$sql .= " LEFT JOIN ";
				$sql .= $join['table'];
				if(isset($join['name']))$sql .= " AS ".$join['name'];
				$sql .= " ON ";
				if(isset($join['join'])){
					$sql .= $join['join'].".".$join['field'];
				}else{
					$sql .= $table['name'].".".$join['field'];
				}
				$sql .= " = ";
				if(isset($join['name'])){
					$sql .= $join['name'].".".$join['with'];
				}else{
					$sql .= $join['table'].".".$join['with'];
				}
			}
		}
		return $sql;
	}
	
	function getSQLGroup($table){
		$sql = "";
		if(isset($table['groupby'])){
			$sql .= " GROUP BY ".$table['groupby'];
		}
		return $sql;
	}
	
	function getSQLOrder($dataset,$columns,$orders){
		$sql = "";
		if(isset($orders) AND COUNT($orders)>0){
			$sql .= " ORDER BY ";
			foreach($orders as $order){
				$column = $columns[$order['column']];
				if($column['orderable']=="true"){
					$data = isset($dataset[$column['data']])?$dataset[$column['data']]:$column['data'];
					$sql .= $data." ".$order['dir'].", ";
				}
			}
			$sql = substr_replace($sql, "", -2 );
		}
		if($sql==" ORDER B")$sql="";
		return $sql;
	}
	
	function getSQLWhere($dataset,$columns,$search){
		$sql = "";
		if($search['value'] != ""){
			$sql = " WHERE (";
			for ( $i=0 ; $i<count($columns) ; $i++ ){
				$column = $columns[$i];
				if($column['searchable']=="true"){
					$data = isset($dataset[$column['data']])?$dataset[$column['data']]:$column['data'];
					$sql .= $data." LIKE '%".$this->Escape_String($search['value'])."%' OR ";
				}
			}
			$sql = substr_replace( $sql, "", -3 );
			$sql .= ')';
		}
		
		if(isset($this->table['where'])){
			if($sql == ""){
				$sql .= " WHERE (";
			}else{
				$sql .= " AND (";
			}
			$sql .= $this->table['where'].")";
		}
		return $sql;
	}
	
	function getSQLLimit($start,$length){
		if($length == -1){
			$sql = "";
		}else{
			$sql = " LIMIT ".$this->Escape_String($start).", ".$this->Escape_String($length);
		}
		
		return $sql;
	}
	
	function createSQLTotal($table){
		if(isset($table['join'])){
			$index = $table['name'].".".$table['index'];
		}else{
			$index = $table['index'];
		}
		$sql = "";
		$sql .= "SELECT COUNT($index) FROM ";
		$sql .= $table['name'];
		if(isset($table['join'])){
			foreach($table['join'] as $join){
				$sql .= " LEFT JOIN ";
				$sql .= $join['table'];
				if(isset($join['name']))$sql .= " AS ".$join['name'];
				$sql .= " ON ";
				if(isset($join['join'])){
					$sql .= $join['join'].".".$join['field'];
				}else{
					$sql .= $table['name'].".".$join['field'];
				}
				$sql .= " = ";
				if(isset($join['name'])){
					$sql .= $join['name'].".".$join['with'];
				}else{
					$sql .= $join['table'].".".$join['with'];
				}
			}
		}
		return $sql;
	}
	
	
	

	
	
}
?>