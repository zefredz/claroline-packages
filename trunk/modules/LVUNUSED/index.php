<?php
/**
 * CLAROLINE
 * This tool run some check to detect abnormal situation
 *
 * @version 1.8 $Revision$
 * @copyright 2001-2009 HE LEONARD DE VINCI
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @see http://www.claroline.net/wiki/
 *
 * Status for the course :
 * enable : activate, visible in My course list
 * pending : activate and waiting for reactivation, visible in My course list
 * disable : desactivate and can be reactivate only by admin, visible in My course list
 * trash : desactivate and can be reactivate only by admin, NOT visible in My course list
 *
 */

//Admin tool
$tlabelReq = 'LVUNUSED';

//Load Claroline Kernel
require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

//Load librairies
require_once dirname ( __FILE__ ) . '/../../claroline/inc/lib/sendmail.lib.php';
require_once dirname ( __FILE__ ) . '/../../claroline/inc/lib/utils/datagrid.lib.php';
require_once 'lib/LVUNUSED_sql_query.php';
include_once get_path ( 'incRepositorySys' ) . '/lib/statsUtils.lib.inc.php';
include_once get_path ( 'incRepositorySys' ) . '/lib/thirdparty/pear/Lite.php';
//NameTool
$nameTools = get_lang('Service key administration');

define ( 'DISP_RESULT', __LINE__ );
define ( 'DISP_NOT_ALLOWED', __LINE__ );

// Security check
if (! claro_is_user_authenticated ())
	claro_disp_auth_form ();
if (! claro_is_platform_admin ())
	claro_die ( get_lang ( 'Not allowed' ) );

//JavascriptLoader
JavascriptLoader::getInstance ()->load ( 'jquery' );
JavascriptLoader::getInstance ()->load ( 'ui.datepicker' );
CssLoader::getInstance ()->load ( 'ui.datepicker' );

// DATA TREATMENT

// $POST values for the check box
$noUser = false;
$noAdmin = false;
$noUsed = false;
$noDis = false;
$noPubl = false;
$noEmail = false;
$subModif = false; //Used to execute changes.
$choice = false; // Choice of execution.
$sendMail = false;
$emailBody = false; //retrieve email adresses to send mail to;
$emailSubject = false;

// EDIT CHECK BOX SELECTION
$noUser_C = '';
$noAdmin_C = '';
$noUsed_C = '';
$noDis_C = '';
$noPubl_C = array ();
$noEmail_C = array ();

// Retrieve changes in table
$creationDate = array ();
$expirationDate = array ();
$status = array ();
$select = array ();

$j = count ( $_SESSION ['data'] );

// Getting modifications from the displayed table
for($i = 0; $i < $j; $i ++) {
	
	if (isset ( $_POST['creationDate' . $i] ))
		$creationDate_POST[] = $_POST['creationDate' . $i];
	else
		$creationDate_POST[] = '';
	
	if (isset ( $_POST['expirationDate' . $i] ))
		$expirationDate_POST[] = $_POST ['expirationDate' . $i];
	else
		$expirationDate_POST[] = '';
	
	if (isset ( $_POST['status' . $i] ))
		$status_POST[] = $_POST['status' . $i];
	else
		$status_POST[] = '';
	
	if (isset ( $_POST['select' . $i] ))
		$select_POST[] = $_POST['select' . $i];
	else
		$select_POST[] = '';
}

// Save de flag witch enable modifications
if (isset ( $_POST['Execute'] )) {
	$subModif = $_POST['Execute'];
}
if (isset ( $_POST['CourseNoUser'] )) {
	$noUser = $_POST['CourseNoUser'];
}
if (isset ( $_POST['CourseNoAdmin'] )) {
	$noAdmin = $_POST['CourseNoAdmin'];
}
if (isset ( $_POST['CourseNoUsed'] )) {
	$noUsed = $_POST['CourseNoUsed'];
}
if (isset ( $_POST['CourseNoDis'] )) {
	$noDis = $_POST['CourseNoDis'];
}
if (isset ( $_POST['CourseNoPubl'] )) {
	$noPubl = $_POST['CourseNoPubl'];
}
if (isset ( $_POST['CourseNoEmail'] )) {
	$noEmail = $_POST['CourseNoEmail'];
}
if (isset ( $_POST['Choice'] )) {
	$choice = $_POST['Choice'];
}
if (isset ( $_POST['Send'] )) //To execute email sender
{
	$sendMail = $_POST['Send'];
}
if (isset ( $_POST['mail'] )) //get email body;
{
	$emailBody = $_POST['mail'];
}
if (isset ( $_POST['Subject'] )) //Get email subject;
{
	$emailSubject = $_POST['Subject'];
}

