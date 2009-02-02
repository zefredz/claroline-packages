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
    //This script is used to add a question to a survey. It asks first if your want to add a existing question or to create a new one
    //Then the script is used to list available question and choose.
    //If we choose to create a new question, this is the script edit_question wich is loaded.
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
    
    require_once 'lib/question.class.php';
    
    require 'lib/survey.class.php';
    $survey = new Survey(claro_get_current_course_id(), $is_allowedToEdit);
    
    if(isset($_REQUEST['surveyId']))
    {
        //this script requires a survey id
        $surveyId = (int)$_REQUEST['surveyId'];
        $survey->load($surveyId);
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
    
    if((isset($_REQUEST['fromPool'])) && ((int)$_REQUEST['fromPool']==1))
    {
        //we have chosen to add an existing question
        //we will show the list to choose
        $contenttoshow = '';
        require_once 'lib/questionlist.class.php';
        if($survey->isAnswered() == true)
        {
            //a warning if some users already answered to the survey
            $dialogBox = new DialogBox();
            $dialogBox->warning( get_lang('Some users have already answered to this survey.').' '
                                .get_lang('It\'s not a good idea to add a question.'));

            $contenttoshow .= $dialogBox->render();
        }
        $pageTitle = get_lang("Add a question to the survey");
        
        $questions = new questionList(claro_get_current_course_id());
        $reverse = false;
        //questions ordered by title by default
        $orderby = 'title';
        if(isset($_REQUEST['orderby']))
            $orderby = $_REQUEST['orderby'];
        if(isset($_REQUEST['reverse']) && ($_REQUEST['reverse'] == '1'))
            $reverse = true;
            
        //load the question list in specified order
        $questions->load($orderby, $reverse);
        $contenttoshow .= $questions->renderChooseList($surveyId);
        $contenttoshow .= '<a href="edit_question.php?surveyId='.$surveyId.'">'.
                        get_lang("Create a new question")
                        .'</a>';
    	
    }
    else
    {
        if($questionId>0)
        {
            //we have chosen a question, add it to the survey now
            $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
            $question->load($questionId);
            $question->addToSurvey($surveyId);
            //then reshow the survey
            header("Location: show_survey.php?surveyId=".$surveyId);
            exit();
        }
        else
        {
            //ask existing or new question
            $contenttoshow='';
            if($survey->isAnswered() == true)
            {
                //a warning if some users already answered to the survey
                $dialogBox = new DialogBox();
                $dialogBox->warning( get_lang('Some users have already answered to this survey.').' '
                        . get_lang('It\'s not a good idea to add a question.'));
                $contenttoshow .= $dialogBox->render();
            }
            $pageTitle = get_lang("Add a question to the survey");
            $contenttoshow .='<ul>';

            $contenttoshow .='<li><a href="add_question.php?fromPool=1&surveyId='.$surveyId.'">'.
                        get_lang("Select an existing question")
                        .'</a></li>'."\n";
                        
            $contenttoshow .= '<li><a href="edit_question.php?surveyId='.$surveyId.'">'.
                        get_lang("Create a new question")
                        .'</a></li></ul>';
        }
    }
    
    //generate $out
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    
    $out .= $contenttoshow;
    // Name of the tool (displayed in title)
    //$nameTools = get_lang($pageTitle);
    
    //create breadcrumb
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append(htmlspecialchars($survey->getTitle()), 'show_survey.php?surveyId='.$surveyId);
    $claroline->display->banner->breadcrumbs->append($pageTitle);
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
?>