<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package CLONLINE
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

$tlabelReq = 'CLONLINE';

include_once claro_get_conf_repository().'CLONLINE.conf.php'; 

$tbl = claro_sql_get_tbl('user_online', array('course'=>null));

$sql = "SELECT COUNT(`id`) AS `user_id` FROM `".$tbl['user_online']."`";

$countOfUsers = claro_sql_query_get_single_value($sql);


//-- Output

$html = '';

$html.= '<a href="' . get_module_url('CLONLINE') . '/user_connected.php">';

if( $countOfUsers > 1 )
{
    $html.= get_lang('%countOfUsers users connected', array('%countOfUsers'=> $countOfUsers));
}
elseif( $countOfUsers == 1 )
{
    $html.= get_lang('1 user connected');
}
else // $countOfUsers < 1
{
    $html.= get_lang('No user connected');
}

$html.= '</a>';

$claro_buffer->append($html);

?>
