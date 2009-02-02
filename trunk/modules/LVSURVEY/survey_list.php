 <?php 
  /**
     * This is a tool to create surveys. It's the new version better than older CLSURVEY
     * @copyright (c) Haute Ecole Léonard de Vinci
     * @version     0.1 $Revision$
     * @author      BAUDET Gregory <gregory.baudet@gmail.com>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     LVSURVEY
     */
   
    // This script is used to show the survey list, and some action on it
    
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    // Name of the tool (displayed in title)
    //$nameTools = 'CLSurvey';
   
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);
    
    add_module_lang_array($tlabelReq);
    
    $pageTitle = get_lang('List of surveys');
    
    claro_set_display_mode_available(true);
    
    $is_allowedToEdit = claro_is_allowed_to_edit();
    
    require_once 'lib/surveylist.class.php';
    require_once 'lib/survey.class.php';
    
    if(($is_allowedToEdit == true) && (isset($_REQUEST['cmd'])))
    {
        if(($_REQUEST['cmd']=='surveyMkInvis') || ($_REQUEST['cmd']=='surveyMkVis'))
        {
            //make survey visible or invisible
        	$survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
        	$survey->load((int) $_REQUEST['surveyId']);
        	if($_REQUEST['cmd']=='surveyMkInvis')
        		$vis = 'INVISIBLE';
        	else
        		$vis = 'VISIBLE';
        	$survey->setVisibility($vis);
        	$survey->save();
        	header("Location: survey_list.php");
        	exit();
        }
        else if(($_REQUEST['cmd']=='surveyStart') || ($_REQUEST['cmd']=='surveyRestart') || ($_REQUEST['cmd']=='surveyStop'))
        {
            //start or stop a survey by changing start and end date
            $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
        	$survey->load((int) $_REQUEST['surveyId']);
        	if($_REQUEST['cmd']=='surveyStart')
        	{
        	    $survey->setStartDate(time());
        	    if($survey->getEndDate() < time())
        	        $survey->setEndDate(0);
        	}
        	else if($_REQUEST['cmd']=='surveyRestart')
        	{
	            $survey->setStartDate(time());
	            $survey->setEndDate(0);
        	}
        	else if($_REQUEST['cmd']=='surveyStop')
        	{
        	    $survey->setEndDate(time());
        	    if($survey->getStartDate() > time())
        	        $survey->setStartDate(0);
        	}
        	$survey->save();
        	header("Location: survey_list.php");
        	exit();
        }
        else if(($_REQUEST['cmd']=='surveyMoveUp') || ($_REQUEST['cmd']=='surveyMoveDown'))
        {
            //move the survey in the list up or down
        	$tmpsurveys = new surveyList(claro_get_current_course_id(), $is_allowedToEdit);
        	if($_REQUEST['cmd']=='surveyMoveUp')
        		$tmpsurveys->moveSurveyUp((int) $_REQUEST['surveyId']);
        	else
        		$tmpsurveys->moveSurveyDown((int) $_REQUEST['surveyId']);
        	header("Location: survey_list.php");
        	exit();
        }
        else if(($_REQUEST['cmd']=='surveyDel'))
        {
            if(isset($_REQUEST['surveyId']))
                $surveyId = (int)$_REQUEST['surveyId'];
            else
            {
                header('Location: survey_list.php');
                exit();
            }
            $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
            if(!$survey->load($surveyId))
            {
                header('Location: survey_list.php');
                exit();
            }
            if(isset($_REQUEST['conf']) && ((int)$_REQUEST['conf']==1))
            {
                //delete the survey
                require_once 'lib/survey.class.php';
                $survey->delete();
                $dialogBox = new DialogBox();
                $boxcontent = '<ul>
            			<li><a href="survey_list.php">'.
                        get_lang("Get back to the survey list")
                        .'</a></li>'."\n"
                        .'</ul>';
                $dialogBox->success( get_lang('Survey has been deleted')."!".$boxcontent);
                $contenttoshow = $dialogBox->render();
            }
            else //ask confirmation
            {
                $form = '<table><tr>'
                       .'<td><form method="post" action="survey_list.php">'
                       .'<input type="submit" name="submit" value="'.get_lang('Cancel').'" />'
                       .'</form>'
                       .'</td><td><form method="post" action="survey_list.php">'
                       .'<input type="hidden" name="conf" value="1" />'
                       .'<input type="hidden" name="surveyId" value="'.$surveyId.'" />'
                       .'<input type="hidden" name="cmd" value="surveyDel" />'
                       .'<input type="submit" name="submit" value="'.get_lang('Confirm').'" />'
                       .'</form></td></tr></table>';
                $dialogBox = new DialogBox();
                if($survey->isAnswered())    
                     $dialogBox->warning(get_lang('Some users have already answered to this survey.').' '.get_lang('Results will be removed.'));

                $dialogBox->question( get_lang('Are you sure you want to delete this survey?')
                                . '<br />' . get_lang('Title of the survey'). ' : ' . htmlspecialchars($survey->getTitle()));
                $dialogBox->form($form);
                $contenttoshow = $dialogBox->render();
                
            }
            $breadapp = get_lang('Delete survey');
            $pageTitle = get_lang('Delete survey');
            
        }
    }
    else
    {
        //show the list of surveys
        if($is_allowedToEdit)
        {
            $cmd_menu[] = '<a class="claroCmd" href="edit_survey.php">'.get_lang('New survey').'</a>';
            $cmd_menu[] = '<a class="claroCmd" href="question_pool.php">'.get_lang('Question pool').'</a>';
        }
        $surveys = new surveyList(claro_get_current_course_id(), $is_allowedToEdit);
        $surveys->load();
        
        $contenttoshow = $surveys->render();
    }
    
    

    //generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    if($is_allowedToEdit && isset($cmd_menu))
        $out .= '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>' . "\n";
    
    $out .= $contenttoshow;

	//breadcrumb
	if(isset($breadapp))
	{
	    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php'); 
	    $claroline->display->banner->breadcrumbs->append($breadapp); 
	}
	else
	    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys')); 
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();

?>