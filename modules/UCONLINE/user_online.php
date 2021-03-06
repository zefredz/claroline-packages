<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 1.2.9 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( count( get_included_files() ) == 1 ) die( '---' );

$tbl = get_module_main_tbl( array('user_online') );

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

echo $html;
