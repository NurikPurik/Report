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



$title = get_string('scheduleofexams', 'course');

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
    	<!--<div class="row">
        <div class="col-sm-12" style="float:right;text-align:right">
        	<div class="btn btn-info" id="pdf" role="button"><i class="fa fa-eye"></i> Show</div>
        	<div class="btn btn-success" id="download" role="button"><i class="fa fa-download"></i> Download</div>
        </div>
        <div>-->
    	<div class="row">
          <div class="col-md-4">
         	<label>Факультеты: </label>
         	<br/>
          	<select class="browser-default custom-select" id="faculty">
            	<option selected>Select faculty</option>
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
          <div class="col-md-4">
         	<label>Кафедры: </label>
         	<br/>
          	<select class="browser-default custom-select cafedra" id="cafedra">
            	<option selected>Select cathedra</option>
        	</select>
         </div>
        <div class="col-md-4">
         	<label>Преподаватели: </label>
         	<br/>
          	<select class="browser-default custom-select teacher">
            	<option selected>Select teacher</option>
        	</select>
         </div>
       </div>
   </div>

</section>
</div>
<br/>
<div class="records_tree" style="height:400px;">
  <section>
  <label>Total: <b class="totalrow"></b> records</label>
  		<form method="post" action="">
        <table class="table table-hover table-striped w-auto" id="mytable">
           <thead>
             <tr>
               <th>№</th>
               <th>Факультет</th>
               <th>Кафедра</th>
               <th>Шифр Специальность</th>
               <th>Шифр Дисциплины</th>
               <th>ФИО руководителя метод обьединения</th>
               <th>Виды экзаменов</th>
               <th>Даты экзаменов</th>
               <th>Время начала</th>
               <th>Продолжительность</th>
             </tr>
            </thead>
              <tbody>
             	
			  </tbody>
       	 </table>
  	</form>
	</section>
</div>

<?php 
echo $OUTPUT->footer();


?>
<script type="text/javascript">

