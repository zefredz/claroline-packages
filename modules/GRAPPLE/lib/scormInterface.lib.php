<?php

interface ScormInterface
{
	public function getApiName();
	public function getApiFileName();
	public function getVersion();

	/**
	 * This method is used to fill an attempt with data
	 */
	public function api2ItemAttempt($dataModelValues, &$itemAttempt);
	public function itemAttempt2Api($dataModel);
}
?>