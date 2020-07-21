<?php 

class Facultet extends Dbh{
	
	public function getAllFacultets(){
    	$sql = "SELECT * FROM jos_euniversity_cat_edu_facultets WHERE active=1";
    	$result = $this->connect()->query($sql);
    	$numRows = $result->num_rows;
    	if($numRows > 0){
    		while($row = $result->fetch_array()){
        		$data[] = $row;
       		 }
    		return $data;
       }
    }

}


?>