<?php 
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
From::module('LVSURVEY')->uses(	'model/question.class', 
								'controller/managerSurveyLessPage.class');

class QuestionPoolPage extends ManagerSurveyLessPage
{
	
	const DEFAULT_ORDER_BY = 'text';
	const DEFAULT_ASCDESC = 'ASC';
    
    const DEFAULT_AUTHOR_FILTER = null; 
    const DEFAULT_COURSE_FILTER = null; 
	
	private $questionId = 0;
	private $question = null;
	private $surveyId = 0;
	private $showDeleteConfirm = false;
	
	private $orderby = self::DEFAULT_ORDER_BY;
	private $ascDesc = self::DEFAULT_ASCDESC;
    
    private $author_filter = self::DEFAULT_AUTHOR_FILTER;
    private $course_filter = self::DEFAULT_COURSE_FILTER;
	
	
	public function __construct()
	{
		parent::__construct();
		$input = Claro_UserInput::getInstance();
		$orderByOptionValidator = new Claro_Validator_AllowedList(array('text', 'type', 'used'));
		$input->setValidator('orderby', $orderByOptionValidator);
		$ascDescValidator = new Claro_Validator_AllowedList(array('ASC', 'DESC'));
		$input->setValidator('ascDesc', $ascDescValidator);
		
		$this->orderby = $input->get('orderby', self::DEFAULT_ORDER_BY);
		$this->ascDesc = $input->get('ascDesc', self::DEFAULT_ASCDESC);
        
        
        $this->author_filter = $input->get('author_filter', self::DEFAULT_AUTHOR_FILTER);
        $this->course_filter = $input->get('course_filter', self::DEFAULT_COURSE_FILTER);
        
		$idValidator = new Claro_Validator_ValueType('intstr');
		$input->setValidator('questionId', $idValidator);
		$input->setValidator('surveyId', $idValidator);
		
		$this->questionId = $input->get('questionId', 0);
		$this->surveyId = $input->get('surveyId', 0);
		
		
	}
	
	public function render()
	{
		if($this->showDeleteConfirm)
		{
			return $this->showDeleteConfirm();
		}
		return $this->showQuestionPool();
	}
	
	private function showDeleteConfirm()
	{
		$question = $this->getQuestion();
		
		
		if($question->getUsed() > 0 )
		{
			parent::error(get_lang("This question is used in some surveys . You can't delete it"));
			return $this->showQuestionPool();
		}
		
		
		$delConfTpl = new PhpTemplate(dirname(__FILE__).'/templates/delete_question_conf.tpl.php');
   		$delConfTpl->assign('question', $question);        
   		$form = $delConfTpl->render();
   	
   		$dialogBox = new DialogBox();
   		
		$dialogBox->question( get_lang('Are you sure you want to delete this question?'));
	   	$dialogBox->form($form);
	   	  	
	   	return $dialogBox->render();
	}
	
	private function showQuestionPool()
	{
		try
		{
		    $questionList = Question::loadQuestionPool($this->orderby, $this->ascDesc, $this->author_filter, $this->course_filter);		    
		}catch(Exception $e)
		{
			parent::error($e->getMessage());
			return '';
		}
		
		$questionListTpl = new PhpTemplate(dirname(__FILE__).'/templates/question_pool.tpl.php');
	    $questionListTpl->assign('questionList', $questionList);
	    $questionListTpl->assign('orderby', $this->orderby);
	    $questionListTpl->assign('ascDesc', $this->ascDesc);
		if(!empty($this->surveyId)){
			$questionListTpl->assign('surveyId', $this->surveyId);
		}
	    return $questionListTpl->render();	
	    
	}
	
	public function defineBreadCrumb()
	{
		parent::defineBreadCrumb();		
		$questionPoolUrl = 'question_pool.php?';
		if(!empty($this->surveyId))
		{
			$questionPoolUrl .= "surveyId={$this->surveyId}";
		}
		$questionPoolUrl .= "&ascDesc={$this->ascDesc}&orderby={$this->orderby}";
		parent::appendBreadCrumbElement(get_lang('Question pool'), $questionPoolUrl);
		if($this->showDeleteConfirm)
		{
			parent::appendBreadCrumbElement(get_lang('Delete question'));
		}
	}
	
	private function getQuestion()
	{
		if($this->question != null){
			return $this->question;
		}
		try{
			$this->question = Question::load($this->questionId);
			return $this->question;
		} catch(Exception $e){
			parent::errorAndDie($e->getMessage());
		}
	}
	
