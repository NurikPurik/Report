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



$title = get_string('allreports', 'course');

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
        <div class="col-sm-12" style="float:right;text-align:right">
        	<div class="btn btn-info" id="pdf" role="button"><i class="fa fa-eye"></i> Show</div>
        	<div class="btn btn-success" id="download" role="button"><i class="fa fa-download"></i> Download</div>
        </div>
        <div>
    	<div class="row">
          <div class="col-md-6">
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
          <div class="col-md-6">
         	<label>Кафедры: </label>
         	<br/>
          	<select class="browser-default custom-select cafedra">
            	<option selected>Select cathedra</option>
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
               <th>Отделение</th>
               <th>Курс</th>
               <th>Семестр</th>
               <th>Кол-во кредитов</th>
               <th>ФИО руководителя метод обьединения</th>
               <th>UserID</th>
               <th>1</th>
               <th>2</th>
               <th>3</th>
               <th>4</th>
               <th>5</th>
               <th>6</th>
               <th>7</th>
               <th>8</th>
               <th>9</th>
               <th>10</th>
               <th>11</th>
               <th>12</th>
               <th>13</th>
               <th>14</th>
               <th>Примечание</th>
             
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

  var cafID=e.target.value;
  var facultyId = $('#faculty').val();
  var funcname ="getAll";

  $('.table tbody').empty();
  $('b.totalrow').empty();

    	$.ajax({
                url: '/test/selValue.php',
                type: 'POST',
                data: {fname:funcname, facultyId: facultyId, cafedraId:cafID },
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
                    	var course = data[index]['course'];
                    	var semestr = data[index]['semestr'];
                    	var credit = data[index]['credit'];
                    	var department = data[index]['department'];
                    	var author = data[index]['Autor'];
                    
                    		var sel1 	= 	data[index]['sel1'];
                        	var sel2 	= 	data[index]['sel2'];
                        	var sel3 	= 	data[index]['sel3'];
                        	var sel4 	= 	data[index]['sel4'];
                        	var sel4 	= 	data[index]['sel4'];
                        	var sel5 	= 	data[index]['sel5'];
                        	var sel6 	= 	data[index]['sel6'];
                        	var sel7 	= 	data[index]['sel7'];
                        	var sel8 	= 	data[index]['sel8'];
                        	var sel9 	= 	data[index]['sel9'];
                        	var sel10 	= 	data[index]['sel10'];
                        	var sel11 	= 	data[index]['sel11'];
                        	var sel12 	= 	data[index]['sel12'];
                        	var sel13 	= 	data[index]['sel13'];
                        	var sel14 	= 	data[index]['sel14'];
                        	var userNote = 	data[index]['userNote'];
                        	var selElement = sel1+' - '+sel2+' - '+sel3+' - '+sel4+' - '+sel5+' - '+sel6+' - '+sel7+' - '+sel8+' - '+sel9+' - '+sel10+' - '+sel11+' - '+sel12+' - '+sel13+' - '+sel14;
							
                    		html += '<tr><td>'+j+'</td>';
        					html += '<td class="fname">'+facultyName+'</td>';
                  			html += '<td class="kafname">'+kafedraName+'</td>';
                   	 		html += '<td class="allspec">'+allSpec+'</td>';
                  			html += '<td class="alldisc">'+allDiscip+'</td>';
                  			html += '<td class="department">'+department+'</td>';
                  			html += '<td class="course">'+course+'</td>';
                  			html += '<td class="semestr">'+semestr+'</td>';
                  			html += '<td class="credit">'+credit+'</td>';
                  			html += '<td class="teacher">'+teacherName+'</td>';
                    		html += '<td><a href=\'http://lms.ablaikhan.kz/user/profile.php?id='+muserID+'\' target=\'_blank\'>'+muserID+'</a></td>';
                    	if(queryAttr == 'insert'){
                       
							html += '<td class=two'+j+'><select class="browser-default custom-select s1" id="selectbox1" name="sbox1"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s2" id="selectbox2" name="sbox2"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s3" id="selectbox3" name="sbox3"><option value="1">есть</option><option value="0">нет</option>></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s4" id="selectbox4" name="sbox4"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s5" id="selectbox5" name="sbox5"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s6" id="selectbox6" name="sbox6"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s7" id="selectbox7" name="sbox7"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s8" id="selectbox8" name="sbox8"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s9" id="selectbox9" name="sbox9"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s10" id="selectbox10" name="sbox10"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s11" id="selectbox11" name="sbox11"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s12" id="selectbox12" name="sbox12"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s13" id="selectbox13" name="sbox13"><option value="1">есть</option><option value="0">нет</option></td>';
                  			html += '<td class=two'+j+'><select class="browser-default custom-select s14" id="selectbox14" name="sbox14"><option value="1">есть</option><option value="0">нет</option></td>';
                    		html +='<td><textarea id="usernote" class="md-textarea form-control" rows="3"></textarea></td>';
                  			
                        }else{

                        	if(sel1==1){
                            	html += '<td><select class="custom-select" id="selectbox1" name="sbox1"><option value='+sel1+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox1" name="sbox1"><option value="1">есть</option><option selected="selected" value='+sel1+'>нет</option></select></td>';
                            }
                        
                        	if(sel2==1){
                        		html += '<td><select class="custom-select" id="selectbox2" name="sbox2"><option value='+sel2+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox2" name="sbox2"><option value="1">есть</option><option value='+sel2+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel3==1){
                        		html += '<td><select class="custom-select" id="selectbox3" name="sbox3"><option value='+sel3+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox3" name="sbox3"><option value="1">есть</option><option value='+sel3+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel4==1){
                        		html += '<td><select class="custom-select" id="selectbox4" name="sbox4"><option value='+sel4+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox4" name="sbox4"><option value="1">есть</option><option value='+sel4+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel5==1){
                        		html += '<td><select class="custom-select" id="selectbox5" name="sbox5"><option value='+sel5+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox5" name="sbox5"><option value="1">есть</option><option value='+sel5+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel6==1){
                        		html += '<td><select class="custom-select" id="selectbox6" name="sbox6"><option value='+sel6+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox6" name="sbox6"><option value="1">есть</option><option value='+sel6+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel7==1){
                        		html += '<td><select class="custom-select" id="selectbox7" name="sbox7"><option value='+sel7+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox7" name="sbox7"><option value="1">есть</option><option value='+sel7+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel8==1){
                        		html += '<td><select class="custom-select" id="selectbox8" name="sbox8"><option value='+sel8+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox8" name="sbox8"><option value="1">есть</option><option value='+sel8+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel9==1){
                        		html += '<td><select class="custom-select" id="selectbox9" name="sbox9"><option value='+sel9+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox9" name="sbox9"><option value="1">есть</option><option value='+sel9+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel10==1){
                        		html += '<td><select class="custom-select" id="selectbox10" name="sbox10"><option value='+sel10+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox10" name="sbox10"><option value="1">есть</option><option value='+sel10+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel11==1){
                        		html += '<td><select class="custom-select" id="selectbox11" name="sbox11"><option value='+sel11+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox11" name="sbox11"><option value="1">есть</option><option value='+sel11+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel12==1){
                        		html += '<td><select class="custom-select" id="selectbox12" name="sbox12"><option value='+sel12+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox12" name="sbox12"><option value="1">есть</option><option value='+sel12+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel13==1){
                        		html += '<td><select class="custom-select" id="selectbox13" name="sbox13"><option value='+sel13+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox13" name="sbox13"><option value="1">есть</option><option value='+sel13+' selected="selected">нет</option></select></td>';
                            }
                        	if(sel14==1){
                        		html += '<td><select class="custom-select" id="selectbox14" name="sbox14"><option value='+sel14+' selected="selected">есть</option><option value="0">нет</option></select></td>';
                            }else{
                            	html += '<td><select class="custom-select" id="selectbox14" name="sbox14"><option value="1">есть</option><option value='+sel14+' selected="selected">нет</option></select></td>';
                            }
                        
                    		html +='<td><textarea id="usernote" class="md-textarea form-control" rows="3">'+userNote+'</textarea></td>';
                        	html +='<td><input type="hidden" class="recordID"  value="'+tableID+'"></td>';
                        	
                        }
                    
                    	html +='<td><input type="submit"  class="btn btn-warning" id="changeoptionvalue'+j+'" name="changebtn" value="Action"/></td>';
                    	html +='<td><input type="hidden" class="facultyRef"  value="'+facultyRef+'"></td>';
                  		html +='<td><input type="hidden" class="kafedraRef"  value="'+kafedraRef+'"></td>';
                    	html +='<td><input type="hidden" class="teacherId"  value="'+teacherId+'"></td>';
						html +='</tr>';

                    	$('.table tbody').append(html);
                   		j++;
                    
                     });
                
                	if(fstatus == "insert"){
                    
                    	$('.table tbody').append('<input type="submit" class="btn btn-primary" name="savebtn" id="savebtn" value="Сохранить">');
                    
                    }else{
                    
                    	$('.table tbody').append('<input type="submit" class="btn btn-primary" name="updatebtn" id="updatebtn" value="Изменить">');
                    }

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





