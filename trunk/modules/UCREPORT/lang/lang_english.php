<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$_lang['Action failed'] = 'Action failed';
$_lang['Activate'] = 'Activate';
$_lang['Active / inactive'] = 'Active / inactive';
$_lang['Actualize'] = 'Actualize';
$_lang['An error occured: the report has not been created!'] = 'An error occured: the report has not been created!';
$_lang['An error occured: the report has not been deleted!'] = 'An error occured: the report has not been deleted!';
$_lang['Average'] = 'Average';
$_lang['Back to the examination list'] = 'Back to the examination list';
$_lang['Back to the report list'] = 'Back to the report list';
$_lang['Choose a title'] = 'Choose a title';
$_lang['Comment for'] = 'Comment for';
$_lang['Comments'] = 'Comments';
$_lang['Comments without marks has been ignored!'] = 'Comments without marks has been ignored!';
$_lang['Create a new report'] = 'Create a new report';
$_lang['Create a new session'] = 'Create a new session';
$_lang['Course managers have the right to publish reports where students can see each other scores?'] = 'Course managers have the right to publish reports where students can see each other scores?';
$_lang['Delete the examination?'] = 'Delete the examination?';
$_lang['Do you really want to delete this report?'] = 'Do you really want to delete this report?';
$_lang['Manage plugins'] = 'Manage plugins';
$_lang['empty'] = 'empty';
$_lang['Error'] = 'Error';
$_lang['Examination'] = 'Examination';
$_lang['Examinations'] = 'Examinations';
$_lang['Examination list'] = 'Examination list';
$_lang['Examination Report'] = 'Examination Report';
$_lang['Export to csv'] = 'Export to csv';
$_lang['Export to MS-Excel xlsx file'] = 'Export to MS-Excel xlsx file';
$_lang['Export to pdf'] = 'Export to pdf';
$_lang['Gathering datas'] = 'Gathering datas';
$_lang['inactive'] = 'inactive';
$_lang['Items selection'] = 'Items selection';
$_lang['Learningpath'] = 'Learning path';
$_lang['Mark'] = 'Mark';
$_lang['Max value'] = 'Max value';
$_lang['My examination results and comments'] = 'My examination results and comments';
$_lang['My report'] = 'My report';
$_lang['No report available'] = 'No report available';
$_lang['No result at this time'] = 'No result at this time';
$_lang['No session for this course yet'] = 'No session for this course yet';
$_lang['Number of marks'] = 'Number of marks';
$_lang['Plugin'] = 'Plugin';
$_lang['Plugin active : click to desactivate'] = 'Plugin active : click to desactivate';
$_lang['Plugin inactive : click to activate'] = 'Plugin inactive : click to activate';
$_lang['Plugin management'] = 'Plugin management';
$_lang['Private : click to open'] = 'Private : click to open';
$_lang['Public : click to close'] = 'Public : click to close';
$_lang['Public / private'] = 'Public / private';
$_lang['Public report allowed'] = 'Public report allowed';
$_lang['Publication date'] = 'Publication date';
$_lang['Publish the report'] = 'Publish the report';
$_lang['Report'] = 'Report';
$_lang['Report list'] = 'Report list';
$_lang['Reset'] = 'Reset';
$_lang['Reset scores'] = 'Reset scores';
$_lang['Reset the examination?'] = 'Reset the examination?';
$_lang['See my examination result details'] = 'See my examination result details';
$_lang['Select'] = 'Select';
$_lang['Session'] = 'Session';
$_lang['Session list'] = 'Session list';
$_lang['Student Report'] = 'Student Report';
$_lang['Student\'s name'] = 'Student\'s name';
$_lang['Success'] = 'Success';
$_lang['The changes has been recorded'] = 'The changes has been recorded';
$_lang['The examination %title has been created'] = 'The examination "title" has been created';
$_lang['The examination has been reseted'] = 'The examination has been reseted';
$_lang['The report has been successfully created!'] = 'The report has been successfully created!';
$_lang['The report has beeen successfully deleted!'] = 'The report has beeen successfully deleted!';
$_lang['To import from'] = 'To import from';
$_lang['Weight'] = 'Weight';
$_lang['weight'] = 'weight';
$_lang['Weighted global score'] = 'Weighted global score';
$_lang['Work'] = 'Assignments';
$_lang['wt.'] = 'wt.';
$_lang['You are not a course member'] = 'You are not a course member';
$_lang['You don\'t have score in this report'] = 'You don\'t have score in this report';
$_lang['You have no mark yet for this session'] = 'You have no mark yet for this session';
$_lang['You must give a score to add a comment'] = 'You must give a score to add a comment';

