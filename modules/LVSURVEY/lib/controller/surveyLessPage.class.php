<?php
FromKernel::uses('utils/input.lib');

abstract class SurveyLessPage{
	
	
	private $flash;
	
	private static $DEFAULT_ERROR_MESSAGE;
	private static $DEFAULT_SUCCES_MESSAGE;
	
	
	public function __construct(){
		$this->flash = new DialogBox();
		self::$DEFAULT_ERROR_MESSAGE = get_lang('Error');
		self::$DEFAULT_SUCCES_MESSAGE = get_lang('Success');	
		
		$this->init();
	}
	
	public function execute(){
		$this->assertSecurityAccess();
		$this->performCommandIfNeeded();
		
		$this->defineBreadCrumb();
		$contents = $this->render();
		$this->display($contents);
	}

	
	protected abstract function render();

	
	protected function errorAndDie($message){
			$dialogBox = new DialogBox();
			$dialogBox->error( $message);
			$contents = $dialogBox->render();
			$this->appendBreadCrumbElement(get_lang('Surveys'), 'survey_list.php');
			$this->appendBreadCrumbElement(get_lang("Error"));
			$this->display($contents);
			die();
	}

	
	protected function defineBreadCrumb(){
		$this->appendBreadCrumbElement(get_lang('Surveys'), 'survey_list.php');	
	}
	protected function appendBreadCrumbElement($name,$url = null, $icon = null){
		$claroline = Claroline::getInstance();	
    	$claroline->display->banner->breadcrumbs->append($name,$url,$icon);
	}
	protected function performCommandIfNeeded(){
		if(!(isset($_REQUEST['cmd'])))
			return;
		$commandName = $this->getCommandName();
		$methodName = 'perform' .$commandName;		
		if(!method_exists($this,$methodName)){
			$this->error('Cannot perform command : ' . $commandName);
			return;
		}			
		
		try{
			$this->{$methodName}();
		}catch(Exception $e){
			$this->error($e->getMessage());
		}
			
		
	}
	private function getCommandName(){
		$command = $this->getUserAlNum('cmd');
		return ucwords($command);
	}
	
	private function display($contents){
		$claroline = Claroline::getInstance();
		$claroline->display->body->appendContent($this->flash->render());
		$claroline->display->body->appendContent($contents);
		echo $claroline->display->render();
	}
	
	protected function assertSecurityAccess(){
        
        if(claro_is_platform_admin())
        {
            return;
        }        
        if(!$this->checkAccess())
        {
            $this->errorAndDie('Access denied');
        }
    }
    
    protected function checkAccess()
    {
        
        if (    !claro_is_in_a_course() 
            ||  !claro_is_course_allowed() 
        )
        {
                return false;
        }
        return true;
    }
        
	private function init(){
		$tlabelReq = 'LVSURVEY';
		add_module_lang_array($tlabelReq);
		claro_set_display_mode_available(true);
	}
	
	protected function error($message = 'Error', $var_to_replace=null ){
		$this->flash->error(get_lang($message, $var_to_replace));
	}
	protected function success($message =  'Success', $var_to_replace=null ){
		$this->flash->success(get_lang($message, $var_to_replace));
	}
	protected function info($message, $var_to_replace=null){
		$this->flash->info(get_lang($message, $var_to_replace));
	}
	protected function getUserInt($paramName)
	{
		$input = Claro_UserInput::getInstance();
		$input->setValidator($paramName, new Claro_Validator_ValueType('intstr'));
		return $input->getMandatory($paramName);
	}
	protected function getUserAlNum($paramName)
	{
		$input = Claro_UserInput::getInstance();
		$input->setValidator($paramName, new Claro_Validator_ValueType('alnum'));
		return $input->getMandatory($paramName);
	}
	protected function isConfirmed(){
		try {
			$conf = $this->getUserInt('conf');
			return $conf == 1;
		}catch(Exception $e){
			return false;
		}
	}
}