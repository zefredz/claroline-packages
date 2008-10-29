<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLPAGES
 *
 * @author Claroline team <info@claroline.net>
 *
 */
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    class VideoRegistry
    {
        private static $instance = false;

        private $videos;

        public function __construct()
        {
            $this->videos = array();

            $this->loadAll();
        }

        public function register($type, $displayName, $className = '', $category = '')
        {
            $type = strtolower($type);

            $this->videos[$type]['displayName'] = $displayName;

            if( $className == '' ) $className = $type;
            $this->videos[$type]['className'] = $className;

            $this->videos[$type]['category'] = strtolower($category);
        }

        public function getRegisteredTypes()
        {
            return array_key( $this->videos );
        }

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

        public function getVideoClass( $type )
        {
            $type = strtolower($type);

            if( isset($this->videos[$type]['className']) )
            {
                return $this->videos[$type]['className'];
            }
            else
            {
                return '';
            }
        }

        public function getList()
        {
            return $this->videos;
        }

        public static function getInstance()
        {
            if ( ! VideoRegistry::$instance )
            {
                VideoRegistry::$instance = new VideoRegistry;
            }

            return VideoRegistry::$instance;
        }

    }

?>