<?php 


include 'Dbc.php';

require('tcpdf/tcpdf.php');

$nObj = new Dbc();
$con = $nObj->connect();

if(isset($_GET['fac']) && isset($_GET['kaf'])){
	$faculty = $_GET['fac'];
	$kafedra = $_GET['kaf'];
	$actionType = $_GET['types'];

	$sql = "SELECT * FROM mdlReports WHERE facultyRef='".$faculty."' AND kafedraRef='".$kafedra."' AND is_active=1";
	$result = $con->query($sql);  

	$sqlscount = "SELECT SUM(countzero) AS allrecord,COUNT(*) AS countrecord FROM mdlReports WHERE facultyRef='".$faculty."' AND kafedraRef='".$kafedra."' AND is_active=1";
	$allrecord = $con->query($sqlscount);
	$arrrow = $allrecord->fetch_array();
    $tProcent =100-intval(($arrrow[0]*100)/($arrrow[1]*14)).' %';


	$sql1 = "SELECT facultyName,kafedraName,date FROM mdlReports WHERE facultyRef='".$faculty."' AND kafedraRef='".$kafedra."' AND is_active=1";
	$result1 = $con->query($sql1);
	$row = $result1->fetch_array();
    
 	$date = substr($row[2], 0,10);
	
}


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Jabai Bakhytqan');
$pdf->SetTitle('Список нагрузки кафедры');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


// set header and footer fonts
//$this->mytcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$this->mytcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
$pdf->SetFont('freeserif', '', 10, '', 'true');

$pdf->AddPage('L', 'A4');

$facultet = <<<EOD
  <strong>Факультет:</strong>  $row[0]
EOD;

$cathedra = <<<EOD
  <strong>Кафедра:</strong> $row[1] $subate
EOD;

$structure = '<h4>Структура УМКД</h4>
<table border="0" cellspacing="0" cellpadding="0">
    <tr><th bgcolor="#336699" color="yellow">1 Типовая учебная программа по дисциплине; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">2 Рабочая учебная программа дисциплины (РУПр); </th></tr>
    <tr><th bgcolor="#336699" color="yellow">3 Силлабус (Syllabus); </th></tr>
    <tr><th bgcolor="#336699" color="yellow">4 Методические рекомендации по изучению дисциплины; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">5 Календарно-тематический план дисциплины; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">6 Компетентностно- технологическая карта организации; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">7 Карта обеспеченности учебно-методической литературой; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">8 Лекционный комплекс: тезисы лекций с указанием количества часов, список рекомендуемой литературы; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">9 Планы семинарских (практических) занятий с указанием количества часов, заданий, кейс-ситуаций, список рекомендуемой литературы; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">10 График выполнения и сдачи заданий СРОП и СРО по дисциплине; Матери-алы для СРОП: кейс-заданий с указанием трудоемкости и литературы; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">11 Материалы для самостоятельной обучающегося (СРО): перечень типовых, прагмо-профессиональных и проблемных задач, материалы самоконтроля по каждой те-ме, задания по выполнению текущих видов работ и других заданий с указанием трудоем-кости и литературы; </th></tr>
    <tr><th bgcolor="#336699" color="yellow">12 Материалы по контролю и оценке учебных достижений обучающегося: письменные контрольные задания, типовые ситуации, перечень прагмо-профессиональных и теоретических вопросов,  перечень вопросов для самоподготовки, перечень проектных работ и т.д. </th></tr>
	<tr><th bgcolor="#336699" color="yellow">13 Программное и мультимедийное сопровождение учебных занятий  (видео-лекции, слайды, новости на языке, подкасты, электронные учебные пособия, и др.) в за-висимости от содержания дисциплины. </th></tr>
    <tr><th bgcolor="#336699" color="yellow">14 Работа со студентами	</th></tr>
</table>';


