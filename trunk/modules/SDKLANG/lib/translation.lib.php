<?php

require_once get_path('includePath') . '/lib/module.manage.lib.php';

/**
 * Get Claroline Version
 */

function get_claroline_version ()
{
    $new_version = null;

    include ( get_path('incRepositorySys') . '/installedVersion.inc.php' ) ;

    return $new_version;
}

/**
 * Get the language list
 */

function get_kernel_language_list ($path)
{
    $language_list = array();

    $dirname = realpath($path) . '/' ;

    if ( is_dir($dirname) )
    {
        $handle = opendir($dirname);
        while ( false !== ($elt = readdir($handle) ) )
        {
            // skip '.', '..' and 'CVS'
            if ( $elt == '.' || $elt == '..' || $elt == 'CVS' ) continue;

            if ( is_dir($dirname.$elt) )
            {
                if ( file_exists($dirname.$elt.'/complete.lang.php' ) )
                {
                    $language_list[] = $elt;
                }
            }
        }
    }
    return $language_list;
}

/**
 *
 */

function get_kernel_translation_source_info ()
{
    $source_info = array();

    $source_info['version'] = get_claroline_version();
    $source_info['path'] = get_path('rootSys');
    $source_info['path_lang'] = $source_info['path'] . '/claroline/lang' ;
    $source_info['language_list'] = get_kernel_language_list($source_info['path_lang']);

    return $source_info;
}

/**
 * 
 */

function get_module_translation_source_info ($moduleId)
{
    $source_info = array();

    $module_info = get_module_info($moduleId);

    if ( $module_info )
    {
        $source_info['name'] = $module_info['module_name'];
        $source_info['version'] = $module_info['version'];
        $source_info['path'] = get_module_path($module_info['label']);
        $source_info['path_lang'] = $source_info['path'] . '/lang' ;
        $source_info['language_list'] = array();
    }
}

/**
 *
 */

function extract_kernel_translation ($path, $language)
{
    $translation_list = array();

    $_lang = array();

    include($fileName);
}

/**
 *
 */

function extract_kernel_message ( $path )
{
    $messageList = array();

    if ( is_dir($path) )
    {
        $forbiddenDirList = array( get_path('rootSys') . 'claroline/lang',
                                   get_path('rootSys') . 'claroline/install',
                                   get_path('rootSys') . 'claroline/inc/conf',
                                   get_path('rootSys') . 'courses',
                                   get_path('rootSys') . 'platform',
                                   get_path('rootSys') . 'modules',
                                   get_path('rootSys') . 'tmp',
                                   get_path('rootSys') . 'claroline/admin/devTools',
                                   get_path('rootSys') . 'claroline/claroline_garbage');

        $moduleList = get_installed_module_list();

        foreach ( $moduleList as $module)
        {
            $modulePath = get_module_path($module) ;
            if ( is_dir($modulePath) )
            {
                $forbiddenDirList[] = $modulePath;
            }
        }
        
        $scan= scan_dir($path,$recurse=TRUE,$forbiddenDirList,true);

        $fileList = $scan['files'];

        foreach ( $fileList as $file )
        {
            $messageFileList = get_message_from_script($file,$forbiddenDirList);
            $messageList = array_merge($messageFileList, $messageList);
        }
        $messageList = array_unique($messageList);
    }

    return $messageList ;
}

function extract_module_message ( $path )
{
    $forbiddenDirList = array( $path . '/setup',
                               $path . '/lang');

    $messageList = array();
    
    $scan= scan_dir($path,$recurse=TRUE,$forbiddenDirList,true);

    $fileList = $scan['files'];

    foreach ( $fileList as $file )
    {
        $messageFileList = get_message_from_script($file,$forbiddenDirList);
        $messageList = array_merge($messageFileList, $messageList);
    }

    $messageList = array_unique($messageList);

    return $messageList ;
}