	public function performQuestionDelete()
	{
		if(! parent::isConfirmed())
		{
			$this->showDeleteConfirm = true;
			return;
		}
		try{
			$question = $this->getQuestion();
			$question->delete();
			parent::success( get_lang('Question has been deleted')."!");
		}catch (Exception $e){
			parent::error($e->getMessage());
		}
				
	}
}

$page = new QuestionPoolPage();
$page->execute();
die();
    
//=================================
// Security check
//=================================

    if ( 	!claro_is_in_a_course() 
    		|| !claro_is_course_allowed() 
    		|| !claro_is_user_authenticated() )
    {
    	claro_disp_auth_form(true);
    }
    if(!claro_is_allowed_to_edit())
	{
		//not allowed for normal user
	    claro_redirect('survey_list.php');
	    exit();
	}
    
//=================================
// Init section
//=================================
    From::module('LVSURVEY')->uses('question.class');
    
     // Tool label (must be in database)
    $tlabelReq = 'LVSURVEY';
    add_module_lang_array($tlabelReq);
    claro_set_display_mode_available(true);
    
    
//=================================
// Choose action
//=================================

    if(!isset($_REQUEST['cmd']) || !isset($_REQUEST['questionId']))
    {
    	displayQuestionPool();
    	exit();
    }
    
    $questionId = (int)$_REQUEST['questionId'];
    $question = Question::load($questionId);
    switch($_REQUEST['cmd'])
    {
    	case 'questionDel' :
    		deleteQuestion($question);
    		break;
    	default :
    		displayQuestionPool();
    		
    }
    
//=================================
// Action functions
//=================================

function deleteQuestion($question)
{
	if(!isset($_REQUEST['conf']) || ((int)$_REQUEST['conf']!=1))
    {
    	displayDeleteConfirmation($question);
        exit();                
    }
            
    //delete the survey            
    $dialogBox = new DialogBox();
    try{
    	$question->delete();
        $dialogBox->success( get_lang('Question has been deleted')."!");
    }
    catch (Exception $e)
    {
    	$dialogBox->error($e->getMessage());
    }
    displayQuestionPool($dialogBox);
}

//=================================
// Display Section
//=================================

function displayDeleteConfirmation($question)
{		
	$delConfTpl = new PhpTemplate(dirname(__FILE__).'/templates/delete_question_conf.tpl.php');
   	$delConfTpl->assign('question', $question);        
   	$form = $delConfTpl->render();
   	
   	$dialogBox = new DialogBox();
   	if($question->getUsed() > 0 )
	{		
		$dialogBox->error(get_lang('This question is used in some surveys. You can\'t delete it'));
		displayQuestionPool($dialogBox);
		exit();
	}
	$dialogBox->question( get_lang('Are you sure you want to delete this question?'));
   	$dialogBox->form($form);
   	$pageTitle = get_lang('Delete question');    	
   	displayContents($dialogBox->render(), $pageTitle);
}  
    
    
function displayQuestionPool($dialogBox = NULL)
{   
	$orderby = 'text';
	if(isset($_REQUEST['orderby']))
    	$orderby = $_REQUEST['orderby'];
    $ascDesc = 'ASC';
    if(isset($_REQUEST['ascDesc']))
        $ascDesc = $_REQUEST['ascDesc'];
      
        
	
    $contentsToShow = '';
	try
	{
	    $questionList = Question::loadQuestionPool($orderby, $ascDesc);
	    $questionListTpl = new PhpTemplate(dirname(__FILE__).'/templates/question_pool.tpl.php');
	    $questionListTpl->assign('questionList', $questionList);
	    $questionListTpl->assign('orderby', $orderby);
	    $questionListTpl->assign('ascDesc', $ascDesc);
	    if(isset($_REQUEST['surveyId']))
	    {
	    	$questionListTpl->assign('surveyId', (int)$_REQUEST['surveyId']);
	    }  	    
	    if(!is_null($dialogBox))
	    {
	    	$contentsToShow .= $dialogBox->render();
	    }
	    $contentsToShow .= $questionListTpl->render();
	}catch(Exception $e)
	{
		$dialogBox = new DialogBox();
		$dialogBox->error($e->getMessage());
		$contentsToShow = $dialogBox->render();
	}

    
	displayContents($contentsToShow, get_lang('Question pool'));
	
}

function displayContents($contents, $pageTitle)
{
	$claroline = Claroline::getInstance();
	
    $claroline->display->banner->breadcrumbs->append(get_lang('Surveys'), 'survey_list.php'); 
    $claroline->display->banner->breadcrumbs->append($pageTitle); 
	
    $claroline->display->body->appendContent($contents);
   
    // render output
    echo $claroline->display->render();
	
}  
        

?>