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
   
    //This script is used to create a new question or to edit an existing question
    //This script may be initiated from question pool or a survey
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
        header('Location : survey_list.php');
        exit();
    }
    
    require_once 'lib/question.class.php';
    
    if(isset($_REQUEST['questionId']))
        $questionId = (int)$_REQUEST['questionId'];
    else
        $questionId = -1;
        
    if(isset($_REQUEST['surveyId']))
        $surveyId = (int)$_REQUEST['surveyId'];
    else
        $surveyId = -1;
    
    $dialogBox = new DialogBox();
    $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
    $question->setSurveyId($surveyId);    
    
    if($questionId==-1)
    {
        //creating a new a question
    	$pageTitle = get_lang('New question');
    	//we want to edit a survey
    	if(isset($_REQUEST['claroFormId']))
    	{
    		//we are trying to submit new
    		if($question->loadFromEditForm())
    		{    	

    			$question->save();    			
    		    if($surveyId!= -1)
                {
                    //the script was initiated from a survey
                    //add the question in the survey
                    $question->addToSurvey($surveyId);
                
                    $boxcontent = '<ul>
            			<li><a href="add_question.php?surveyId='.$surveyId.'">'.
                        get_lang("Add another question to the survey")
                        .'</a></li>'."\n"
                        .'<li><a href="show_survey.php?surveyId='.$surveyId.'">'.
                        get_lang("Return to the survey")
                        .'</a></li></ul>';
    			    $dialogBox->success( get_lang("Question has been saved")."!".$boxcontent);
    			    $contenttoshow = "";
                }
                else{                    
                	header("Location: question_pool.php");
                    exit();
                }
    		}
    		else
    		{
    			$dialogBox->error( $question->getValidationErrors() );
    			$contenttoshow = $question->renderEditForm();
    		}
    	}
    	else
    	{
            //show the empty form for new auestion
    		$contenttoshow = $question->renderEditForm();
    	}
    }
    else
    {
        //we are editing  a question
        $question->load($questionId);
        $question->setSurveyId($surveyId);
    	$pageTitle = get_lang('Edit question');
    	if(isset($_REQUEST['claroFormId']))
    	{
    		//we are trying to submit modifications
    		if($question->loadFromEditForm())
    		{
    			$question->save();
                if($surveyId != -1){
                    $boxcontent = '<ul>
            			<li><a href="show_survey.php?surveyId='.$surveyId.'">'.
                        get_lang("Return to the survey")
                        .'</a></li>'."\n"
                        .'<li><a href="survey_list.php">'.
                        get_lang("Go to the survey list")
                        .'</a></li></ul>';
    			    $dialogBox->success( get_lang("Question has been updated").$boxcontent);
    			    $contenttoshow = "";
                }
                else{
                    $boxcontent = '<ul>
            			<li><a href="question_pool.php">'.
                        get_lang("Return to question pool")
                        .'</a></li>'."\n"
                        .'</ul>';
    			    $dialogBox->success( get_lang("Question has been updated").$boxcontent);
    			    $contenttoshow = "";
                }
    		}
    		else
    		{
                $dialogBox->error( $question->getValidationErrors() );
    			$contenttoshow = $question->renderEditForm();
    		}
    	}
    	else
    	{
    		//show the form for editing the question
            $question->load($questionId);
    		$contenttoshow = $question->renderEditForm();
    	}
    }
    

    
    
    //generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    //-- dialogBox
    $out .= $dialogBox->render();

    $out .= $contenttoshow;
    // Name of the tool (displayed in title)
    //$nameTools = get_lang($pageTitle);
    
    // create breadcrumbs
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    if($surveyId==-1){
        $claroline->display->banner->breadcrumbs->append(get_lang('Question pool'), 'question_pool.php');
    }
    else
    {
        require_once 'lib/survey.class.php';
        $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
        $survey->load($surveyId);
        $claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->getTitle()), 'show_survey.php?surveyId='.(int)$_REQUEST['surveyId']);
    }
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();

    
    
?>