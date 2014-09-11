<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Main script for Terms Of Use module
 *
 * @version     1.0 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icterms
 */

try
{
    //$tlabelReq = 'ICTERMS';
    
    $cidReset = true;
    $cidReq = false;
    
    define( 'ICTERMS_PAGE_ALLOWED', true );
    
    //Load claroline kernel
    require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
    
    language::load_module_translation('ICTERMS');
    load_module_config('ICTERMS');
    
    FromKernel::uses('utils/input.lib','utils/validator.lib');
    
    if ( ! claro_is_user_authenticated() )
    {
        claro_disp_auth_form();
        die();
    }
    
    require_once dirname(__FILE__).'/lib/icterms.lib.php';
    
    $dialogBox = new DialogBox;
    
    if ( !icterms_user_must_accept_terms( claro_get_current_user_id() ) )
    {
        $dialogBox->info(get_lang('You have already accepted the terms of use'));
        
        $dialogBox->info(
            get_lang('Continue to the <a href="%homePage%">home page</a>',
                array('%homePage%' => get_path('rootWeb') )
            )
        );
    }
    else
    {
        $cmd = Claro_UserInput::getInstance()->get('cmd', 'rqAcceptTerms');
        
        switch( $cmd )
        {
            case 'exAcceptTerms':
            {
                $acceptTerms = ('true' == Claro_UserInput::getInstance()->get('acceptTerms', 'false'));
                // check caheckbox checked
                // if true :
                if ( $acceptTerms )
                {
                    // success message
                    icterms_register_user_terms_acceptance( claro_get_current_user_id() );
                    $dialogBox->success(get_lang('You can now continue to the home page.'));
                    // with link to index.php 'continue'
                    $dialogBox->info(
                        get_lang('Continue to the <a href="%homePage%">home page</a>',
                            array('%homePage%' => get_path('rootWeb') )
                        )
                    );
                    Claro_KernelHook_Lock::releaseLock( 'ICTERMS' );
                    break;
                }
                else
                {
                // else :
                    // error message
                    $dialogBox->error(get_lang('You must accept the terms of use to access to this web site.'));
                    // no break;
                }
            }
            default:
            {
                // load template
                $tpl = new ModuleTemplate('ICTERMS', 'touacceptform.tpl.php' );
                $tpl->assign( 'termsContents', icterms_get_terms_contents() );
                
                $dialogBox->warning($tpl->render());
            }
        }
    }
    
    ClaroBreadCrumbs::getInstance()->setCurrent( get_lang('Terms of use') );
    Claroline::getDisplay()->body->appendContent( claro_html_tool_title( get_lang('Terms of use') ) );
    Claroline::getDisplay()->body->appendContent($dialogBox->render());
    
    echo Claroline::getDisplay()->render();
}
catch (Exception $e)
{
    if ( !claro_debug_mode() )
    {
        claro_die( $e->getMessage() );
    }
    else
    {
        claro_die( $e->__toString() );
    }
}
