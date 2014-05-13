<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * 
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     CLCFG
 */

class ICCRSCFG_Configuration
{
    protected
        $courseConfig = array(),
        $platformConfig = array(),
        $fullConfig = array();
    
    public function __construct( $courseId )
    {
        $this->loadCourseConfig($courseId, 'CLDOC');
        $this->loadCourseConfig($courseId, 'CLWRK');
        
        $this->courseConfig = array(
            'maxFilledSpace_for_course' => isset( $GLOBALS['maxFilledSpace_for_course'] ) ? $GLOBALS['maxFilledSpace_for_course'] : null,
            'maxFilledSpace_for_groups' => isset( $GLOBALS['maxFilledSpace_for_groups'] ) ? $GLOBALS['maxFilledSpace_for_groups'] : null,
            'openNewWindowForDoc' => isset( $GLOBALS['openNewWindowForDoc'] ) ? $GLOBALS['openNewWindowForDoc'] : null,
            'cldoc_allowAnonymousToDownloadFolder' => isset( $GLOBALS['openNewWindowForDoc'] ) ? $GLOBALS['openNewWindowForDoc'] : null,
            'cldoc_allowNonManagersToDownloadFolder' => isset( $GLOBALS['openNewWindowForDoc'] ) ? $GLOBALS['openNewWindowForDoc'] : null,
            'max_file_size_per_works' => isset( $GLOBALS['max_file_size_per_works'] ) ? $GLOBALS['max_file_size_per_works'] : null,
            'maxFilledSpace' => isset( $GLOBALS['maxFilledSpace'] ) ? $GLOBALS['maxFilledSpace'] : null,
        );
        
        if ( isset( $GLOBALS['maxFilledSpace_for_course'] ) ) unset ( $GLOBALS['maxFilledSpace_for_course'] );
        if ( isset( $GLOBALS['maxFilledSpace_for_groups'] ) ) unset ( $GLOBALS['maxFilledSpace_for_groups'] );
        if ( isset( $GLOBALS['openNewWindowForDoc'] ) ) unset ( $GLOBALS['openNewWindowForDoc'] );
        if ( isset( $GLOBALS['cldoc_allowAnonymousToDownloadFolder'] ) ) unset ( $GLOBALS['cldoc_allowAnonymousToDownloadFolder'] );
        if ( isset( $GLOBALS['cldoc_allowNonManagersToDownloadFolder'] ) ) unset ( $GLOBALS['cldoc_allowNonManagersToDownloadFolder'] );
        if ( isset( $GLOBALS['max_file_size_per_works'] ) ) unset ( $GLOBALS['max_file_size_per_works'] );
        if ( isset( $GLOBALS['maxFilledSpace_for_groups'] ) ) unset ( $GLOBALS['maxFilledSpace'] );
        
        $this->loadPlatformConfFile('CLDOC');
        $this->loadPlatformConfFile('CLWRK');
        
        $this->platformConfig = array(
            'maxFilledSpace_for_course' => $GLOBALS['maxFilledSpace_for_course'],
            'maxFilledSpace_for_groups' => $GLOBALS['maxFilledSpace_for_groups'],
            'openNewWindowForDoc' => $GLOBALS['openNewWindowForDoc'],
            'cldoc_allowAnonymousToDownloadFolder' => $GLOBALS['cldoc_allowAnonymousToDownloadFolder'],
            'cldoc_allowNonManagersToDownloadFolder' => $GLOBALS['cldoc_allowNonManagersToDownloadFolder'],
            'max_file_size_per_works' => $GLOBALS['max_file_size_per_works'],
            'maxFilledSpace' => isset( $GLOBALS['maxFilledSpace'] ) ? $GLOBALS['maxFilledSpace'] : 100000000,
        );
        
        $this->loadCourseConfig($courseId, 'CLDOC');
        $this->loadCourseConfig($courseId, 'CLWRK');
        
        $this->fullConfig = array(
            'maxFilledSpace_for_course' => $GLOBALS['maxFilledSpace_for_course'],
            'maxFilledSpace_for_groups' => $GLOBALS['maxFilledSpace_for_groups'],
            'openNewWindowForDoc' => $GLOBALS['openNewWindowForDoc'],
            'cldoc_allowAnonymousToDownloadFolder' => $GLOBALS['cldoc_allowAnonymousToDownloadFolder'],
            'cldoc_allowNonManagersToDownloadFolder' => $GLOBALS['cldoc_allowNonManagersToDownloadFolder'],
            'max_file_size_per_works' => $GLOBALS['max_file_size_per_works'],
            'maxFilledSpace' => isset( $GLOBALS['maxFilledSpace'] ) ? $GLOBALS['maxFilledSpace'] : 100000000,
        );
    }
    
