 <?php 
  /**
     * This is a tool to create surveys. It's the new version better than older CLSURVEY
     * @copyright (c) Haute Ecole L�onard de Vinci
     * @version     0.1 $Revision$
     * @author      BAUDET Gregory <gregory.baudet@gmail.com>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     LVSURVEY
     */

require_once "choices.class.php";

class Question {
    
    //unique id of the course
    protected $courseId;
    
    //surveyId
    protected $surveyId;
    
    //unique id of the question
    protected $id;
    
    //title of the question
    protected $title;
    
    //type of question
    protected $type;
    
    //choices
    protected $choices;
    
    //choices alignment
    protected $choiceAlignment;
    
    //answer of the question (may be an array for MCMA)
    protected $answer;
    
    //results of the questions
    protected $results;
    
    //table which contains surveys
    protected $tblQuestion;
    
    //table containing choices
    protected $tblChoice;
    
    //table for multiple choice answers 
    protected $tblAnswerChoice;
    
    //table for text answers 
    protected $tblAnswerText;
    
    //table which contains relation between questions and surveys
    protected $tblRelSurvQuest;
    
    //if user is allowed to edit survey
    protected $editMode;
    
    //if we must duplicate question on save
    protected $duplicate;
    
    public function __construct($courseId, $editMode)
    {
        $this->id = -1;
        $this->surveyId = -1;
        $this->title = '';
        $this->type = 'TEXT';
        $this->courseId = mysql_real_escape_string($courseId);
        $tbl = claro_sql_get_tbl(
             array('survey2_question', 
                   'survey2_choice', 
                   'survey2_rel_survey_question', 
                   'survey2_answer_choice', 
                   'survey2_answer_text'
                 )
             );
        $this->tblQuestion = $tbl['survey2_question']; //TODO : not hard coded
        $this->tblChoice = $tbl['survey2_choice']; //TODO : nothardcoded
        $this->tblRelSurvQuest = $tbl['survey2_rel_survey_question'];
        $this->tblAnswerChoice = $tbl['survey2_answer_choice'];
        $this->tblAnswerText = $tbl['survey2_answer_text'];
        $this->choices = new Choices($courseId);
        $this->choiceAlignment = 'VERTI';
        $this->answer = '';
        $this->editMode = $editMode;
        $this->duplicate = false;
    }
    
