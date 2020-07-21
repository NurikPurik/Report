<?php

// @author Jabai Bakhytkhan
// @desc Custom reports for all teachers created courses 
// @comm Using OOPs -- Dbc.php=> Class connect to database, Ajax function
// @date 20.12.2019 10:17
// @route /test/DepReport
// @access Public


include 'Dbc.php';

require_once('../config.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/messagelib.php');



$title = get_string('reportdto', 'course');

$PAGE->set_title($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$dbObj = new Dbc();
$con =  $dbObj->connect();

$sql ="SELECT * FROM mdlfacultets WHERE active=1";
$result = $con->query($sql);




$query ="SELECT * FROM mdlReports";
$allresult = $con->query($query);



ob_start();
require('fpdf/fpdf.php');
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('helvetica','',16);
header("Content-Encoding: None", true);
//$pdf->Cell(189 ,20,' I am in FPDF',1,1,'C');
$pdf->Cell(40,10,'My name is bakhitkhan!');





while($row = $allresult->fetch_array()){
	
    $facultyName = $row['TeacherName'];
	$pdf->Cell(10,1,$facultyName,1);
	break;
	
}
ob_end_flush();
$pdf->Output(F,'reportCafedra.pdf');


?>

<div class="profile_tree">
 <section class="node_category">

    <div class="containter">
    	<div class="row">
 			<!--<a href="customPDF.php" class="btn btn-info"  target="_blank" >SHOW PDF FILE</a>-->
       <!-- <div class="col-sm-12" style="float:right;text-align:right">
        	<div class="btn btn-info" id="pdf" role="button"><i class="fa fa-eye"></i> Show</div>
        	<div class="btn btn-success" id="download" role="button"><i class="fa fa-download"></i> Download</div>
        </div>-->
        <div>
    	<div class="row">
          <div class="col-md-6">
         	<label>Факультеты: </label>
         	<br/>
          	<select class="browser-default custom-select" id="faculty">
            	<option selected>Выберите факультет</option>
            	<?php  if($result)
						{
    						while ($obj = $result->fetch_object())
                        	{
           	 					echo "<option value='".$obj->ref."'>$obj->description</option>";
            	  			}
    					 $result->close();
					   }   
           		  ?>
        	</select>
         </div>
          <div class="col-md-6">
         	<label>Кафедры: </label>
         	<br/>
          	<select class="browser-default custom-select cafedra">
            	<option selected>Выберите кафедру</option>
        	</select>
         </div>
       </div>
   </div>

</section>
</div>
<br/>
<div class="records_tree" style="height:400px;">
  <section>
  <label>Результат: <b class="totalrow"></b> посещаемость</label>
  		<form method="post" action="">
        <table class="table table-hover table-striped w-auto" id="mytable">
           <thead>
             <tr>
               <th>№</th>
               <th>Наименование ОП</th>
               <th>Дисциплина</th>
               <th>Тип</th>
               <th>День</th>
               <th>Преподователь</th>
               <th>Ссылка на курс</th>
               <th>Курс</th>
               <th>Кол-во</th>
               <th>Кол-во</th>
             </tr>
            </thead>
              <tbody>
             	
			  </tbody>
       	 </table>
  	</form>
	</section>
</div>

<?php 
 /*
echo '<pre>';
 print_r($cafID);
echo '</pre>';*/
echo $OUTPUT->footer();


?>

<script type="text/javascript">
$('#faculty').change(function (e){
    //alert(e.target.value);
    var facultyID=e.target.value;
	var funcname ="kaf";
	$('.cafedra').empty(); 
		$.ajax({
                url: '/test/dtovalue.php',
                type: 'POST',
                data: {fname:funcname,fid:facultyID },
        		dataType: 'JSON',
                success:function(data){
                
                  var len= data.length;
                	// console.log(data);
                  for($i=0; $i<len; $i++){
                  	var id = data[$i]['cafedraId'];
                  	var name = data[$i]['cafedraName'];
                    $('.cafedra').append("<option value='"+id+"'>"+name+"</option>");
                  }
                }
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
});
$('.cafedra').change(function(e){

  var cafID=e.target.value;
  var facultyId = $('#faculty').val();
  var funcname ="getAll";
 
  
  $('.table tbody').empty();
  $('b.totalrow').empty();

    	$.ajax({
                url: '/test/dtovalue.php',
                type: 'POST',
                data: {fname:funcname, facultyId: facultyId, cafedraId:cafID},
        		dataType: 'json',
                success:function(data)
        		{
               
                  // console.log(data);
                  var len= data.length;
                  var fstatus ='';
                  for($i=0; $i<len; $i++){ fstatus = data[$i]['attr']; break; }
                   $('.totalrow').append(len);
                    var j = 1;
                 	var html = "";
                  	$.each(data, function(index, value){
                    	var facultyRef = data[index]['sel1'];
                       	
                    	var queryAttr = data[index]['attr'];
                       
                    	var html = "";
                        var tableID	= data[index]['tableID'];
                    	var courseId = data[index]['uid'];
                    
                    	
                    
                    	var facultyName = data[index]['facultyName'];
                    	var kafedraRef = data[index]['kafedraRef'];
                    	var kafedraName =data[index]['kafedraName'];
                    	var teacherName	 = data[index]['TeacherName'];
                    	var typeschedule	 = data[index]['type_schedule'];
                    	var teacherId	 = data[index]['TeacherID'];
                        var registeredStudents = data[index]['registeredStudents'];
                    	var muserID	= data[index]['muserID'];
                    	var allSpec = data[index]['allSpec'];
                    	var allDiscip = data[index]['allDiscip'];
                    	var course = data[index]['course'];
                    	var semestr = data[index]['semestr'];
                    	var credit = data[index]['credit'];
                    	var department = data[index]['department'];
                    	var author = data[index]['Autor'];
                        var ccompstudent = data[index]['ccompstudent'];
                   		var cstud = data[index]['cstud'];
                    	var weekday= data[index]['weekday'];
                    
                  
                    		html += '<tr><td>'+j+'</td>';
        					
                  			html += '<td class="allspec">'+allSpec+'</td>';
                   	 		html += '<td class="alldisc">'+allDiscip+'</td>';
                  			html += '<td class="typschedule">'+typeschedule+'</td>';
                    		html += '<td class="typschedule">'+weekday+'</td>';
                  			html += '<td class="teacher">'+teacherName+'</td>';
                    	    html += '<td><a href=\'http://lms.ablaikhan.kz/user/profile.php?id='+muserID+'\' target=\'_blank\'>'+muserID+'</a></td>';
                  			html += '<td class="course">'+course+'</td>';
                  			html += '<td class="semestr">'+registeredStudents+'</td>';
                  			// html += '<td class="credit">'+ccompstudent+'</td>';
                    		html += '<td class="credit">'+cstud+'</td>';
 
                    		
                    	
          
						html +='</tr>';

                    	$('.table tbody').append(html);
                   		j++;
                    
                     });
                
                

               }
            })
      
});
</script>





