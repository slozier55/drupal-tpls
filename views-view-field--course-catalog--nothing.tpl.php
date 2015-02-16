<?php
/**
 * @file
 * This template is used to add shopping and room data from http://www.gse.harvard.edu/sites/default/files/feeds/courses.xml
 * 
 *  page output /about/catalogue/courses
 * you must remove the  end div in the view for this to work: /admin/structure/views/view/course_catalog/
 *<div class="collapseSectionHeader" id="[field_course_id]"> [title] </div>
 *<div class="collapseSectionContent">
 *<p>[field_course_faculty][field_course_faculty_2][field_course_faculty_3][field_course_faculty_4]</p>
*<p>[body]</p>
*<p>[field_course_prerequiste]</p>
*<!--p><strong>Shopping Options: </strong>[field_course_shopping]</p-->
*<p><strong>Room:</strong> [field_course_building_room] </p>
*<p><strong>[field_course_type]<br>[field_course_time]</strong></p>
*<p>[field_course_scheduling_notes]</p>
*<p>[field_course_url]</p>
 */
?>
<?php
//dsm($row);
// print the views output which is a rewritten field in /admin/structure/views/view/course_catalog/
print $output;
// create var to get the nid
// $nodetitle = $row->node_title;
$permlink = $row->nid;
// $coursecredits = $row->field_data_field_course_type_field_course_type_value;
// $coursebody = $row->field_data_body_body_value;
// $prereq = $row->field_data_field_course_prerequiste_field_course_prerequiste;
// echo "<br> <b>$nodetitle</b>";
// echo "<br> <b>$courseid</b>";
// echo "<br> <b>$coursecredits</b>";
// echo "<br> $coursebody";
// echo "<br> <i>$prereq</i>";
// in case you neeed to print the nid 
// print $thenid;

$faculty =$row->field_field_course_faculty[0]['rendered']['#markup'];
$facultytwo = $row->field_field_course_faculty_2[0]['rendered']['#markup'];
$facultythree = $row->field_field_course_faculty_3[0]['rendered']['#markup'];
$facultyfour = $row->field_field_course_faculty_4[0]['rendered']['#markup'];
$courseid = $row->field_data_field_course_id_field_course_id_value;
$reqid = $row->field_field_course_req_id[0]['rendered']['#markup'];
$shopping_display = $b[0]['#object']->field_course_display_options['und'][0]['value'];
if ((isset($facultyfour))){
  $facultyoutput = $faculty.', '.$facultytwo.', '.$facultythree.', '.$facultyfour;
#print '<tr class="coursefaculty"><br><b>Facutly: </b>'. $cfaculty .'</tr>';
  }
elseif ((isset($facultythree))){
  $facultyoutput = $faculty.', '.$facultytwo.', '.$facultythree;
  }
elseif ((isset($facultytwo))){
$facultyoutput = $faculty.', '.$facultytwo;
  }
elseif ((isset($faculty))){
 $facultyoutput = $faculty;
  }
// /** add XPATH here */

$feed ='http://www.gse.harvard.edu/sites/default/files/feeds/courses.xml';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $feed);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// get the result of http query
$output = curl_exec($ch);
curl_close($ch);
// feed the curl output to simplexml_load_string
//set instead of $courseid to $recid
$xml = simplexml_load_string($output) or die('Data not loading');
  $courses = $xml->xpath('/CATALOG/COURSE[contains(COURSE_REC_ID, "' .$reqid. '") ]'); 

 // dsm($courses);
 // print $reqid . $courses[0]->COURSEID_DASH;


$fulltitle = $courses[0]->FULL_TITLE; 
 if (strlen($fulltitle) > 80) {
  $leng = strlen($fulltitle);
$fulltitle = wordwrap($fulltitle, 80, "... <br />\n");

  } 
  else {
   $fulltitle = $courses[0]->FULL_TITLE;   
  }



   print '<div class="collapseSectionHeader" id="field-course-id"><span class="course-enroll-title"> 
 <table> <tr class="coursetitle"><td class="course-label">'. $courses[0]->COURSEID_DASH . '</td><td class = "values">'. 
