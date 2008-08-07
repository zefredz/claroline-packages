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
	    // FIXME find better mechanism to do that
		$itemAttempt->setLocation($dataModelValues->cmi.location);
		$itemAttempt->setCompletionStatus($dataModelValues->cmi.completion_status);
		$itemAttempt->setEntry($dataModelValues->cmi.entry);
		$itemAttempt->setScoreRaw($dataModelValues->cmi.score.raw);
		$itemAttempt->setScoreMin($dataModelValues->cmi.score.min);
		$itemAttempt->setScoreMax($dataModelValues->cmi.score.max);
		$itemAttempt->setSessionTime($dataModelValues->cmi.session_time);
		$itemAttempt->setTotalTime($dataModelValues->cmi.total_time);
		$itemAttempt->setSuspendData($dataModelValues->cmi.suspend_data);
		$itemAttempt->setCredit($dataModelValues->cmi.credit);

		return true;
	}

	public function itemAttempt2Api($dataModel)
	{

	}
}
?>