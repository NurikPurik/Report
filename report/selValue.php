<?php 


include 'Dbc.php';


$nObj = new Dbc();
$con = $nObj->connect();


if(!empty($_POST['fname'])){

	 $func = $_POST['fname'];
    
	
	if(!empty($_POST['fid'])){
    	$fId=$_POST['fid'];
    }
	
	if(!empty($_POST['facultyId']) && !empty($_POST['cafedraId'])){
    
		$facultyID =  $_POST['facultyId'];
		$cafedraID =  $_POST['cafedraId'];

	}
    if(!empty($_POST['teacherId'])){
    
      $teacherID = $_POST['teacherId'];
    
    }

	if(!empty($_POST['facultyRef']) && !empty($_POST['facultyname']) &&  !empty($_POST['kafedraRef']) && 
       !empty($_POST['kafedraname']) && !empty($_POST['allspec']) && !empty($_POST['alldisc']) && !empty($_POST['department'])
      && !empty($_POST['course']) && !empty($_POST['semestr']) && !empty($_POST['credit']) && !empty($_POST['teacher'])){
    
    	$tableID = $_POST['tableID'];
    	$fref=$_POST['facultyRef'];
        $fname = $_POST['facultyname'];
    	$kref = $_POST['kafedraRef'];
        $kname = $_POST['kafedraname'];
        $allspec = $_POST['allspec'];
        $alldis = $_POST['alldisc'];
    	$deparment = $_POST['department'];
        $course =$_POST['course'];
    	$semester = $_POST['semestr'];
        $credit = $_POST['credit'];
        $teacher = $_POST['teacher'];
        $s1 =$_POST['sel1'];
    	$s2 =$_POST['sel2'];
    	$s3 =$_POST['sel3'];
    	$s4 =$_POST['sel4'];
    	$s5 =$_POST['sel5'];
    	$s6 =$_POST['sel6'];
    	$s7 =$_POST['sel7'];
    	$s8 =$_POST['sel8'];
    	$s9 =$_POST['sel9'];
    	$s10 =$_POST['sel10'];
    	$s11 =$_POST['sel11'];
    	$s12 =$_POST['sel12'];
    	$s13 =$_POST['sel13'];
    	$s14 =$_POST['sel14'];
        $notes = $_POST['usernote'];
    	$cdate = $_POST['createdate'];
    	$teacherId = $_POST['teacherId'];
    	$czero = $_POST['zerocount'];
    }

}


switch($func){
	case "kaf":
		showkafedra($con,$fId);
	break;
	case "teacher":
		showTeacher($con,$facultyID,$cafedraID);
	break;
	case "insert":
		func_insert($con,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$teacherId,$czero);        
	break;
	case "update":
		updateFunc($con,$tableID,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$czero);
	break;
	case "genPdf":
		generateCustomPDF($con,$facultyID,$cafedraID);
	break;
	case "open":
		teacherKaf($con,$facultyID,$cafedraID);
	break;
	case "scheduleofexams":
		showAll_scheduleofexams($con,$facultyID,$cafedraID,$teacherID);
	break;
default:
		showAllRec($con,$facultyID,$cafedraID);
	break;
}


function showkafedra($con,$fId){

		$facultyID = $fId;

		$sql = "SELECT ref, description FROM jos_euniversity_cat_edu_kafedra WHERE ref_facultet='".$facultyID."' and active=1 ORDER BY description";	
		$result = $con->query($sql);       
		$cafedra_arr = array();

		while( $row = $result->fetch_array()){
    		$cafedraId = $row['ref'];
    		$cafedraName = $row['description'];
    		$cafedra_arr[] = array("cafedraId" => $cafedraId, "cafedraName" => $cafedraName);
		}
		// encoding array to json format
		echo json_encode($cafedra_arr);

}

