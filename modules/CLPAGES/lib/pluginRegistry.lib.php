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

	class PluginRegistry
	{
		private static $instance = false;

		private $plugins;

		public function __construct()
		{
			$this->plugins = array();

			$this->loadAll();
		}

		public function register($type, $displayName, $className = '')
		{
			$type = strtolower($type);
			if( $className == '' ) $className = $type;

			$this->plugins[$type]['displayName'] = $displayName;
			$this->plugins[$type]['className'] = $className;
		}

		public function getRegisteredTypes()
		{
			return array_key( $this->plugins );
		}

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
	            return false;
	        }
		}

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

		public function getList()
		{
			return $this->plugins;
		}

        public static function getInstance()
        {
            if ( ! PluginRegistry::$instance )
            {
                PluginRegistry::$instance = new PluginRegistry;
            }

            return PluginRegistry::$instance;
        }

	}

?>