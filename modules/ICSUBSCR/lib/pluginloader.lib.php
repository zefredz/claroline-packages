<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that loads plugins
 * @protected string $pluginDir
 * @protected array $pluginList
 */
class PluginLoader
{
    protected $lang;
    protected $pluginDir;
    protected $pluginList = array();
    
    /**
     * Constructor
     * @param string $pluginDir
     */
    public function __construct( $pluginDir , $lang )
    {
        $this->pluginDir = $pluginDir;
        $this->lang = $lang;
    }
    
    /**
     * gets plugin list (names)
     * @return array $pluginList
     */
    public function getPluginList( $force = false )
    {
        if ( empty( $this->pluginList ) || $force )
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
            if ( $plugin->isDir()
                && ! $plugin->isDot() )
            {
                $pluginName = $plugin->getFileName();
                $part = explode( '.' , $pluginName );
                
                if ( count( $part ) == 3
                    && $part[ 1 ] == 'plugin'
                    && $part[ 0 ] == 'icsubscr' )
                {
                    try
                    {
                        require( $this->pluginDir . $pluginName . '/controller.lib.php' );
                        require( $this->pluginDir . $pluginName . '/view.lib.php' );
                        $this->pluginList[] =  $part[ 2 ];
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
     * Get plugged in object
     * @param string or int $plugin
     * @return plugged in objet $plugin
     */
    public function get( $plugin , $param = null )
    {
        if( is_int( $plugin )
           && array_key_exists( $plugin , $this->pluginList ) )
        {
            return new $this->pluginList[ $plugin ];
        }
        elseif( in_array( $plugin , $this->pluginList ) )
        {
            $lang = file_exists( get_module_path( 'ICSUBSCR' )
                . '/plugins/icsubscr.plugin.'
                . $plugin . '/lang/lang_' . $this->lang . '.php')
                ? $this->lang
                : 'english';
                
            include get_module_path( 'ICSUBSCR' )
                . '/plugins/icsubscr.plugin.'
                . $plugin . '/lang/lang_' . $lang . '.php';
            
            $className = $plugin . 'Controller';
            
            return new $className( $param );
        }
    }
    
    /**
     *
     */
    public function pluginExists( $plugin )
    {
        return in_array( $plugin , $this->getPluginList() );
    }
}