    public function getCourseEffectiveConfiguration()
    {
        return $this->fullConfig;
    }
    
    public function getCourseConfiguration()
    {
        return $this->courseConfig;
    }
    
    public function getPlatformConfiguration()
    {
        return $this->platformConfig;
    }
    
    protected function loadPlatformConfFile( $config_code )
    {
        if (file_exists(claro_get_conf_repository() . $config_code . '.conf.php'))
        {
            include claro_get_conf_repository() . $config_code . '.conf.php';
        }
    }
    
    protected function loadCourseConfig( $courseId, $config_code )
    {
        $_course = claro_get_course_data( $courseId );
        
        if (file_exists( get_conf('coursesRepositorySys')
            . $_course['path'] . '/conf/' . $config_code . '.conf.php' ) )
        {
            include get_conf('coursesRepositorySys') . $_course['path'] 
                . '/conf/' . $config_code . '.conf.php';
        }
    }
    
    public function writeConfig( $courseId, $config )
    {
        $this->writeCourseModuleConfig( $courseId, 'CLDOC', $config['CLDOC'] );
        $this->writeCourseModuleConfig( $courseId, 'CLWRK', $config['CLWRK'] );
    }
    
    protected function writeCourseModuleConfig( $courseId, $config_code, $configArray )
    {
        $_course = claro_get_course_data( $courseId );
        
        $courseConfigFolder = get_conf('coursesRepositorySys')
            . $_course['path'] . '/conf/';
            
        $courseModuleConfigPath = $courseConfigFolder . $config_code . '.conf.php';
        
        if ( empty ( $configArray ) && !file_exists($courseModuleConfigPath) )
        {
            pushClaroMessage("No configuration to write for {$config_code} in {$courseId}",'debug');
            return false; // nothing to do
        }
        elseif ( empty ( $configArray ) && file_exists($courseModuleConfigPath) )
        {
            if( !claro_delete_file( $courseModuleConfigPath ))
            {
                throw new Exception("Cannot delete configuration file {$courseModuleConfigPath}");
            }
        }
        else
        {
            if ( !file_exists( $courseConfigFolder ) )
            {
                if ( !claro_mkdir( $courseConfigFolder, CLARO_FILE_PERMISSIONS ))
                {
                    throw new Exception("Cannot create config folder {$courseConfigFolder}");
                }
            }
            
            $contents = '<'.'?php'."\n\n";
            
            foreach ( $configArray as $name => $value )
            {
                if ( is_bool($value) )
                {
                    if ( $value )
                    {
                        $valueToWrite = 'true';
                    }
                    else
                    {
                        $valueToWrite = 'false';
                    }
                }
                elseif ( is_numeric( $value ) )
                {
                    $valueToWrite = $value;
                }
                else //string
                {
                    $valueToWrite = "'$value'";
                }
                
                $contents .= '$GLOBALS[\''.$name.'\'] = '. $valueToWrite .';'."\n";
                
                $contents .= "\n\n";
            }
            
            file_put_contents( $courseModuleConfigPath, $contents );
            
            $debugContents = var_export( $contents, true );
            
            pushClaroMessage("Configuration written for {$config_code} in {$courseId} : {$debugContents}",'debug');
            
            return true;
        }
    }
}
