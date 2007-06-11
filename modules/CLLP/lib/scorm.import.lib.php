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
    
    var $manifestContent = '';
    var $insertedPath;
    var $insertedItemList = array();
    
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
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
            clean($step); 
            return false;
        }
        $step++;
        
        // step 2
        // extract uploaded file to tmp dir
        if( !$this->unzip() )
        {
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
            $this->clean($step);
            return false;
        }
        $step++;
        
        // step 3
        // store all the list of files contained in the extracted zip for easier use
        if( !$this->buildFileList() )
        {
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
            $this->clean($step);
            return false;
        }   
        $step++;
        
        // step 4
        // find manifest file
        $manifestPath = $this->findMainManifest();
        if( $manifestPath == '' )
        {
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
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
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
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
            return $this->uploadDir . 'imsmanifest.xml';
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
                    // keep only the less deep
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
        
       // var_dump($this->manifestContent);
        
        // check if we have a xml:base in manifest
        
        if( isset($this->manifestContent['manifest']['@']['xml:base']) )
        {
            $manifestXmlBase = $this->manifestContent['manifest']['@']['xml:base']; 
        }
        else
        {
            $manifestXmlBase = '';
        }
        
        // we need default organization identifier, fond in organizations 'default' attribute
        // so we will be able to skip any other organization
        $defaultOrganizationId = $this->manifestContent['manifest']['#']['organizations'][0]['@']['default'];
        $organizationList = &$this->manifestContent['manifest']['#']['organizations'][0]['#']['organization'];
        
        if( is_array($organizationList) )
        {
            foreach( $organizationList as $organization )
            {
                // take care only of the default organization
                // TODO handle several organizations ?
                if( $organization['@']['identifier'] == $defaultOrganizationId ) 
                {
                    $organizationTitle = trim($organization['#']['title'][0]['#']);
                    $defaultOrganization = &$organization;
                    break;
                }
                else
                {
                    continue;                    
                }

                // $organizationList = &$organization['manifest']['#']['organizations'][0]['#'];
            }
            // TODO catch error when no organization has the default identifier
        }
        else
        {
            $this->backlog->failure('No organization in manifest.');
            return false;
        }
        
        // create a path from the organization
        $this->insertedPath = new path();
        $this->insertedPath->setTitle($organizationTitle);
        $this->insertedPath->setInvisible();
        // todo handle identifier ? type ? encoding ? viewmode ?
        
        if( $this->insertedPath->validate() )
        {
            if( $this->insertedPath->save() )
            {
                $this->backlog->success(get_lang('New path created : %pathTitle', array('%pathTitle' => $organizationTitle)));
            }
            else
            {
                $this->backlog->failure(get_lang('Fatal error : cannot save path'));
                return false;
            }
        }
        else
        {
            $this->backlog->failure(get_lang('Cannot save path : informations missing.'));
            return false;
        }

        $this->addItems($defaultOrganization['#']['item']);
        return true;
    }   
    
    function addItems(&$itemList, $parentId = -1)
    {
        // go through all item ..['item'][0] ['item'][1] ...
        foreach( $itemList as $item )
        {
            $insertedItem = new item();
            $insertedItem->setTitle($item['#']['title'][0]['#']);
            $insertedItem->setIdentifier($item['@']['identifier']);
            $insertedItem->setPathId($this->insertedPath->getId());
            
            // parent 
            if( $parentId > -1 )
            {
                $insertedItem->setParentId($parentId);
            }
            
            // visibility
            if( isset($item['@']['isvisible']) && $item['@']['isvisible'] == 'true' )
            {
                $insertedItem->setInvisible();
            }
            else
            {
                $insertedItem->setVisible(); // IMS consider that the default value of 'isvisible' is true
            }
            
            // set sys path
            if( isset($item['@']['identifierref']) )
            {
                if( $resourceRef = $this->getResourceByRef($item['@']['identifierref']) )
                {
                    $insertedItem->setSysPath($resourceRef['@']['href']);
                }
            }
            
            // chapter or module
            if( isset($item['#']['item']) )
            {
                $insertedItem->setType('CONTAINER');
            }

            if( $insertedItem->validate() )
            {
                if( $insertedItem->save() )
                {
                    $this->backlog->success(get_lang('New item created : %pathTitle', array('%pathTitle' => $insertedItem->getTitle())));
                }
                else
                {
                    $this->backlog->failure(get_lang('Fatal error : cannot save item'));
                    return false;
                }
            }
            else
            {
                $this->backlog->failure(get_lang('Cannot save item : informations missing.'));
                return false;
            }            
            
            if( isset($item['#']['item']) )
            {
                $this->addItems($item['#']['item'], $insertedItem->getId());
            }
                   
        }
    }
    
    function getResourceByRef($identifierref)
    {
        $resourceList = &$this->manifestContent['manifest']['#']['resources'][0]['#']['resource'];
        
        foreach( $resourceList as $resource )
        {
            if( $resource['@']['identifier'] == $identifierref )
            {
                return $resource;
            }
            // else continue
        }
        
        // not found
        return false;
    }
    
    function clean($step = 0)
    {
        // for each step we have to clean all step before 
        switch( $step )
        {
            case 5 :
                // delete inserted path
                $this->insertedPath->delete();
                // delete
            case 4 : 
            case 3 : 
            case 2 :
            case 1 :
                claro_delete_file($this->uploadDir);
            default : 
                break;
        }
        
        return true;            
    }
}

?>
