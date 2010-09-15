<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Terms Of Use module function library
 *
 * @version     1.0 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icterms
 */

/**
 * Check if the current page can be accessed while in terms acceptance mode
 * @return bool
 */
function icterms_is_page_allowed()
{
    return defined('ICTERMS_PAGE_ALLOWED') && ICTERMS_PAGE_ALLOWED;
}

/**
 * Check if the acceptance process is in progress
 * @return bool
 */
function icterms_terms_acceptance_in_progress()
{
    return isset($_SESSION['icterms_terms_acceptance_in_progress'])
        && $_SESSION['icterms_terms_acceptance_in_progress'];
}

/**
 * Check if the given user has already accepted the terms of use
 * @return boolean
 */
function icterms_user_has_accepted_tou( $userId )
{
    // get value from database
    $tbl = get_module_main_tbl(array('icterms_acceptances'));

    $res = Claroline::getDatabase()->query("
        SELECT terms_acceptance_timestamp
        FROM `{$tbl['icterms_acceptances']}`
        WHERE user_id = ".(int)Claroline::getDatabase()->escape($userId)."
    ");
    
    return ($res->numRows() == 0);
}

/**
 * Check if the user must accept the terms of use
 * @return bool
 */
function icterms_user_must_accept_terms( $userId )
{
    $userMustAcceptTermsFromSession = isset($_SESSION['icterms_user_must_accept_terms'])
        && $_SESSION['icterms_user_must_accept_terms'];
    
    if ( ! $userMustAcceptTermsFromSession )
    {
        $userMustAcceptTermsFromDatabase = icterms_user_has_accepted_tou( $userId );
    }
    else
    {
        $userMustAcceptTermsFromDatabase = false;
    }
    
    return !claro_is_platform_admin()
        && get_conf( 'icterms_forceTermsAcceptance', true )
        && ( $userMustAcceptTermsFromDatabase || $userMustAcceptTermsFromSession )
        ;
}

/**
 * Register the acceptance of the terms of use by the user by saving the timestamp
 * in the database.
 * @param int $userId
 */
function icterms_register_user_terms_acceptance( $userId )
{
    // update database
    $tbl = get_module_main_tbl(array('icterms_acceptances'));
    
    Claroline::getDatabase()->exec("
        INSERT `{$tbl['icterms_acceptances']}`
        SET
            user_id = ".(int)Claroline::getDatabase()->escape($userId).",
            terms_acceptance_timestamp = ".claro_time()."
    ");
    
    // update session
    $_SESSION['icterms_terms_acceptance_in_progress'] = false;
    $_SESSION['icterms_user_must_accept_terms'] = false;
}

/**
 * Load the textzone containing the terms of use or a generic text or returns
 * the default text in get_block('ictermsTermsOfUse').
 * The textzone is located at platform/module_data/ICTERMS/textzone_terms_of_use.html
 * @return string
 */
function icterms_get_terms_contents()
{
    if ( get_conf('icterms_useAccountCreationAgreement', false) )
    {
        $termsOfUse = claro_text_zone::get_content('textzone_inscription_form');
    }
    else
    {
        $textzone = get_path('rootSys').'platform/module_data/ICTERMS/textzone_terms_of_use.html';
    
        if ( file_exists( $textzone ) )
        {
            $termsOfUse = trim( file_get_contents( $textzone ) );
        }
        else
        {
            $termsOfUse = '';
        }
    }
    
    if ( empty( $termsOfUse ) )
    {
        $termsOfUse = get_block('ictermsTermsOfUse');
    }
    
    return $termsOfUse;
}

/**
 * Save the terms of use to a html textzone.
 * The textzone is located at platform/module_data/ICTERMS/textzone_terms_of_use.html
 * @param string $contents terms of use
 */
function icterms_put_terms_contents( $contents )
{
    $textzone = get_path('rootSys').'platform/module_data/ICTERMS/textzone_terms_of_use.html';
    
    if ( !file_exists( dirname( $textzone ) ) )
    {
        claro_mkdir( dirname($textzone), CLARO_FILE_PERMISSIONS, true );
    }
    
    file_put_contents( $textzone, $contents . "\n<!-- content: html tiny_mce -->\n" );
}

/**
 * Delete the terms of use and reset them to the module defaults define in
 * get_block('ictermsTermsOfUse').
 * @return bool
 */
function icterms_delete_terms_contents()
{
    $textzone = get_path('rootSys').'platform/module_data/ICTERMS/textzone_terms_of_use.html';
    
    if ( file_exists( $textzone ) )
    {
        return claro_delete_file( $textzone );
    }
    else
    {
        return false;
    }
}
