<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Listing of the course administration pages for this course.
 *
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');

$courseid = required_param('courseid', PARAM_INT);

echo $courseid;
$PAGE->set_url('/course/admin.php', array('courseid'=>$courseid));

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_pagelayout('incourse');
$title = get_string('addinggroup', 'course');

$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$groups = $DB->get_records_sql("SELECT g.`*`,r.`check`  FROM mdl_groups g
left join ripo_groups r on g.id = r.mdl_groups_id
LEFT JOIN mdl_enrol e on g.courseid = e.courseid
LEFT JOIN mdl_user_enrolments ue on e.id = ue.enrolid
WHERE r.mdl_groups_id =g.id
AND e.courseid = $courseid
AND ue.userid = $USER->id

order by g.id
");
$course = $DB->get_records_sql("SELECT * FROM mdl_course");

// echo "<pre>";
// //print_r($groups);
// echo "</pre>";

foreach($course as $course){
	if($course->id = $courseid){
    	$key = "kazumo-" . $course->id . "-";  
    }
}

?>

<script>

function getdetails(){

	$('#gname').prop("required", true);
    var gname = $('#gname').val();
	$('#email').prop("required", true);
    var email = $('#email').val();
    var col = $('#i').val();
	var gkey = "<?php echo $key; ?>";
	var id = <?php echo $courseid; ?>;
	var userid = <?php echo $USER->id; ?>;
	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;


	if(this.gname.value == "" && this.email.value == "") {
    	alert("Введите группу и email");
      	this.name.focus();
      	return false;
    }

	if(this.gname.value == "") {
    	alert("Введите группу");
      	this.name.focus();
      	return false;
    }

	if(this.email.value == "") {
    	alert("Введите email");
      	this.name.focus();
      	return false;
    }

	if (IsEmail(this.email.value) == false) {
    	
    	alert("Введите корретный адрес email!");
		this.name.focus();
		return false;
    }
	
    $.ajax({
        type: "POST",
        url: "/test/obrabotka.php",
        data: {gname:gname, email:email, col:col, gkey:gkey, id:id, userid:userid}
    }).done(function( result )
        {
        	alert("Данные добавлены под № "+col+"!");
            $("#msg2").html( result );
        	location.reload();
        });
}

function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}
</script>

<div id="msg2"></div>

<hr/><br>
<p style="color: red; text-align: center;">Внимание, Необходимо вводить каждую группу отдельно.</p>
<div class="form-label col-sm-3 text-sm-right">
	<label>
		<?php echo get_string('input_name', 'course'); ?>
	</label>
	<span class="form-shortname d-block small text-muted"><?php echo get_string('input_example', 'course'); ?></span>
    <br>
	<label>
		<?php echo "Введите Ваш email"; ?>
	</label>
	<span class="form-shortname d-block small text-muted"><?php echo "Например: user@gmail.ru"; ?></span>
    <br>
</div>

<div class="form-setting col-sm-9">
	
	<input type="text" name="gname"  id="gname" class="form-control text-ltr">
	<br>
	<input type="email" name="email"  id="email" class="form-control text-ltr">
	<br>
	<div class="form-setting col-sm-12">
		<input type="button" name="submit" value="<?php echo get_string('add'); ?>" id="submit" class="btn btn-primary" onClick = "getdetails()" style="width: -webkit-fill-available; margin-bottom: 20px;"/>
    
	</div>
</div>
    <br>
<table cellspacing="0" class="flexible reportlog generaltable generalbox">
	<thead>
		<tr>
			<th class="header" scope="col"><?php echo get_string('number', 'course'); ?></th>
			<th class="header" scope="col"><?php echo get_string('table_name', 'course'); ?></th>
			<th class="header" scope="col"><?php echo get_string('table_code', 'course'); ?></th>
			<th class="header" scope="col"><?php echo get_string('table_confirm', 'course'); ?></th>
        	<th class="header" scope="col"><?php echo get_string('table_date_create', 'course'); ?></th>
			<th class="header" scope="col"><?php echo get_string('table_date_change', 'course'); ?></th>
		</tr>
	</thead>
	<tbody>

<?php $i=1;	foreach($groups as $groups){ ?>

		<tr class="">
			<td class="cell"><?php echo $i++; ?></td>
			<td class="cell"><?php echo $groups->name; ?></td>
			            
            <?php if($groups->check == 0) { ?>
            
            	<?php if($groups->check == NULL) { ?>
                	<td class="cell"><?php echo $groups->enrolmentkey; ?></td>
                	<td class="cell">Да</td>
            	<?php } else { ?>
                	<td class="cell">****************</td>
                	<td class="cell">Нет</td>
            	<?php } ?>

            <?php } else { ?>
            	<td class="cell"><?php echo $groups->enrolmentkey; ?></td>
                <td class="cell">Да</td>
            <?php } ?>
            
			<td class="cell"><?php echo date('d-m-Y', $groups->timecreated); ?></td>
			<td class="cell"><?php echo date('d-m-Y', $groups->timemodified); ?></td>
		</tr>

<?php	}	?>
		<tr id="msg"></tr>

	</tbody>
</table>

<input type="hidden" id="i" value="<?php echo $i; ?>"></div>

<?php

//echo $key;
//echo $courseid."<br>";
echo "<pre>";
//print_r($USER->id);
echo "</pre>";

echo $OUTPUT->footer();