/**
 * Browse a dirname and returns all files and subdirectories
 *
 * @return - array('files'=>array(), 'directories=>array())
 *
 * @param  - string $dirname
 * @param  - boolean $recurse
 */

function scan_dir($dirname,$recurse,$forbiddenDirList,$reset)
{
    static $file_array=array();
    static $dir_array=array();
    static $ret_array=array();
    
    if ($reset === true)
    {
        $file_array = array();
        $dir_array = array();
        $ret_array = array();    
    }

    if($dirname[strlen($dirname)-1]!='/')
    {
        $dirname.='/';
    }

    $handle=opendir($dirname);

    while (false !== ($element = readdir($handle)))
    {
        if ( is_scannable($dirname.$element,$forbiddenDirList) )
        {
            if(is_dir($dirname.$element))
            {
                $dir_array[]=$dirname.$element;

                if($recurse)
                {
                    scan_dir($dirname.$element.'/',$recurse,$forbiddenDirList,false);
                }
            }
            else
            {
                $file_array[]=$dirname.$element;
            }
        }
    }

    closedir($handle);

    $ret_array['files']=$file_array;
    $ret_array['directories']=$dir_array;

    return $ret_array;

}

/**
 * Get the list of language variables in a script and its included files
 *
 * @return - array $languageVarList or boolean FALSE
 * @param - string $file
 */

function get_message_from_script ($file, $forbiddenDirList)
{
    $messageList = array();

    $languageVarList = array();

    $sourceFile = file_get_contents($file);
    $tokenList  = token_get_all($sourceFile);

    $messageList = detect_get_lang_message($tokenList);

    return $messageList;
}

/**
 * Extract the parameter name of get_lang function from a script
 *
 * @return - array $languageVarList
 * @param  - array $tokenList
 */

function detect_get_lang_message($tokenList)
{
    $messageList = array();

    $total_token = count($tokenList);

    $i = 0;

    // Parse token list

    while ( $i < $total_token )
    {
        $thisToken = $tokenList[$i];

        if ( is_array($thisToken) && is_int($thisToken[0]) && $thisToken[0] == T_STRING )
        {

            // find function 'get_lang'

            if ( $thisToken[1] == 'get_lang' || $thisToken[1] == 'get_block' )
            {
                $varName = '';

                $i++;

                // Parse get_lang function parameters

                while ($i < $total_token)
                {
                    $thisToken = $tokenList[$i];

                    if ( is_string($thisToken) && $thisToken == '(')
                    {
                        // bracket open - begin parsong of parameters
                        $i++;
                        continue;
                    }
                    elseif ( is_string($thisToken) && $thisToken == ')')
                    {
                        // bracket close - end parsing of parameters
                        $i++;
                        break;
                    }
                    elseif ( is_string($thisToken) && $thisToken == ',')
                    {
                        // comma, end parsing of parameters
                        $i++;
                        break;
                    }
                    elseif ( is_int($thisToken[0]) && ( $thisToken[0] == T_VARIABLE ) )
                    {
                        // variable - end parsing
                        $i++;
                        break;
                    }
                    elseif ( is_array($thisToken) )
                    {
                        // get parameters name
                        if ( $thisToken[0] == T_CONSTANT_ENCAPSED_STRING )
                        {
                            $search = array ('/^[\'"]/','/[\'"]$/','/\134\047/');
                            $replace = array('','','\'');
                            $varName .= preg_replace($search,$replace,$thisToken[1]);
                        }

                    }
                    $i++;
                }
                $varName = trim($varName);
                if ( !empty($varName) )
                {
                    $messageList[]=$varName;
                }
            }
        }
        $i++;

    } // end token parsing

    $messageList = array_unique($messageList);

    return $messageList;
}

/**
 * Check if the file or directory is an element scannable
 *
 * @return - boolean
 * @param  - string
 * @param  - array
 * @param  - array
 */