$fulltitle . '</td><td>(' .$courses[0]->TERM . ')</td></tr> </table></span>
     </div><div class="collapseSectionContent">';
    print  '<table border="0">';


      print '<tr class="titledata"><td class="course-label"><b>Title: </b></td><td class = "values">'. $courses[0]->FULL_TITLE .'</td></tr>';

      print '<tr class="facultydata"><td class="course-label"><b>Faculty: </b></td><td class = "values">'. $facultyoutput .'</td></tr>';

       
    //term
          if ($courses[0]->TERM != '') {
           $cterm =  $courses[0]->TERM;
          }
      //credits
          if ($courses[0]->COURSE_CREDITS != '') {
           $ccoursecredits =  $courses[0]->COURSE_CREDITS;
          }  
        print '<tr class="ccoursecredits"><td class="course-label"><b>Semester/Credits: </b></td><td class = "values">'. $cterm .", ". $ccoursecredits . '</td></tr>';
      //description
          if ($courses[0]->DESCRIPTION_1 != '') {
           $cdescription =  $courses[0]->DESCRIPTION_1;
         print '<tr class="coursedescription"><td class="course-label"><b>Description: </b></td><td class = "values">'. $cdescription .'</td></tr>';

          }
    //pre reqs
          if ($courses[0]->PREREQS != '') {
           $cprereqs =  $courses[0]->PREREQS;
          print '<tr class="courseprereqs"><td class = "course-label"><b>Prerequisites: </b></td><td class="values"><em>'. $cprereqs .'</em></td></tr>';
          }
    //subjects
          
           $csubject =  $courses[0]->SUBJECT;
             $csubject =  str_replace("<P>", "; " , $csubject);
          print '<tr class="coursesubject"><td class = "course-label"><b>Subject(s): </b></td><td class="values">'. $csubject .'</td></tr>';
        
    //time
          if ($courses[0]->TIME != '') {
           $ctime =  $courses[0]->TIME;
          }
          else {
            $ctime = 'TBD';
          }
        print '<tr class="coursetime"><td class = "course-label"><b>Time: </b></td><td class="values">'. $ctime .'</td></tr>';
        //need to figure out what the difference is with rooms
    //room
          if ((!isset($courses[0]->BUILDING_ROOM)) || ($courses[0]->DISPLAY_ROOM == 'F')) {
            $croom = 'TBD';
          }
          else {
            $croom =  $courses[0]->BUILDING_ROOM;
          }
          print '<tr class="courseroom"><td class="course-label"><b>Room: </b></td><td class="values">'. $croom .'</td></tr>';
    //Schdule Notes
          if ($courses[0]->SCHEDULING_NOTES != '') {
           $cschedulingnotes =  $courses[0]->SCHEDULING_NOTES;
           print '<tr class="courseschedulingnotes"><td class="course-label"><b>Scheduling: </b></td><td class="values">'. $cschedulingnotes .'</td></tr>';

          }
          else {
          }
    //shopping
          if ($courses[0]->DISPLAY_SHOPPING_OPTIONS == 'T') {
           $cshop =  $courses[0]->SHOPPING_OPTIONS;
          print '<tr class="courseshoppinginformation"><td class="course-label"><b>Shopping Information: </b></td><td class="values">'. $cshop .'</td></tr>';
          }
          else {
          }
    //website
           if ($courses[0]->URL != '') {
            $courseurl =  $courses[0]->URL;
          }
          else {
          }
    //courseurl
        print '<tr class="courseswebsite"><td class="course-label"><b>Website: </b></td><td><a href="'. $courseurl.'">Course link</a></td>';

        print '<tr class="coursespermlink"><td class="course-label"><b>Direct Link: </b></td><td><a href="/node/'. $permlink.'">Course Description Web Page</a></td>';
          
  
  echo "</table>" 
    //}
?>
 <!-- Remove me -->
  <!-- Div to close the accordion that was started in the view -->
 </div>