<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.2.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads "plugged in" resources
 * Plugins names must be something like "{pluginname}.plugin.php"
 * @protected string $pluginDir
 * @protected string $configFile
 * @protected array $pluginList
 */
class PluginLoader
{
    protected $pluginDir;
    protected $configFile;
    protected $pluginList = array();
    
    /**
     * Constructor
     * @param string $pluginDir
     */
    public function __construct( $pluginDir , $configFile )
    {
        $this->pluginDir = $pluginDir;
        $this->configFile = $configFile;
        $this->loadActiveList();
        $this->loadPlugins();
        
    }
    
    /**
     * Gets plugin list (names)
     * @return array $pluginList
     */
    public function getPluginList( $active_only = true )
    {
        $pluginList = $this->pluginList;
        
        if ( $active_only )
        {
            foreach( $pluginList as $pluginName => $plugin )
            {
                if ( ! $plugin ) unset( $pluginList[ $pluginName ] );
            }
        }
        
        return $pluginList;
    }
    
    /**
     * Searchs for plugins in the plugins directory
     * and try to instanciate them
     */
    private function loadPlugins()
    {
        $pluginsRepository = new DirectoryIterator( $this->pluginDir );
        
        foreach( $pluginsRepository as $plugin )
        {
            $fileName = $plugin->getFileName();
            
            if ( ! $plugin->isDir() && ! $plugin->isDot() )
            {
                $fileName = $plugin->getFileName();
                $part = explode( '.' , $fileName );
                
                if ( $part[ 1 ] == 'plugin' && $part[ 2 ] == 'php' )
                {
                    try
                    {
                        $pluginName = ucwords( $part[ 0 ] );
                        $className = $pluginName . 'Plugin';
                        
                        if ( ! array_key_exists( $pluginName , $this->pluginList ) )
                        {
                            $this->pluginList[ $pluginName ] = true;
                        }
                        
                        if ( $this->pluginList[ $pluginName ] )
                        {
                            require( $this->pluginDir . $fileName );
                            $this->pluginList[ $pluginName ] = new $className;
                        }
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
     * Loads the active plugins list
     */
    private function loadActiveList()
    {
        if ( file_exists( $this->configFile ) )
        {
            $pluginList = unserialize( file_get_contents( $this->configFile ) );
            return $this->pluginList = $pluginList;
        }
    }
    
    /**
     * Saves the active plugins list
     */
    public function saveActiveList()
    {
        $activeList = array();
        
        foreach( $this->pluginList  as $pluginName => $plugin )
        {
            $activeList[ $pluginName ] = (boolean)$plugin;
        }
        
        return create_file( $this->configFile , serialize( $activeList ) );
    }
    
    /**
     * Sets/Unsets the specified plugin (in)active
     * @param string $pluginName
     * @param boolean $is_active
     */
    public function setActive( $pluginName , $is_active = true )
    {
        if ( array_key_exists( $pluginName , $this->pluginList ) )
        {
            $this->pluginList[ $pluginName ] = (boolean)$is_active;
        }
    }
}