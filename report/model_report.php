<?php

// Basic example of PHP script to handle with jQuery-Tabledit plug-in.
// Note that is just an example. Should take precautions such as filtering the input data.

require_once('../config.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/adminlib.php');

header('Content-Type: application/json');

$input = filter_input_array(INPUT_POST);

$mysqli = new mysqli('localhost', '', '', '');
mysqli_set_charset($mysqli,"utf8");

if (mysqli_connect_errno()) {
  echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  exit;
}

global $DB;

// $input['check'] =  1;
// $input['uid'] = 4696;
// $input['id'] = 98;

// print_r($DB->get_record('user', array('email' => 'zeinollaev.e@ablaikhan.kz')));

$user_email = $input['email'];

$toUser = $DB->get_record('user', array('email' => "$user_email"));
$fromUser = $CFG->supportemail;
$subject = 'Данное письмо содержит необходимые данные для записи на курс';
$messagehtml = '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style type="text/css">
   body { 
    font-size: 16px; 
    font-family: Times New roman; 
    color: black;
   }
</style>
</head>
<body>
	<p>Здравствуйте, <br>
Данное письмо содержит необходимые данные для записи на учебный курс -  '.$input['cname'].' <br>
Данное письмо необходимо переслать Вашим студентам. </br></p>
<p>-----------------------Инструкция Для Студентов-------------------------------------</p>
<p>Уважаемые студенты, </p>
<p>Для того чтобы записаться на курс - <b>'.$input['cname'].'</b><br>
<ul>
	<li>перейдите по <b>прямой ссылки</b> ниже</li>
	<li>на открывшейся странице необходимо ввести свой <b>логин(ID)</b> и <b>пароль</b> с помощью которого вы обычно заходите на портал нажмите кнопку "Вход",</li>
	<li>после чего у Вас откроется страница на которой будет предложено ввести кодовое слово. </li>
</ul></p>
<p><em><u>Кодовое слово вводится БЕЗ ПРОБЕЛОВ, НА АНГЛИЙСКОМ ЯЗЫКЕ, в точности как указано ниже.</u></em><br>
После ввода кодового слова Вы будете автоматически привязаны к данному курсу в роли студента и он всегда будет отображаться в меню навигации слева, в пункте "мои курсы".</p></br>
<p><u>Кодовое слово для:</u><b> <em>Группа '.$input['gname'].'</em></b></p></br> 
<p><u>Пароль:</u> <em><b>'.$input['gkey'].'</b></em></p>
<p><b>Прямая ссылка: </b></p>
<p><a href="http://lms.ablaikhan.kz/course/view.php?id='.$input['cid'].'">http://lms.ablaikhan.kz/course/view.php?id='.$input['cid'].'</a></br></p>
</body>
</html>
';

$toadmin = $DB->get_record('user', array('email' => "$USER->email"));
$messagehtml2 = '
<p>Копия отправленного письма на запроса на привязку студентов</p>
<p><u>От Преподавателя :</u> '.$user_email.' <a href="http://lms.ablaikhan.kz/user/profile.php?id='.$input['uid'].'"> Открыть профиль</a><br></p>
<p><u>Название Курса:</u> '.$input['cname'].' <br></p>
<p><u>Ссылка:</u> <a href="http://lms.ablaikhan.kz/course/view.php?id='.$input['cid'].'">http://lms.ablaikhan.kz/course/view.php?id='.$input['cid'].'</a></br></p>
<p><u>Название:</u> <em>Группа '.$input['gname'].'</em></br></p>
<p><u>Кодовое слово:</u> <em><b>'.$input['gkey'].'</b></em></p>
';


if($input['check'] == 1){

	$emailuser = email_to_user($toUser, $fromUser, $subject, '', $messagehtml, '', '', true);
	$emailadmin = email_to_user($toadmin, $toUser, $subject, '', $messagehtml2, '', '', true);

}

	$mysqli2 = new mysqli('ablaikhan.kz', 'ernur', 'SDd2m8=oz345os', 'www_portal');
	mysqli_set_charset($mysqli2,"utf8");

	if (mysqli_connect_errno()) {
  		echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  	exit;
	}

	$usr_id = $input['uid'];
	// print_r( $DB->get_record('user', array('id' => "$usr_id")));
	$portal_user = $DB->get_record('user', array('id' => "$usr_id"));

	// echo "UPDATE jos_users SET `email`='" . $user_email . "' WHERE id='" .$portal_user. "';";
	$mysqli2->query("UPDATE jos_users SET `email`='" . $user_email . "' WHERE id='" .$portal_user->idnumber. "';");

if ($input['action'] == 'edit') {
    $mysqli->multi_query("UPDATE ripo_groups SET `check`='" . $input['check'] . "', `coment`='" . $input['coment'] . "' WHERE id='" . $input['rid'] . "'; 
    				UPDATE mdl_groups SET `enrolmentkey`='" . $input['gkey'] . "', `name`='" . $input['gname'] . "' WHERE id='" . $input['id'] . "';
                    UPDATE mdl_user SET `email`='" . $user_email . "' WHERE id='" .$input['uid']. "';");
} else if ($input['action'] == 'delete') {
    $mysqli->query("DELETE ripo_groups, mdl_groups FROM mdl_groups INNER JOIN ripo_groups WHERE mdl_groups.id=ripo_groups.mdl_groups_id and mdl_groups.id ='" . $input['id'] . "'");
}

mysqli_close($mysqli);

echo json_encode($input);
