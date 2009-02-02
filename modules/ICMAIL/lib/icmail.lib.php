<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
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

class ICMAIL
{
    public static function getUserList( $type )
    {
        $tbl_cdb_names   = claro_sql_get_main_tbl();
        $tbl_course_user = $tbl_cdb_names['rel_course_user'];
        $tbl_user        = $tbl_cdb_names['user'];
        
        switch($type)
        {
            // all users
            /*case 'all':
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'`';
            break;*/
            // course creators
            case 'creators':
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'`
                        WHERE `isCourseCreator` = 1';
            break;
            // users with no courses
            /*case 'nocourse':
                $sql = 'SELECT DISTINCT  user_id AS id
                        FROM `'.$tbl_user.'` AS u 
                        INNER JOIN `'.$tbl_course_user.'` AS cu
                        ON  u.user_id = cu.user_id
                        WHERE cu.user_id IS NULL';
            break;*/
            // course managers
            case 'managers':
                $sql = 'SELECT DISTINCT u.user_id AS id
                        FROM `'.$tbl_user.'` AS u 
                        INNER JOIN `'.$tbl_course_user.'` AS cu
                        ON  u.user_id = cu.user_id
                        AND `isCourseManager` = 1';
            break;
            // admins
            case 'admin':
            default:
                $sql = 'SELECT user_id AS id
                        FROM `'.$tbl_user.'` 
                        WHERE `isPlatformAdmin` = 1';
        }
    
        $cols =  claro_sql_query_fetch_all_cols($sql);
        
        if ( is_array( $cols ) && count( $cols ) >= 1 )
        {
            return $cols['id'];
        }
        else
        {
            return array();
        }
    }
    
    public static function sendHtmlMailToList( $userIdList, $message, $subject , $specificFrom='', $specificFromName='', $altBody='' )
    {
        if ( ! is_array($userIdList) ) $userIdList = array($userIdList);
        if ( count($userIdList) == 0)  return 0;
    
        $tbl      = claro_sql_get_main_tbl();
        $tbl_user = $tbl['user'];
    
        $sql = 'SELECT DISTINCT email
                FROM `'.$tbl_user.'`
                WHERE user_id IN ('. implode(', ', array_map('intval', $userIdList) ) . ')';
    
        $emailList = claro_sql_query_fetch_all_cols($sql);
        $emailList = $emailList['email'];
    
        $emailList = array_filter($emailList, 'is_well_formed_email_address');
    
        $mail = new ClaroPHPMailer();
        $mail->IsHTML(true);
        
        $mail->Subject = $subject;
        $mail->Body    = "<html><head></head><body>"
            . claro_parse_user_text( $message )
            . '<hr />'
            . get_conf('administrator_name') . '&lt;' . get_conf('administrator_email') . '&gt;<br />'
            . '<a href="' . get_conf('rootWeb') . '">' . get_conf('siteName') . '</a><br />'
            . '</body></html>'
            ;
        
        if (!empty($altBody))
        {
            $mail->AltBody = $altBody;
        }    
        
        if ($specificFrom != '')     $mail->From = $specificFrom;
        else                         $mail->From = get_conf('administrator_email');
    
        if ($specificFromName != '') $mail->FromName = $specificFromName;
        else                         $mail->FromName = get_conf('administrator_name');
    
        $mail->Sender = $mail->From;
    
        foreach ($emailList as $thisEmail)
        {
            $mail->AddAddress($thisEmail);
            
            $mail->Send();
            
            $mail->ClearAddresses();
        }
    }
    
    public static function sendHtmlMailToUser( $to, $toName = '', $message, $subject , $specificFrom='', $specificFromName='', $altBody='' )
    {
        $mail = new ClaroPHPMailer();
        $mail->IsHTML(true);
        
        $mail->Subject = $subject;
        $mail->Body    = "<html><head></head><body>"
            . claro_parse_user_text( $message )
            . '<hr />'
            . get_conf('administrator_name') . '&lt;' . get_conf('administrator_email') . '&gt;<br />'
            . '<a href="' . get_conf('rootWeb') . '">' . get_conf('siteName') . '</a><br />'
            . '</body></html>'
            ;
        
        if (!empty($altBody))
        {
            $mail->AltBody = $altBody;
        }    
        
        if ($specificFrom != '')     $mail->From = $specificFrom;
        else                         $mail->From = get_conf('administrator_email');
    
        if ($specificFromName != '') $mail->FromName = $specificFromName;
        else                         $mail->FromName = get_conf('administrator_name');
    
        $mail->Sender = $mail->From;
        
        if ( !empty( $toName ) )
        {
            $mail->AddAddress( $to, $toName );
        }
        else
        {
            $mail->AddAddress( $to );
        }
        
        $mail->Send();
    }
}
