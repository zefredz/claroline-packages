<?php
require_once dirname(__FILE__).'/DUPUtils.class.php' ;

class DUPToolManager{
    /** 
     * @var string the label of the tool we want to duplicate
     */
    protected $toolLabel;
    /**
     * @var int the id of the tool in DB
     */
    protected $toolId;
    
        
    /**
     * @var SimpleXMLElement : contents of the XML file used to configure which data must be copied
     * This is a representation of the file conf/<TOOL_LABEL>.xml
     */
    protected $xml;
    
    /**
     * @var array of string The files which need to be copied in order for the tool to be considered as duplicated.
     * 
     * some keywords are allowed : 
     *     %courseSysCode% => the sysCode of the course (which is different ion the source course and in the duplicated course)
     */
    protected $fileList;
    
    /**
     * @var array of string The names of the tables which need to be copied in order for the tool to be considered as duplicated.
     * 
     * some keywords are allowed : 
     *     %courseSysCode% => the sysCode of the course (which is different ion the source course and in the duplicated course)
     */
    protected $tableList;
    
    //============================ CONSTRUCTOR SECTION ===========================================
    
    /**
     * @param $toolLabel : string the label of the tool we want to duplicate
     * @param $xmlFile : string path to the file containing the config for duplicating this tool ( usually conf/<TOOL_LABEL>.xml )
     */
    public function __construct( $toolLabel, $xmlFile )
    {
    	//label
        $this->toolLabel = $toolLabel;
        //id
        //TODO check difference between $tbl['tool'] & $tbl['module']
        $tbl = claro_sql_get_main_tbl();
        $sql = "
        	SELECT id      		AS 		toolId
              FROM `" . $tbl['tool'] . "` 
             WHERE `claro_label` LIKE '" . $this->toolLabel . "'; ";
        $this->toolId = claro_sql_query_get_single_value($sql);
        
        
        
        //xml        
        if( ! is_file( $xmlFile ) )
        {
            $this->xml = new SimpleXMLElement();
        }
        $this->xml = simplexml_load_file( $xmlFile );
        //parse xml
        $this->fileList = $this->getFilesToBeCopied();
        $this->tableList = $this->getTablesToBeCopied();
    }
    
    /**
     * return a list of files (or directories) which must be copied from the source course to the target course in order for
     * a specific tool to be duplicated.These files must be specified in the xml file
     *
     * the files are relative to /courses/[MyCourse]/
     *
     * @return array<String>: list of files and directories to be copied
     */
    private function getFilesToBeCopied()
    {        
        $res = array();
        foreach( $this->xml->xpath('/duplication/files/path') as $path )
        {
            $cleanedPath = trim( (string) $path );
            if( '' != trim( $cleanedPath ) )
            {
              $res[] = $cleanedPath;
            }
        }
        return $res;
    }
    
    /**
     * return a list of tables which must be copied from the source course to the target course in order for
     * a specific tool to be duplicated.These files must be specified in conf/<TOOL_LABEL>.xml
     *
     * the tables names are appended to the result of "claro_get_course_db_name_glued($courseId)"
     *
     * @return array<String>: list of table which contents need to be copied
     */
    private function getTablesToBeCopied()
    {        
        $res = array();
        foreach( $this->xml->xpath('/duplication/tables/table') as $table )
        {
            $tableName = trim( (string) $table );
            if( '' != trim( $tableName ) )
            {
              $res[] = $tableName;
            }
        }
        return $res;
    }
    
    //=========================== PRIVATE SECTION ============================================
    
    private function replaceKeyWords( $str, $courseData )
    {
        $search = array(
                            '%courseSysCode%'
                        );
        $replace = array(
                           $courseData['sysCode']
                        );
        return str_replace( $search, $replace, $str );
    }    
    
    private function copyFiles( $sourceCourseData, $targetCourseData )
    {
        $courseRepo = get_path( 'coursesRepositorySys' );
        $sourcePath = $sourceCourseData['path'];
        $targetPath = $targetCourseData['path'];
        foreach( $this->fileList as $file )
        {
            $sourceFile = DUPUtils::joinPaths( $courseRepo, $sourcePath, $this->replaceKeyWords( $file, $sourceCourseData ) );
            $targetFile = DUPUtils::joinPaths( $courseRepo, $targetPath, $this->replaceKeyWords( $file,$targetCourseData) );
            if(file_exists($sourceFile))
            {
            	
            	DUPLogger::log_copy_file($this->toolLabel,$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$sourceFile,$targetFile);
            	DUPUtils::copyr( $sourceFile, $targetFile);
            	// I do not use claro_copy_file because it has problem when targetPath is a filename
            }
        }
    }
    
