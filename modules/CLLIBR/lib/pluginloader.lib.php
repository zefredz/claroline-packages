<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads "plugged in" resources
 * Plugins names must be something like "{plugintype}.{pluginname}.plugin.php"
 * @protected string $pluginDir
 * @protected array $pluginList
 */
class PluginLoader
{
    protected $pluginDir;
    protected $pluginList = array();
    
    /**
     * Constructor
     * @param string $pluginDir
     */
    public function __construct( $pluginDir )
    {
        $this->pluginDir = $pluginDir;
    }
    
    /**
     * gets plugin list (names)
     * @return array $pluginList
     */
    public function getPluginList()
    {
        if ( empty( $this->pluginList ) )
        {
            $this->loadPlugins();
        }
        
        return $this->pluginList;
    }
    
    /**
     * Searches for plugins in the plugins directory
     * @return array $pluginList
     */
    public function loadPlugins()
    {
        $pluginsRepository = new DirectoryIterator( $this->pluginDir );
        
        foreach( $pluginsRepository as $plugin )
        {
            if( ! $plugin->isDot() )
            {
                $fileName = $plugin->getFileName();
                $part = explode( '.' , $fileName );
                $pluginEntry = $this->pluginDir . $fileName;
                
                if( $plugin->isDir() )
                {
                    $pluginEntry .= '/entry.php';
                }
                
                if ( count( $part ) > 2 && $part[ 2 ] == 'plugin' )
                {
                    try
                    {
                        require( $pluginEntry );
                        
                        $pluginName = $part[ 1 ];
                        $pluginType = $part[ 0 ];
                        
                        $this->pluginList[ $pluginType ][] = $pluginName;
                    }
                    catch( Exception $e )
                    {
                        return $e->getMessage();
                    }
                }
            }
        }
    }
    
    /**
     * Controls if the specified plugin exists
     * @param string $type
     * @param string $name
     * @return boolean true if exists
     */
    public function pluginExists( $type , $name )
    {
        return in_array( $name , $this->pluginList[ $type ] );
    }
    
    /**
     * Gets plugins by its name
     * @param string $name : the plugin's name
     * @return object : a plugin's instance
     */
    public function getPlugin( $name )
    {
        foreach( $this->pluginList as $pluginType )
        {
            if( $this->pluginExists( $name , $pluginType ) )
            {
                return new $name;
            }
        }
    }
}