$result = array ();
$dataTreat = false;
$DataConcat = false;

/*Course without a teacher*/
if ($noUser) {
	$noUser_C = 'CHECKED';
	$data = courseNoUser();
	$DataConcat = true;
	$dataTreat  = true;
}

/*Course without a teacher*/
if ($noAdmin) {
	$noAdmin_C = 'CHECKED';
	if ($DataConcat == true) {
		$datatest = courseNoTeach(); //fortest
		$data = interArray ( $datatest, $data, 'course' );
	} else {
		$data = $datatest = courseNoTeach ();
		$DataConcat = true;
	}
	
	$dataTreat  = true;
}

/*Time delay for unused course in days with 183 days =~ 6 months*/

$timeLap = "6";

/*Course no used*/
if ($noUsed) {
	$noUsed_C = 'CHECKED';
	if ($DataConcat == true) {
		$datatest = delUnusedCourse ( $timeLap ); //fortest
		$data = interArray ( $datatest, $data, 'course' );
	} else {
		$data = $datatest = delUnusedCourse ( $timeLap );
		$DataConcat = true;
	}
	
	$dataTreat  = true;
}

/*Disabled courses*/
if ($noDis) {
	$noDis_C = 'CHECKED';
	if ($DataConcat == true) {
		$datatest = courseDisabled (); //fortest
		$data = interArray ( $datatest, $data, 'course' );
	} else {
		$data = $datatest = courseDisabled ();
		$DataConcat = true;
	}
	
	$dataTreat  = true;
}

/*Unpublished courses*/
if ($noPubl) {
	$noPubl_C = 'CHECKED';
	if ($DataConcat == true) {
		$datatest = UnpublishedCourse (); //fortest
		$data = interArray ( $datatest, $data, 'course' );
	} else {
		$data = $datatest = UnpublishedCourse ();
		$DataConcat = true;
	}
	
	$dataTreat  = true;
}

/*Courses without admin's e-mail*/
if ($noEmail ) {
	$noEmail_C = 'CHECKED';
	if ($DataConcat == true) {
		$datatest = NoAdminEmail (); //fortest
		$datatest = array_unique ( $datatest );
		$data = interArray ( $data, $datatest, 'course' );
	} else {
		$datatest = NoAdminEmail ();
		$datatest = array_unique ( $datatest );
		$data = $datatest;
		$DataConcat = true;
	}
	
	$dataTreat  = true;
}

/*  Display of the result of selection  */

$out = '<form name="lunch" METHOD="POST"><h3>' . claro_html_tool_title ( get_lang ( 'Scan technical fault' ) ) . '</h3>
		<input type="checkbox" name="CourseNoUser" value="true"' . $noUser_C . '>' . get_lang ( 'Courses without student' ) . '<br>
		<input type="checkbox" name="CourseNoAdmin" value="true"' . $noAdmin_C . ' >' . get_lang ( 'Courses without a lecturer' ) . '<br>
		<input type="checkbox" name="CourseNoUsed" value="true" ' . $noUsed_C . '>' . get_lang ( 'Courses not used' ) . '<br>
		<input type="checkbox" name="CourseNoDis" value="true" ' . $noDis_C . '>' . get_lang ( 'Courses desactive' ) . '<br>		
		<input type="checkbox" name="CourseNoPubl" value="true" ' . $noPubl_C . '>' . get_lang ( 'Courses unpublished' ) . '<br>
		<input type="checkbox" name="CourseNoEmail" value="true" ' . $noEmail_C . '>' . get_lang ( 'Courses without lecturer\'s email' ) . '<br><br>
		<input type="submit" name="Search" value=' . get_lang ( 'Search' ) . '><br><br>		
		</form>';

