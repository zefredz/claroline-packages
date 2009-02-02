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

    //This script is used to create a survey or edit properties of an existing survey
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);
    
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    add_module_lang_array($tlabelReq);
    
    $is_allowedToEdit = claro_is_allowed_to_edit();
    if($is_allowedToEdit == false)
    {
        //not allowed for normal user
        header('Location: survey_list.php');
        exit();
    }
    
    if(isset($_REQUEST['surveyId']))
        $surveyId = (int)$_REQUEST['surveyId'];
    else
        $surveyId = -1;
    
    require_once 'lib/surveylist.class.php';
    require_once 'lib/survey.class.php';
    
    $dialogBox = new DialogBox();
	$survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
    
    if($surveyId > 0)
    {
        //we want to edit an existing survey
    	$pageTitle = get_lang('Edit this survey');
    	if(isset($_REQUEST['claroFormId']))
    	{
    		//we are trying to submit changes
    		if($survey->loadFromEditForm())
    		{
    			$survey->save();
    			
    			$boxcontent = '<ul><li><a href="survey_list.php">'.
                        get_lang("Get back to the survey list")
                        .'</a></li></ul>';
    			$dialogBox->success( get_lang("Survey has been updated")."!".$boxcontent);
    			$contenttoshow = "" ;
    		}
    		else
    		{
                $dialogBox->error( $survey->getValidationErrors() );
    			$contenttoshow = $survey->renderEditForm();
    		}
    	}
    	else
    	{
    		//show the form for editing
    		$survey->load($surveyId);
    		$contenttoshow = $survey->renderEditForm();
    	}
    	
    }
    else
    {
        //we are creating a survey
    	$pageTitle = get_lang('New survey');
    	if(isset($_REQUEST['claroFormId']))
    	{    
    		//we are trying to submit new
    		if($survey->loadFromEditForm())
    		{
    			$survey->save();
    			$id = $survey->getId();
    			$boxcontent = '<ul>
            			<li><a href="add_question.php?surveyId='.$id.'">'.
                        get_lang("Add a question to the survey")
                        .'</a></li>'."\n"
                        .'<li><a href="survey_list.php">'.
                        get_lang("Get back to the survey list")
                        .'</a></li></ul>';
    			$dialogBox->success( get_lang("Survey has been saved")."!".$boxcontent);
    			$contenttoshow = "" ;
    		}
    		else
    		{
                //if there is an error while loading form
    			$dialogBox->error( $survey->getValidationErrors() );
    			$contenttoshow = $survey->renderEditForm();
    		}
    		
    	}
    	else
    	{
    		//show the empty form for new survey
    		$contenttoshow = $survey->renderEditForm();
    	}
    }
    
    if($surveyId > 0)
    {
        $cmd_menu[] = '<a class="claroCmd" href="show_survey.php?surveyId='.$surveyId.'">'.'<img src="'. get_path('imgRepositoryWeb') . '/edit.gif" border="0" alt="'.get_lang('Modify').'" />'.get_lang('Edit questions of this survey').'</a>';
    }
    
    //generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    
    if($surveyId > 0)
        $out .= '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>' . "\n";
    //-- dialogBox
    $out .= $dialogBox->render();
    //$out .= '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>' . "\n";

    $out .= $contenttoshow;
    // Name of the tool (displayed in title)
    //$nameTools = get_lang($pageTitle);
    
    //create breadcrumbs
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
    
?>