$mainBody = '<table border="1" cellspacing="0" cellpadding="2">
    <tr bgcolor="#336699" color="white">
        <th width="2%" align="center">№</th>
        <th width="8%" align="center">Шифр Специальности</th>
        <th width="6%" align="center">Шифр Дисциплина</th>
        <th width="3%" align="center">Курс</th>
        <th width="4%" align="center">Семестр</th>
        <th width="5%" align="center">Отделение</th>
        <th width="5%" align="center">Кол-во кредитов</th>
        <th width="8%" align="center">ФИО руководителя метод объединения</th>
        <th width="8%" align="center">ФИО Соавторов</th>
        <th width="3%" align="center">1</th>
        <th width="3%" align="center">2</th>
        <th width="3%" align="center">3</th>
        <th width="3%" align="center">4</th>
        <th width="3%" align="center">5</th>
        <th width="3%" align="center">6</th>
        <th width="3%" align="center">7</th>
        <th width="3%" align="center">8</th>
        <th width="3%" align="center">9</th>
        <th width="3%" align="center">10</th>
        <th width="3%" align="center">11</th>
        <th width="3%" align="center">12</th>
        <th width="3%" align="center">13</th>
        <th width="3%" align="center">14</th>
        <th width="8%" align="center">Примечание</th>
        <th width="3%" align="center">Готовность</th>
    </tr>';

        $i=1;
		$eachProcent = 0;
		while($row = $result->fetch_array()){
        
          $allSpec = $row['allSpec'];
          $allDiscip = $row['allDiscip'];
          $department = $row['department'];
    	  $course = $row['course'];
    	  $semestr = $row['semestr'];
    	  $credit = $row['credit'];
    	  $TeacherName = $row['TeacherName'];
            $val1='';$val2='';$val3='';$val4='';$val5='';$val6='';$val7='';$val8='';$val9='';$val10='';$val11='';$val12='';$val13=''; $val14 = '';
        	if($row['sel1']==1){ $val1="<td>есть</td>"; }else{ $val1="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel2']==1){ $val2="<td>есть</td>"; }else{ $val2="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel3']==1){ $val3="<td>есть</td>"; }else{ $val3="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel4']==1){ $val4="<td>есть</td>"; }else{ $val4="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel5']==1){ $val5="<td>есть</td>"; }else{ $val5="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel6']==1){ $val6="<td>есть</td>"; }else{ $val6="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel7']==1){ $val7="<td>есть</td>"; }else{ $val7="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel8']==1){ $val8="<td>есть</td>"; }else{ $val8="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel9']==1){ $val9="<td>есть</td>"; }else{ $val9="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel10']==1){ $val10="<td>есть</td>"; }else{ $val10="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel11']==1){ $val11="<td>есть</td>"; }else{ $val11="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel12']==1){ $val12="<td>есть</td>"; }else{ $val12="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel13']==1){ $val13="<td>есть</td>"; }else{ $val13="<td style=\"background-color:#dc3545\">нет</td>";}
        	if($row['sel14']==1){ $val14="<td>есть</td>"; }else{ $val14="<td style=\"background-color:#dc3545\">нет</td>";}
        
    		$note = $row['usernote'];
            $eachProcent =100 - intval(($row['countzero']*100)/14).'%';
 
   	$mainBody= $mainBody.'<tr>';	
    	$mainBody = $mainBody.'<td>'.$i.'</td>
        <td>'.$allSpec.'</td>
        <td>'.$allDiscip.'</td>
        <td>'.$course.'</td>
        <td>'.$semestr.'</td>
        <td>'.$department.'</td>
        <td>'.$credit.'</td>
        <td>'.$TeacherName.'</td>
        <td></td>
        '.$val1.'
        '.$val2.'
        '.$val3.'
        '.$val4.'
        '.$val5.'
        '.$val6.'
        '.$val7.'
        '.$val8.'
        '.$val9.'
        '.$val10.'
        '.$val11.'
        '.$val12.'
        '.$val13.'
        '.$val14.'
        <td>'.$note.'</td>
        <td>'.$eachProcent.'</td>
    </tr>';
        $i++;
        }

$mainBody= $mainBody.'</table>';

$dd= date("d.m.Y");
$curDate = <<<EOD
  <label>Дата:</label> $date
EOD;

$total = <<<EOD
  <label>Готовность кафедры:</label><strong> $tProcent </strong>
EOD;

$pdf->writeHTMLCell(0, 0,10, 5,$curDate, 0, 1, 0, true, 'R', true);
$pdf->writeHTMLCell(0, 0,10, 15,$total, 0, 1, 0, true, 'R', true);
$pdf->writeHTMLCell(0, 0,10, 5,'Список Нагрузки кафедры на 2019-20 уч.год', 0, 1, 0, true, 'C', true);
$pdf->writeHTMLCell(200, 0,25, 10,$facultet, 0, 1, 0, true, 'L', true);
$pdf->writeHTMLCell(200, 0,25, 15,$cathedra, 0, 1, 0, true, 'L', true);

$pdf->SetFont('freeserif', '', 8, '', 'true');
$pdf->Ln(2);

$pdf->writeHTML($structure, true, false, true, false, '');

$pdf->Ln(4);
$pdf->writeHTML($mainBody, true, false, true, false, '');



ob_end_clean();
if($date){
	$filename = $date.'.pdf';
}else{
	$filename='Noname.pdf';
}
if($actionType=="input"){
	$pdf->Output($filename, 'I');
}else{
	$pdf->Output($filename, 'D');
}
 




?>


