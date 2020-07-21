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

}


switch($func){
	case "kaf":
		showkafedra($con,$fId);
	break;
	/*case "insert":
		func_insert($con,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$teacherId,$czero);        
	break;
	case "update":
		updateFunc($con,$tableID,$fref,$fname,$kref,$kname,$allspec,$alldis,$deparment,$course,$semester,$credit,$teacher,$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,$s10,$s11,$s12,$s13,$s14,$notes,$cdate,$czero);
	break;
	case "genPdf":
		generateCustomPDF($con,$facultyID,$cafedraID);
	break;
	
    */

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

function showAllRec($con,$facultyID,$cafedraID){
          
 			$con->query("SET lc_time_names = 'ru_RU'");
          	$allRecord = " SELECT 
  facultet.ref AS facultyRef,
  facultet.description AS facultyName,
  kafedra.ref AS kafedraRef,
  kafedra.description AS kafedraName,
  trup.type_schedule,
  teacher.uid,
  CONCAT(teacher.LastName, ' ',teacher.Name, ' ',teacher.SecondName) AS TeacherName, 
  CONCAT('[',rspec.code,'] ' ,rspec.description)  AS allSpec,
  CONCAT('[',rdis.code,'] ',rdis.description) AS allDiscip,
  rdis.description as disname,
  trup.course,
  trup.semestr,
  disrup.Credit as credit,
  trup.autor,
   ( SELECT COUNT(student) 
    	FROM 
    		jos_euniversity_cat_teachers_rup_students AS studrup 
    			WHERE studrup.doc_ref = trup.ref
   ) AS regStudent, trup.spec,trup.week_day
  
FROM 
 
  mdlfacultets AS facultet,
  jos_euniversity_cat_edu_kafedra AS kafedra,
  jos_euniversity_ref_teachers_kafedra AS reftk,
  jos_euniversity_users_teachers_profile AS teacher,
  jos_euniversity_cat_teachers_rup AS trup,
  jos_euniversity_ref_discipline AS rdis,
  jos_euniversity_cat_edu_specs AS rspec,
  jos_euniversity_ref_discipline_rup AS disrup
  
WHERE 
  facultet.ref= kafedra.ref_facultet AND  kafedra.ref=reftk.Kafedra AND 
  reftk.Prepod=teacher.ref AND teacher.ref = trup.teacher AND 
  trup.discipline=rdis.ref AND trup.spec = rspec.ref AND 
  trup.discipline = disrup.Discipline
  AND kafedra.active = 1 AND facultet.active=1 AND teacher.active=1 AND trup.year=1920 AND rdis.active=1 AND rspec.active=1 AND disrup.Year=1920 AND trup.apply=1 AND trup.active=1 
  AND trup.semestr IN (2,4,6) AND trup.type_schedule IN ('Семинар','ЛекцияСеминар','ПрактичРабота')
  AND trup.week_day=DAYNAME(NOW())
  AND facultet.ref='".$facultyID."'  AND kafedra.ref='".$cafedraID."'
  GROUP BY trup.teacher,rdis.ref,trup.week_day,trup.spec";
 $results = $con->query($allRecord);
 
 $allRecords_arr = array();

		while( $row = $results->fetch_array()){
        
    		$facultyRef 	= 	$row['facultyRef'];
    		$facultyName 	= 	$row['facultyName'];
        	$cafedraRef 	= 	$row['kafedraRef'];
        	$cafedraName 	= 	$row['kafedraName'];
            $teacherName 	=	$row['TeacherName'];
            $type_schedule  =   $row['type_schedule'];
        	$teacherid		=	$row['uid'];
            $regStudents	=	$row['regStudent'];
        	$allSpec		=	$row['allSpec'];
        	$allDiscip		=	$row['allDiscip'];
        	$course			= 	$row['course'];
        	$semestr		=	$row['semestr'];
        	$credit			=	$row['credit'];
        	$department		=	$row['department'];
        	$author			=	$row['autor'];
            $refSpec		= 	$row['spec'];
        	$disName		=	$row['disname'];
            $weekday		=	$row['week_day'];
        	
        
        	$idnum= mdl_user($teacherid);
        
       	//	$ccompStudent= mdlCourseCompletions($refSpec);
        	$cstud= mdl_teacher_with_course($idnum,$disName,$course);
        	
    		$allRecords_arr[] = array("attr" =>"insert",
            						  "facultyRef" 			=> 	$facultyRef, 
                                      "facultyName" 		=> 	$facultyName,
                                      "kafedraRef" 			=> 	$cafedraRef,
                                      "kafedraName" 		=> 	$cafedraName,
                                      "TeacherName" 		=> 	$teacherName,
                                      "type_schedule"		=>  $type_schedule,
                                      "TeacherID"			=>  $teacherid,
                                      "registeredStudents"	=>  $regStudents,
                                      "muserID"				=>	$idnum,
                                      "allSpec"				=> 	$allSpec,
                                      "allDiscip"			=> 	$allDiscip,
                                      "course"				=> 	$course,
                                      "semestr"				=> 	$semestr,
                                      "credit"				=> 	$credit,
                                      "department"			=> 	$department,
                                      "Autor"				=> 	$author,
                                     // "ccompstudent"		=> 	$ccompStudent,
                                      "cstud"				=> 	$cstud,
                                      "weekday"			=>	$weekday
                                     );
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

//GET how many students completed this course
function mdlCourseCompletions($refSpec){
	$return_arr = array();

	$mysqli = new mysqli('localhost', 'db_lms_main3v', 'HmE7nlFGZGT9zASf', 'db_lms_main3v_1819');
		if (mysqli_connect_errno()) {
  			echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  			exit;
		}
		$mysqli->set_charset("utf8");

		$arrvalue = $mysqli->query("SELECT COUNT(ccomp.userid) as regstudent
  								FROM  mdl_course_completions ccomp, mdl_course c, mdl_user u, mdl_course_categories mcc  
  									WHERE ccomp.course=c.id AND ccomp.userid = u.id AND mcc.id =c.category AND mcc.name='2019-2020' AND u.aim='".$refSpec."' ");

		
		while($row = $arrvalue->fetch_assoc()){
        	$return_arr = $row['regstudent'];
        }
	
return $return_arr;


}


//GET teachers with your course on moodle
function mdl_teacher_with_course($muID,$disName,$course){
	$return_arr = array();

	$mysqli = new mysqli('localhost', 'db_lms_main3v', 'HmE7nlFGZGT9zASf', 'db_lms_main3v_1819');
		if (mysqli_connect_errno()) {
  			echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  			exit;
		}
		$mysqli->set_charset("utf8");

	
		$arrvalue = $mysqli->query("SELECT
									(SELECT COUNT(ccomp.userid) FROM  mdl_course_completions ccomp WHERE ccomp.course=c.id AND ccomp.reaggregate > UNIX_TIMESTAMP(curdate())) AS ccompletedstudent
									FROM 
  										mdl_user u, mdl_role_assignments r, mdl_context cx, mdl_course c, mdl_course_categories mcc  
									WHERE 
  										u.id = r.userid 
                                		AND r.contextid = cx.id	
                                		AND cx.instanceid = c.id
										AND r.roleid IN (3,4)	
                                		AND cx.contextlevel =50 	
                                		AND c.category =mcc.id
  										AND u.id=$muID 
                                        AND mcc.name='2019-2020'  
                                		AND  c.fullname LIKE '$disName %'
                                        AND  c.fullname LIKE '%$course%'
                                        ");
		
		while($row = $arrvalue->fetch_assoc()){
        	$return_arr = $row['ccompletedstudent'];
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


