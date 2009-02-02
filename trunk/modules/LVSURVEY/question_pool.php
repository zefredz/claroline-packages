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
   
    //This script is the question bank
    //It makes listing of questions and deletion of question from pool
    
    // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    // Name of the tool (displayed in title)
    //$nameTools = 'CLSurvey';
   
    // load Claroline kernel
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( !claro_is_in_a_course() || !claro_is_course_allowed() || !claro_is_user_authenticated() ) claro_disp_auth_form(true);
    
    add_module_lang_array($tlabelReq);
    
    $pageTitle = get_lang('Question pool');
    
    $is_allowedToEdit = claro_is_allowed_to_edit();
    if($is_allowedToEdit == false)
    {
        //not allowed for normal user
        header('Location: survey_list.php');
        exit();
    }
    
    require_once 'lib/questionlist.class.php';
    
    if(isset($_REQUEST['cmd']))
    {
        //there is action to complete
        if(($_REQUEST['cmd']=='questionDel'))
        {
            //delete a question
            if(isset($_REQUEST['questionId']))
                $questionId = (int)$_REQUEST['questionId'];
            else
            {
                header('Location: question_pool.php');
                exit();
            }
            require_once 'lib/question.class.php';
            $question = new Question(claro_get_current_course_id(), $is_allowedToEdit);
            if(!$question->load($questionId))
            {
                header('Location: question_pool.php');
                exit();
            }
            if(isset($_REQUEST['conf']) && ((int)$_REQUEST['conf']==1))
            {
                //if we confirm we want to delete
                //delete the question
                $question->delete();
                $dialogBox = new DialogBox();
                $boxcontent = '<ul>
            			<li><a href="question_pool.php">'.
                        get_lang("Return to question pool")
                        .'</a></li>'."\n"
                        .'</ul>';
                $dialogBox->success( get_lang('Question has been deleted').$boxcontent);
                $contenttoshow = $dialogBox->render();
            }
            else //ask confirmation
            {
                $form = '<table><tr>'
                       .'<td><form method="post" action="question_pool.php">'
                       .'<input type="submit" name="submit" value="'.get_lang('Cancel').'" />'
                       .'</form>'
                       .'</td><td><form method="post" action="question_pool.php">'
                       .'<input type="hidden" name="conf" value="1" />'
                       .'<input type="hidden" name="questionId" value="'.$questionId.'" />'
                       .'<input type="hidden" name="cmd" value="questionDel" />'
                       .'<input type="submit" name="submit" value="'.get_lang('Confirm').'" />'
                       .'</form></td></tr></table>';
                $dialogBox = new DialogBox();
                if($question->isUsedInSurvey())    
                     $dialogBox->warning(get_lang('This question is used in some surveys. This question will be removed from them.'));
                $dialogBox->question( get_lang('Are you sure you want to delete this question?')
                    . '<br />' .get_lang('Title of the question') . ' : '. htmlspecialchars($question->getTitle()) );
                $dialogBox->form($form);
                $contenttoshow = $dialogBox->render();
            }
            
        }
        else
        {
            header('Location: question_pool.php');
            exit();
        }
    }
    else
    {
        //no special action, show question pool
        $cmd_menu[] = '<a class="claroCmd" href="edit_question.php">'.get_lang('New question').'</a>';
        //$cmd_menu[] = '<a class="claroCmd" href="survey_list.php">'.get_lang('List of surveys').'</a>';
        
        $questions = new questionList(claro_get_current_course_id());
        $reverse = false;
        $orderby = 'title';
        if(isset($_REQUEST['orderby']))
            $orderby = $_REQUEST['orderby'];
        if(isset($_REQUEST['reverse']) && ($_REQUEST['reverse'] == '1'))
            $reverse = true;
            
        $questions->load($orderby, $reverse);
        $contenttoshow = $questions->render();
    }
    
    //generate output
    $out = '';
    $out .= claro_html_tool_title($pageTitle);
    if(isset($cmd_menu))
        $out .= '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>' . "\n";
    
    
    $out.= $contenttoshow;
    
    
	//breadcrumb
	$claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php');
	$claroline->display->banner->breadcrumbs->append($pageTitle);
    
    // append output
    $claroline->display->body->appendContent($out);
   
    // render output
    echo $claroline->display->render();

?>