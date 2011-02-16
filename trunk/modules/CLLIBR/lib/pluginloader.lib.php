<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.1.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads "plugged in" resources
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
     * search for plugins in the plugins directory
     * @return array $pluginList
     */
    public function loadPlugins()
    {
        $pluginsRepository = new DirectoryIterator( $this->pluginDir );
        
        foreach( $pluginsRepository as $directory )
        {
            $dirName = $directory->getFileName();
            
            if ( $directory->isDir() && ! $directory->isDot() && substr( $dirName , -4 ) == 'type' )
            {
                $pluginType = substr( $dirName, 0 , strlen( $dirName ) - 4 );
                $pluginList = new DirectoryIterator( $this->pluginDir . $dirName );
                
                foreach( $pluginList as $plugin )
                {
                    $fileName = $plugin->getFileName();
                    
                    if ( $plugin->isFile() && substr( $fileName , -11 ) == '.plugin.php' )
                    {
                        try
                        {
                            require( $this->pluginDir . $dirName . '/' . $fileName );
                            
                            $pluginName = ucwords( substr( $fileName , 0 , strlen( $fileName ) - 11 ) );
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
    }
}