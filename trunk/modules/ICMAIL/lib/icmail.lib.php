<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.1 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ICMAIL
 */

class ICMAIL
{
    public static function getUserList( $type )
    {
        $tbl_cdb_names      = claro_sql_get_main_tbl();
        $tbl_course_user    = $tbl_cdb_names['rel_course_user'];
        $tbl_course         = $tbl_cdb_names['course'];
        $tbl_user           = $tbl_cdb_names['user'];
        
        switch($type)
        {
            // all users
            case 'all':
                $sql = "SELECT DISTINCT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u
                        WHERE
                            u.authSource != 'disabled'";
            break;
        
            // course creators
            case 'creators':
                $sql = "SELECT DISTINCT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u
                        WHERE
                            u.`isCourseCreator` = 1
                        AND
                            u.authSource != 'disabled'";
            break;
        
            // users with no courses
            case 'nocourse':
                $sql = "SELECT DISTINCT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u 
                        INNER JOIN
                            `{$tbl_course_user}` AS cu
                        ON
                            u.user_id = cu.user_id
                        WHERE
                            cu.user_id IS NULL
                        AND
                            u.authSource != 'disabled'";
            break;
        
            // course managers
            case 'managers':
                $sql = "SELECT DISTINCT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u 
                        INNER JOIN
                            `{$tbl_course_user}` AS cu
                        ON
                            u.user_id = cu.user_id
                        AND
                            cu.`isCourseManager` = 1
                        AND
                            u.authSource != 'disabled'";
            break;
        
            // course managers with public courses
            case 'publicmanagers':
                $sql = "SELECT DISTINCT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u 
                        INNER JOIN
                            `{$tbl_course_user}` AS cu
                        ON
                            u.user_id = cu.user_id
                        INNER JOIN
                            `{$tbl_course}` AS c
                        ON
                            c.`code` = cu.`code_cours`
                        WHERE
                            cu.`isCourseManager` = 1
                        AND
                            c.`access` = 'public'
                        AND
                            u.authSource != 'disabled'";
            break;
        
            // admins
            case 'admin':
            default:
                $sql = "SELECT
                            u.email AS email
                        FROM
                            `{$tbl_user}` AS u
                        WHERE
                            u.`isPlatformAdmin` = 1
                        AND
                            u.authSource != 'disabled'";
            break;
        }
    
        $cols =  claro_sql_query_fetch_all_cols($sql);
        
        if ( is_array( $cols ) && count( $cols ) >= 1 )
        {
            return $cols['email'];
        }
        else
        {
            return array();
        }
    }
    
    public static function sendHtmlMailToList( $emailList, $message, $subject , $specificFrom='', $specificFromName='', $altBody='' )
    {
        if ( ! is_array( $emailList ) )
        {
            $emailList = array( $emailList );
        }
        
        if ( count($emailList) == 0 )
        {
            return 0;
        }
        
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
        
        $mail->From = !empty($specificFrom)
            ? $specificFrom
            : get_conf('administrator_email')
            ;
    
        $mail->FromName = !empty($specificFromName)
            ? $specificFromName
            : get_conf('administrator_name')
            ;
    
        $mail->Sender = $mail->From;
        
        $emailSent = 0;
    
        foreach ($emailList as $thisEmail)
        {
            if ( empty( $thisEmail ) )
            {
                continue;
            }
            
            $mail->AddAddress($thisEmail);
            
            $mail->Send();
            
            $eamilSent++;
            
            $mail->ClearAddresses();
        }
        
        return $emailSent;
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
        
        $mail->From = !empty($specificFrom)
            ? $specificFrom
            : get_conf('administrator_email')
            ;
    
        $mail->FromName = !empty($specificFromName)
            ? $specificFromName
            : get_conf('administrator_name')
            ;
    
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
