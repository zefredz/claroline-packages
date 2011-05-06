<?php // $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 *
 */

// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

require_once dirname( __FILE__ ) . '/plugins/videoPlugins/videoPlugin.class.php';

/**
 * Viddeo plugin registry
 */
class videoPluginRegistry
{
      
    //Class property  
    protected static $instance = false;

    //Video plugin property
    protected $videos;

    /**
     * Constructor
     * Set properties default values
     * 
     */
    public function __construct()
    {
        $this->videos = array();

        $this->loadAll();
    } 
    
    /**
     * Scan the videoPlugins directory and load all available video plugin php classes
     *
     * @return boolean return false if the video plugins directory isn't found
     */    
    public function loadAll()
    {
        $allowedExtensions = array('.php','.php3','.php4','.php4','.php6');

        $path = get_module_path('CLPAGES') . '/lib/plugins/videoPlugins';

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
            return false;
        }
    }
    
    /**
     * Return the registered video plugins keys array
     *
     * @return array The registered video plugins keys
     */   
    public function getRegisteredVideoTypes()
    {
        return array_keys( $this->videos );
    }

    /**
     * Return the registered video plugins list
     *
     * @return array The registered video plugins list
     */   
    public function getVideoList()
    {
        return $this->videos;
    }
    
    /**
     * Return the corresponding display name of the input video type
     *
     * @return string Empty if the input type doesn't exist
     * @return string The display name of the specific video type
     */ 
    public function getVideoDisplayName( $type )
    {
        $type = strtolower($type);

        if( isset($this->videos[$type]) )
        {
            return $this->videos[$type]['displayName'];
        }
        else
        {
            return '';
        }
    }

    /**
     * Return the corresponding video plugin instance of the input video type class
     *
     * @return string Empty if the input type doesn't exist
     * @return object The video plugin instance of the specific video type class
     */ 
    public function getVideoClassInstance( $type )
    {
        $type = strtolower($type);

        if( isset($this->videos[$type]['className']) )
        {
            $className = $this->videos[$type]['className'];
            $videoInstance = new $className();
            return $videoInstance;
        }
        else
        {
            return '';
        }
    }

    /**
     * Define the corresponding video plugin type of an input url
     *
     * @param string $url An input video url
     * @return string The relatif video plugin type
     * @return boolean false If any relation isn't found
     */       
    public function defineVideoType($url)
    {
        foreach( $this->getRegisteredVideoTypes() as $type)
        {
            if($type != 'automatic')
            {
                if($this->getVideoClassInstance($type)->isValidUrl($url))
                {
                    return $type;
                }
            }
        }
        return false;
    }    
           
    /**
     * Allow external video plugin class to self register to the videoPluginRegister
     *
     * @param string $type A video plugin type
     * @param string $displayName A video plugin display name
     * @param string $className A video plugin class name
     * @param string $category A video plugin category
     */   
    public function register($type, $displayName, $className = '', $category = '')
    {
        $type = strtolower($type);

        $this->videos[$type]['displayName'] = get_lang($displayName);

        if( $className == '' ) $className = $type;
        $this->videos[$type]['className'] = $className;

        $this->videos[$type]['category'] = strtolower($category);
    }
}