/*   Display of checkbox for enable disable  */
if ($dataTreat) {
	
	$data2 = array ();
	
	foreach ( $data as $line ) {

		if($line['status'] == 'enable')
		{
			$line['enable']='selected';
		}
		if($line['status'] == 'disable')
		{
			$line['disable']='selected';
		}		
		if($line['status'] == 'pending')
		{
			$line['pending']='selected';
		}
		if($line['status'] == 'trash')
		{
			$line['trash']='selected';
		}
		if ($line ['creationDate'] != '') {
			$mod_date = date_create ( $line ['creationDate'] );
			$line ['creationDate'] = date_format ( $mod_date, "d/m/y" );
		}
		if ($line ['expirationDate'] != '') {
			$mod2_date = date_create ( $line ['expirationDate'] );
			$line ['expirationDate'] = date_format ( $mod2_date, "d/m/y" );
		}
		$data2 [] = $line;
	}
	/*Saving the retrieved data in a session for modification tests*/
	
	$_SESSION ['data'] = $data2;
	
	$message = get_lang ( 'No document to publish' );
	$dg = new Claro_Utils_Clarogrid ( );
	$dg->setTitle ( '<h5>' . get_lang ( 'Results' ) . '</h5>' );
	$dg->emphaseLine ();
	$dg->setRows ( $data2 );
	$dg->addColumn ( 'course', get_lang ( 'Code' ), '<a href=' . get_path ( 'clarolineRepositoryWeb' ) . 'admin/admincourseusers.php?cidToEdit=%course%>%course%' );
	
	/*Way of modifying date with datapicker*/
	$dg->addColumn ( 'creationDate', get_lang ( 'Publish date' ), '
				<input type="text" name="creationDate%_lineNumber_%"  id="creationDate%_lineNumber_%" value=%creationDate%>
				<script>        	
          		$.datepicker.setDefaults({dateFormat: \'dd/mm/y\'});
            	$(\'#Text%_lineNumber_%\').datepicker({showOn: \'button\'});          
         		</script>' );
	
	$dg->addColumn ( 'expirationDate', get_lang ( 'Remove date' ), '
				<input type="text" name="expirationDate%_lineNumber_%"  id="expirationDate%_lineNumber_%"value=%expirationDate% >
				<script>        	
          		$.datepicker.setDefaults({dateFormat: \'dd/mm/y\'});
           	 	$(\'#Text2%_lineNumber_%\').datepicker({showOn: \'button\'});           
         		</script>' );
	
	$dg->addColumn ( 'status', get_lang ( 'Status' ), '
		<select name="status%_lineNumber_%">
		<option value=enable %enable%>'.get_lang('Enable').'</option>
		<option value=pending %pending%>'.get_lang('Pending').'</option>
		<option value=disable %disable%>'.get_lang('Disable').'</option>
		<option value=trash %trash%>'.get_lang('Trash').'</option>
		</select>' );
	$dg->addColumn ( 'Select', get_lang ( 'Selection' ), '<input type="checkbox" name="select%_lineNumber_%"/>' );
	$dg->setEmptyMessage ( get_lang ( $message ) );
	$dg->fullWidth ();
	$DataTable = $dg->render ();
	
	$DataTable = '<form name="Update" METHOD="POST">' . $DataTable . '
				<input type="hidden" name="Execute" value="true"><br>
				<OPTION SELECTED VALUE="Execute">
				<SELECT NAME="Choice">
				<OPTION SELECTED VALUE="Modify">' . get_lang ( 'Submit changes' ) . '
				<OPTION VALUE="mail">' . get_lang ( 'Sumbmit changes and send e-mail to admin' ) . '
				<OPTION VALUE="Delete">' . get_lang ( 'Delete a course' ) . '	
				</SELECT>
				<input type="submit" name="bupdate" Value=' . get_lang ( 'OK' ) . '>				
				</form>';
	$out = $out . $DataTable;
}

/*Execute changes*/
if ($subModif) {
	
	$m_data = array ();
	$sendTo = array ();
	$idnames = array ();
	$disChange = array (); //make a concatenation of results
	$m_data = $_SESSION ['data']; //Retrieve saved data
	$showdelete = false;
	$i = 0;
	$dis_result = new DialogBox ( );
	
	foreach ( $m_data as $data_mod ) {
		$tabAdd = false;
		
		if ($choice == "Modify" || $choice == "mail") // so modification are made while sending a email to admins
		{
			/*Setting creation date*/
			if (($data_mod ['creationDate'] != $creationDate[$i]) || ($data_mod ['expirationDate'] != $expirationDate[$i])) //change of creation date (publication date in fact);
			{
				if (! ereg ( "^([0-9]{2})/([0-9]{2})/([0-9]{2})$", $creationDate[$i], $regs ) || ! ereg ( "^([0-9]{2})/([0-9]{2})/([0-9]{2})$", $expirationDate[$i], $regs2 )) {
					$dis_result->error ( get_lang ( 'Error' ) . ': ' . $data_mod ['course'] . ' ' . get_lang ( 'Submited data error.Publish date is set after expiration date.' ) );
				} 
				else 
				{
					if (($regs ['1'] > 31) || ($regs ['2'] > 12) || ($regs2 ['1'] > 31) || ($regs2 ['2'] > 12)) {
						$dis_result->error ( get_lang ( 'Error' ) . ': ' . $data_mod ['course'] . '-> ' . get_lang ( 'Error on days and months' ) );
					} else {
						$timestp1 = mktime ( 0, 0, 0, intval ( $regs ['2'] ), intval ( $regs ['1'] ), intval ( $regs ['3'] ) );
						$timestp2 = mktime ( 0, 0, 0, intval ( $regs2 ['2'] ), intval ( $regs2 ['1'] ), intval ( $regs2 ['3'] ) );
						
						if ($timestp1 > $timestp2) {
							$dis_result->error ( get_lang ( 'Error' ) . ': ' . $data_mod ['course'] . '-> ' . get_lang ( 'Submited data error.Publish date is set after expiration date.' ) );
						} else {
							if ($data_mod ['creationDate'] != $creationDate[$i]) {
								$change = $creationDate[$i];
								$change = explode ( "/", $change );
								$change = $change ['2'] . "/" . $change ['1'] . "/" . $change ['0'];
								$code = $data_mod ['course'];
								UpdateDatePublish ( $code, $change );
								$dis_result->success ( '<a href=' . get_path ( 'clarolineRepositoryWeb' ) . 'admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' . ' ' . get_lang ( 'Publish date' ) . '-> ' . get_lang ( 'Update done with success.' ) );
								$tabAdd = true;
							}
							
							if ($data_mod ['expirationDate'] != $expirationDate[$i]) {
								$change = $expirationDate[$i]; //Change french date format to english format
								$change = explode ( "/", $change );
								$change = $change ['2'] . "/" . $change ['1'] . "/" . $change ['0'];
								$code = $data_mod ['course'];
								SetUnpublish ( $code, $change );
								$dis_result->success ( '<a href=' . get_path ( 'clarolineRepositoryWeb' ) . 'admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' . ' ' . get_lang ( 'Remove date' ) . '-> ' . get_lang ( 'Update done with success.' ) );
							}
							$tabAdd = true;
						}
					
					}
				
				}
			}
			
			/*Enable and desable a course*/
			if ($data_mod ['status'] != $status[$i])
			{
				updateDisabled ( $data_mod['course'], $status[$i] );
				$dis_result->success ( ' <a href=' . get_path ( 'clarolineRepositoryWeb' ) . 'admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' . get_lang ( 'State' ) . ' :' . get_lang ( 'Enabled' ) . '-> ' . get_lang ( 'Update done with success.' ) );
				$tabAdd = true;
			}	
		}
		
		/*Deleting a course*/
		if ($select[$i] == "on") 
		{
			if ($choice == "Delete") 
			{
				$code = $data_mod ['course'];
				deleteCourse ( $code );
				$dis_result->success ( ' <a href=' . get_path ( 'clarolineRepositoryWeb' ) . 'admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' . ' ' . get_lang ( 'Course' ) . ' ' . get_lang ( 'Deleted' ) . '-> ' . get_lang ( 'Update done with success.' ) );
				$tabAdd = true;
				$showdelete = true;
			} 
			elseif ($choice == "mail") 
			{
				/*To complete  test if every course administrator has a email to send a mail to.*/
				$idTest = getIdForMail ( $data_mod ['course'] );
				foreach ( $idTest as $testMail ) 
				{
					if ($testMail ['mail'] != "") 
					{
						$sendTo [] = $testMail ['id'];
						$idnames [] = $testMail ['nom'] . ' ' . $testMail ['prenom'];
						$dis_result->info ( get_lang ( 'Sending Mail to: ' ) . ' ' . $testMail ['nom'] . ' ' . $testMail ['prenom'] . ' ' . get_lang ( 'Course Admin' ) . ' <a href=/claroline19bis/claroline/admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' );
					} 
					else 
					{
						//TODO : Placer un warning pour les mails manquants
						$sendTo [] = $testMail ['id'];
						$idnames [] = $testMail ['nom'] . $testMail ['prenom'];
						$dis_result->warning ( get_lang ( 'No email for this user: ' ) . ' ' . $testMail ['nom'] . ' ' . $testMail ['prenom'] . ' ' . get_lang ( 'Course Admin' ) . ' <a href=/claroline19bis/claroline/admin/admincourseusers.php?cidToEdit=' . $data_mod ['course'] . '> ' . $data_mod ['course'] . '</a>' );
					}
				}
				$tabAdd = true;
			
			}
		}
		if ($tabAdd == true)
			$disChange [] = $data_mod;
		$i ++;
	}
	/*Saving email ids for sending Email in Session*/
	$_SESSION ['id'] = array_unique ( $sendTo );
	$_SESSION ['names'] = array_unique ( $idnames );
	
	/*Displaying changes or selections*/
	$DataTable = '';
	$dis_box = $dis_result->render ();
	if ($dis_box == '') {
		$dis_result->error ( get_lang ( 'None user selected/No modification' ) );
		$dis_box = $dis_result->render ();
		$out = $out . $dis_box;
	} else {
		$dis_box = $dis_result->render ();
		$out = $out . $dis_box;
		/*Displaying a textArea for sending emails*/
		if ($choice == "mail") {
			
			$DataTable = '<form name="UpdateDone" METHOD="POST">' . $DataTable . '<h3>' . claro_html_tool_title ( get_lang ( 'Sending email to the course teacher' ) ) . '</h3>' . get_lang ( 'Subject :' ) . ' <input type="text" Name="Subject"><br>' . '<br>' . '<br>' . claro_html_textarea_editor ( 'mail' ) . '<input type="hidden" name="Send" value="ok">' . '<br><input type="submit" name="Submit" Value=' . get_lang ( 'Submit' ) . '></form>';
		
		}
		$out .= $DataTable;
	}
}
if ($sendMail == 'ok') {
	$userId = array ();
	$names = array ();
	$names = $_SESSION ['names'];
	$userId = $_SESSION ['id'];
	$emailBody = $emailBody;
	$emailSubject = $emailSubject;
	claro_mail_user ( $userId, $emailBody, $emailSubject );
	$resume = new DialogBox ( );
	$resume->success ( get_lang ( 'Mail sent.' ) . '<br><br><b>' . get_lang ( 'Subject :' ) . '</b>' . $emailSubject );
	$sentTO = implode ( ";", $names );
	$resume->info ( get_lang ( '<b>' . get_lang ( 'Sent to:' ) . '</b> ' ) . $sentTO );
	$resume->info ( '<b>' . get_lang ( 'Mail: ' ) . '</b><br>' . $emailBody );
	$resume = $resume->render ();
	$out = $out . $resume;

}

// DISPLAY SECTION
$claroline->display->header;

// Breadcrumb
$claroline->display->banner->breadcrumbs->append ( 'Administration', $url = null );
$claroline->display->banner->breadcrumbs->append ( get_lang ( 'Clean unused courses' ), 'index.php' );

// Body
$claroline->display->body->appendContent ( $out );
echo $claroline->display->render ();

?>
