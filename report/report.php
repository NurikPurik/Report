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

require_once('../config.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$title = get_string('addinggroup', 'course');

$PAGE->set_title($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$rep_Grup = $DB->get_records_sql("
SELECT

g.id,
r.id as rid,
g.courseid,
cc.path,
c.fullname,
g.name,
g.enrolmentkey,

CASE WHEN r.mdl_user_id IS NULL THEN '0' ELSE r.mdl_user_id END as mdl_user_id,
u.email,
CASE WHEN r.`check` IS NULL THEN '-1' ELSE r.`check` END as `check`,
g.timecreated,
g.timemodified,
r.Coment

FROM mdl_groups g

left join ripo_groups r on g.id = r.mdl_groups_id
left join mdl_course c on g.courseid = c.id
left join mdl_course_categories cc on c.category = cc.id
left join mdl_user u on r.mdl_user_id = u.id


WHERE r.mdl_groups_id is null or g.id is not null

order by g.id
");

$category = $DB->get_records_sql("
SELECT id, name, parent FROM mdl_course_categories
");

function mdl_category($id){

$return_arr = array();

	$mysqli = new mysqli('localhost', 'db_lms_main3v', 'HmE7nlFGZGT9zASf', 'db_lms_main3v_1819');
		if (mysqli_connect_errno()) {
  			echo json_encode(array('mysqli' => 'Failed to connect to MySQL: ' . mysqli_connect_error()));
  			exit;
		}
		$mysqli->set_charset("utf8");

		$category = $mysqli->query("SELECT id, name, parent FROM mdl_course_categories WHERE id = $id");
		
		while($row = $category->fetch_assoc()){
        	$return_arr = $row['name'];
        }
	
return $return_arr;

}

// echo "<pre>";
// print_r($USER);
// echo "</pre>";

?>
<input id="myInput" type="text" placeholder="Поиск.." style="right: 20px;width: 100%;padding: 10px 10px;margin: 10px 0;">
<table id="tabledit">
	<thead>
		<tr>
			<th><?php echo get_string('number', 'course'); ?></th>
			<th style="display: none;"><?php echo get_string('id_group', 'course'); ?></th>
			<th style="display: none;"><?php echo get_string('id_course', 'course'); ?></th>
			<th><?php echo 'Факультет'; ?></th>
			<th style="display: none;"><?php echo get_string('id_category_facultaty_id', 'course'); ?></th>
			<th><?php echo 'Кафедра'; ?></th>
			<th style="display: none;"><?php echo get_string('id_category_kaf_id', 'course'); ?></th>
			<th style="display: none;"><?php echo "Course name"; ?></th>
			<th style="display: none;"><?php echo "Course id"; ?></th>
			<th><?php echo 'Дисциплина'; ?></th>            
			<th style="display: none;"><?php echo 'Группа'; ?></th>
			<th><?php echo 'Группа'; ?></th>
			<th style="display: none;"><?php echo get_string('table_code', 'course'); ?></th>
			<th><?php echo get_string('table_code', 'course'); ?></th>
			<th><?php echo 'Статус Дисциплины'; ?></th>
			<th style="display: none;"><?php echo 'Преп.'; ?></th>
			<th><?php echo 'Преп.'; ?></th>
			<th><?php echo get_string('table_date_create', 'course'); ?></th>
			<th><?php echo get_string('table_date_change', 'course'); ?></th>
			<th style="display: none;"><?php echo 'RiPO'; ?></th>
			<th><?php echo 'email'; ?></th>
			<th><?php echo 'Коммент'; ?></th>
            <th><?php echo "Готов"; ?></th>   
		</tr>
	</thead>
	<tbody id="myTable">
<?php $i=1; foreach($rep_Grup as $rep_Grups){ ?>
	
	<?php $path = explode("/", $rep_Grups->path); ?> 
        <tr>
			<td><?php echo $i++; ?></td>            
			<td style="display: none;"><?php echo $rep_Grups->id; ?></td>            
			<td style="display: none;"><a href="/course/view.php?id=<?php echo $rep_Grups->courseid; ?> " target="_blank"> <?php echo $rep_Grups->courseid; ?></a></td>        
            <td><?php echo mdl_category($path[1]); ?></td>
			<td style="display: none;"><?php echo $path[1]; ?></td>            
			<td><?php echo $path[2] ? mdl_category($path[2]) : "0"; ?></td>
			<td style="display: none;"><?php echo $path[2] ? $path[2] : "0"; ?></td>            
			<td style="display: none;"><?php echo $rep_Grups->fullname; ?></td>            
			<td style="display: none;"><?php echo $rep_Grups->courseid; ?></td>            
			<td><a href="/course/view.php?id=<?php echo $rep_Grups->courseid; ?>" target="_blank"> <?php echo $rep_Grups->fullname; ?></a> <a href="/course/management.php?courseid=<?php echo $rep_Grups->courseid; ?>" target="_blank">(кр.обзор)</a></td>            
			<td><?php echo $rep_Grups->name; ?></td>            
			<td style="display: none;"><?php echo $rep_Grups->enrolmentkey; ?></td>            
			<td><?php echo $rep_Grups->enrolmentkey; ?></td>                     
            <td>
            <?php 
            
            // echo $rep_Grups->check == 1 ? '<font color="green">Готов</font>' : '<font color="red">Не готов</font>'; 
             if($rep_Grups->check == 1){
             	echo '<font color="green">Готов</font>';
             } else {
             	echo '<font color="red">Не готов</font>';
             }
                                             
             ?>
            </td>           
			<td style="display: none;"><?php echo $rep_Grups->mdl_user_id; ?></td>         
			<td><a href="/user/profile.php?id=<?php echo $rep_Grups->mdl_user_id; ?> " target="_blank"> <?php echo $rep_Grups->mdl_user_id; ?> </a></td>         
			<td><?php echo date('d-m-Y H:i:s', $rep_Grups->timecreated); ?></td>
			<td><?php echo date('d-m-Y H:i:s', $rep_Grups->timemodified); ?></td>
			<td style="display: none;"><?php echo $rep_Grups->rid; ?></td>
			<td><?php echo $rep_Grups->email; ?></td>
			<td><?php echo $rep_Grups->coment; ?></td>
            <td>
            <?php 
            
            // echo $rep_Grups->check == 1 ? '<font color="green">Готов</font>' : '<font color="red">Не готов</font>'; 
             if($rep_Grups->check == 1){
             	echo '<font color="green">Да</font>';
             } else {
             	echo '<font color="red">Нет</font>';
             }
                                             
             ?>
            </td>
		</tr>
<?php } ?>

	</tbody>
</table>

<div id="id01" class="modal">
	<span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
	<div class="modal-content">
		<div class="container">
			<div class="container_collapse">
    			<div class="header_collapse">
    				<span><?php echo $member_user[0]->parent1; ?></span>
    			</div>
			</div>
		</div>
	</div>
</div>

<?php
//echo $key;
//echo $courseid."<br>";
echo "<pre>";
// print_r($USER->email);
echo "</pre>";

echo $OUTPUT->footer();

?>

<script>
(function($) {
  
  $("#but button").click(function(e){
    e.stopPropagation();
    var pos = $(this).offset(),
        div = $("#mydiv");
    
    // Make it visible off-page so
    // we can measure it
    div.css({
      "display": "block",
      "border": "1px solid black",
      "position": "fixed",
    });
    
    // Move it where we want it to be
  });
$(document).click(function(e){
  $('#mydiv').fadeOut(300);
});
})(jQuery);
</script>

<script>

$('#tabledit').Tabledit({
    url: 'model_report.php',
	restoreButton: false,
	buttons: {
		    edit: {
		        class: 'yui3-button',
		        html: '<span class="yui3-button-ico"><i class="fa fa-pencil" aria-hidden="true"></i></span>',
		        action: 'edit'
		    },
		    delete: {
		        class: 'yui3-button',
		        html: '<span class="yui3-button-ico"><i class="fa fa-ban" aria-hidden="true"></i></span>',
		        action: 'delete'
		    }, 
		    confirm: {
		        class: 'btn btn-sm btn-danger',
		        html: 'Confirm'
		    }
		},

    columns: {
        identifier: [1, 'id'],
        editable: [[7, 'cname'], [8, 'cid'], [10, 'gname'], [12, 'gkey'], [13, 'check', '{"1": "Готов", "0": "Не готов"}'], [14, 'uid'], [18, 'rid'], [19, 'email'], [20, 'coment']]
    },

    onSuccess: function(data, textStatus, jqXHR) {
        console.log('onSuccess(data, textStatus, jqXHR)');
        console.log(data);
        console.log(textStatus);
        console.log(jqXHR);
    },
    onFail: function(jqXHR, textStatus, errorThrown) {
        console.log('onFail(jqXHR, textStatus, errorThrown)');
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
    }

});

</script>

<script>
$(document).ready(function(){
      $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().replace(/\s+/g, ' ').toLowerCase().indexOf(value) > -1)
        });
      });
    });
</script>
