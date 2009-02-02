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
    // This script is used to show a preview of a question from question pool
    
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    // Name of the tool (displayed in title)
    //$nameTools = 'CLSurvey';
   
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);
    
    add_module_lang_array($tlabelReq);
    
    $pageTitle = get_lang('Question preview');
    
    $is_allowedToEdit = claro_is_allowed_to_edit();
    if($is_allowedToEdit == false)
    {
        //not allowed for normal user
        header('Location: survey_list.php');
        exit();
    }
    
    require_once 'lib/question.class.php';

    if(isset($_REQUEST['questionId']))
        $questionId = (int) $_REQUEST['questionId'];
    else
    {
        header('Location: question_pool.php');
        exit();
    }

    // generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    
    $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
    if($question->load($questionId) == false)
    {
        $dialogBox = new DialogBox();
        $dialogBox->error( get_lang('Error'));
        $out .= $dialogBox->render();
    }
    else
    {
        $out .= '<div>';
        $out .= $question->renderFillForm();
        $out .= '</div>';
        //it could be interesting to show a list of survey using the question
    }

	//breadcrumb
	$claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
    $claroline->display->banner->breadcrumbs->append(get_lang('Question pool'), 'question_pool.php');
	$claroline->display->banner->breadcrumbs->append($pageTitle);
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();
    
?>