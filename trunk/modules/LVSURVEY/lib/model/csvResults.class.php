<?php 
FromKernel::uses('csv.class');
From::module('LVSURVEY')->uses('model/result.class');


abstract class CSVResults extends CsvRecordlistExporter
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
        if( $question->type == 'LIKERT' )
        {
            foreach( $lineResults->predefinedResultList as $predefinedValue => $predefinedResults )
            {
                $predefinedResultList = $predefinedResults->resultList;
                $this->appendPredefinedResult($predefinedResultList[0], $question);
            }
        }
        else
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
    
    function appendPredefinedResult($predefinedResult,$question)
    {
        $line = array (
            $this->survey->id,
            $question->id,
            $question->text,
            $predefinedResult->comment,
            null,
            get_lang( $predefinedResult->predefinedValue ),
            null,
            null
        );
        $this->recordList[] = $line;
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
    
    function appendChoiceResultList($choiceResultList,$question, $choice)
    {
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
    
    function appendPredefinedResult($predefinedResult,$question)
    {
        $line = array (
            $this->survey->id,
            $question->id,
            $question->text,
            $predefinedResult->userId,
            $predefinedResult->firstName,
            $predefinedResult->lastName,
            $predefinedResult->comment,
            null,
            get_lang( $predefinedResult->predefinedValue ),
            null,
            null
        );
        $this->recordList[] = $line;
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

            if( $question->type == 'LIKERT' )
            {
                foreach( $lineResults->predefinedResultList as $predefinedValue => $predefinedResults )
                {
                    $line = array(  $question->text,
                                    get_lang( $predefinedValue ),
                                    null,
                                    count($predefinedResults->resultList),
                                    $participationCount);
                    
                    $this->recordList[] = $line;                    
                }
            }
            else
            {
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
}

class PerUserCSVResults extends CSVResults
{
    protected function getTitleLine()
    {
        $titleLine = array();
        $titleLine[] = 'Participant';
        $index = 1;
        foreach( $this->survey->getSurveyLineList() as $surveyLine )
        {
            if( $surveyLine instanceof QuestionLine ) 
            {
                if( $surveyLine->question->type == 'ARRAY' ) 
                {
                    $subindex = 1;
                    foreach( $surveyLine->question->getChoiceList() as $choice )
                    {
                        $titleLine[] = 'Question ' . $index . '/' . $subindex++;
                    }
                    $titleLine[] = 'Commentaire question ' . $index++; 
                }
                else
                {
                    $titleLine[] = 'Question ' . $index;
                    $titleLine[] = 'Commentaire question ' . $index++; 
                }
            }
        }   
        return $titleLine;
    }
    
    protected function appendRecords()
    {
        $participationList = $this->survey->getParticipationList();
        $index = 1;
        foreach( $participationList as $participation )
        {
            if( $this->survey->is_anonymous )
            {
                $thisUser = $index++;
                $data = $this->appendParticipationLine( $participation, $this->survey->id );
            }
            else
            {
                $thisUser = $participation->getUser()->firstName . ' ' . $participation->getUser()->lastName;
                $data = $this->appendParticipationLine( $participation, $this->survey->id );
            }
            array_unshift( $data, $thisUser ); 
            $this->recordList[] = $data;
        }
    }
    
    protected function appendParticipationLine( $participation, $surveyId )
    {
        $participationLine = array();
        if( $participation->getUser()->userId )
        {
            $answerList = Participation::loadParticipationOfUserForSurvey( $participation->getUser()->userId, $surveyId )->getAnswerList();
        }
        else
        {
            $answerList = $participation->getAnswerList();
        }
        
        foreach( $answerList as $answer )
        {
            if( $answer->getQuestionLine()->question->type == 'ARRAY' ) 
            {               
                $subanswers = $answer->getSelectedOptionList();
                foreach( $subanswers as $subanswer )
                {
                    $participationLine[] = $subanswer->getText();
                }
            }
            elseif( $answer->getQuestionLine()->question->type == 'LIKERT' )
            {
                $participationLine[] = get_lang( $answer->getPredefinedValue() );
            }
            else
            {
                $choiceList = $answer->getSelectedChoiceList();
                if( sizeof( $choiceList ) == 1 ) 
                {
                    $participationLine[] = $choiceList[0]->text;
                }
                else
                {
                    $tmp = array();
                    foreach( $choiceList as $choice )
                    {
                        $tmp[] = $choice->text;
                    }
                    $participationLine[] = implode( ', ', $tmp );
                }
            }
            $participationLine[] = $answer->comment;
        } 
        return $participationLine;
    }
}