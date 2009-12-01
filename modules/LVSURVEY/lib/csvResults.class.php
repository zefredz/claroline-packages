<?php 
require_once get_path('incRepositorySys') . '/lib/csv.class.php';
From::module('LVSURVEY')->uses('result.class');


abstract class CSVResults extends csv
{
	
	protected $survey;
	protected $surveyResults;
    
    function __construct($survey)
    {
    	parent::csv(';', '"');
    	$this->survey = $survey;
    	$this->surveyResults = SurveyResults::loadResults($survey->id);    	
    }
    
    abstract public function buildRecords();
}

class AnonymousCSVResults extends CSVResults
{
	function buildRecords()
    {
        $surveyLineList = $this->survey->getSurveyLineList();     
    	
    	
    	$this->recordList[0] = array ('surveyId', 'questionId', 'question', 'choiceId', 'choice', 'comment');
    	$i = 1;
        
    	foreach($this->surveyResults->lineResultList as $questionId => $lineResults)
        {        	
        	$question = $surveyLineList[$questionId]->question;
        	$choiceList = $question->getChoiceList();
        	foreach($lineResults->choiceResultList as $choiceId => $choiceResults)
        	{
        		$choice = $choiceList[$choiceId];
        		foreach($choiceResults->resultList as $userId => $result)
        		{	
        			$line = array (
        						$this->survey->id,
        						$questionId,
        						$question->text,
        						$choiceId,
        						$choice->text,
        						$result->comment, 
        						);
        			$this->recordList[$i] = $line;
        			++$i;
        						
        		}
        	}
        }       
    }
}

class NamedCSVResults extends CSVResults
{
	function buildRecords()
    {
        $surveyLineList = $this->survey->getSurveyLineList();     
    	
    	
    	$this->recordList[0] = array ('surveyId', 'questionId', 'question', 'choiceId', 'choice', 'userId', 'userFirstName', 'userLastName', 'comment');
    	$i = 1;
        
    	foreach($this->surveyResults->lineResultList as $questionId => $lineResults)
        {        	
        	$question = $surveyLineList[$questionId]->question;
        	$choiceList = $question->getChoiceList();
        	foreach($lineResults->choiceResultList as $choiceId => $choiceResults)
        	{
        		$choice = $choiceList[$choiceId];
        		foreach($choiceResults->resultList as $userId => $result)
        		{	
        			$line = array (
        						$this->survey->id,
        						$questionId,
        						$question->text,
        						$choiceId,
        						$choice->text,
        						$userId, 
        						$result->firstName,
        						$result->lastName, 
        						$result->comment, 
        						);
        			$this->recordList[$i] = $line;
        			++$i;
        						
        		}
        	}
        }       
    }
}

class SyntheticResults extends CSVResults
{
	function buildRecords()
    {
        $surveyLineList = $this->survey->getSurveyLineList();  
        $participationCount = count($this->survey->getParticipationList());   
    	
    	
    	$this->recordList[0] = array ('question', 'choice', 'count', 'participationCount');
    	$i = 1;
        
    	foreach($this->surveyResults->lineResultList as $questionId => $lineResults)
        {        	
        	$question = $surveyLineList[$questionId]->question;
        	$choiceList = $question->getChoiceList();
        	foreach($lineResults->choiceResultList as $choiceId => $choiceResults)
        	{
        		$choice = $choiceList[$choiceId];
        		$line = array(	$question->text, 
        						$choice->text, 
        						count($choiceResults->resultList),
        						$participationCount);
        		
        		$this->recordList[$i] = $line;
        		++$i;
        	}
        }       
    }
}

?>