function showTeacher($con,$facultyID,$cafedraID){

	//	$facultyID = $facultyID;
		
		


	$sql = "SELECT 
  				teacher.ref, CONCAT(teacher.LastName, ' ',teacher.Name, ' ',teacher.SecondName) AS TeacherName
			FROM 
  				mdlfacultets AS facultet,
  				jos_euniversity_cat_edu_kafedra AS kafedra,
  				jos_euniversity_ref_teachers_kafedra AS reftk,
  				jos_euniversity_users_teachers_profile AS teacher
			WHERE 
  				facultet.ref= kafedra.ref_facultet AND  kafedra.ref=reftk.Kafedra AND reftk.Prepod=teacher.ref AND 
  				kafedra.active = 1 AND facultet.active=1 AND teacher.active=1 AND 
  				facultet.ref='".$facultyID."' AND reftk.Kafedra='".$cafedraID."'   ORDER BY TeacherName ASC";	
		$result = $con->query($sql);       
		$teacher_arr = array();

		while( $row = $result->fetch_array()){
    		$teacherId = $row['ref'];
    		$teacherName = $row['TeacherName'];
    		$teacher_arr[] = array("teacherId" => $teacherId, "teacherName" => $teacherName);
		}
		// encoding array to json format
		echo json_encode($teacher_arr);

}

