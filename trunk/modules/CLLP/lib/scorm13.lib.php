<?php


class Scorm13 implements ScormInterface
{
    private $apiName;
    private $apiFileName;
    private $version;

    private $mapping;

    function __construct()
    {
        $this->apiName = 'API_1484_11';
        $this->apiFileName = 'scorm13.api';
        $this->version = '1.3';

        $this->mapping = '';
    }

    public function getApiName()
    {
        return $this->apiName;
    }

    public function getApiFileName()
    {
        return $this->apiFileName;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @see Scorm class
     */
    public function api2ItemAttempt( $dataModelValues, &$itemAttempt)
    {
        // TODO maybe raise some kind of error if nothing is set ?
        // cast to array for easier use
        $dataModelValues = (array) $dataModelValues;

        if( !empty($dataModelValues['cmi.location']) )
        {
            $itemAttempt->setLocation($dataModelValues['cmi.location']);
        }

        if( !empty($dataModelValues['cmi.completion_status']) )
        {
            $itemAttempt->setCompletionStatus($dataModelValues['cmi.completion_status']);
        }
        
        if( !empty($dataModelValues['cmi.entry']) )
        {
            $itemAttempt->setEntry($dataModelValues['cmi.entry']);
        }
        
        if( !empty($dataModelValues['cmi.score.raw']) )
        {
            $itemAttempt->setScoreRaw($dataModelValues['cmi.score.raw']);
        }
        
        if( !empty($dataModelValues['cmi.completion_status']) )
        {
            $itemAttempt->setScoreMin($dataModelValues['cmi.score.min']);
        }
        
        if( !empty($dataModelValues['cmi.completion_status']) )
        {
            $itemAttempt->setScoreMax($dataModelValues['cmi.score.max']);
        }
        
        if( !empty($dataModelValues['cmi.session_time']) )
        {
            $itemAttempt->setSessionTime($dataModelValues['cmi.session_time']);
        }
        
        if( !empty($dataModelValues['cmi.total_time']) )
        {
            $itemAttempt->setTotalTime($dataModelValues['cmi.total_time']);
        }
        
        if( !empty($dataModelValues['cmi.suspend_data']) )
        {
            $itemAttempt->setSuspendData($dataModelValues['cmi.suspend_data']);
        }
        
        if( !empty($dataModelValues['cmi.credit']) )
        {
            $itemAttempt->setCredit($dataModelValues['cmi.credit']);
        }
        
        // add other elements

        return true;
    }

    public function itemAttempt2Api($dataModel)
    {

    }
}
?>