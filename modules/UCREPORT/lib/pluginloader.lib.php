<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads "plugged in" resources
 * Plugins names must be something like "{pluginname}.plugin.php"
 * @cprotected string $pluginDir
 * @protected array $pluginList
 */
class PluginLoader
{
    protected $pluginDir;
    protected $pluginList = array();
    protected $userList;
    
    /**
     * Constructor
     * @param string $pluginDir
     */
    public function __construct( $pluginDir )
    {
        $this->pluginDir = $pluginDir;
    }
    
    /**
     * Gets plugin list (names)
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
     * Searchs for plugins in the plugins directory
     * and try to instanciate them
     */
    public function loadPlugins()
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
                        require( $this->pluginDir . $fileName );
                        $pluginName = ucwords( $part[ 0 ] ) . ucwords( $part[ 1 ] );
                        $this->pluginList[] = new $pluginName;
                    }
                    catch( Exception $e )
                    {
                        return $e->getMessage();
                    }
                }
            }
        }
    }
}