    private function copy_db_tables( $sourceCourseData, $targetCourseData )
    {
        $prefixSource = $sourceCourseData['dbNameGlu'];
        $prefixTarget = $targetCourseData['dbNameGlu'];
        foreach( $this->tableList as $tableName )
        {
            
            $sourceTable = $prefixSource . $this->replaceKeyWords( $tableName, $sourceCourseData );
            $targetTable = $prefixTarget . $this->replaceKeyWords( $tableName, $targetCourseData );
            
            
            //TODO handdle transactions
            $sqlDrop = "
                DROP TABLE IF EXISTS `" . $targetTable . "; ";
            //create like = copy structure with constraints (except fk)
            $sqlCreate = "
                CREATE TABLE `" . $targetTable . "` LIKE `" . $sourceTable . "`; ";
            $sqlInsert = "
            	INSERT INTO `" . $targetTable . "` SELECT * FROM `" . $sourceTable . "`; ";
            
            DUPLogger::log_copy_table($this->toolLabel,$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$sourceTable,$targetTable);
            
            
            //CREATE TABLE ... LIKE ... (INLCUDING DEFAULTS )
            claro_sql_query($sqlDrop);
            claro_sql_query($sqlCreate);
            claro_sql_query($sqlInsert);
            
        }
    }
    
    private function copy_course_tool_relation( $sourceCourseData, $targetCourseData )
    {
    	$prefixSource = $sourceCourseData['dbNameGlu'];
    	$prefixTarget = $targetCourseData['dbNameGlu'];
    	$sourceTableList = claro_sql_get_course_tbl( $prefixSource );
    	$targetTableList = claro_sql_get_course_tbl( $prefixTarget );
    	$sqlDelete = "
    						  DELETE 
    					  	    FROM `".$targetTableList['tool']."` 
    					       WHERE `tool_id` = ".$this->toolId.";  ";
    	$sqlInsert = "
    					INSERT INTO `".$targetTableList['tool']."` ( 
    								`tool_id`, 
    								`rank`, 
    								`visibility`, 
    								`script_url`, 
    								`script_name`, 
    								`addedTool` ) 
    					    SELECT  `tool_id`, 
    					    		`rank`, 
    					    		`visibility`, 
    					    		`script_url`, 
    					    		`script_name`, 
    					    		`addedTool` 
    						   FROM `".$sourceTableList['tool']."` 
    					      WHERE `tool_id` = ".$this->toolId.";  ";
    	
    	DUPLogger::log_copy_row($this->toolLabel,$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$sourceTableList['tool'],$targetTableList['tool']);
            	
            	
    	//TODO handle transaction    	
    	claro_sql_query($sqlDelete);
    	claro_sql_query($sqlInsert);
    }
    
	private function copy_course_tool_rights( $sourceCourseData, $targetCourseData )
    {
    	$mainTableList = claro_sql_get_main_tbl();
    	$table = $mainTableList['right_rel_profile_action'];
    	
    	$sql = "
    				INSERT INTO `".$table."` ( 
    							`profile_id`, 
    							`action_id`, 
    							`courseId`, 
    							`value` ) 
    					 SELECT `profile_id`, 
    					        `action_id`, 
    					        '".$targetCourseData['sysCode']."', 
    					        `value`  
    					   FROM `".$table."` 
    					   WHERE `courseId` LIKE '".$sourceCourseData['sysCode']."';  ";
    	
    	DUPLogger::log_copy_row($this->toolLabel,$sourceCourseData['sysCode'],$targetCourseData['sysCode'],
            	claro_get_current_user_data("firstName") . " " . claro_get_current_user_data("lastName") ,
            	$table,$table);   	
    	claro_sql_query($sql);
    }
    
    //========================PUBLIC STUFF ===============================
    
    /**
     * copy the module from a source Course to a target Course (copy files then db )
     * 
     * @param sourceCourseId : int : the id of the source course in DB
     * @param targetCourseId : int : the id of the target course in DB
     * 
     */
    public function copyTool( $sourceCourseId, $targetCourseId )
    {
        $sourceCourseData = claro_get_course_data( $sourceCourseId );
        $targetCourseData = claro_get_course_data( $targetCourseId );
        
        $this->copyFiles( $sourceCourseData, $targetCourseData );
        $this->copy_db_tables( $sourceCourseData, $targetCourseData );
        $this->copy_course_tool_relation( $sourceCourseData, $targetCourseData );
        $this->copy_course_tool_rights( $sourceCourseData, $targetCourseData );
    }
    
}



?>