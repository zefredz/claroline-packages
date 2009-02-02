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
    // This script is used to show results of a survey
   
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
    
    require_once 'lib/survey.class.php';
    
    if(isset($_REQUEST['surveyId']))
    {
        //load the survey
        $surveyId = (int) $_REQUEST['surveyId'];
        $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
        if($survey->load($surveyId) == true)
        {
            $pageTitle = htmlspecialchars( $survey->getTitle());
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
    
    
    if(($is_allowedToEdit == true) && (isset($_REQUEST['cmd'])))
    {
        //something special to do
        if($_REQUEST['cmd']=='resultsDel')
        {
            //clear results
            if(isset($_REQUEST['claroFormId']))
            {
                $contenttoshow = '';
                $survey->loadQuestions();
                $survey->removeAnswers();
                
                $dialogBox = new DialogBox();
                $dialogBox->success(get_lang('Results have been deleted'));
                $contenttoshow .= $dialogBox->render();
                $contenttoshow .= $survey->renderResults();
            }
            else //ask confirmation
            {
                $form = '<table><tr>'
                       .'<td><form method="post" action="show_results.php?surveyId='.$surveyId.'">'
                       .'<input type="submit" name="submit" value="'.get_lang('Cancel').'" />'
                       .'</form>'
                       .'</td><td><form method="post" action="show_results.php?surveyId='.$surveyId.'">'
                       .'<input type="hidden" name="claroFormId" value="'.uniqid('').'" />'
                       .'<input type="hidden" name="cmd" value="resultsDel" />'
                       .'<input type="submit" name="submit" value="'.get_lang('Confirm').'" />'
                       .'</form></td></tr></table>';
                $dialogBox = new DialogBox();
                
                $dialogBox->question( get_lang('Are you sure you want to delete results of this survey?')
                            . '<br />' .get_lang('Title of the survey') . ' : '. htmlspecialchars($survey->getTitle()) . $form);
                $contenttoshow = $dialogBox->render();
            }
            $breadapp = get_lang('Delete all results');
            $pageTitle = get_lang('Delete all results');
        }
    }
    else
    {
        //show results
        $contenttoshow = '';
        $survey->loadQuestions();
        $survey->loadResults();
        $contenttoshow .= $survey->renderResults();
        
        if($is_allowedToEdit)
        {
            $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$surveyId.'&amp;cmd=resultsDel">'.get_lang('Delete all results').'</a>';
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
	$claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->getTitle()), 'show_survey.php?surveyId='.$surveyId); 
	
	
    //breadcrumb
	if(isset($breadapp))
	{
	    $claroline->display->banner->breadcrumbs->append(get_lang('Results'), 'show_results.php?surveyId='.$surveyId); 
	    $claroline->display->banner->breadcrumbs->append($breadapp); 
	}
	else
	    $claroline->display->banner->breadcrumbs->append(get_lang('Results')); 
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
    
?>