$('#faculty').change(function (e){
    //alert(e.target.value);
    var facultyID=e.target.value;
	var funcname ="kaf";
	$('.cafedra').empty();
    
		$.ajax({
                url: '/test/selValue.php',
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

	var facultyId = $('#faculty').val();
    var cafedraId=e.target.value;
	var funcname ="teacher";
	$('.teacher').empty();

	$.ajax({
                url: '/test/selValue.php',
                type: 'POST',
                data: {fname:funcname,facultyId:facultyId,cafedraId:cafedraId },
        		dataType: 'JSON',
                success:function(data){
                  var len= data.length;
                	// console.log(data);
                  for($i=0; $i<len; $i++){
                  	var id = data[$i]['teacherId'];
                  	var name = data[$i]['teacherName'];
                    $('.teacher').append("<option value='"+id+"'>"+name+"</option>");
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

$('.teacher').change(function(e){

  
  var facultyId = 	$('#faculty').val();
  var cafID		=	$('#cafedra').val();
  var teacherID =   e.target.value;
	
	/*console.log(facultyId);
	console.log(cafID);
	console.log(teacherID);
    */
    

  var funcname ="scheduleofexams";

  $('.table tbody').empty();
  $('b.totalrow').empty();

    	$.ajax({
                url: '/test/selValue.php',
                type: 'POST',
                data: {fname:funcname, facultyId: facultyId, cafedraId:cafID, teacherId: teacherID },
        		dataType: 'json',
                success:function(data){
                
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
                    
                    	
                    	var facultyRef = data[index]['facultyRef'];
                    	var facultyName = data[index]['facultyName'];
                    	var kafedraRef = data[index]['kafedraRef'];
                    	var kafedraName =data[index]['kafedraName'];
                    	var teacherName	 = data[index]['TeacherName'];
                    	var teacherId	 = data[index]['TeacherID'];
                    	var muserID	= data[index]['muserID'];
                    	var allSpec = data[index]['allSpec'];
                    	var allDiscip = data[index]['allDiscip'];
                    	var cont_exdate = data[index]['control_exam_date'];
                    	var stime = data[index]['stime'];
                    	var longtime = data[index]['longtime'];
                    	var typeExam = data[index]['typeExam'];
                    	var author = data[index]['Autor'];
                    	var typeofexam="";
                    		if(typeExam=='СП'){
                              typeofexam ='Самостоятельно преподаватель';
                            }else if(typeExam=='КТ'){
                                 typeofexam ='Компьютерное тестирование';    
                            }else if(typeExam=='ОТ'){
                                 typeofexam ='Шифрованное тестирование';    
                            }
                   
                    		html += '<tr><td>'+j+'</td>';
        					html += '<td class="fname">'+facultyName+'</td>';
                  			html += '<td class="kafname">'+kafedraName+'</td>';
                   	 		html += '<td class="allspec">'+allSpec+'</td>';
                  			html += '<td class="alldisc">'+allDiscip+'</td>';
                    		html += '<td class="teacher">'+teacherName+'</td>';
                    		html += '<td class="typeexam">'+typeofexam+'</td>';
                  			html += '<td class="exdate">'+cont_exdate+'</td>';
                  			html += '<td class="stime">'+stime+'</td>';
                  			html += '<td class="longtime">'+longtime+' минут</td>';
                  		//	html += '<td class="department">'+studName+'</td>';
                    	//	html += '<td><a href=\'http://lms.ablaikhan.kz/user/profile.php?id='+muserID+'\' target=\'_blank\'>'+muserID+'</a></td>';

                    	html +='<td><input type="hidden" class="facultyRef"  value="'+facultyRef+'"></td>';
                  		html +='<td><input type="hidden" class="kafedraRef"  value="'+kafedraRef+'"></td>';
                    	html +='<td><input type="hidden" class="teacherId"  value="'+teacherId+'"></td>';
						html +='</tr>';

                    	$('.table tbody').append(html);
                   		j++;
                    
                     });
                
                	
                    
                    //	$('.table tbody').append('<input type="submit" class="btn btn-primary" name="updatebtn" id="updatebtn" value="Изменить">');
                    

               }
            })
      
});

$("#mytable").on('click', '#changeoptionvalue1', function () {

	/*var sOpValue = $("#mytable >tbody>tr").find("#selectbox1").val(0);
	console.log(sOpValue);
*/

$("#mytable>tbody>tr>td").each(function(index, value) {
	/*var td= $(this).children('td1').find('#selectbox1').val();	
	console.log(td);
*/
	var currentTd = $(this).find('.two1');
	
	console.log(currentTd);
	/*$(this).children('td').each(function(index, value){
    	var colst = $(this).value;	
    	console.log(colst);
    });*/
    	
 });

});


$("#pdf").click(function(e){
	var facultyId = $('#faculty').val();
	var cafedra = $(".cafedra option:selected" ).val();

	if(facultyId=="Select faculty"){
    	alert("Please select faculty");	
    }else{
    	window.open('/test/customPDF.php?fac='+facultyId+'&kaf='+cafedra+'&types=input', '_blank');
    }

});


$("#download").click(function(e){
	var facultyId = $('#faculty').val();
	var cafedra = $(".cafedra option:selected" ).val();

	if(facultyId=="Select faculty"){
    	alert("Please select faculty");	
    }else{
    	window.open('/test/customPDF.php?fac='+facultyId+'&kaf='+cafedra+'&types=download');
    }

});

$("form").submit(function (e) {
    e.preventDefault();
	var count =0;
    var totalRow =  $("#mytable td").closest("tr").length;
	var PressBtn = $(this).find("input[type=submit]:focus" );

   $("#mytable >tbody>tr").each(function(index, value) {
   
   var selectedOptions= $(value).find('select  option[value="0"]:selected').length;
   //console.log(selectedOptions);
     
   var tableID = $(value).find(".recordID").val(); 
   var facultyRef = $(value).find(".facultyRef").val(); 
   var facultyname = $(value).find(".fname").html();
   var kafedraRef = $(value).find(".kafedraRef").val();
   var kafedraname = $(value).find(".kafname").html();
   var allspec = $(value).find(".allspec").html();
   var alldisc = $(value).find(".alldisc").html();
   var department = $(value).find(".department").html();
   var course = $(value).find(".course").html();
   var semestr = $(value).find(".semestr").html();
   var credit = $(value).find(".credit").html();
   var teacher = $(value).find(".teacher").html();
   var teacherId = $(value).find(".teacherId").val();
   var sel1=  $(value).find("#selectbox1 option:selected" ).val();
   var sel2=  $(value).find("#selectbox2 option:selected" ).val();
   var sel3=  $(value).find("#selectbox3 option:selected" ).val();
   var sel4=  $(value).find("#selectbox4 option:selected" ).val();
   var sel5=  $(value).find("#selectbox5 option:selected" ).val();
   var sel6=  $(value).find("#selectbox6 option:selected" ).val();
   var sel7=  $(value).find("#selectbox7 option:selected" ).val();
   var sel8=  $(value).find("#selectbox8 option:selected" ).val();
   var sel9=  $(value).find("#selectbox9 option:selected" ).val();
   var sel10= $(value).find("#selectbox10 option:selected" ).val();
   var sel11= $(value).find("#selectbox11 option:selected" ).val();
   var sel12= $(value).find("#selectbox12 option:selected" ).val();
   var sel13= $(value).find("#selectbox13 option:selected" ).val();
   var sel14= $(value).find("#selectbox14 option:selected" ).val();
   var usernote = $(value).find('#usernote').val();
   var date = new Date();
   var dateval=date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
   
   
   if(PressBtn[0]['name']=="savebtn"){
   		
   		$.ajax({
                url: '/test/selValue.php',
                type: 'POST',
                data: {	fname:			"insert",
            			facultyRef : 	facultyRef,
            			facultyname : 	facultyname,
            			kafedraRef : 	kafedraRef,
            			kafedraname:	kafedraname,
            			allspec:		allspec,
            			alldisc:		alldisc,
            			department:		department,
   						course:			course,
            			semestr:		semestr,
            			credit:			credit,
            			teacher:		teacher,
   						sel1:			sel1,
   						sel2:			sel2,
   						sel3:			sel3,
   						sel4:			sel4,	
   						sel5:			sel5,
   						sel6:			sel6,
   						sel7:			sel7,
   						sel8:			sel8,
   						sel9:			sel9,
   						sel10:			sel10,
   						sel11:			sel11,
   						sel12:			sel12,
   						sel13:			sel13,
   						sel14:			sel14,
   						usernote:		usernote,
                        zerocount:      selectedOptions,
   						createdate:	    dateval,
                       	teacherId:		teacherId
                      },
        		dataType: 'JSON',
                success:function(data){
                  count = count +1;
                }
            })
            .done(function() {
   
   				if(totalRow == count){
      				alert("Successfully inserted");
                	window.location.reload();
   				}
            })
   }
   else if(PressBtn[0]['name']=="updatebtn"){
   			$.ajax({
                url: '/test/selValue.php',
                type: 'POST',
                data: {	fname:			"update",
                       	tableID:		tableID,
            			facultyRef : 	facultyRef,
            			facultyname : 	facultyname,
            			kafedraRef : 	kafedraRef,
            			kafedraname:	kafedraname,
            			allspec:		allspec,
            			alldisc:		alldisc,
            			department:		department,
   						course:			course,
            			semestr:		semestr,
            			credit:			credit,
            			teacher:		teacher,
   						sel1:			sel1,
   						sel2:			sel2,
   						sel3:			sel3,
   						sel4:			sel4,	
   						sel5:			sel5,
   						sel6:			sel6,
   						sel7:			sel7,
   						sel8:			sel8,
   						sel9:			sel9,
   						sel10:			sel10,
   						sel11:			sel11,
   						sel12:			sel12,
   						sel13:			sel13,
   						sel14:			sel14,
                        zerocount:      selectedOptions,
   						usernote:		usernote,
   						createdate:	    dateval
                      },
        		dataType: 'JSON',
                success:function(data){
                  count = count +1;
                }
            })
            .done(function() {
   
   				if(totalRow == count){
      				alert("Successfully Updated");
                	window.location.reload();
   				}
            })
   }
   
   /*if(PressBtn[0]['name']=="changebtn"){
       console.log(value);
   
   }*/
   
   });


});

 

function create_customPDF(response){

  	


}
</script>