// HELP FILE
$_lang['blockReportHelp'] = '<h1>Student Report tool : teacher\'s manual</h1>

<h2>Introduction</h2>
The aim of the "Student Report" tool is:
<ul>
    <li>to give examination marks to your students, eventually with comments</li>
    <li>to agregate results from other tools, and generate average scores</li>
    <li>to import this results in various formats (xlxs, csv, pdf)
    <li>to communicate these results throught their own desktop</li>
</ul>

<h2>Plugins management</h2>
This tool actually imports results from others tools throught a plugin system.<br />
By default, import from the following tools is available: Assignement, Exercices, Learning Path and... Examination.<br />
In order to keep the UI as clean as possible, you can desactivate plugins for import you don\'t need.<br />
Plugin management page is accessible bu clicking on <strong>Plugin management</strong> button in the report tool main page.<br />
To (des)activate a plugin, simply click on the little "puzzle piece" icon.<br />
<img src="../../module/UCREPORT/img/help/plugin_manage.png" alt="plugins management page" /><br />
Yellow icons indicate activated plugins... and greys ones stand for the others. 

<h2>Examination marks</h2>
The report module, integrates a examination marks management tool.<br />
Actually, it works as a separate bundled module. That\'s why there is a import plugin for that.<br /><br />
In order to access to this part, just click on the <strong>Examinations</strong> button in the tool main page.<br />
<img src="../../module/UCREPORT/img/help/tool_entry.png" alt="report tool main page" />

<h3>Create a new examination session</h3>
First, you have to create a new "session" by clicking on the <strong>Create a new session</strong> button.<br />
Then, a form asking you for its title and maximum score will appear.<br />
By default, maximum score is set to 20.<br />
<img src="../../module/UCREPORT/img/help/exam_create.png" alt="exam creation form" />

<h3>Marks encoding</h3>
Once you validated the form - if nothing goes wrong - a nice message appears, saying that your session has been successfully created.<br />
And you are redirected to a page where you can encode the examination marks for each of your course\'s users.<br />
<img src="../../module/UCREPORT/img/help/exam_created.png" alt="exam created" /><br />
Optionnaly, you can give them a comment aswell.<br /><br />
Then click on <strong>OK</strong> to save your datas.<br />
<img src="../../module/UCREPORT/img/help/exam_edit.png" alt="exam editiing form" /><br />
After that, you stay in the same page though, for examination marks can be modified at any time - unlike report marks...<br /><br />
If you return to the examination list by clicking on <strong>Back to examination list</strong>, you can see that your session has been added to the list.<br />
<img src="../../module/UCREPORT/img/help/exam_list.png" alt="session list" /><br />
From that page, you can modify its visibility... or delete it.

<h3>How my students can access to their marks and comments?</h3>
Students can view their marks and comments by clicking on the <strong>Examination</strong> button.<br />
However, they can\'t see the scores of their fellow in this page.<br />
<img src="../../module/UCREPORT/img/help/exam_student.png" alt="student view" />

<h2>Create a report</h2>
The report creation process includes three steps.

<h3>Step 1 : Items selection and weighting encoding</h3>
First of all, click on the <strong>Create a new report</strong> button.<br />
You are led to a page displaying a list of items detected by the system.<br />
<img src="../../module/UCREPORT/img/help/result_import.png" alt="item import GUI" /><br />
Select the items you want to import by checking the corresponding checkbox in the <strong>Select</strong> column.<br />
Items which are visibles in their tool are selected by default.<br /><br />

