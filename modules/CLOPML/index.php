<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * OPML generator : create OPML file for a user containing all RSS from
     *  his course. User is identified by user id, username or official code
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLOPML
     */

    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

    require_once dirname(__FILE__) . '/lib/clopml.lib.php';

    $userId = isset( $_REQUEST['userId'] )
        ? trim($_REQUEST['userId'])
        : null
        ;
        
    $userName = isset( $_REQUEST['userName'] )
        ? trim($_REQUEST['userName'])
        : null
        ;
    
    $officialCode = isset( $_REQUEST['officialCode'] )
        ? trim($_REQUEST['officialCode'])
        : null
        ;
        
    if ( empty ( $userId ) )
    {
        $tblList = claro_sql_get_main_tbl();
        
        if ( !empty( $userName ) )
        {
            // get userId from userName
            $sql = "SELECT user_id
                FROM `".$tblList['user']."`
                WHERE  username = '" . claro_sql_escape( $userName ) . "'";
                
            $res = claro_sql_query_get_single_value( $sql );
            
            if ( $res )
            {
                $userId = (int) $res;
            }
        }
        elseif ( !empty( $officialCode ) )
        {
            // get userId from officialCode
            $sql = "SELECT user_id
                FROM `".$tblList['user']."`
                WHERE  officialCode = '" . claro_sql_escape( $officialCode ) . "'";
                
            $res = claro_sql_query_get_single_value( $sql );

            if ( $res )
            {
                $userId = (int) $res;
            }
        }
    }
        
    // get userId from official code or username
        
    if ( !empty( $userId ) )
    {
        $opml = generate_opml( $userId );
        
        if ( $opml )
        {
            header("Content-Type: text/xml");
            echo $opml;
        }
    }
?>