function showAllRec($con,$facultyID,$cafedraID){
			
			
          $newTableRecord =$con->query("SELECT * FROM mdlReports WHERE facultyRef='".$facultyID."'  AND kafedraRef='".$cafedraID."' AND is_active=1 ORDER BY id ASC "  );
		  $countRows = $newTableRecord->num_rows;
          if($countRows > 0){
          
          		while( $row = $newTableRecord->fetch_array()){

    					$tableID		=	$row['id'];
                		$facultyRef 	= 	$row['facultyRef'];
    					$facultyName 	= 	$row['facultyName'];
        				$cafedraRef 	= 	$row['kafedraRef'];
        				$cafedraName 	= 	$row['kafedraName'];
            			$teacherName 	=	$row['TeacherName'];
        				$allSpec		=	$row['allSpec'];
        				$allDiscip 		=	$row['allDiscip'];
        				$course			= 	$row['course'];
        				$semestr		=	$row['semestr'];
        				$credit			=	$row['credit'];
        				$department		=	$row['department'];
                		$teacherId		=	$row['teacherId'];
                
                		$sel1			=	$row['sel1'];
                		$sel2			=	$row['sel2'];
                		$sel3			=	$row['sel3'];
                		$sel4			=	$row['sel4'];
                		$sel5			=	$row['sel5'];
                		$sel6			=	$row['sel6'];
                		$sel7			=	$row['sel7'];
                		$sel8			=	$row['sel8'];
                		$sel9			=	$row['sel9'];
                		$sel10			=	$row['sel10'];
                		$sel11			=	$row['sel11'];
                		$sel12			=	$row['sel12'];
                		$sel13			=	$row['sel13'];
                		$sel14			=	$row['sel14'];
                		$userNote		=	$row['usernote'];
                
                		$idnum= mdl_user($teacherId);
        	
    					$allRecords_arr[] = array("attr" =>"update",
                                                  	"tableID"				=>	$tableID,
                        							"facultyRef" 			=> 	$facultyRef, 
                                      			  	"facultyName" 			=> 	$facultyName,
                                      				"kafedraRef" 			=> 	$cafedraRef,
                                      				"kafedraName" 			=> 	$cafedraName,
                                      				"TeacherName" 			=> 	$teacherName,
                                                  	"teacherID"				=>  $teacherId,
                                                  	"muserID"				=>	$idnum,
                                      				"allSpec"				=> 	$allSpec,
                                      				"allDiscip"				=> 	$allDiscip,
                                      				"course"				=> 	$course,
                                      				"semestr"				=> 	$semestr,
                                      				"credit"				=> 	$credit,
                                      				"department"			=> 	$department,
                                                  	"sel1"					=>	$sel1,
                                                  	"sel2"					=>	$sel2,
                                                  	"sel3"					=>	$sel3,
                                                  	"sel4"					=>	$sel4,
                                                  	"sel5"					=>	$sel5,
                                                  	"sel6"					=>	$sel6,
                                                  	"sel7"					=>	$sel7,
                                                  	"sel8"					=>	$sel8,
                                                  	"sel9"					=>	$sel9,
                                                  	"sel10"					=>	$sel10,
                                                  	"sel11"					=>	$sel11,
                                                  	"sel12"					=>	$sel12,
                                                  	"sel13"					=>	$sel13,
                                                  	"sel14"					=>	$sel14,
                                                 	"userNote"				=>	$userNote);
				}
				echo json_encode($allRecords_arr);
          	
          }else{
          
          	$allRecord = "SELECT 
  facultet.ref AS facultyRef,facultet.description AS facultyName,kafedra.ref AS kafedraRef,kafedra.description AS kafedraName,teacher.uid,
  CONCAT(teacher.LastName, ' ',teacher.Name, ' ',teacher.SecondName) AS TeacherName, 
  CONCAT('[',rspec.code,'] ' ,rspec.description)  AS allSpec,
  CONCAT('[',rdis.code,'] ',rdis.description) AS allDiscip,
  trup.course,trup.semestr,disrup.Credit as credit,lang.description AS department,trup.autor
FROM 
 
  mdlfacultets AS facultet,
  jos_euniversity_cat_edu_kafedra AS kafedra,
  jos_euniversity_ref_teachers_kafedra AS reftk,
  jos_euniversity_users_teachers_profile AS teacher,
  jos_euniversity_cat_teachers_rup AS trup,
  jos_euniversity_ref_discipline AS rdis,
  jos_euniversity_cat_edu_specs AS rspec,
  jos_euniversity_cat_edu_lang AS lang,
  jos_euniversity_ref_discipline_rup AS disrup
  
WHERE 
  facultet.ref= kafedra.ref_facultet AND  kafedra.ref=reftk.Kafedra AND 
  reftk.Prepod=teacher.ref AND teacher.ref = trup.teacher AND 
  trup.discipline=rdis.ref AND trup.spec = rspec.ref AND trup.lang = lang.ref AND
  trup.discipline = disrup.Discipline
  AND kafedra.active = 1 AND facultet.active=1 AND teacher.active=1 AND trup.year=1920 AND rdis.active=1 AND rspec.active=1 AND disrup.Year=1920 AND trup.apply=1 AND trup.active=1
  AND facultet.ref='".$facultyID."'  AND kafedra.ref='".$cafedraID."'
  GROUP BY trup.teacher,rdis.ref";
 $results = $con->query($allRecord);
 
 $allRecords_arr = array();

		while( $row = $results->fetch_array()){
        
    		$facultyRef 	= 	$row['facultyRef'];
    		$facultyName 	= 	$row['facultyName'];
        	$cafedraRef 	= 	$row['kafedraRef'];
        	$cafedraName 	= 	$row['kafedraName'];
            $teacherName 	=	$row['TeacherName'];
        	$teacherid		=	$row['uid'];
        	$allSpec		=	$row['allSpec'];
        	$allDiscip		=	$row['allDiscip'];
        	$course			= 	$row['course'];
        	$semestr		=	$row['semestr'];
        	$credit			=	$row['credit'];
        	$department		=	$row['department'];
        	$author			=	$row['autor'];
        	
    		$allRecords_arr[] = array("attr" =>"insert",
            						  "facultyRef" 			=> 	$facultyRef, 
                                      "facultyName" 		=> 	$facultyName,
                                      "kafedraRef" 			=> 	$cafedraRef,
                                      "kafedraName" 		=> 	$cafedraName,
                                      "TeacherName" 		=> 	$teacherName,
                                      "TeacherID"			=>  $teacherid,
                                      "allSpec"				=> 	$allSpec,
                                      "allDiscip"			=> 	$allDiscip,
                                      "course"				=> 	$course,
                                      "semestr"				=> 	$semestr,
                                      "credit"				=> 	$credit,
                                      "department"			=> 	$department,
                                      "Autor"				=> 	$author);
		}
		// encoding array to json format
		echo json_encode($allRecords_arr);     
  }

}