    //load data from DB
    public function load($id)
    {
        /*
         * get row of table
         */
         $sql = "SELECT
                    `id`,
                    `title`,
                    `type`,
                    `alignment`
            FROM `".$this->tblQuestion."`
            WHERE `id` = ".(int) $id." AND `courseId`='".$this->courseId."'";
        
        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            
            $this->title = $data['title'];
            $this->type = $data['type'];
            
            if($this->type != 'TEXT')
            {
                $this->choiceAlignment = $data['alignment'];
                $this->choices->setQuestionId($this->id);
                $this->choices->load();
            }
            else
                $this->choiceAlignment = null;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    //load data from parameter
    public function loadFromVar($data)
    {
        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->type = $data['type'];
            if($this->type != 'TEXT')
            {
                $this->choiceAlignment = $data['alignment'];
                $this->choices->setQuestionId($this->id);
                $this->choices->load();
            }
            else
                $this->choiceAlignment = null;
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //load answers from fill-in form
    public function loadFromFillForm()
    {
        if($this->type == 'TEXT')
        {
            if(isset($_REQUEST['question'.$this->id]))
                $this->answer = $_REQUEST['question'.$this->id];
        }
        else if($this->type == 'MCSA')
        {
            if(isset($_REQUEST['question'.$this->id]))
            {
                $this->answer = array();
                $this->answer []= (int) $_REQUEST['question'.$this->id];
                $this->choices->setSelection($this->answer);
            }
        }
        else if($this->type == 'MCMA')
        {
            if(isset($_REQUEST['question'.$this->id]))
            {
                $reps = $_REQUEST['question'.$this->id];
                if(is_array($reps))
                    $this->answer = $reps;
                else
                    $this->answer = array();
                $this->choices->setSelection($this->answer);
            }
        }
    }
    
    //load data from edit form
    public function loadFromEditForm()
    {
    	$this->validationErrors = '';
		if((int)$_REQUEST['questionId'] != -1)
		{
			if($this->load((int)$_REQUEST['questionId'])==false){
				return false;
			}
		}
		else
		{
			$this->id = (int) $_REQUEST['questionId'];
			$this->setType($_REQUEST['questionType']);
		}
        $this->setTitle($_REQUEST['questionTitle']);
        //visibility is not set with edit survey form
        if($this->getType()!='TEXT')
        {
            $nbchoice = (int)$_REQUEST['questionNbCh'];
            $list = array();
            for($i=1; $i<=$nbchoice; $i++)
            {
                if($this->id == -1)
                {
                    if((isset($_REQUEST['questionCh'.$i]))&&($_REQUEST['questionCh'.$i]!=""))
                        $list[]=$_REQUEST['questionCh'.$i];
                }
                else
                {
                    if((!isset($_REQUEST['questionCh'.$i])) || ($_REQUEST['questionCh'.$i]==""))
                        $this->validationErrors .= get_lang('%name is required', array('%name'=>get_lang('Choice').' '.$i)).'<br />';
                    $list[]=$_REQUEST['questionCh'.$i];
                }
            }
            $this->choices->setChoices($list);
            if(isset($_REQUEST['questionAlign']))
                $this->setChoiceAlignment($_REQUEST['questionAlign']);
        }
        
        if(isset($_REQUEST['questionDuplicate']))
        {
            if((int)$_REQUEST['questionDuplicate'] == 1)
            {
               
                if((int)$this->surveyId > 0)
                {
                    $this->duplicate = true;
                }
            }
        }
        
        return $this->isValid();
            
    }
    
    //load answers from db
    public function loadAnswer($userId)
    {
        if($this->type == 'TEXT')
        {
            $sql = 'SELECT `questionId`, `answer`  FROM `'.$this->tblAnswerText.'`
					WHERE `userId` = '.(int)$userId.' AND `surveyId` = '.(int)$this->surveyId.' LIMIT 1';
    	    $textAnswer = claro_sql_query_get_single_row($sql);
    	    $this->answer = $textAnswer['answer'];
        }
        else
        {
    	    if($this->type == 'MCSA')
    	    {
    	        $sql = "SELECT `questionId`, `answer`  FROM `".$this->tblAnswerChoice."`
					WHERE `userId` = '".(int)$userId."' 
                    AND `surveyId` = '".(int)$this->surveyId."' 
                    AND `questionId` = '".(int)$this->id."'
                    LIMIT 1";
    	        $choiceAnswer = claro_sql_query_get_single_row($sql);
    	        $this->answer = array();
    	        $this->answer []= $choiceAnswer['answer'];
    	        $this->choices->setSelection($this->answer);
    	    }
    	    else
    	    {
    	        $sql = "SELECT `questionId`, `answer`  FROM `".$this->tblAnswerChoice."`
					WHERE `userId` = '".(int)$userId."' 
                    AND `surveyId` = '".(int)$this->surveyId."'
                    AND `questionId` = '".(int)$this->id."'" ;
    	        $choiceAnswer = claro_sql_query_fetch_all($sql);
    	        $this->answer = array();
    	        foreach ($choiceAnswer as $answer)
    	        {
    	            $this->answer []= $answer['answer'];
    	        }
    	        $this->choices->setSelection($this->answer);
    	    }
        }
    }
    
    //remove answers to this question for one survey
    public function removeAnswers($surveyId)
    {
        if($this->type=='TEXT')
        {        
            $sql = "DELETE FROM `".$this->tblAnswerText."`
        		WHERE `surveyId` = '".(int) $surveyId."'
        		AND `questionId` = '".(int) $this->id."'";
        }
        else
        {
            $sql = "DELETE FROM `".$this->tblAnswerChoice."`
        		WHERE `surveyId` = '".(int) $surveyId."'
        		AND `questionId` = '".(int) $this->id."'";
        }
        claro_sql_query($sql);
    }
    
    //remove answers to this question for all surveys
    private function removeAllAnswers()
    {
        if($this->type=='TEXT')
        {        
            $sql = "DELETE FROM `".$this->tblAnswerText."`
        		WHERE `questionId` = '".(int) $this->id."'";
        }
        else
        {
            $sql = "DELETE FROM `".$this->tblAnswerChoice."`
        		WHERE `questionId` = '".(int) $this->id."'";
        }
        claro_sql_query($sql);
    }
    
    //save answers
	public function saveAnswers($userId)
	{
		if($this->type == 'TEXT')
		{
		    if(!is_null($userId))
		    {
    			$sql = "DELETE FROM `".$this->tblAnswerText."`
    					WHERE `userId` = ".(int)$userId." 
    					AND `surveyId` = ".(int)$this->surveyId." 
    					AND `questionId` = ".(int)$this->id;
    			claro_sql_query($sql);
    			$sql = "INSERT INTO `".$this->tblAnswerText."` (
					`userId`,
					`questionId`,
					`surveyId`,
					`answer` )
					VALUES ( '".(int)$userId."',
					'".(int)$this->id."', 
					'".(int)$this->surveyId."', 
					'".addslashes($this->answer)."');";
		    }
		    else
		    {
		        $sql = "INSERT INTO `".$this->tblAnswerText."` (
					`userId`,
					`questionId`,
					`surveyId`,
					`answer` )
					VALUES ( NULL,
					'".(int)$this->id."', 
					'".(int)$this->surveyId."', 
					'".addslashes($this->answer)."');";
		    }

			claro_sql_query($sql);
		}
		else
		{
			$sql = "DELETE FROM `".$this->tblAnswerChoice."`
					WHERE `userId` = ".(int)$userId." 
					AND `surveyId` = ".(int)$this->surveyId." 
					AND `questionId` = ".(int)$this->id;
			claro_sql_query($sql);
			
			$list = $this->choices->getChoices();
            foreach( $list as $aChoice )
            {
				if($this->choices->isSelected($aChoice['id']))
				{
					$sql = "INSERT INTO `".$this->tblAnswerChoice."` (
							`userId`,
							`questionId`,
							`surveyId`,
							`answer` )
							VALUES ( '".(int)$userId."',
							'".(int)$this->id."', 
							'".(int)$this->surveyId."', 
							'".(int)$aChoice['id']."');";
					claro_sql_query($sql);
				}
            }
		}
	}
	
    //save a new question or edited question
    public function save()
    {
    	if(!$this->isValid())
    		return false;
    		
        if($this->duplicate == true)
        {
            $oldid = $this->id;
            $this->choices->setDuplicate(true);
            //remove answers and unlink old question
            $this->removeFromSurvey($this->surveyId);
            $this->id = -1;
        }
        if($this->id == -1)
        {
            //Insert new question in DB
            $sql = "INSERT INTO `".$this->tblQuestion."`
                    SET `courseId` = '".$this->courseId."',
                    	`title` = '".addslashes($this->title)."',
                        `type` = '".addslashes($this->type)."'"
                        .(is_null($this->choiceAlignment)?"":",`alignment` = '".addslashes($this->choiceAlignment)."'");
			
            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->id = (int) $insertedId;

                if($this->type != "TEXT")
                {
                    $this->choices->setQuestionId($this->id);
                    $this->choices->save();
                }
                
                if($this->duplicate == true)
                {
                    //if we duplicate, we relink with the survey
                    $this->addToSurvey($this->surveyId);
                    
                }
                
                
                return $this->id;
            }
            else
            {

                return false;
            }
        }
        else
        {
            //update current survey in DB
            $sql = "UPDATE `".$this->tblQuestion."`
                	SET `title` = '".addslashes($this->title)."'"
                    .(is_null($this->choiceAlignment)?"":",`alignment` = '".addslashes($this->choiceAlignment)."' ")
                	."WHERE `id` = ".$this->id;

            if( claro_sql_query($sql) )
            {
                if($this->type != "TEXT")
                {
                    $this->choices->setQuestionId($this->id);
                    $this->choices->save();
                }
                return $this->id;
            }
            else
            {
                return false;
            }
        }
        
    }
    
    //delete a question
    public function delete()
    {
        //remove answers to the question
        //remove question from all survey
        $this->removeFromAllSurvey();
        
        //remove the question
        if($this->type != "TEXT")
        {
            $this->choices->removeChoices();
        }
        
        $sql = "DELETE FROM `".$this->tblQuestion."`
        	WHERE `id` = ".(int)$this->id;
        claro_sql_query($sql);
    }
    
    /**
          * add the question to the survey
          * Should be used after the new question was saved
          * or on a existing question (loaded)
          */
    public function addToSurvey($surveyId)
    {
        if($this->id==-1)
            return false;
        
        //add a relation survey-question
        $sql = "INSERT INTO `".$this->tblRelSurvQuest."`
                    SET `surveyId` = ".(int) $surveyId.",
                    	`questionId` = ".(int) $this->id.",
                        `rank` = 0
                        ";
        // execute the creation query and get id of inserted assignment
        $insertedId = claro_sql_query_insert_id($sql);
           
        //don't forget rank
        $sql = "UPDATE `".$this->tblRelSurvQuest."`
            	SET `rank` = ".(int) $insertedId."
        		WHERE `id` = ".(int) $insertedId;
        claro_sql_query($sql);
        
        return true;
    }
    
    //remove the question from one survey
    public function removeFromSurvey($surveyId)
    {
        if((int)$this->id > 0)
        {
            //remove answers about this question and this survey
            $this->removeAnswers($surveyId);
            
            //remove the question from the survey
            $sql = "DELETE FROM `".$this->tblRelSurvQuest."`
            		WHERE `surveyId` = ".(int) $surveyId."
            		AND `questionId` = ".(int) $this->id;
            claro_sql_query($sql);
        }
        else
            return false;
    }
    
    //remove the question from all surveys
    private function removeFromAllSurvey()
    {
        $this->removeAllAnswers();
        
        $sql = "DELETE FROM `".$this->tblRelSurvQuest."`
        		WHERE `questionId` = ".(int) $this->id;
        claro_sql_query($sql);
        //TODO : remove answers about this question and this survey
    }
    
    //check if data are valid for database
    private function isValid()
    {
    	
    	if(empty($this->title))
    		$this->validationErrors .= get_lang('%name is required', array('%name'=>get_lang('Title'))).'<br />';
    	
        if($this->getType()!='TEXT')
            if(count($this->choices->getChoices())<2)
                $this->validationErrors .= get_lang('Minimum 2 choices are required').'<br />';

    	if(empty($this->validationErrors))
    		return true;
    	else
    		return false;
    	
    }
    
    //get list of errors while reading the form
    public function getValidationErrors()
    {
    	return $this->validationErrors;
    }
       
    //check if the question has been answered
	public function isAnswered()
	{
	    if($this->type == 'TEXT')
	    {
	        $sql = "SELECT COUNT(`questionId`) 
    	    		FROM `".$this->tblAnswerText."` 
    	    		WHERE `questionId`='".$this->id."'";
	    }
	    else{
    	    $sql = "SELECT COUNT(`questionId`) 
    	    		FROM `".$this->tblAnswerChoice."` 
    	    		WHERE `questionId`='".$this->id."'";
	    }
	    
	    if($this->surveyId > 0)
	        $sql .= " AND `surveyId`='".(int)$this->surveyId."'";
	    
    	$val = (int)claro_sql_query_fetch_single_value($sql);
	    
	    if((int)$val>0)
	        return true;
	    else
	        return false;
	}
	
    //check if the question is used in a survey
	public function isUsedInSurvey()
	{
	    $sql = "SELECT COUNT(`questionId`) 
    	    		FROM `".$this->tblRelSurvQuest."` 
    	    		WHERE `questionId`='".$this->id."'";
    	$val = (int)claro_sql_query_fetch_single_value($sql);
	    
	    if((int)$val>0)
	        return $val;
	    else
	        return false;
	}
	
    //load results of the question
	public function loadResults()
	{
	    if($this->type == 'TEXT')
	    {
	        $sql = "SELECT `userId`, `answer` 
	        	FROM `".$this->tblAnswerText."` 
	        	WHERE `surveyId`='".(int)$this->surveyId."' 
	        	AND	`questionId`='".(int)$this->id."'";
	        
	    }
	    else
	    {
	        $sql = "SELECT an.`userId`, COUNT(an.`answer`) as qty, an.`answer`, ch.`text` 
	        	FROM `".$this->tblAnswerChoice."` as an 
	        	INNER JOIN `".$this->tblChoice."` as ch
	        	ON an.`answer` = ch.`id`
	        	WHERE an.`surveyId`='".(int)$this->surveyId."' 
	        	AND	an.`questionId`='".(int)$this->id."' 
	        	GROUP BY an.`answer` 
	        	ORDER BY qty DESC";
	        
	    }
	    
	    $this->results = claro_sql_query_fetch_all($sql);
	}
	
    /*
           * RENDERING FUNCTIONS
           */
    //render the question for filling
    public function renderFillForm($arrowup = false, $arrowdown= false)
    {
        $out = '<div class="LVSURVEYQuestion">';
        //show title
        $out .= '<div class="LVSURVEYQuestionTitle">';
        if($this->editMode == true)
		{
            if($arrowup == true)
                $out .= '<a href="show_survey.php?surveyId='.$this->surveyId.'&amp;questionId='.$this->id.'&amp;cmd=questionMoveUp">'
                    .     '<img src="' .get_icon_url('move_up').'" border="0" alt="'.get_lang('Move up').'" />'
                    .     '</a>';
            if($arrowdown == true)
                $out .= '<a href="show_survey.php?surveyId='.$this->surveyId.'&amp;questionId='.$this->id.'&amp;cmd=questionMoveDown">'
                    .     '<img src="' .get_icon_url('move_down').'" border="0" alt="'.get_lang('Move down').'" />'
                    .     '</a>';
            $out .= '<a href="edit_question.php?surveyId='.$this->surveyId.'&amp;questionId='.$this->id.'">'
                 .'<img src="' .get_icon_url('edit').'" border="0" alt="'.get_lang('Modify').'" />'
                 .'</a>';
			if((int)$this->surveyId != -1)
            {
    			$out .='<a href="show_survey.php?surveyId='.$this->surveyId.'&amp;questionId='.$this->id.'&amp;cmd=questionRemove" >'
                     .'<img src="' .get_icon_url('delete').'" border="0" alt="'.get_lang('Delete').'" />'
                     .'</a>';
            }
        }
        $out .= htmlspecialchars($this->title).'</div>';
        //show question
        $out .= '<div class="LVSURVEYQuestionContent">'."\n";
        if($this->type == 'TEXT'){
            $out .= '<textarea name="question'.$this->id.'" id="question'.$this->id.'" rows="3" cols="40">'
            		.htmlspecialchars($this->answer)
                    .'</textarea>'."\n";
        }
        else
        {
            $typechoice = 'checkbox';
            if($this->type=='MCSA')
                $typechoice = 'radio';
                
            $list = $this->choices->getChoices();
            if($this->choiceAlignment == 'HORIZ')
            {
                $out .= '<div id="horizChoices">';
                foreach( $list as $aChoice )
                {
                    $out.= '<input name="question'.$this->id.($this->type == 'MCMA'?'[]':'').'" '
                        //.'id="question'.$this->id.'" '
                        .'type="'.$typechoice.'" '
                        .'value="'.$aChoice['id'].'" '
                        .($this->choices->isSelected($aChoice['id'])?'checked="checked"':'')
                        .' />' 
                    	. htmlspecialchars($aChoice['text'])
                        . "\n";
                    
                }
                $out .= '</div>';
            }
            else
            {
                $out .= '<ul>'."\n";
                foreach( $list as $aChoice )
                {
                    $out.= '<li><input name="question'.$this->id.($this->type == 'MCMA'?'[]':'').'" '
                        //.'id="question'.$this->id.'" '
                        .'type="'.$typechoice.'" '
                        .'value="'.$aChoice['id'].'" '
                        .($this->choices->isSelected($aChoice['id'])?'checked="checked"':'')
                        .' />' 
                    	. htmlspecialchars($aChoice['text']).'</li>'
                        . "\n";
                    
                }
                $out .='</ul>'."\n";
            }
        }
        $out .= '</div>'."\n";
        $out.= '</div>'."\n";
        return $out;
    }
    
    //render confirmation form when filling survey
    public function renderConfForm()
    {
        $out = '<div class="LVSURVEYQuestion">';
        //show title
        $out .= '<div class="LVSURVEYQuestionTitle">'.htmlspecialchars($this->title).'</div>';
        //show question
        $out.='<div class="LVSURVEYQuestionContent">';
        if($this->type == 'TEXT'){
            $out .= '<div>'.htmlspecialchars($this->answer)
                .'</div>'."\n";
        }
        else
        {
            
            $list = $this->choices->getChoices();

            $out .= '<ul>'."\n";
            foreach( $list as $aChoice )
            {
                if($this->choices->isSelected($aChoice['id']))
                {
                $out.= '<li>'
                	. htmlspecialchars($aChoice['text']).'</li>'
                    . "\n";
                }
                
            }	
            $out .='</ul>'."\n";
        }
        $out.='</div>';
        $out.= '</div>'."\n";
        return $out;
    }
    
    //used in confirmation
	public function renderConfFormHidden()
    {
		$out = '';
        //show question
        if($this->type == 'TEXT'){
            $out .= '<input type="hidden" name="question'.$this->id.'" id="question'.$this->id.'" value="'.htmlspecialchars($this->answer).'">'."\n";
        }
        else
        {
            $list = $this->choices->getChoices();
            foreach( $list as $aChoice )
            {
                if($this->choices->isSelected($aChoice['id']))
                {
                $out.= '<input type="hidden" name="question'.$this->id.($this->type == 'MCMA'?'[]':'').'" '
                    .'id="question'.$this->id.'" '
                    .'value="'.$aChoice['id'].'" '
                    .' />'. "\n";
                }
            }	
        }
        return $out;
    }
	
    //render a form to edit or create a question
    public function renderEditForm()
    {

        $dialogBox = new DialogBox();
        $out = '';
        if($this->id==-1)
        {
            JavascriptLoader::getInstance()->load('jquery');
            JavascriptLoader::getInstance()->load('surveyQuestionForm');
        
        }
        
        	    
        if($this->isAnswered()){
            $dialogBox->warning(get_lang('Some users have already answered to this question.'));
        }
        
        if(($this->surveyId == -1) && ($this->isUsedInSurvey()>1)){
            $dialogBox->warning(get_lang('This question is used in several surveys. Changes will take effect on all these surveys.'));
        }
        
        $out .= $dialogBox->render();
        
    	$out .= '<form method="post" action="./edit_question.php" >' . "\n\n"
    	.	'<input type="hidden" name="questionId" value="'.$this->id.'" />' . "\n"
    	.	'<input type="hidden" name="surveyId" value="'.$this->surveyId.'" />' . "\n"
        .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'">' . "\n"
        .    '<table border="0" cellpadding="5" id="formTable">' . "\n";
    	        
       
        if(($this->surveyId > 0) && ($this->isUsedInSurvey()>1)){
            //new question or changge for all?
            $out .=    '<tr>' . "\n"
            .	 '<td valign="top">&nbsp;</td>' . "\n"
            .	 '<td>' . "\n"
            . get_lang('This question is used in several surveys.') . '<br />'
            .	 '<input  type="radio" name="questionDuplicate" id="questionDuplicate" size="60" maxlength="200" value="0" '
            .($this->duplicate?'':'checked')
            .' />' 
            . get_lang('Change for all surveys') . "\n"
            .	 '<input  type="radio" name="questionDuplicate" id="questionDuplicate" size="60" maxlength="200" value="1" '
            .($this->duplicate?'checked':'')
            .' />'
            . get_lang('Create a new question'). "\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n";
        }
        
        //--
        // title
        $out .=    '<tr>' . "\n"
        .	 '<td valign="top">' . "\n"
        .	 '<label for="surveyTitle">' . get_lang('Title').'&nbsp;' . "\n"
        .	 '<span class="required">*</span>&nbsp;:' . "\n"
        .	 '</label>' . "\n"
        .	 '</td>' . "\n"
        .	 '<td>' . "\n"
        .	 '<input  type="text" name="questionTitle" id="questionTitle" size="60" maxlength="200" value="' .htmlspecialchars($this->title) . '" />' . "\n"
        .	 '</td>' . "\n"
        .	 '</tr>' . "\n\n"
    
        // type of question
        .($this->id==-1? 
        		'<tr>' . "\n"
    		.    '<td>'.get_lang('Type of question').' : </td>' . "\n"
    	    .    '<td>' . "\n"
    		.    '<input type="radio" id="questionType" name="questionType" value="TEXT" '.($this->type=="TEXT"?"checked":"").' />'.get_lang('Text') . "\n"
    		.    '<input type="radio" id="questionType" name="questionType" value="MCSA" '.($this->type=="MCSA"?"checked":"").' />'.get_lang('Multiple choice, single answer') . "\n"
    		.    '<input type="radio" id="questionType" name="questionType" value="MCMA" '.($this->type=="MCMA"?"checked":"").' />'.get_lang('Multiple choice, multiple answers') . "\n"
    	    .    '</td>' . "\n"
    		.    '</tr>' . "\n"
		:
		     '<tr>' . "\n"
    		.    '<td>'.get_lang('Type of question').' : </td>' . "\n"
    	    .    '<td>' . "\n"
    		.    ($this->type=="TEXT"?get_lang('Text'):"") . "\n"
    		.    ($this->type=="MCSA"?get_lang('Multiple choice, single answer'):"") . "\n"
    		.    ($this->type=="MCMA"?get_lang('Multiple choice, multiple answers'):"")."\n"
    	    .    '</td>' . "\n"
    		.    '</tr>' . "\n"
		);
		
		if($this->id == -1)
		{
		    $out.='<tr>' . "\n"
        		.    '<td>&nbsp;' . "\n"
        		.    '</td>' . "\n"
        		.    '<td>' . "\n";
    		if(($this->type == "TEXT"))
    		{
        		$out.='<div id="divquestionCh">'.get_lang('Choices').' : <input name="questionNbCh" id="questionNbCh" type="hidden" value="10" /></div>' . "\n"
            		.    '<div id="divquestionCh1">1: <input name="questionCh1" id="questionCh1" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh2">2: <input name="questionCh2" id="questionCh2" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh3">3: <input name="questionCh3" id="questionCh3" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh4">4: <input name="questionCh4" id="questionCh4" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh5">5: <input name="questionCh5" id="questionCh5" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh6">6: <input name="questionCh6" id="questionCh6" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh7">7: <input name="questionCh7" id="questionCh7" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh8">8: <input name="questionCh8" id="questionCh8" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh9">9: <input name="questionCh9" id="questionCh9" type="text" value="" /></div>' . "\n"
            		.    '<div id="divquestionCh10">10: <input name="questionCh10" id="questionCh10" type="text" value="" /></div>' . "\n";
    		}
    		else
    		{
    		    $list = $this->choices->getChoices();
    		    $out.='<div id="divquestionCh">'.get_lang('Choices').' : <input name="questionNbCh" id="questionNbCh" type="hidden" value="'.(count($list)<2?2:count($list)).'" /></div>' . "\n";
            	$i = 0;
            	foreach( $list as $aChoice )
                {
                    $i++;
                    $out.= '<div id="divquestionCh'.$i.'">'.$i.': <input name="questionCh'.$i.'" id="questionCh'.$i.'" type="text" value="'.htmlspecialchars($aChoice['text']).'" /></div>' . "\n";
                }	
                
                for($j = $i + 1; $j<=10; $j++)
                {
                    $out.= '<div id="divquestionCh'.$j.'">'.$j.': <input name="questionCh'.$j.'" id="questionCh'.$j.'" type="text" value="" /></div>' . "\n";
                }
    		}
            
            $out.='<div id="divquestionAlign">'."\n"
                . '<input type="radio" name="questionAlign" id="questionAlign" value="VERTI" '
                .($this->choiceAlignment=="VERTI"?"checked":"").' />' . "\n"
                . get_lang('Vertical alignment')
                . '<input type="radio" name="questionAlign" id="questionAlign" value="HORIZ" '
                .($this->choiceAlignment=="HORIZ"?"checked":"").' />' . "\n"
                . get_lang('Horizontal alignment')
                .    '</div>' . "\n";
    		$out.='<div id="menuaddrem">' . "\n"
        		.    '<a href="#" id="addChoice">'.get_lang('Add a choice').'</a> - ' . "\n"
        		.    '<a href="#" id="removeChoice">'.get_lang('Remove a choice').'</a>' . "\n"
        		.    '</div>' . "\n"
        		.    '<div id="questionNoJavascript">Javascript isn\'t working!</div>' . "\n"
        		.    '</td>' . "\n"
        		.    '</tr>';
    		
		}
        else
		{
    		if(($this->type != "TEXT"))
    		{
                   $list = $this->choices->getChoices();
                   $out.='<tr>' . "\n"
            		.    '<td>&nbsp;' . "\n"
            		.    '</td>' . "\n"
            		.    '<td>' . "\n"
            		.    '<div id="ch">'.get_lang('Choices').' : <input name="questionNbCh" id="questionNbCh" type="hidden" value="'.(count($list)<2?2:count($list)).'" /></div>' . "\n";
                	$i = 0;
                	foreach( $list as $aChoice )
                    {
                        $i++;
                        $out.= '<div id="ch'.$i.'">'.$i.': <input name="questionCh'.$i.'" id="questionCh'.$i.'" type="text" value="'.htmlspecialchars($aChoice['text']).'" /></div>' . "\n";
                    }	
                    $out.='<div id="divquestionAlign">'."\n"
                        . '<input type="radio" name="questionAlign" id="questionAlign" value="VERTI" '
                        .($this->choiceAlignment=="VERTI"?"checked":"").' />' . "\n"
                        . get_lang('Vertical alignment')
                        . '<input type="radio" name="questionAlign" id="questionAlign" value="HORIZ" '
                        .($this->choiceAlignment=="HORIZ"?"checked":"").' />' . "\n"
                        . get_lang('Horizontal alignment')
                        .    '</div>' . "\n";
                    $out.='</td>' . "\n"
            		.    '</tr>';
    		}
		}
        // submit
        $out.=    '<tr>'
        .'<td>&nbsp;</td>'
        .	 '<td>'
        .	 '<input type="submit" value="'.get_lang('Finish').'" />' . "\n"
        .	 '</td>' . "\n"
        .	 '</tr>' . "\n\n"
        .    '</tbody>' . "\n\n"
        .	 '</table>' . "\n\n"
        .    '</form>';
        ;
        return $out;
    }
    
    //render results page for the survey
    public function renderResults()
    {
        $out = '';
        $out = '<div class="LVSURVEYQuestion">';
        $out .= '<div class="LVSURVEYQuestionTitle">'.htmlspecialchars($this->title).'</div>';
        //test if no results
        $out .= '<div class="LVSURVEYQuestionContent">';
        if(count($this->results)==0)
        {
            $out.='<div class="answer">'.get_lang('No result').'</div>';
        }
        else
        {
            //results layout depend on the type of question
            if($this->type == 'TEXT')
            {
                foreach($this->results as $aresult)
                {
                    $out.='<div class="answer">'.htmlspecialchars($aresult['answer']).'</div>';
                }
            }
            else if($this->type == 'MCSA')
            {
                $out.='<div class="answer">';
                $total = 0;
                foreach($this->results as $aresult)
                {
                    $total += (int)$aresult['qty'];
                }
                $out.='<table>';

                foreach($this->results as $aresult)
                {
                    $out.='<tr>'
                	    . '<td>'
                	    . $aresult['text']
                	    . '</td>'
                	    . '<td>'
                	    . (int)$aresult['qty']*100/$total . htmlspecialchars('%')
                	    . '</td>'
                	    . '</tr>';
                }
                
                $out .= '</table>';
                $out .= '</div>';
            }
            else if($this->type == 'MCMA')
            {
                $out.='<div class="answer">';
                $out.='<table>';
                foreach($this->results as $aresult)
                {
                    $out.='<tr>'
                	    . '<td>'
                	    . $aresult['text']
                	    . '</td>'
                	    . '<td>'
                	    . $aresult['qty']
                	    . '</td>'
                	    . '</tr>';
                }
                $out .= '</table>';
                $out .= '</div>';
            }

        }
        $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    /*
           * GETTER AND SETTER
           */
    public function getId()
    {
        return (int)$this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($value)
    {
        $this->title = trim($value);
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($value)
    {
        $acceptedValues = array('TEXT', 'MCSA', 'MCMA');

        if( in_array($value, $acceptedValues) && ($this->id == -1))
        {
            $this->type = $value;
            return true;
        }
        return false;
    }
    
    public function setChoiceAlignment($value)
    {
        $acceptedValues = array('HORIZ', 'VERTI');

        if( in_array($value, $acceptedValues))
        {
            $this->choiceAlignment = $value;
            return true;
        }
        return false;
    }
    
    public function setSurveyId($value)
    {
        $this->surveyId = (int)$value;
    }
    
}
    
?>