<?php // $Id$

/**
 * Add missing kernel features to allow modules to work with older versions
 * of Claroline 1.9 and 1.10 kernel
 *
 * @version 0.2 $Revision$
 * @copyright (c) 2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLSUBSCR
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */
if ( !class_exists( 'ModulePluginLoader' ) )
{
    class ModulePluginLoader
    {
        protected $moduleLabel;

        public function __construct( $moduleLabel )
        {
            $this->moduleLabel = $moduleLabel;
        }

        /**
         * Load a list of plugins from a given module
         * Usage : From::module(ModuleLable)->loadPlugins( list of connectors );
         * @since Claroline 1.9.6
         * @params  list of plugins
         * @return  array of not found plugins
         */
        public function loadPlugins()
        {
            $args = func_get_args();
            $notFound = array();

            foreach ( $args as $cnr )
            {
                if ( substr($cnr, -4) !== '.php' && substr( $cnr, -4 ) === '.lib' )            
                {
                    $cnr .= '.php';
                }
                elseif ( substr($cnr, -8) !== '.lib.php' )
                {
                    $cnr .= '.lib.php';
                }

                $cnr = protect_against_file_inclusion( $cnr );

                $cnrPath = get_module_path( $this->moduleLabel ) . '/plugins/' . $cnr;

                if ( file_exists( $cnrPath ) )
                {
                    require_once $cnrPath;
                }
                else
                {
                    if ( claro_debug_mode() )
                    {
                        throw new Exception( "Cannot load plugin {$cnrPath}" );
                    }

                    $notFound[] = $cnr;

                    continue;
                }

            }

            return $notFound;
        }
    }
}
