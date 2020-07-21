<?php
//$_POST['gname'] = 4;
$id = $_POST['id'];
$col = $_POST['col'];
$gname = $_POST['gname'];
$email = $_POST['email'];
$gkey = $_POST['gkey'];
$userid = $_POST['userid'];
$time = time();

$key = $gkey . str_pad($col, 2, "00", STR_PAD_LEFT);

$link = mysqli_connect('localhost', '', '') or die ('Error connecting to mysql: ' . mysqli_error($link));
mysqli_select_db($link, 'db_lms_main3v_1819');
$link->set_charset("utf8");

$sql_user = $link->query("UPDATE mdl_user SET email = \"$email\" WHERE id = $userid");
$sql = $link->query("INSERT INTO mdl_groups (courseid, name, enrolmentkey, timecreated) VALUES ($id, \"$gname\", \"$key\", $time)");

$sql2 = $link->query("SELECT * FROM mdl_groups WHERE id = $link->insert_id");

while ($row = $sql2->fetch_object()){

	echo "<td class='cell'>". $_POST['col'] ."</td>".
			"<td class='cell'>". $row->name ."</td>".
			"<td class='cell'>". $row->enrolmentkey ."</td>".
			"<td class='cell'>". $row->timecreated ."</td>".
			"<td class='cell'>". $row->timemodified ."</td>";
	$link->query("INSERT INTO ripo_groups (mdl_groups_id, mdl_user_id) VALUES ($row->id, $userid)");
}



?>
