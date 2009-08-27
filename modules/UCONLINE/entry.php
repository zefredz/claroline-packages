<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 0.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository() . 'UCONLINE.conf.php'; 

$tbl = claro_sql_get_tbl( 'user_online' , array( 'course' => null ) );

$sql = "SELECT COUNT(`id`) AS `user_id` FROM `".$tbl[ 'user_online' ]."`";

$countOfUsers = claro_sql_query_get_single_value($sql);

// Output
if ( $countOfUsers < 1 )
{
        $html = get_lang('No user connected');
}
else
{
    $html = '<a href="' . get_module_url( 'UCONLINE' ) . '/user_connected.php">';
    
    if( $countOfUsers == 1 )
    {
        $html .= get_lang('1 user connected');
    }
    else // $countOfUsers > 1
    {
        $html .= get_lang( '%countOfUsers users connected' , array( '%countOfUsers' => $countOfUsers ) );
    }
    
    $html .= '</a>';
}

$claro_buffer->append($html);