function showAll_scheduleofexams($con,$facultyID,$cafedraID,$teacherID){

	/*$allRecord = "

  SELECT 
  facultet.ref AS facultyRef,facultet.description AS facultyName,kafedra.ref AS kafedraRef,kafedra.description AS kafedraName,teacher.uid,teacher.ref,
  CONCAT(teacher.LastName, ' ',teacher.Name, ' ',teacher.SecondName) AS TeacherName, 
  CONCAT('[',rspec.code,'] ' ,rspec.description)  AS allSpec,
  CONCAT('[',rdis.code,'] ',rdis.description) AS allDiscip,trup.control_exam_date,trup.stime,trup.longtime,control_exam_type

FROM 
 
  mdlfacultets AS facultet,
  jos_euniversity_cat_edu_kafedra AS kafedra,
  jos_euniversity_ref_teachers_kafedra AS reftk,
  jos_euniversity_users_teachers_profile AS teacher,
  jos_euniversity_cat_teachers_rup AS trup,
  jos_euniversity_ref_discipline AS rdis,
  jos_euniversity_cat_edu_specs AS rspec,
  jos_euniversity_cat_edu_lang AS lang,
  jos_euniversity_ref_discipline_rup AS disrup
  
WHERE 
  facultet.ref= kafedra.ref_facultet AND  kafedra.ref=reftk.Kafedra AND 
  reftk.Prepod=teacher.ref AND teacher.ref = trup.teacher AND 
  trup.discipline=rdis.ref AND trup.spec = rspec.ref AND trup.lang = lang.ref AND
  trup.discipline = disrup.Discipline 
  AND kafedra.active = 1 AND facultet.active=1 AND teacher.active=1 AND trup.year=1920 AND rdis.active=1 AND rspec.active=1 AND disrup.Year=1920 AND trup.apply=1 AND trup.active=1
  AND facultet.ref='".$facultyID."'  AND kafedra.ref='".$cafedraID."' AND trup.teacher='".$teacherID."'
  AND trup.type_schedule='Экзамен' AND trup.control_exam_type='СП' AND trup.year=1920 AND  UNIX_TIMESTAMP(trup.control_exam_date) > '1589328000'
GROUP BY teacher.ref";
*/
$allRecord = "

  SELECT 
  facultet.ref AS facultyRef,facultet.description AS facultyName,kafedra.ref AS kafedraRef,kafedra.description AS kafedraName,teacher.uid,teacher.ref,
  CONCAT(teacher.LastName, ' ',teacher.Name, ' ',teacher.SecondName) AS TeacherName, 
  CONCAT('[',rspec.code,'] ' ,rspec.description)  AS allSpec,
  CONCAT('[',rdis.code,'] ',rdis.description) AS allDiscip,trup.control_exam_date,trup.stime,trup.longtime,control_exam_type

FROM 
 
  mdlfacultets AS facultet,
  jos_euniversity_cat_edu_kafedra AS kafedra,
  jos_euniversity_ref_teachers_kafedra AS reftk,
  jos_euniversity_users_teachers_profile AS teacher,
  jos_euniversity_cat_teachers_rup AS trup,
  jos_euniversity_ref_discipline AS rdis,
  jos_euniversity_cat_edu_specs AS rspec,
  jos_euniversity_cat_edu_lang AS lang,
  jos_euniversity_ref_discipline_rup AS disrup
  
WHERE 
  facultet.ref= kafedra.ref_facultet AND  kafedra.ref=reftk.Kafedra AND 
  reftk.Prepod=teacher.ref AND teacher.ref = trup.teacher AND 
  trup.discipline=rdis.ref AND trup.spec = rspec.ref AND trup.lang = lang.ref AND
  trup.discipline = disrup.Discipline 
  AND kafedra.active = 1 AND facultet.active=1 AND teacher.active=1 AND trup.year=1920 AND rdis.active=1 AND rspec.active=1 AND disrup.Year=1920 AND trup.apply=1 AND trup.active=1 AND trup.semestr IN (2,4,6)
  AND facultet.ref='".$facultyID."'  AND kafedra.ref='".$cafedraID."' AND trup.teacher='".$teacherID."'
GROUP BY disrup.Discipline";

 $results = $con->query($allRecord);
 
 $allRecords_arr = array();

		while( $row = $results->fetch_array()){
        
    		$facultyRef 	= 	$row['facultyRef'];
    		$facultyName 	= 	$row['facultyName'];
        	$cafedraRef 	= 	$row['kafedraRef'];
        	$cafedraName 	= 	$row['kafedraName'];
            $teacherName 	=	$row['TeacherName'];
        	$teacherid		=	$row['uid'];
        	$allSpec		=	$row['allSpec'];
        	$allDiscip		=	$row['allDiscip'];
        	$cont_exdate	= 	$row['control_exam_date'];
        	$stime			=	$row['stime'];
        	$longtime		=	$row['longtime'];
        	$typeexam		=	$row['control_exam_type'];
        	$author			=	$row['autor'];
        	
    		$allRecords_arr[] = array("facultyRef" 			=> 	$facultyRef, 
                                      "facultyName" 		=> 	$facultyName,
                                      "kafedraRef" 			=> 	$cafedraRef,
                                      "kafedraName" 		=> 	$cafedraName,
                                      "TeacherName" 		=> 	$teacherName,
                                      "TeacherID"			=>  $teacherid,
                                      "allSpec"				=> 	$allSpec,
                                      "allDiscip"			=> 	$allDiscip,
                                      "control_exam_date"	=> 	$cont_exdate,
                                      "stime"				=> 	$stime,
                                      "longtime"			=> 	$longtime,
                                      "typeExam"			=> 	$typeexam,
                                      "Autor"				=> 	$author);
		}
		// encoding array to json format
		echo json_encode($allRecords_arr); 
}

