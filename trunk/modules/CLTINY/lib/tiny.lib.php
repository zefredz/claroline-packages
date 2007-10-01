<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    class TinyUrl
    {
        private $tbl;
        
        public function __construct()
        {
            $this->tbl = get_module_main_tbl( array( 'cltiny_urls' ) );
        }
        
        public function create( $url )
        {
            $tinyId = uniqid(); // $url );
            
            if ( false === $this->getUrl( $tinyId ) )
            {
                $sql = "INSERT INTO `".$this->tbl['cltiny_urls']."`\n"
                    . "SET "
                    . " tinyId = '" . addslashes( $tinyId ) . "',\n"
                    . " url = '" . addslashes( $url ) . "'"
                    ;
                    
                if ( false !== claro_sql_query( $sql ) )
                {
                    return $tinyId;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function remove( $tinyId )
        {
            if ( false !== $this->getUrl( $tinyId ) )
            {
                $sql = "DELETE FROM `".$this->tbl['cltiny_urls']."`\n"
                    . "WHERE "
                    . " tinyId = '" . addslashes( $tinyId ) . "'"
                    ;

                if ( false !== claro_sql_query( $sql ) )
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function update( $tinyId )
        {
            if ( false === ($url = $this->getUrl( $tinyId ) ) )
            {
                $this->remove ( $tinyId );
                return $this->create( $url );
            }
            else
            {
                return false;
            }
        }
        
        public function getUrl( $tinyId )
        {
            $sql = "SELECT tinyId, url FROM `".$this->tbl['cltiny_urls']."`\n"
                . "WHERE "
                . " tinyId = '" . addslashes( $tinyId ) . "'"
                ;
                
            $res = claro_sql_query_get_single_row( $sql );
            
            return ( !empty($res) && array_key_exists('url', $res )
                ? $res['url']
                : false );
        }
        
        public function listAll()
        {
            $sql = "SELECT tinyId, url FROM `".$this->tbl['cltiny_urls']."`\n"
                ;

            $res = claro_sql_query_fetch_all_rows( $sql );

            return ( !empty($res) ? $res : array() );
        }
    }
?>