The <strong>Weight</strong> column allows you to define the weighting of each item.<br /><br />
The default weighting is 100.<br />
You are free to put any numeric values in these fields. The system will automatically calculate the proportionnal weights.<br /><br />

<em>For instance,<br />
&nbsp;&nbsp;&nbsp;&nbsp;in case of 4 items with the following weight values: 50, 200, 100 et 150,<br />
&nbsp;&nbsp;&nbsp;&nbsp;you\'ll get the subsequent proportionnal weights: 10%, 40%, 20% et 30%.</em><br /><br />
Once you\'ve done, validate your choices by clicking in <strong>Import</strong>.

<h3>Step 2 : Students selection and marks adjustment</h3>
The following page displays the results of all your students, baked with weighted average scores.<br />
<img src="../../module/UCREPORT/img/help/result_edit.png" alt="report editing GUI" /><br />
Final scores are only generated for student with a mark in ALL columns.<br />
The other ones are "unabled" : their marks will not appear in the published report.<br />
You can easily identify an unabled student:<br />
&nbsp;&nbsp;&nbsp;&nbsp;his "little eyed" in the <strong>Activate</strong> column is closed.<br />
&nbsp;&nbsp;&nbsp;&nbsp;instead of his final score displayed in the las column, there is a "inactive" statement (in light grey).<br /><br />
You can simply activate or desactivate any student by click on the "little eye".<br /><br />
When activating an user, his missing marks are coneverted into zeros.<br />
The average scores are automatically regenerated at each change.<br /><br />
At this stage, you can modify (or adjust) any marks.<br />
For instance, you can give a score to a student who send a (late) work by mail, and therefore has no score in the Assignements tool...<br /><br />
Don\'t forget to click on <strong>Actualize</strong> after modifying datas... or your changes will not be saved!<br /><br />
You can also export this datas into three different formats : MS-Excel 2007 (*.xlsx), CSV and PDF.

<h3>Step 3 : Report publishing</h3>
Once you\'re done, you are ready to "publish" the report by clicking on <strong>Publish the report</strong>.<br />
Then a form appears, asking you to choose a title:<br />
<img src="../../module/UCREPORT/img/help/report_create.png" alt="report creation form" /><br />
After validation, you are redirected to the tool\'s main page where you can see the available reports list.<br />
<img src="../../module/UCREPORT/img/help/report_created.png" alt="report created" />

<h2>Published reports</h2>
Published reports can\'t be modified.<br />
You can althought change their visibility or delete them.<br />
<img src="../../module/UCREPORT/img/help/report_list.png" alt="report list" /><br />
To consult a report, just click on its name in the list.<br />
A report looks like this:<br />
<img src="../../module/UCREPORT/img/help/report_view.png" alt="published report" /><br />
From this page, you can export it into the same formats as step 2...

<h3>Report confidentiality</h3>
Our tool implements a functionality which allow students to see each other scores.<br />
When this functionality is enabled, you can change the report status by clicking on the icon in the <strong>Public / Private</strong> column.<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="../../web/img/user.png" alt="one little head" /> means that students can only see their own results.<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="../../web/img/group.png" alt="two little heads" /> means that they can see each other results.<br /><br />
<strong>Warning : </strong>This feature can be in conflict with the security policy of your e-Learning platform.<br />
That\' why it can be disabled by the platform administrator.<br />
If it\'s the case, the <strong>Public / Private</strong> column simply not appears...

<h3>Students access to published reports</h3>

Students can consult published report, (under the conditions defined by the security policy of your institution) in the "report" page of your course.<br /><br />
They also have an access to any published reports concerning them throught their desktop.<br />
<img src="../../module/UCREPORT/img/help/desktop_portlet.png" alt="desktop portlet" /><br />
They can export report into PDF format';