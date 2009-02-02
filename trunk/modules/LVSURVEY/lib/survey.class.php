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

require_once 'question.class.php';
//Contains Survey class
class Survey {
    
    //unique id of the course
    protected $courseId;
    
    //unique id of the survey
    protected $id;
    
    //title of the survey
    protected $title;

    //description of the survey
    protected $description;
      
    //if the survey is anonymous
    protected $anonymous;
    
    //visibility of the survey to users
    protected $visibility;
      
    //visibility of results to users
    protected $resultsVisibility;
    
    //startDate of the survey
    protected $startDate;
    
    //endDate of the survey
    protected $endDate;

    //table which contains surveys
    protected $tblSurvey;
    
    //table which contains surveys
    protected $tblQuestion;
    
    //table which contains relation between questions and surveys
    protected $tblRelSurvQuest;
    
    //table for multiple choice answers 
    protected $tblAnswerChoice;
    
    //table for text answers 
    protected $tblAnswerText;
	
    //table which contains relation between users and surveys
	protected $tblRelSurvUser;
    
    //errors when validating datas
    protected $validationErrors;
    
    //questions of the survey
    protected $questions;
	
	//if user is allowed to edit survey
    protected $editMode;
    
    //if user already filled the survey
    protected $alreadyFilled;

    public function __construct($courseId, $editMode)
    {
        $this->id = -1;
        $this->title = '';
        $this->description = '';
        $this->anonymous = 'YES';
        $this->startDate = null;
        $this->endDate = null; //null means endDate is not used
        $this->visibility = 'INVISIBLE';
        $this->resultsVisibility = 'INVISIBLE';
        $this->courseId = mysql_real_escape_string($courseId);
        
        $tbl = claro_sql_get_tbl(
             array('survey2_survey', 
                   'survey2_question', 
                   'survey2_rel_survey_question', 
                   'survey2_answer_choice', 
                   'survey2_answer_text', 
                   'survey2_rel_survey_user'
                 )
             );
        $this->tblSurvey = $tbl['survey2_survey']; //TODO : not hard coded
        $this->tblQuestion = $tbl['survey2_question']; //TODO : not hard coded
        $this->tblRelSurvQuest = $tbl['survey2_rel_survey_question'];
        $this->tblAnswerChoice = $tbl['survey2_answer_choice'];
        $this->tblAnswerText = $tbl['survey2_answer_text'];
		$this->tblRelSurvUser = $tbl['survey2_rel_survey_user'];
        $this->questions = array();
		$this->editMode = $editMode;
		$this->alreadyFilled = false;
    }
    
