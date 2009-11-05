<?php
From::module('LVSURVEY')->uses('SurveyConstants.class', 'Question.class');
FromKernel::uses(/*'claroCourse.class', 'utils/input.lib', 'utils/validator.lib'*/);


class Choice
{
	public $id;
	
	public $text;
	
	protected $questionId;
	protected $question;
	
	public function __construct($questionId)
    {
        $this->id = -1;
        $this->text = '';      
        $this->questionId = $questionId;
        $this->question = NULL;
    }
	
	static function __set_state($array)
    {
    	if(empty($array)) return false;
    	static $properties = array();
    	if(empty($properties))
    	{
    		$properties = array_keys(get_object_vars(new Choice(-1)));
    	}
    	
    	$res = new Choice($array['questionId']);
        foreach ($array as $akey => $aval) {
            if(in_array($akey,$properties))
            {
            	$res -> {$akey} = $aval;
            }
        }
        return $res;
    }	
    
    //load survey from the db
    static function load($id)
    {
    	$dbCnx = Claroline::getDatabase();
        /*
        * get row of table
        */
        $sql = "
        	SELECT
            	       `id` 							AS id,
            	       `questionId`						AS questionId,
            	       `text`							AS text
           	FROM 		`".SurveyConstants::$QUESTION_TBL."` 
           	WHERE 		`id` = ".(int) $id."; "; 
         
         
        $resultSet = $dbCnx->query($sql);
        $data = $resultSet->fetch();
        return self::__set_state($data);
    }
    
    static function loadSelectedChoicesFromForm($question)
    {
    	
    	if('OPEN' == $question->type)
    	{
    		$choiceText = $_REQUEST['choiceText'.$question->id];
    		$choice = new Choice($question->id);
    		$choice->text = $choiceText;
    		return array($choice);
    	}
    	if('MCSA' == $question->type)
    	{
    		$choiceId = $_REQUEST['choiceId'.$question->id];
    		$choice = new Choice($question->id);
    		$choice->id = $choiceId;
    		return array($choice);
    	}
    	$choices = $_REQUEST['choiceId'.$question->id];
    	$res = array();
    	foreach($choices as $choiceId)
    	{
    		$choice = new Choice($question->id);
    		$choice->id = $choiceId;
    		$res[] = $choice;
    	}
    	return $res;
    	
    }
    
    public function getQuestion(){
    	if(empty($this->question))
    	{
    		$this->loadQuestion();
    	}
    	return $this->question;
    }
    private function loadQuestion()
    {
        $this->question = Question::load($this->questionId);
    }
	public function setQuestion($question)
    {
    	$this->question = $question;
    	$this->questionId = $question->id;
    }
	private function validate()
    {
    	$validationErrors = array();
    	if(empty($this->text))
    		$this->validationErrors[] = 'Choice body is required';
    	if(-1 == $this->questionId)
    		$this->validationErrors[] = 'Question not defined';    	
    	
    	return $validationErrors;    	
    }
    public function save()
    {
    	$validationErrors = $this->validate();
    	if(!empty($validationErrors)){
    		throw new Exception(implode('<br/>', $validationErrors));
    	}
    	
    	if($this->id == -1){
    		$this->insertInDB();
    		return;
    	}
    	
    	$this->updateInDB();
    	
    }
    
    
    private function insertInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
    	
        //Insert new survey in DB
        $sql = "
        		INSERT INTO `".SurveyConstants::$CHOICE_TBL."`
                SET 		`text` 				= ".$dbCnx->quote($this->text).",
                			`questionId`		= ".(int)($this->questionId)."; ";
                    
			
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sql);
        $insertedId = $dbCnx->insertId($sql);
                   		
           
        $this->id = (int) $insertedId;        
        
    }
    private function updateInDB()
    {
    	$dbCnx = ClaroLine::getDatabase();
        //update current survey in DB (we cannot change id, courseId or anonimity)
        $sql = "
        	UPDATE 		`".SurveyConstants::$CHOICE_TBL."`
            SET 		`text` 				= ".$dbCnx->quote($this->text).",
                		`id` 				= ".(int)$this->id.",
                    	`questionId`		= ".(int)($this->questionId)." 
            WHERE 		`id` = ".(int)$this->id ;
            
            $dbCnx->exec($sql);
    }
	
	
	
}