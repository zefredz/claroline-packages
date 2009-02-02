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
    // This script is used to remove a question from a survey
    
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    // Name of the tool (displayed in title)
    //$nameTools = 'CLSurvey';
    echo "page abandonneee";
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);

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
        
    if(isset($_REQUEST['questionId']))
        $questionId = (int)$_REQUEST['questionId']; 
    else
        $questionId = -1;
    
    if($surveyId>0)
    {
        //we remove a question from a survey
        if($questionId > 0)
        {
            require_once 'lib/question.class.php';
            $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
            $question->load($questionId);
            $question->removeFromSurvey($surveyId);
        }
        header("Location: show_survey.php?surveyId=".$surveyId);
        exit();
    }
    else
    {
        //error, don't know what to do, no survey
        header("Location: survey_list.php");
        exit();
    }
        
    
    
?>