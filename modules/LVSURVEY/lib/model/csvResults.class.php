<?php 
FromKernel::uses('csv.class');
From::module('LVSURVEY')->uses('model/result.class');


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
    
    public function buildRecords()
    {
        $this->recordList[0] = $this->getTitleLine();
        $this->appendRecords();
    }
    
    protected abstract function getTitleLine();
    protected abstract function appendRecords();
}

class AnonymousCSVResults extends CSVResults
{
    protected function getTitleLine()
    {
        return array (
            'surveyId', 
            'questionId', 
            'question', 
            'comment', 
            'choiceId', 
            'choice', 
            'optionId', 
            'option' );  
    }
    
    function appendRecords()
    {
        $surveyLineList = $this->survey->getSurveyLineList();
        
        foreach($this->surveyResults->lineResultList as $questionId => $lineResults)
        {
            $question = $surveyLineList[$questionId]->question;
            $this->appendLineResults($lineResults, $question);
        }
    }
    
    function appendLineResults($lineResults, $question)
    {
        foreach($lineResults->choiceResultList as $choiceId => $choiceResults)
        {
            $choice = $question->getChoice($choiceId);
            
            if($question->type == "ARRAY")
            {
                $optionResults = $choiceResults->optionResultList;
                $this->appendOptionResults($optionResults,$question,$choice);
            }
            else
            {
                $choiceResultList = $choiceResults->resultList;
                $this->appendChoiceResultList($choiceResultList, $question, $choice);
            }
        }
    }
    
    function appendOptionResults($optionResults,$question, $choice)
    {
        foreach($optionResults as $optionId => $optionResults)
        {
            $option = $choice->getOption($optionId);
            $optionResultList = $optionResults->resultList;
            $this->appendOptionResultList($optionResultList,$question, $choice, $option);
        }
    }
    
    function appendOptionResultList($optionResultList,$question, $choice, $option)
    {
        foreach($optionResultList as  $result)
        {
            $optionLine = array (
                $this->survey->id,
                $question->id,
                $question->text,
                $result->comment,
                $choice->id,
                $choice->text,
                $option->getId(),
                $option->getText()
            );
            
            $this->recordList[] = $optionLine;
        }
    }
    
    function appendChoiceResultList($choiceResultList,$question, $choice)
    {
        foreach($choiceResultList as $result)
        {
            $choiceLine = array (
                $this->survey->id,
                $question->id,
                $question->text,
                $result->comment,
                $choice->id,
                $choice->text,
                null,
                null
            );
            $this->recordList[] = $choiceLine;
        }
    }
}

class NamedCSVResults extends AnonymousCSVResults
{
    protected function getTitleLine()
    {
        return array (
            'surveyId',
            'questionId',
            'question',
            'userId',
            'userFirstName',
            'userLastName',
            'comment',
            'choiceId',
            'choice',
            'optionId',
            'option' );
    }
    
    function appendOptionResultList($optionResultList,$question, $choice, $option)
    {
        foreach($optionResultList as $result)
        {
                $optionLine = array (
                        $this->survey->id,
                        $question->id,
                        $question->text,
                        $result->userId,
                        $result->firstName,
                        $result->lastName,
                        $result->comment,
                        $choice->id,
                        $choice->text,
                        $option->getId(),
                        $option->getText()
                );
                $this->recordList[] = $optionLine;
        }
    }
    
    function appendChoiceResultList($choiceResultList,$question, $choice){
        foreach($choiceResultList as $result)
        {
            $choiceLine = array (
                $this->survey->id,
                $question->id,
                $question->text,
                $result->userId,
                $result->firstName,
                $result->lastName,
                $result->comment,
                $choice->id,
                $choice->text,
                null,
                null
            );
            $this->recordList[] = $choiceLine;
        }
    }
}

class SyntheticResults extends CSVResults
{
    function getTitleLine()
    {
        return array (
            'question', 
            'choice', 
            'option', 
            'count', 
            'participationCount', 
        );
    }
    
    function appendRecords()
    {
        $surveyLineList = $this->survey->getSurveyLineList();
        $participationCount = count($this->survey->getParticipationList());
        
        
        foreach($this->surveyResults->lineResultList as $questionId => $lineResults)
        {
            $question = $surveyLineList[$questionId]->question;
            foreach($lineResults->choiceResultList as $choiceId => $choiceResults)
            {
                $choice = $question->getChoice($choiceId);
                if($question->type != 'ARRAY')
                {
                    $line = array(  $question->text,
                                    $choice->text,
                                    null,
                                    count($choiceResults->resultList),
                                    $participationCount);
                    
                    $this->recordList[] = $line;
                }
                else
                {
                    foreach($choiceResults->optionResultList as $optionId => $optionResults)
                    {
                        $option = $choice->getOption($optionId);
                        $line = array(  
                                    $question->text, 
                                    $choice->text, 
                                    $option->getText(), 
                                    count($optionResults->resultList),
                                    $participationCount,
                        );
                        
                        $this->recordList[] = $line;
                    }
                }
            }
        }
    }
}