<?php


class Scorm12 implements ScormInterface
{
	private $apiName;
	private $apiFileName;
	private $version;

	private $mapping;

	function __construct()
	{
		$this->apiName = 'API';
		$this->apiFileName = 'scorm12.api';
		$this->version = '1.2';

		$this->mapping = '';
	}

	function getApiName()
	{
		return $this->apiName;
	}

	public function getApiFileName()
	{
		return $this->apiName;
	}

	function getVersion()
	{
		return $this->version;
	}

	/**
	 * @see Scorm class
	 */
	function api2ItemAttempt( $dataModelValues, &$itemAttempt)
	{

		$itemAttempt->setLocation($dataModelValues['cmi.location']);
		$itemAttempt->setCompletionStatus($dataModelValues['cmi.completion_status']);
		$itemAttempt->setEntry($dataModelValues['cmi.entry']);
		$itemAttempt->setScoreRaw($dataModelValues['cmi.score.raw']);
		$itemAttempt->setScoreMin($dataModelValues['cmi.score.min']);
		$itemAttempt->setScoreMax($dataModelValues['cmi.score.max']);
		$itemAttempt->setSessionTime($dataModelValues['cmi.session_time']);
		$itemAttempt->setTotalTime($dataModelValues['cmi.total_time']);
		$itemAttempt->setSuspendData($dataModelValues['cmi.suspend_data']);
		$itemAttempt->setCredit($dataModelValues['cmi.credit']);

		return true;
	}

	public function itemAttempt2Api($dataModel)
	{

	}
}
?>