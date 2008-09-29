<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    class PDOMapperCache
    {
        protected $cacheDir;
        protected $cacheFileName;
        protected $cache = array();
        
        public function __construct( $cacheDir = '.', $cacheFileName = 'pdocrud.cache.php' )
        {
            $this->cacheDir = $cacheDir;
            $this->cacheFileName = $cacheFileName;
            $this->reload();
        }
        
        public function reload()
        {
            $schemaCache = array();
            
            if (!file_exists( $this->cacheDir.'/'.$this->cacheFileName ) )
            {
                $this->clear();
            }
            
            include $this->cacheDir.'/'.$this->cacheFileName;
            
            $this->cache = $schemaCache;
        }
        
        public function getMapperSchema( $name )
        {
            if ( ! array_key_exists( $name, $this->cache ) )
            {
                throw new Exception( 'No schema found for ' . $name );
            }
            else
            {
                // var_dump( $this->cache );
                return $this->cache[$name];
            }
        }
        
        public function addMapperSchema( $schema, $replace = false )
        {
            if ( true == $replace 
                || ! array_key_exists( $schema->getClass(), $this->cache ) )
            {
                $this->cache[$schema->getClass()] = $schema;
                $this->generate();
            }
        }
        
        public function replaceMapperSchema( $schema )
        {
            $this->addMapperSchema( $schema, true );
        }
        
        public function deleteMapperSchema( $name )
        {
            if ( ! array_key_exists( $name, $this->cache ) )
            {
                throw new Exception( 'No schema found for ' . $name );
            }
            else
            {
                unset( $this->cache[$name] );
                $this->generate();
            }
        }
        
        public function clear()
        {
            $ret = '<'.'?php'."\n";
            $ret .= '$schemaCache = array();' . "\n";
            $ret .= '?'.'>';
            
            file_put_contents( $this->cacheDir.'/'.$this->cacheFileName, $ret );
        }
        
        protected function generate()
        {
            $ret = '<'.'?php'."\n";
            
            $ret .= '$schemaCache = array();' . "\n";
            
            foreach ( $this->cache as $name => $schema )
            {
                $ret .= '$schemaCache[\''.$name.'\']=unserialize(\''.serialize($schema).'\');' . "\n";
            }
            
            $ret .= '?'.'>';
            
            file_put_contents( $this->cacheDir.'/'.$this->cacheFileName, $ret );
        }
    }
?>