//GET Only one user ID;
function mdl_user($id){
	$return_arr = array();

	$mysqli = new mysqli('localhost', 'db_lms_main3v', 'HmE7nlFGZGT9zASf', 'db_lms_main3v_1819');
		if (mysqli_connect_errno()) {
  			echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  			exit;
		}
		$mysqli->set_charset("utf8");

		$category = $mysqli->query("SELECT id FROM mdl_user WHERE idnumber = $id");
		
		while($row = $category->fetch_assoc()){
        	$return_arr = $row['id'];
        }
	
return $return_arr;


}



function func_insert($con,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$teacherId,$czero){

	$sql ="INSERT INTO mdlReports 
    		(facultyRef,facultyName,kafedraRef,kafedraName,allSpec,allDiscip,department,course,
            semestr,credit,TeacherName,sel1,sel2,sel3,sel4,sel5,sel6,sel7,sel8,sel9,sel10,sel11,sel12,sel13,sel14,usernote,date,pdf,teacherId,countzero,is_active) 
    	  VALUES ('".$fref."', '".$fname."', '".$kref."' ,'".$kname."','".$allspec."','".$alldis."','".$deparment."',
          			$course,$semester,$credit,'".$teacher."',$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,'".$notes."','".$cdate."','',$teacherId, $czero,1)";	
	$results = $con->query($sql);
	if($results){
    	echo 'true';
    }else{
    	echo 'false';
    }

}


