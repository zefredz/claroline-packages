<?php // $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */

// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

/**
 * Plugin registry class
 */
class PluginRegistry
{
    protected static $instance = false;

    protected $plugins;

    public function __construct()
    {
        $this->plugins = array();

        $this->loadAll();
    }
    
    /**
     * Register a plugin
     * @param string $type
     * @param string $displayName
     * @param string $className
     * @param string $category
     * @param string $img 
     */
    public function register( $type, $displayName, $className = '', $category = '', $img = '' )
    {
        $type = strtolower($type);

        $this->plugins[$type]['displayName'] = $displayName;

        if( $className == '' ) $className = $type;
        $this->plugins[$type]['className'] = $className;

        $this->plugins[$type]['category'] = strtolower($category);

        if( $img == '' ) $img = '';
        $this->plugins[$type]['img'] = $img;
    }
    
    /**
     * Get the types of plugins in the registry
     * @return array
     */
    public function getRegisteredTypes()
    {
        return array_key( $this->plugins );
    }
    
    /**
     * Load all plugins
     */
    public function loadAll()
    {
        $allowedExtensions = array('.php','.php3','.php4','.php4','.php6');

        $path = get_module_path('CLPAGES') . '/lib/plugins';

        $dirname = realpath($path) . '/' ;
        
        if ( is_dir($dirname) )
        {
            $handle = opendir($dirname);
            while ( false !== ($elt = readdir($handle) ) )
            {
                // skip '.', '..' and 'CVS'
                if ( $elt == '.' || $elt == '..' || $elt == 'CVS' ) continue;

                // skip folders
                if ( !is_file($dirname.$elt) ) continue ;

                // skip file with wrong extension
                $ext = strrchr($elt, '.');
                if ( !in_array(strtolower($ext),$allowedExtensions) ) continue;

                // add elt to array
                require_once $path . '/' . $elt;
            }
        }
        else
        {
            throw new Exception("{$dirname} is not a directory");
        }
    }
    
    /**
     * Get plugin class name
     * @param string $type
     * @return string 
     */
    public function getPluginClass( $type )
    {
        $type = strtolower($type);

        if( isset($this->plugins[$type]['className']) )
        {
            return $this->plugins[$type]['className'];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Get plugin list
     * @return array 
     */
    public function getList()
    {
        return $this->plugins;
    }
    
    /**
     * Oh no ! This is a singleton !!!!! Oh Great Cthulhu protect us from those 
     * ugly things from beyond the wall of object-oriented programming !
     * @return PluginRegistry 
     */
    public static function getInstance()
    {
        if ( ! PluginRegistry::$instance )
        {
            PluginRegistry::$instance = new PluginRegistry;
        }

        return PluginRegistry::$instance;
    }

}
