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
    var $uploadDir = '';
    var $scormDir = '';
    var $pathDir = '';
    var $uploadPath = '';

    var $manifestContent = '';
    var $path;
    var $itemList = array();

	var $manifestXmlBase = '';

    var $backlog;

    function ScormImporter(&$uploadedZipFile)
    {
        $this->uploadedZipFile = $uploadedZipFile;

        // create an empty path
        $this->path = new path();

        // paths
        $this->scormDir = get_path('coursesRepositorySys') . claro_get_course_path() . '/scormPackages/';

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
        //  - find all manifest (several manifests is still according ADL so find only one)
        //  - check if ressources really exists in extracted file
        //  - save path and items
        if( ! $this->parseManifest($manifestPath) )
        {
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
            $this->clean($step);
            return false;
        }
        $step++;

        // step 6
        // copy files from tmp folder to correct path_X folder
        if( ! $this->renameDir() )
        {
            $this->backlog->failure(get_lang('Import failed at step %step', array('%step' => $step)));
            $this->clean($step);
            return false;
        }
        $step++;

        // import is successfull
        return true;
    }

    function createUploadDir()
    {
        $this->uploadDir = claro_mkdir_tmp($this->scormDir, 'path_');

        if( $this->uploadDir === false )
        {
            $this->backlog->failure(get_lang('Cannot create upload directory.'));
            return false;
        }

        return true;
    }

    function unzip()
    {
        // upload path is the difference between scormDir and uploadDir / something like
        $this->uploadPath = str_replace($this->scormDir,'',$this->uploadDir);

        if( !isset($this->uploadedZipFile) || !is_uploaded_file($this->uploadedZipFile['tmp_name']))
        {
            // upload failed
            $this->backlog->failure(get_lang('Choose a file to upload.'));
            return false;
        }

        include_once get_path('incRepositorySys') . '/lib/pclzip/pclzip.lib.php';


        if ( preg_match('/.zip$/i', $this->uploadedZipFile['name'])
            && treat_uploaded_file($this->uploadedZipFile, $this->scormDir, $this->uploadPath, get_conf('maxFilledSpace' , 100000000), 'unzip', true))
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
        if( in_array($this->uploadDir . 'imsmanifest.xml', $this->zipContent) )
        {
            return $this->uploadDir . 'imsmanifest.xml';
        }

        // if we do not find it at top level search the 'toper' imsmanifest.xml file
        // we have seen packages that had all files nested in a subdirectory ...
        $deep = 9999;
        $manifestPath = '';
        foreach( $this->zipContent as $thisFile )
        {
            // check that file is imsmanifest.xml
            if( strtolower(basename($thisFile)) == 'imsmanifest.xml' )
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

		// try to discover SCORM version
		if( isset($this->manifestContent['manifest']['#']['metadata'][0]['#']['schemaversion'][0]['#']) )
		{
			$schemaVersion = $this->manifestContent['manifest']['#']['metadata'][0]['#']['schemaversion'][0]['#'];

			if( preg_match("/^(CAM )?(1\.3)$/", $schemaVersion, $matches)
				|| $schemaVersion == '2004 3rd Edition' )
			{
				$this->path->setVersion('scorm13');
			}
			else
			{
				$this->path->setVersion('scorm12');
			}
		}
		else
		{
			$this->path->setVersion('1.2');
		}

        // check if we have a xml:base in manifest

        if( isset($this->manifestContent['manifest']['@']['xml:base']) )
        {
            $this->manifestXmlBase = $this->manifestContent['manifest']['@']['xml:base'];
        }
        else
        {
            $this->manifestXmlBase = '';
        }

        // we need default organization identifier, fond in organizations 'default' attribute
        // so we will be able to skip any other organization
        // but if there is no default organization set we will take the first of the list
        if( !empty($this->manifestContent['manifest']['#']['organizations'][0]['@']['default']) )
        {
        	$defaultOrganizationId = $this->manifestContent['manifest']['#']['organizations'][0]['@']['default'];
        }
        else
        {
        	$defaultOrganizationId = '';
        }

        $organizationList = &$this->manifestContent['manifest']['#']['organizations'][0]['#']['organization'];

        if( is_array($organizationList) )
        {
            foreach( $organizationList as $organization )
            {
                // take care only of the default organization
                // take the 1rst one if defaultOrganizationId has not been found
                if( $organization['@']['identifier'] == $defaultOrganizationId || empty($defaultOrganizationId) )
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

        // fill path data
        $this->path->setTitle($organizationTitle);
        $this->path->setInvisible();
        // todo handle identifier ? type ? encoding ? viewmode ?

        if( $this->path->validate() )
        {
            if( $this->path->save() )
            {
                $this->backlog->success(get_lang('Path created : %pathTitle', array('%pathTitle' => $organizationTitle)));
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
    	$resources = &$this->manifestContent['manifest']['#']['resources'];

    	// resources xml base
		if( isset($resources['@']['xml:base']) )
        {
            $resourcesXmlBase = $resources['@']['xml:base'];
        }
        else
        {
            $resourcesXmlBase = '';
        }

        // go through all item ..['item'][0] ['item'][1] ...
        foreach( $itemList as $item )
        {
            $insertedItem = new item();
            $insertedItem->setTitle($item['#']['title'][0]['#']);
            $insertedItem->setIdentifier($item['@']['identifier']);
            $insertedItem->setPathId($this->path->getId());
            $insertedItem->setType('SCORM');

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
            if( isset($item['@']['identifierref']) && !empty($item['@']['identifierref']) )
            {
                $resourceRef = $this->getResourceByRef($item['@']['identifierref']);

				// resource xml base
				if( isset($resourceRef['@']['xml:base']) )
		        {
		            $resourceXmlBase = $resourceRef['@']['xml:base'];
		        }
		        else
		        {
		            $resourceXmlBase = '';
		        }

                if( is_array($resourceRef) && isset($resourceRef['@']['href']) )
                {
                	// full path is the sum of all xml:base
                    $itemPath = $this->manifestXmlBase . $resourcesXmlBase . $resourceXmlBase . $resourceRef['@']['href'];

                    // parameters
                    if( !empty($item['@']['parameters']) )
                    {
                        if( substr($item['@']['parameters'],0,1) == '#' || substr($item['@']['parameters'],0,1) == '?' )
                        {
                            // anchor or url parameters
                            $itemPath .= $item['@']['parameters'];
                        }
                        else
                        {
                            // url parameters but ? is missing
                            $itemPath .= '?' . $item['@']['parameters'];
                        }
                    }

                    $insertedItem->setSysPath($itemPath);
                }
                else
                {
                    $this->backlog->failure(get_lang('An item has an reference to a ressource but that ressource cannot be find.'));
                    return false;
                }
            }
            else
            {
                $insertedItem->setSysPath('');
                // no associated ressource
            }

            // time limit action
            if( !empty($item['#']['adlcp:timeLimitAction']) )
            {
                $insertedItem->setTimeLimitAction($item['#']['adlcp:timeLimitAction']);
            }

            // launch data
            if( !empty($item['#']['adlcp:dataFromLms']) )
            {
                $insertedItem->setLaunchData($item['#']['adlcp:dataFromLms']);
            }

            // completionThreshold
            if( !empty($item['#']['adlcp:completionThreshold']) )
            {
                $insertedItem->setCompletionThreshold($item['#']['adlcp:completionThreshold']);
            }

            // chapter or module
            if( isset($item['#']['item']) && is_array($item['#']['item']) )
            {
                $insertedItem->setType('CONTAINER');
            }

            // try to save new item
            if( $insertedItem->validate() )
            {
                if( $insertedItem->save() )
                {
                    $this->backlog->success(get_lang('Item created : %pathTitle', array('%pathTitle' => $insertedItem->getTitle())));

                    // add object to pile
                    $this->itemList[] = $insertedItem;
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

            // get 'children' of this item  if any
            if( isset($item['#']['item']) )
            {
                $this->addItems($item['#']['item'], $insertedItem->getId());
            }

        }
    }

    function getResourceByRef($identifierref)
    {
        $resourceList = &$this->manifestContent['manifest']['#']['resources'][0]['#']['resource'];

        if( is_array($resourceList) )
        {
            foreach( $resourceList as $resource )
            {
                if( $resource['@']['identifier'] == $identifierref )
                {
                    return $resource;
                }
                // else continue
            }
        }

        // not found
        return false;
    }

    function renameDir()
    {
        // rename file from its tmp name to path_X where X is the path id
        if( ! claro_rename_file($this->scormDir . $this->uploadPath.'/', $this->scormDir . 'path_'.$this->path->getId().'/') )
        {
            $this->backlog->failure(get_lang('Cannot rename tmp folder.'));
            return false;
        }

        return true;

    }

    function clean($step = 0)
    {
        // for each step we have to clean all step before
        switch( $step )
        {
            case 6 :
            case 5 :
                // delete inserted path - this should also delete created items
                $this->path->delete();
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
