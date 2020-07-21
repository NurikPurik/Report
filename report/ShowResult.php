<?php 

class ShowResult extends  Facultet{
	public function showAllFacultets(){
    	$datas = $this->getAllFacultets();
    	foreach($datas as $data){
        	echo $data['description'];
        }
    }	

}


?>