<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

	// protect file
	if ( count( get_included_files() ) == 1 )
	{
		die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
	}

    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision$
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package NETQUIZ
     */

	 

	
	 
// class netquizInstaller
class importYml 
{
	
	//var $extractDirectory;
	var $fileStruct;
	/*
	// extractDirectory
	function setExtractDirectory($extractDirectory)
	{
		$this->extractDirectory = $extractDirectory;
	}	
	
	// vérifie si le zip decompressé contient 1 repertoire et 1 fichier xml
	function chkFileStruct()
	{
	
		$file_struct = array();
		$file_struct['path'] = '';
		$file_struct['data'] = '';
		$file_struct['xml'] = '';
		
		$handle = opendir( $this->extractDirectory );
		
		while ( false !== ( $file = readdir( $handle ) ) )
		{
			
			if ( $file != "." && $file != ".." && $file != "" && !preg_match('/^\./', $file) && $file != "__MACOSX" )
			{ 
				
				$file_struct['path'] = $this->extractDirectory;
				
				if ( is_dir( $this->extractDirectory . '/' . $file ) 
					&& empty( $file_struct['data'] ) )  
				{
					$file_struct['data'] = $file;
				}
				elseif ( is_file( $this->extractDirectory . '/' . $file ) 
					&& empty( $file_struct['xml'] )
					&& substr( $file, -4 ) == ".xml" ) 
				{
					$file_struct['xml'] = $file;
				}

			}
			
		}

		closedir($handle);
		
		if ( !empty( $file_struct['path']) && !empty( $file_struct['data']) && !empty( $file_struct['xml']) )
		{
			return $file_struct;
		}
		else
		{
			if ( empty( $file_struct['path']) )
			{
				pushClaroMessage("invalid netquiz archive : missing path");
			}
			
			if ( empty( $file_struct['data']) )
			{
				pushClaroMessage("invalid netquiz archive : missing dat");
			}
			
			if ( empty( $file_struct['xml']) )
			{
				pushClaroMessage("invalid netquiz archive : missing xml");
			}
			
			return false;
		}
		
	}
	*/
	// fileStruct
	function setFileStruct($fileStruct)
	{
		$this->fileStruct = $fileStruct;
	}

	// vérifie si le fichier xmlest valide
	function chkValidYml()
	{
		return true;
    }	
	
	
	
}

?>