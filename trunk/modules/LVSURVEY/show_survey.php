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
    // This script is used to show the survey and to answer to it
  
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    // Name of the tool (displayed in title)
    //$nameTools = 'CLSurvey';
   
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);
    
    add_module_lang_array($tlabelReq);
    
    $pageTitle = get_lang('Survey');
    
    claro_set_display_mode_available(true);
    
    $is_allowedToEdit = claro_is_allowed_to_edit();
    
    //require_once 'lib/questionlist.class.php';
    require_once 'lib/survey.class.php';
    
    
    if(isset($_REQUEST['surveyId']))
    {
        $surveyId = (int) $_REQUEST['surveyId'];
        $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
        if($survey->load($surveyId) == true)
        {
            $pageTitle = htmlspecialchars($survey->getTitle());
        }
        else
        {
            header('Location: survey_list.php');
            exit();
        }
    }
    else
    {
        header('Location: survey_list.php');
        exit();
    }
    
    if(isset($_REQUEST['questionId']))
        $questionId = (int)$_REQUEST['questionId']; 
    else
        $questionId = -1;
    
    
    if(($is_allowedToEdit == true) && (isset($_REQUEST['cmd'])))
    {
        //which action
        if(($_REQUEST['cmd']=='questionMoveUp') || ($_REQUEST['cmd']=='questionMoveDown'))
        {
            //change order of questions in survey
            if($questionId>0)
            {   
            	$tmpsurvey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
                $tmpsurvey->load($surveyId);
                $tmpsurvey->loadQuestions();
            	if($_REQUEST['cmd']=='questionMoveUp')
                    $tmpsurvey->moveQuestionUp($questionId);
            	else
            		$tmpsurvey->moveQuestionDown($questionId);
            }
        	header("Location: show_survey.php?surveyId=".$surveyId);
        	exit();
        }
        else if($_REQUEST['cmd']=='questionRemove')
        {
            //remove question of the survey
            if(isset($_REQUEST['claroFormId']))
            {
                require_once 'lib/question.class.php';
                $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
                $question->load($questionId);
                $question->removeFromSurvey($surveyId);
                header("Location: show_survey.php?surveyId=".$surveyId);
                exit();
            }
            else //ask confirmation
            {
                $form = '<table><tr>'
                       .'<td><form method="post" action="show_survey.php?surveyId='.$surveyId.'">'
                       .'<input type="submit" name="submit" value="'.get_lang('Cancel').'" />'
                       .'</form>'
                       .'</td><td><form method="post" action="show_survey.php?surveyId='.$surveyId.'">'
                       .'<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'
                       .'<input type="hidden" name="questionId" value="'.$questionId.'" />'
                       .'<input type="hidden" name="cmd" value="questionRemove" />'
                       .'<input type="submit" name="submit" value="'.get_lang('Confirm').'" />'
                       .'</form></td></tr></table>';
                $dialogBox = new DialogBox();
                
                require_once 'lib/question.class.php';
                $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
                $question->load($questionId);
                $question->setSurveyId($surveyId);
                if($question->isAnswered())
                    $dialogBox->warning(get_lang('Some users have already answered to this survey. Answers about this question of this survey will be remove too. '));
                $dialogBox->question( get_lang('Are you sure you want to remove this question from the survey?')
                            . '<br />' .get_lang('Title of the question') . ' : '. htmlspecialchars($question->getTitle()) . $form);
                //$dialogBox->info($form);
                $contenttoshow = $dialogBox->render();
            }
            $breadapp = get_lang('Remove question');
            $pageTitle = get_lang('Remove question');
        }
    }
    else
    {
        //nothing to do, just show the survey
        $contenttoshow = '';
        $survey->loadQuestions();

        if($is_allowedToEdit)
        {
            $cmd_menu[] = '<a class="claroCmd" href="edit_survey.php?surveyId='.$surveyId.'">'.'<img src="'. get_path('imgRepositoryWeb') . 'edit.gif" border="0" alt="'.get_lang('Modify').'" />'.get_lang('Edit survey properties').'</a>';
            $cmd_menu[] = '<a class="claroCmd" href="add_question.php?surveyId='.$surveyId.'">'.get_lang('Add question').'</a>';
            $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$surveyId.'">'.get_lang('View results of this survey').'</a>';
        }
        else
        {
            if($survey->getResultsVisibility()!= 'INVISIBLE')
                $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$surveyId.'">'.get_lang('View results of this survey').'</a>';
        }
        
        if(!isset($_REQUEST['claroFormId']))
        {
            //nothing to do, just show the survey
            $survey->loadAnswers(claro_get_current_user_id());
            $contenttoshow .= $survey->renderFillForm();
        }
        else
        {
            //the form has been filled
            $survey->loadFromFillForm();
            if(isset($_REQUEST['surveyGoToConf']))
            {
                //show the confirmation page
                $contenttoshow .= $survey->renderConfForm();
            }
            else if(isset($_REQUEST['surveyGoToSubmit']))
            {
                //he just confirmed
                $survey->saveAnswers(claro_get_current_user_id());
                $dialogBox = new DialogBox();
                $dialogBox->success( get_lang('Your answers have been saved.'));
                $contenttoshow .= $dialogBox->render();
            }
            else if(isset($_REQUEST['surveyGoToFill']))
            {
                //he wants to change his answers
                $contenttoshow .= $survey->renderFillForm();
            }
        }
    }
     
    //generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    if(!empty($cmd_menu))
        $out .= '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>' . "\n";

    $out .= $contenttoshow;
    
	//breadcrumb
	$claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
	
	if(isset($breadapp))
	{
	    $claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->getTitle()), 'show_survey.php?surveyId='.$surveyId); 
	    $claroline->display->banner->breadcrumbs->append($breadapp); 
	}
	else
	    $claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->getTitle())); 
	    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
    
?>