function updateFunc($con,$tableID,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,
                     $s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$czero){

	$sql = "UPDATE mdlReports SET sel1=$s1,sel2=$s2,sel3=$s3,sel4=$s4,sel5=$s5,sel6=$s6,sel7=$s7,sel8=$s8,sel9=$s9,sel10=$s10,sel11=$s11,sel12=$s12,sel13=$s13,sel14=$s14,usernote='".$notes."',date='".$cdate."',countzero=$czero
             WHERE facultyRef='".$fref."' AND facultyName='".$fname."' AND  kafedraRef='".$kref."' AND kafedraName='".$kname."' AND allSpec='".$allspec."' AND allDiscip='".$alldis."'AND department='".$deparment."'
             AND course=$course AND semestr=$semester AND credit=$credit AND TeacherName='".$teacher."' AND id=$tableID AND is_active=1 ";	
    $results = $con->query($sql);
	if($results){
    	echo 'true';
    }else{
    	echo 'false';
    }

}


function generateCustomPDF($con,$facultyID,$cafedraID){
	$sql1 =$con->query("SELECT * FROM mdlReports WHERE facultyRef='".$facultyID."'  AND kafedraRef='".$cafedraID."' AND is_active=1 ORDER BY id ASC ");
	
	while( $row = $sql1->fetch_array()){
        
                		$facultyRef 	= 	$row['facultyRef'];
    					$facultyName 	= 	$row['facultyName'];
        				$cafedraRef 	= 	$row['kafedraRef'];
        				$cafedraName 	= 	$row['kafedraName'];
            			$teacherName 	=	$row['TeacherName'];
        				$allSpec		=	$row['allSpec'];
        				$allDiscip 		=	$row['allDiscip'];
        				$course			= 	$row['course'];
        				$semestr		=	$row['semestr'];
        				$credit			=	$row['credit'];
        				$department		=	$row['department'];
                
                		$sel1			=	$row['sel1'];
                		$sel2			=	$row['sel2'];
                		$sel3			=	$row['sel3'];
                		$sel4			=	$row['sel4'];
                		$sel5			=	$row['sel5'];
                		$sel6			=	$row['sel6'];
                		$sel7			=	$row['sel7'];
                		$sel8			=	$row['sel8'];
                		$sel9			=	$row['sel9'];
                		$sel10			=	$row['sel10'];
                		$sel11			=	$row['sel11'];
                		$sel12			=	$row['sel12'];
                		$sel13			=	$row['sel13'];
                		$sel14			=	$row['sel14'];
                		$userNote		=	$row['usernote'];
        	
    					$attr_genpdf[] = array("facultyName" 			=> 	$facultyName,
                                      				"kafedraName" 			=> 	$cafedraName,
                                      				"TeacherName" 			=> 	$teacherName,
                                      				"allSpec"				=> 	$allSpec,
                                      				"allDiscip"				=> 	$allDiscip,
                                      				"course"				=> 	$course,
                                      				"semestr"				=> 	$semestr,
                                      				"credit"				=> 	$credit,
                                      				"department"			=> 	$department,
                                                  	"sel1"					=>	$sel1,
                                                  	"sel2"					=>	$sel2,
                                                  	"sel3"					=>	$sel3,
                                                  	"sel4"					=>	$sel4,
                                                  	"sel5"					=>	$sel5,
                                                  	"sel6"					=>	$sel6,
                                                  	"sel7"					=>	$sel7,
                                                  	"sel8"					=>	$sel8,
                                                  	"sel9"					=>	$sel9,
                                                  	"sel10"					=>	$sel10,
                                                  	"sel11"					=>	$sel11,
                                                  	"sel12"					=>	$sel12,
                                                  	"sel13"					=>	$sel13,
                                                  	"sel14"					=>	$sel14,
                                                 	"userNote"				=>	$userNote);
				}
				echo json_encode($attr_genpdf);
}








?>


