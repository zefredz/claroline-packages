<?php
class DUPLogger{
	// this file will contain all the logs about was has been copied (except of course for custom script)
	public static $__COPY_LOG_FILE__ ;

	// this file will contain all the logs about what had been wrong
	public static $__ERROR_LOG_FILE__;
	
	public static $should_log_copy = true;
	public static $should_log_error = true;
	
	//add a new entry in the copy log, call this when you duplicate a file
	public function log_copy_file($toolName, $courseName, $userLogin, $source, $dest)
	{
		if( !  DUPLogger::$should_log_copy) return;
		
		$message = "" 		.	date(DATE_RFC850);
		$message .= "\t"	.	$courseName;
		$message .= "\t"	.	$toolName;
		$message .= "\t"	.	$userLogin;
		$message .= "\t"	.	"COPIING FILE `" . $source . "`\t TO \t`" . $dest ."`";
		$message .= "\n";
		error_log($message, 3, DUPLogger::$__COPY_LOG_FILE__);
	}
	
	//add a new entry in the copy log, call this when you duplicate a table
	public function log_copy_table($toolName, $courseName, $userLogin, $sourceTable, $destTable)
	{
		if( !  DUPLogger::$should_log_copy) return;
		
		$message = "" 		.	date(DATE_RFC850);
		$message .= "\t"	.	$courseName;
		$message .= "\t"	.	$toolName;
		$message .= "\t"	.	$userLogin;
		$message .= "\t"	.	"DUPLICATING TABLE `" . $sourceTable . "`\t TO \t`" . $destTable ."`";
		$message .= "\n" ;
		error_log($message, 3, DUPLogger::$__COPY_LOG_FILE__);
	}
	/**
	 * add a new entry in the copy log, call this when you execute a sql command
	 *  
	 * @param $toolName string label of the tool
	 * @param $coursName string sysCode of the course
	 * @param $userLogin login of the admin who has done the duplication
	 * @param $sql string sql command executed
	 */
	public function log_copy_row($toolName, $courseName, $userLogin, $sourceTable, $targetTable)
	{
		if( !  DUPLogger::$should_log_copy) return;
		
		$message = "" 		.	date(DATE_RFC850);
		$message .= "\t"	.	$courseName;
		$message .= "\t"	.	$toolName;
		$message .= "\t"	.	$userLogin;
		$message .= "\t"	.	"COPIING A ROW FROM `" . $sourceTable . "`\t TO \t`" . $targetTable ."`";
		$message .= "\n" ;
		error_log($message, 3, DUPLogger::$__COPY_LOG_FILE__);
	}
	
	public function log_error($toolName, $courseName, $errorMessage)
	{
		if( !  DUPLogger::$should_log_error) return;
		
		$message = "" 		.	date(DATE_RFC850);
		$message .= "\t"	.	$courseName;
		$message .= "\t"	.	$toolName;
		$message .= "\t"	.	$errorMessage;
		$message .= "\n";
		error_log($message, 3, DUPLogger::$__ERROR_LOG_FILE__);
	}
}

DUPLogger::$__COPY_LOG_FILE__ = ( __DIR__ . "/../logs/copy.log" );
DUPLogger::$__ERROR_LOG_FILE__ = ( __DIR__ . "/../logs/error.log" ) ;


?>