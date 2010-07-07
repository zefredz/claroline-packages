<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Kernel hook script for Terms Of Use module, executed at the end of
 * claro_init_global in the moduleCache file.
 *
 * @version     1.0 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icterms
 */

require_once dirname(__FILE__).'/lib/icterms.lib.php';

language::load_module_translation('ICTERMS');
load_module_config('ICTERMS');

if ( !icterms_is_page_allowed()
    && claro_is_user_authenticated() /* only when user is logged in */
    && icterms_user_must_accept_terms( claro_get_current_user_id() ) /* do not ask terms acceptance on every user login */
    && !icterms_terms_acceptance_in_progress() /* do not loop infinitely here */
    && !icterms_is_page_allowed() /* only ICTERMS page is allowed */
)
{
    $_SESSION['icterms_acceptance_in_progress'] = true;
    
    claro_redirect(
        get_module_url('ICTERMS')
        .'/index.php?cmd=rqAcceptTerms'
    ); // redirect to form
    
    die();
}
