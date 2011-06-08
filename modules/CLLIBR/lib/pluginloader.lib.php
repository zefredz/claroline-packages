<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.6 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads "plugged in" resources
 * Plugins names must be something like "{plugintype}.{pluginname}.plugin.php"
 * @cprotected string $pluginDir
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
            $this->getPlugins();
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
            if ( ! $plugin->isDir() && ! $plugin->isDot() )
            {
                $fileName = $plugin->getFileName();
                $part = explode( '.' , $fileName );
                
                if ( $part[ 2 ] == 'plugin' && $part[ 3 ] == 'php' )
                {
                    try
                    {
                        require( $this->pluginDir . $fileName );
                        
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
        return isset( $this->pluginList[ $type ][ $name ] );
    }
}