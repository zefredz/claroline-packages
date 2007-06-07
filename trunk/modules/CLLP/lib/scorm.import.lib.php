<?php
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileDisplay.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileUpload.lib.php';
require_once get_path('incRepositorySys') . '/lib/file.lib.php';
// for handling of error messages
require_once get_path('incRepositorySys') . '/lib/backlog.class.php';


class ScormImporter
{
    var $uploadedZipFile;
    var $baseDir = '';
    var $uploadDir = '';
    
    var $backlog;
    
    function ScormImporter(&$uploadedZipFile)
    {
        $this->uploadedZipFile = $uploadedZipFile;    
        
        // paths
        $this->baseDir = get_path('rootSys') . get_conf('tmpPathSys') . 'upload/';

        // errors logging
        $this->backlog = new Backlog();
    }
    
    function import()
    {
        $step = 1;
        // step 1
        // create tmp dir
        if( ! $this->createUploadDir() ) 
        { 
            clean($step); 
            return false;
        }
        $step++;
        
        // step 2
        // extract uploaded file to tmp dir
        if( !$this->unzip() )
        {
            $this->clean($step);
            return false;
        }
        $step++;
        
        // step 3
        // store all the list of files contained in the extracted zip for easier use
        if( !$this->buildFileList() )
        {
            $this->clean($step);
            return false;
        }   
        $step++;
        
        // step 4
        // find manifest file
        $manifestPath = $this->findMainManifest();
        if( $manifestPath == '' )
        {
            $this->clean($step);
            return false;
        }
        $step++;

        // step 5
        // parse manifest
        //  - trouver manifestes secondaire
        //  - les parser
        //  - vérifier si les ressources citées existes dans le tmp
        //  - sauver  
        if( ! $this->parseManifest($manifestPath) )
        {
            $this->clean($step);
            return false;
        }
        $step++;
        
        // créer un objet path, le valider et le sauver
        // pour chaque module ou chapitre 
        //  - créer un objet item, le valider et le sauver
        
        // créer répertoire définitif
        // copier les fichiers de tmp vers répertoire définitif
        // effacer le répertoire temporaire
        
        // import is successfull
        // clean tmp upload dir only
        $this->clean(1); 
        return true;
    }
    
    function createUploadDir()
    {
        $this->uploadDir = claro_mkdir_tmp($this->baseDir, 'path_');
        
        if( $this->uploadDir === false )
        {
            $this->backlog->failure(get_lang('Cannot read content of uploaded file.'));
            return false;
        }
        
        return true;
    }
    
    function unzip()
    {
        // upload path is the difference between baseDir to uploadDir
        $uploadPath = str_replace($this->baseDir,'',$this->uploadDir);
        
        if( !isset($this->uploadedZipFile) || !is_uploaded_file($this->uploadedZipFile['tmp_name']))
        {
            // upload failed
            $this->backlog->failure(get_lang('Choose a file to upload.'));
            return false;
        }

        include_once get_path('incRepositorySys') . '/lib/pclzip/pclzip.lib.php';


        if ( preg_match('/.zip$/i', $this->uploadedZipFile['name']) 
            && treat_uploaded_file($this->uploadedZipFile, $this->baseDir, $uploadPath, get_conf('maxFilledSpace' , 10000000), 'unzip', true))
        {
            if (!function_exists('gzopen'))
            {
                $this->backlog->failure(get_lang('Cannot unzip file.'));
                return false;
            }
            
            return true;
        }
        else
        {
            $this->backlog->failure(get_lang('File cannot be uploaded.  Not a zip file or not enough space remaining.'));
            return false;
        }
    }
    
    function buildFileList()
    {
        $this->zipContent = index_dir($this->uploadDir);
        
        if( $this->zipContent === false )
        {
            $this->backlog->failure(get_lang('Cannot read content of uploaded file.'));
            return false;
        }
        
        return true;
    }
    
    function findMainManifest()
    {
        // main manifest is the one that is the less deep in the zip file
        // according to scorm 2004 it should be at top level so check this first
        if( in_array($this->uploadDir . 'imsanifest.xml', $this->zipContent) )
        {
            return $this->uploadDir . 'imsanifest.xml';
        }
        
        // if we do not find it at top level search the 'toper' imsmanifest.xml file
        // we have seen packages that had all files nested in a subdirectory ...
        $deep = 9999;
        $manifestPath = '';
        foreach( $this->zipContent as $thisFile )
        {
            // check that file is imsmanifest.xml (char(15))
            if( strtolower( substr($thisFile, -15) ) == 'imsmanifest.xml' )
            {
                // get deep of the file (counting number of subdirectories)
                $thisFileDeep = count( explode('/', $thisFile) );
                if( $thisFileDeep < $deep )
                {
                    $manifestPath = $thisFile;
                }
            } 
        }
        return $manifestPath;
    }
       
    function parseManifest($manifestPath)
    {
        $data = file_get_contents($manifestPath);

        $this->manifestContent = xmlize($data);
        if( ! is_array($this->manifestContent) ) 
        {
            // xmlize returns array or error message
            $this->backlog->debug($this->manifestContent);
            $this->backlog->failure(get_lang('Unable to read XML file'));
            return false;
        }
        
        //var_dump($manifestContent);
        
        // we need default organization identifier, fond in organizations 'default' attribute
        // so we will be able to skip any other organization
        $defaultOrganizationId = $this->manifestContent['manifest']['#']['organizations'][0]['@']['default'];
        $organizationList = &$this->manifestContent['manifest']['#']['organizations'][0]['#'];
        
        if( is_array($organizationList) )
        {
            foreach( $organizationList as $organization )
            {
                // take care only of the default organization
                if( $organization[0]['@']['identifier'] != $defaultOrganizationId ) continue;
                
                $organizationTitle = trim($organization[0]['#']['title'][0]['#']);
                var_dump($organizationTitle);
                var_dump($organization);
//                $organizationList = &$organization['manifest']['#']['organizations'][0]['#'];
            }
            // also catch error when no organization has the default identifier
        }
        else
        {
            $this->backlog->failure('No organization in manifest.');
            return false;
        }
        
        
        
        return true;
    }   
    
    function clean($step = 0)
    {
        // for each step we have to clean all step before 
        switch( $step )
        {
            case 4 : 
            case 3 : 
            case 2 :
            case 1 :
                echo "clean step 1";
                claro_delete_file($this->uploadDir);
            default : 
                break;
        }
        
        return true;            
    }
}
?>