    //load survey from the db
    public function load($id)
    {
        /*
         * get row of table
         */
         $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `anonymous`,
                    `visibility`,
                    `resultsVisibility`,
                    UNIX_TIMESTAMP(`startDate`) AS `unix_start_date`,
                    UNIX_TIMESTAMP(`endDate`) AS `unix_end_date`
            FROM `".$this->tblSurvey."`
            WHERE `id` = ".(int) $id." AND `courseId`=".(int)$this->courseId;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->anonymous = $data['anonymous'];
            $this->visibility = $data['visibility'];
            $this->resultsVisibility = $data['resultsVisibility'];
            $this->startDate = $data['unix_start_date'];
			$this->endDate = $data['unix_end_date'];
            // unix_end_date is null if the query returns 0 (UNIX_TIMESTAP('0000-00-00 00:00:00') == 0)
            // for this value
            //if( $data['unix_end_date'] == '0' ) $this->endDate = null;
            //else                                $this->endDate = $data['unix_end_date'];
            

            return true;
        }
        else
        {
        	//$loadError = true;
            return false;
        }
    }
    
    //load properties of the survey from editing form
    public function loadFromEditForm()
    {
    	$this->validationErrors = '';
		if((int)$_REQUEST['surveyId'] != -1)
		{
			if($this->load((int)$_REQUEST['surveyId'])==false){
				return false;
			}
		}
		else
		{
			$this->id = (int) $_REQUEST['surveyId'];
			$this->setAnonymous($_REQUEST['surveyAnonymous']);
		}
        $this->setTitle($_REQUEST['surveyTitle']);
        $this->setDescription($_REQUEST['surveyDescription']);
        //visibility is not set with edit survey form
        $this->setResultsVisibility($_REQUEST['surveyResultsVisibility']);
        
        if(!empty($_REQUEST['surveyStartDate']))
        {
        	if(!ereg ("^([0-9]{2})/([0-9]{2})/([0-9]{2})$", $_REQUEST['surveyStartDate'],$regs))
        	{
            	$this->setStartDate(0);
            	$this->validationErrors .= get_lang('Date format must be DD/MM/YY').'<br />';
            }
            else
            {
            	if((intval($regs[2])>12)||(intval($regs[1])>31)||(intval($regs[3])==0)||(intval($regs[2])==0)||(intval($regs[1])==0)){
            		$this->setStartDate(0);
            		$this->validationErrors .= get_lang('Date format must be DD/MM/YY').'<br />';
            	}
            	else
            	{
            		$timestp = mktime(0,0,0, intval($regs[2]),intval($regs[1]), intval($regs[3]));
            		$this->setStartDate($timestp);
            	}
            		
            }
        }
        else
        	$this->setStartDate(0);
        
        if(!empty($_REQUEST['surveyEndDate']))
        {
        	if(!ereg ("^([0-9]{2})/([0-9]{2})/([0-9]{2})$", $_REQUEST['surveyEndDate'],$regs))
        	{
            	$this->setEndDate(0);
            	$this->validationErrors .= get_lang('Date format must be DD/MM/YY').'<br />';
            }
            else
            {
            	if((intval($regs[2])>12)||(intval($regs[1])>31)||(intval($regs[3])==0)||(intval($regs[2])==0)||(intval($regs[1])==0)){
            		$this->setEndDate(0);
            		$this->validationErrors .= get_lang('Date format must be DD/MM/YY').'<br />';
            	}
            	else
            	{
            		$timestp = mktime(0,0,0, intval($regs[2]),intval($regs[1]), intval($regs[3]));
            		$timestp += 3600 * 24 -1; //survey accessible until 11.59pm
            		$this->setEndDate($timestp);
            	}
            		
            }
        }
        else
        	$this->setEndDate(0);
        
        
        return $this->isValid();
            
    }
    
    //load answers to the survey from form
    public function loadFromFillForm()
    {
        $i = 0;
        foreach($this->questions as $quest)
        {
            $i++;
            $quest->loadFromFillForm();
        }
    }
    
    //save the survey, used when create or edit survey
    public function save()
    {
    	if(!$this->isValid())
    		return false;
        if($this->id == -1)
        {
            //Insert new survey in DB
            $sql = "INSERT INTO `".$this->tblSurvey."`
                    SET `courseId` = '".$this->courseId."',
                    	`title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `anonymous` = '".addslashes($this->anonymous)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `resultsVisibility` = '".addslashes($this->resultsVisibility)."',
                        `startDate` = ".(is_null($this->startDate)?"NULL":"FROM_UNIXTIME(".addslashes($this->startDate).")").",
                        `endDate` = ".(is_null($this->endDate)?"NULL":"FROM_UNIXTIME(".addslashes($this->endDate).")");
			
            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            $sql = "UPDATE `".$this->tblSurvey."`
                	SET `rank` = ".(int) $insertedId."
            		WHERE `id` = ".(int) $insertedId;
            claro_sql_query($sql);
            		
            
            if( $insertedId )
            {
                $this->id = (int) $insertedId;
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
            $sql = "UPDATE `".$this->tblSurvey."`
                	SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `anonymous` = '".addslashes($this->anonymous)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `resultsVisibility` = '".addslashes($this->resultsVisibility)."',
                        `startDate` = ".(is_null($this->startDate)?"NULL":"FROM_UNIXTIME(".addslashes($this->startDate).")").",
                        `endDate` = ".(is_null($this->endDate)?"NULL":"FROM_UNIXTIME(".addslashes($this->endDate).")")."
                	WHERE `id` = ".$this->id;
            
            if( claro_sql_query($sql) )
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
        
    }

    //delete the survey
    public function delete()
    {
        //clear answers of the survey
        $sql = "DELETE FROM `".$this->tblAnswerChoice."`
        	WHERE `surveyId` = ".(int)$this->id;
        claro_sql_query($sql);
        
        $sql = "DELETE FROM `".$this->tblAnswerText."`
        	WHERE `surveyId` = ".(int)$this->id;
        claro_sql_query($sql);
        
        $sql = "DELETE FROM `".$this->tblRelSurvUser."`
        	WHERE `surveyId` = ".(int)$this->id;
        claro_sql_query($sql);
        
        //delete links between survey and questions
        $sql = "DELETE FROM `".$this->tblRelSurvQuest."`
        	WHERE `surveyId` = ".(int)$this->id;
        claro_sql_query($sql);
        
        //delete the survey
        
        $sql = "DELETE FROM `".$this->tblSurvey."`
        	WHERE `id` = ".(int)$this->id;
        claro_sql_query($sql);
    }
    
    //check if all ok to go to db
    private function isValid()
    {
    	
    	if(empty($this->title))
    		$this->validationErrors .= get_lang('%name is required', array('%name'=>get_lang('Title'))).'<br />';
    	

    	if(empty($this->validationErrors))
    		return true;
    	else
    		return false;
    	
    }
    
    //get error while reading content of a form
    public function getValidationErrors()
    {
    	return $this->validationErrors;
    }
    
    //move question up in the survey
    public function moveQuestionUp($id)
    {
        $i = 0;
        foreach( $this->questions as $quest )
		{
            $i++;
            if($i>1){
                if($quest->getId() == (int)$id)
                {
                    //exchange rank with 
                    $sql = "SELECT
                            	`id`,
            					`rank`
                    		FROM `".$this->tblRelSurvQuest."`
                    		WHERE `surveyId` = '".(int) $this->id."'
                            AND  `questionId` = '".(int) $id."'";
                    $firstQuestion = claro_sql_query_get_single_row($sql);
                    
                    $sql = "SELECT
                            	`id`,
                            	`rank`
                            FROM `".$this->tblRelSurvQuest."`
                            WHERE `surveyId` = '".$this->id."'
                            AND `rank` < ".(int) $firstQuestion['rank']
                            ." ORDER BY `rank` DESC LIMIT 1";
                    $result = claro_sql_query($sql);//TODO change it and use claroline function
                    if(mysql_num_rows($result)==1)
                    {
                        $nextQuestion = mysql_fetch_array($result, MYSQL_ASSOC);
                        mysql_free_result($result);
                    }
                    else
                        return false; //survey already first
                
                
                    //exchange ranks
                    $sql = "UPDATE `".$this->tblRelSurvQuest."`
                            	SET `rank` = ".(int) $nextQuestion['rank']."
                            	WHERE `id` = ".(int) $firstQuestion['id'];
                    claro_sql_query($sql);
                    $sql = "UPDATE `".$this->tblRelSurvQuest."`
                            	SET `rank` = ".(int) $firstQuestion['rank']."
                            	WHERE `id` = ".(int) $nextQuestion['id'];
                    
                    if( claro_sql_query($sql) )
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            }
		}
    }
    
    //move question down in the survey
    public function moveQuestionDown($id)
    {
        $i = 0;
        foreach( $this->questions as $quest )
		{
            $i++;
            if($i<count($this->questions)){
                if((int)$quest->getId() == (int)$id)
                {
                    //exchange rank with 
                    $sql = "SELECT
                            	`id`,
            					`rank`
                    		FROM `".$this->tblRelSurvQuest."`
                    		WHERE `surveyId` = '".(int) $this->id."'
                            AND  `questionId` = '".(int) $id."'";
                    $firstQuestion = claro_sql_query_get_single_row($sql);
                    
                    $sql = "SELECT
                            	`id`,
                            	`rank`
                            FROM `".$this->tblRelSurvQuest."`
                            WHERE `surveyId` = '".$this->id."'
                            AND `rank` > ".(int) $firstQuestion['rank']
                            ." ORDER BY `rank` ASC LIMIT 1";
                    $result = claro_sql_query($sql);//TODO change it and use claroline function
                    if(mysql_num_rows($result)==1)
                    {
                        $nextQuestion = mysql_fetch_array($result, MYSQL_ASSOC);
                        mysql_free_result($result);
                    }
                    else
                        return false; //survey already first
                
                
                    //exchange ranks
                    $sql = "UPDATE `".$this->tblRelSurvQuest."`
                            	SET `rank` = ".(int) $nextQuestion['rank']."
                            	WHERE `id` = ".(int) $firstQuestion['id'];
                    claro_sql_query($sql);
                    $sql = "UPDATE `".$this->tblRelSurvQuest."`
                            	SET `rank` = ".(int) $firstQuestion['rank']."
                            	WHERE `id` = ".(int) $nextQuestion['id'];
                    
                    if( claro_sql_query($sql) )
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            }
		}
    }
    
    //remove all answers of the survey
    public function removeAnswers()
    {
        foreach($this->questions as $quest)
        {
            $quest->removeAnswers($this->id);
        } 
         
        $sql = "DELETE FROM `".$this->tblRelSurvUser."`
    		WHERE `surveyId` = '".(int) $this->id."'";

        claro_sql_query($sql);
    }
    
    //load questions of the survey from db
    public function loadQuestions()
    {
        $sql = 'SELECT q.`id`, q.`title`, q.`type`, q.`alignment`
                FROM `'.$this->tblRelSurvQuest.'` as rel
                INNER JOIN `'.$this->tblQuestion.'` as q
                ON rel.`questionId` = q.`id`
                WHERE rel.`surveyId` = '.$this->id.'
                ORDER BY rel.`rank` ASC';

	    $list = claro_sql_query_fetch_all($sql);
	    
	    foreach( $list as $data )
	    {
            $quest = new Question($this->courseId, $this->editMode);
            $quest->loadFromVar($data);
            $quest->setSurveyId($this->id);
            $this->questions []= $quest;
	    }
    }
    
    //load answers of the user from db
    public function loadAnswers($userId)
    {
        $sql = 'SELECT COUNT(`surveyId`)
                FROM `'.$this->tblRelSurvUser.'`
                WHERE `surveyId` = '.$this->id.' 
                AND `userId` = '.$userId;

	    $val = claro_sql_query_fetch_single_value($sql);
		if((int)$val == 0)
            $this->alreadyFilled = false;
        else
            $this->alreadyFilled = true;
            
        if($this->anonymous == 'NO')
        {
    	    
            foreach( $this->questions as $quest )
    	    {
    	        $quest->setSurveyId($this->id);
                $quest->loadAnswer($userId);
                //lire les reponses
    	    }
        }
    }
    
    //save answers of the users
	public function saveAnswers($userId)
	{
		foreach( $this->questions as $quest )
		{
			$quest->setSurveyId($this->id);
			if($this->anonymous == 'YES')
			    $quest->saveAnswers(null);
			else
			    $quest->saveAnswers($userId);
			//lire les reponses
		}
		
		$sql = "DELETE FROM `".$this->tblRelSurvUser."`
				WHERE `userId` = ".(int)$userId." 
				AND `surveyId` = ".(int)$this->id;
		claro_sql_query($sql);
		$sql = "INSERT INTO `".$this->tblRelSurvUser."` (
				`userId`,
				`surveyId`)
				VALUES ( '".(int)$userId."',
				'".(int)$this->id."');";
		claro_sql_query($sql);
	}
	
    //check if someone has answered survey
	public function isAnswered()
	{
	    $sql = 'SELECT COUNT(`surveyId`)
                FROM `'.$this->tblRelSurvUser.'`
                WHERE `surveyId` = '.$this->id;
	    $val = (int)claro_sql_query_fetch_single_value($sql);

	    if((int)$val>0)
	        return true;
	    else
	        return false;
	}
    
    //load results of the survey
    public function loadResults()
    {
	    foreach( $this->questions as $data )
	    {
	        $data->setSurveyId($this->id);
	        $data->loadResults();
	    }
    }

	/*
           * RENDERING FUNCTIONS
           */
    //render form to fill survey
    public function renderFillForm()
    {
        $dialogBox = new DialogBox();
        CssLoader::getInstance()->load('LVSURVEY');
        $out = '';
        if($this->editMode==false)
        {
            
            if($this->visibility == 'INVISIBLE')
            {
                $dialogBox->error( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if(is_null($this->startDate) || ($this->startDate > time()))
            {
                $dialogBox->error( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if(!is_null($this->endDate) && ($this->endDate < time()))
            {
                $dialogBox->info( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }

        }
        
        $out = '';
        
        
        if($this->anonymous == 'YES')
        {
            $dialogBox->info( get_lang('This survey is anonymous. We only save your answer not your identification.'));
        }
        else
        {
            $dialogBox->info( get_lang('This survey is not anonymous. Your identification will be saved.'));
        }
        
        if($this->alreadyFilled == true)
        {
            if( $this->anonymous == 'YES')
                $dialogBox->info( get_lang('You already filled this survey.'));
            else
                $dialogBox->info( get_lang('You already filled this survey. You may change your answers.'));
        }
        
        
        
        $out .= $dialogBox->render();
        $out .= '<div>'.$this->description.'</div>';
        
        if(count($this->questions)<1)
        {
            $dialogBox2 = new DialogBox();
            $dialogBox2->error( get_lang('No question in this survey'));
            $out .= $dialogBox2->render();
            return $out;
        }
        
        
        $out .= '<form method="post" action="show_survey.php">'."\n"
            .'<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
            .'<input type="hidden" name="surveyGoToConf" value="" />' . "\n"
            .'<input type="hidden" name="surveyId" value="'.$this->id.'" />' . "\n";
        $out .= '<div class="LVSURVEYQuestionList">';
        
        $i = 0;
        foreach( $this->questions as $quest )
        {
            $i++; 
            $up = false;
            $down = false;
            if($i!=1) //up and down
                $up = true;
            if($i<count($this->questions))
                $down = true;
            $out .= $quest->renderFillForm($up, $down);
        }
        
        $out .= '</div>';
        
        if(($this->alreadyFilled == false) || ($this->anonymous == 'NO'))
            $out .= '<input type="submit" value="'.get_lang('Submit').'" />';
        
        $out .=  '</form>';
        

        return $out;
    }

    //render confirmation form when filling
    public function renderConfForm()
    {
        CssLoader::getInstance()->load('LVSURVEY');
        $out = '';
        if($this->editMode==false)
        {
            if($this->visibility == 'INVISIBLE')
            {
                $dialogBox = new DialogBox();
                $dialogBox->error( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if(is_null($this->startDate) || ($this->startDate > time()))
            {
                $dialogBox = new DialogBox();
                $dialogBox->error( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if(!is_null($this->endDate) && ($this->endDate < time()))
            {
                $dialogBox = new DialogBox();
                $dialogBox->info( get_lang('This survey is not accessible'));
                $out .= $dialogBox->render();
                return $out;
            }
        }
        
        if (($this->alreadyFilled == true) && ($this->anonymous == 'YES'))
        {
            $dialogBox = new DialogBox();
            $dialogBox->info( get_lang('You can\'t fill this survey again'));
            $out .= $dialogBox->render();
            return $out;
        }
        
        $out = '';
        if(count($this->questions)>0)
        {
            $dialogBox = new DialogBox();
            if($this->anonymous == 'YES')
            {
                $dialogBox->info( get_lang('This survey is anonymous. We only save your answer not your identification.'));
            }
            else
            {
                $dialogBox->info( get_lang('This survey is not anonymous. Your identification will be saved.'));
            }
            
            $out .= $dialogBox->render();
            $out .= $this->description;
            $hiddenform = '<form method="post" action="show_survey.php">'."\n"
                .'<input type="hidden" name="claroFormId" value="'.uniqid('').'">' . "\n"
                .'<input type="hidden" name="surveyId" value="'.$this->id.'">' . "\n";

            $out .= '<div class="LVSURVEYQuestionList">';
            
        	foreach( $this->questions as $quest )
    	    {
				$hiddenform .= $quest->renderConfFormHidden();
                $out .= $quest->renderConfForm();
    	    }
    	    
    	    $out .= '</div>';

			$hiddenform2 = $hiddenform;
			$hiddenform .= '<input type="hidden" name="surveyGoToSubmit" value="'.uniqid('').'">' . "\n"
				.'<input type="submit" value="'.get_lang('Confirm').'" />' . "\n"
				.'</form>' . "\n";
			$hiddenform2 .= '<input type="hidden" name="surveyGoToFill" value="'.uniqid('').'">' . "\n"
				.'<input type="submit" value="'.get_lang('Change my answers').'" />' . "\n"
				.'</form>' . "\n";
				
			$out .= '<table><tr><td>'. $hiddenform2 .'</td><td>'.  $hiddenform.'</td></tr></table>';
        }

        return $out;
    }
    
    //render a form to edit survey properties or create a new one
    public function renderEditForm()
    {
        $out = '';
        JavascriptLoader::getInstance()->load('jquery');
        JavascriptLoader::getInstance()->load('ui.datepicker');
        CssLoader::getInstance()->load('ui.datepicker');
        
        
        if($this->isAnswered() == true)
        {
            $dialogBox = new DialogBox();
            $dialogBox->warning( get_lang('Some users have already answered to this survey.'));
            $out .= $dialogBox->render();
        }
        
        
        
    	$out .= '<form method="post" action="./edit_survey.php" >' . "\n\n"
        	.	'<input type="hidden" name="surveyId" value="'.$this->id.'" />' . "\n"
            .    '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
            .    '<table border="0" cellpadding="5">' . "\n"
    	
            //anonymous
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyAnonymous">' . get_lang('Anonymous survey').'&nbsp;' . "\n"
            .	 '<span class="required">*</span>&nbsp;:' . "\n"
            .	 '</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
        
            .	 ($this->id==-1?
        		'<input type="radio" name="surveyAnonymous" id="surveyAnonymous" value="YES" '
                .    ($this->anonymous == 'YES'?'checked="checked" ':'')
                .	 '/>'.get_lang('Yes'). "\n"
                .    '<input type="radio" name="surveyAnonymous" id="surveyAnonymous" value="NO" '
                .    ($this->anonymous == 'NO'?'checked="checked" ':'')
                .	 '/>'.get_lang('No')."\n"
                :    ($this->anonymous == 'NO'?get_lang('No'):get_lang('Yes'))
                )
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
        
        
                

        
        
            //--
            // title
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyTitle">' . get_lang('Title').'&nbsp;' . "\n"
            .	 '<span class="required">*</span>&nbsp;:' . "\n"
            .	 '</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
            .	 '<input  type="text" name="surveyTitle" id="surveyTitle" size="60" maxlength="200" value="' .htmlspecialchars( $this->title) . '" />' . "\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
    
            // description
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyDescription">' . get_lang('Description') . '&nbsp;:</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
            .	 claro_html_textarea_editor('surveyDescription', $this->description) . "\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
    
                        //startdate
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyStartDate">' . get_lang('Start date').'&nbsp;' . "\n"
            .	 ':' . "\n"
            .	 '</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
            .	 '<input  type="text" name="surveyStartDate" id="surveyStartDate" size="20" maxlength="20" value="' 
            .  (is_null($this->startDate)?'':claro_html_localised_date("%d/%m/%y", $this->startDate )). '" />' . "\n"
            .	 '<label for="surveyStartDate">' . get_lang('Leave blank if you don\'t want to set it').'&nbsp;' . "\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"

        
            //enddate
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyEndDate">' . get_lang('End date').'&nbsp;' . "\n"
            .	 ':' . "\n"
            .	 '</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
            .	 '<input  type="text" name="surveyEndDate" id="surveyEndDate" size="20" maxlength="20" value="'
            .  (is_null($this->endDate)?'':claro_html_localised_date("%d/%m/%y", $this->endDate )). '" />' . "\n"
            .	 '<label for="surveyEndDate">' . get_lang('Leave blank if you don\'t want to set it').'&nbsp;' . "\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
        
        
            //visibility of results
            .    '<tr>' . "\n"
            .	 '<td valign="top">' . "\n"
            .	 '<label for="surveyAnonymous">' . get_lang('Results visibility for users').'&nbsp;' . "\n"
            .	 '<span class="required">*</span>&nbsp;:' . "\n"
            .	 '</label>' . "\n"
            .	 '</td>' . "\n"
            .	 '<td>' . "\n"
            .	 '<input type="radio" name="surveyResultsVisibility" value="VISIBLE" '
            .    ($this->resultsVisibility == 'VISIBLE'?'checked ':'')
            .	 '/>'.get_lang('Always visible'). "\n"
            .    '<input type="radio" name="surveyResultsVisibility" value="VISIBLE_AT_END" '
            .    ($this->resultsVisibility == 'VISIBLE_AT_END'?'checked ':'')
             .	 '/>'.get_lang('Only visible at the end of the survey')."\n"
            .    '<input type="radio" name="surveyResultsVisibility" value="INVISIBLE" '
            .    ($this->resultsVisibility == 'INVISIBLE'?'checked ':'')
            .	 '/>'.get_lang('Never visible')."\n"
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
            
            .    '  <script>'
            .	 '$.datepicker.setDefaults({dateFormat: \'dd/mm/y\'});' . "\n"
            .	 '$(\'#surveyStartDate\').datepicker({showOn: \'both\'});' . "\n"
            .	 '$(\'#surveyEndDate\').datepicker({showOn: \'both\'});' . "\n"
            .	 '</script>'
            // submit
            .    '<tr>' . "\n"
            .	 '<td colspan="3">'
            .	 '<input type="submit" value="'.get_lang('Finish').'" />' . "\n"
            .	 '</form>'
            .	 '</td>' . "\n"
            .	 '</tr>' . "\n\n"
            .    '</tbody>' . "\n\n"
            .	 '</table>' . "\n\n"
        ;
        return $out;
    }

    //render results of the survey
    public function renderResults()
    {
        CssLoader::getInstance()->load('LVSURVEY');
        $out = '';
        if($this->editMode==false)
        {
            if($this->visibility =='INVISIBLE')
            {
                $dialogBox = new DialogBox();
                $dialogBox->error( get_lang('You are not allowed to see these results.'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if($this->resultsVisibility == 'INVISIBLE')
            {
                $dialogBox = new DialogBox();
                $dialogBox->error( get_lang('You are not allowed to see these results.'));
                $out .= $dialogBox->render();
                return $out;
            }
            else if($this->resultsVisibility == 'VISIBLE_AT_END')
            {
                //check if end reach
                if(is_null($this->endDate) || ($this->endDate > time()))
                {
                    
                    if(!is_null($this->endDate))
                        $tmp = get_lang('Results will be visible only at the end of the survey on %date.', 
                        array('%date'=>claro_html_localised_date(get_locale('dateFormatLong'), $this->endDate)));
                    else
                        $tmp = get_lang('Results will be visible only at the end of the survey.');
                    $dialogBox = new DialogBox();
                    $dialogBox->info($tmp);
                    $out .= $dialogBox->render();
                    return $out;
                }
            }
        }
        
        $out = '';
        if(count($this->questions)>0)
        {
            $out .= '<div>'.$this->description.'</div>';
            
            $out .= '<div class="LVSURVEYQuestionList">';
            
            //show each question
        	foreach( $this->questions as $quest )
    	    {
                $out .= $quest->renderResults();
    	    }
    	    
    	    $out .= '</div>';

        }

        return $out;
    }
    
    /*
           * GETTER AND SETTER
           */
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($value)
    {
        $this->title = trim($value);
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($value)
    {
        $this->description = trim($value);
    }
    
    public function getAnonymous()
    {
        return $this->anonymous;
    }
    
    public function setAnonymous($value)
    {
        $acceptedValues = array('YES', 'NO');

        if( in_array($value, $acceptedValues) )
        {
            $this->anonymous = $value;
            return true;
        }
        return false;
    }
    
    public function getVisibility()
    {
        return $this->visibility;
    }
    
    public function setVisibility($value)
    {
        $acceptedValues = array('VISIBLE', 'INVISIBLE');

        if( in_array($value, $acceptedValues) )
        {
            $this->visibility = $value;
            return true;
        }
        return false;
    }
    
    public function getResultsVisibility()
    {
        return $this->resultsVisibility;
    }
    
    public function setResultsVisibility($value)
    {
        $acceptedValues = array('VISIBLE', 'INVISIBLE', 'VISIBLE_AT_END');

        if( in_array($value, $acceptedValues) )
        {
            $this->resultsVisibility = $value;
            return true;
        }
        return false;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setStartDate($value)
    {
    	if($value==0)
    		$this->startDate = null;
    	else
            $this->startDate = $value;
    }
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function setEndDate($value)
    {
       	if($value==0)
    		$this->endDate = null;
    	else
        	$this->endDate = $value;
    }
    
    public function getNbrQuestion()
    {
        return count($this->questions);
    }
    
}

?>