function is_scannable($filePath,
                      $additionnalForbiddenDirNameList = array(),
                      $additionnalForbiddenFileSuffixList = array() )
{
    global $rootSys;

    $baseName    = basename($filePath);
    $parentPath  = str_replace('\\', '/', dirname($filePath));
    $parentPath  = str_replace($rootSys, '', $parentPath);

    $forbiddenDirNameList    = array_merge( array(),
                                            $additionnalForbiddenDirNameList);
    $forbiddenParentNameList = array('CVS');

    $forbiddenFileNameList   = array('.', '..','CVS');

    $forbiddenBaseNameList   = array_merge($forbiddenFileNameList,
                                           $forbiddenDirNameList);

    $forbiddenFileSuffixList = array_merge( array('.lang.php', '~'),
                                            $additionnalForbiddenFileSuffixList);

    $forbiddenFilePrefixList = array('~', '#', '\\.');

    // BASENAME CHECK

    if (is_file($filePath) && ! preg_match('/.php$/i',$baseName) ) return false;

    if (in_array($baseName, $forbiddenBaseNameList) )              return false;

    foreach($forbiddenFileSuffixList as $thisForbiddenSuffix)
    {
        if (preg_match('|'.$thisForbiddenSuffix.'^|', $baseName) ) return false;
    }

    foreach($forbiddenFilePrefixList as $thisForbiddenPrefix)
    {
        if (preg_match('|$'.$thisForbiddenPrefix.'|', $baseName) ) return false;
    }

    // DIRECTORY CHECK
    foreach($forbiddenDirNameList as $thisDirName)
    {
        if ( strpos($filePath, $thisDirName) !== FALSE )
        {
            return false;
        }
    }

    // PARENT PATH CHECK

    $pathComponentList = explode('/', $parentPath);

    foreach($pathComponentList as $thisPathComponent)
    {
        if (in_array($thisPathComponent, $forbiddenParentNameList) ) return false;
    }

    return true;
}

/**
 * Get the list of language variables in a script and its included files
 *
 * @return - array $languageVarList or boolean FALSE
 * @param - string $file
 */

function extract_message_from_definition_file ($file)
{
    // initialise local variable
    $conf_def['section'] = array();
    $conf_def_property_list = array();

    $translationList = array();

    // include definition file
    include($file);

    // get configuration name
    if ( array_key_exists('config_name',$conf_def) )
    {
        $translationList[] = $conf_def['config_name'];
    }

    // get label and description of the section
    if ( is_array($conf_def['section']) )
    {
        foreach ($conf_def['section'] as $conf_def_section)
        {
            if (array_key_exists('label',$conf_def_section)) $translationList[] = $conf_def_section['label'];
            if (array_key_exists('description',$conf_def_section)) $translationList[] = $conf_def_section['description'];
        }
    }

    // get properties message
    if ( is_array($conf_def_property_list) )
    {
        foreach ( $conf_def_property_list as $conf_def_property )
        {
            // if display false, skip this property
            if ( array_key_exists('display',$conf_def_property) && $conf_def_property['display'] === false ) continue ;

            // get label of the property
            if ( array_key_exists('label',$conf_def_property) ) $translationList[] = $conf_def_property['label'];

            // get description of the property
            if ( array_key_exists('description',$conf_def_property) ) $translationList[] = $conf_def_property['description'];

            // get type of the property
            if ( array_key_exists('type',$conf_def_property) ) $translationList[] = $conf_def_property['type'];

            // get the accepted values of the property
            if ( array_key_exists('acceptedValue',$conf_def_property) )
            {
                foreach ($conf_def_property['acceptedValue'] as $key => $acceptedValue)
                {
                    if ( $conf_def_property['type'] == 'integer' )
                    {
                        continue ;
                    }
                    elseif ( $key === 'pattern' )
                    {
                        continue ;
                    }
                    else
                    {
                        $translationList[] = $acceptedValue;
                    }
                }
            }
        }
    }

    $translationList = array_unique($translationList);

    return $translationList;

}

?>
