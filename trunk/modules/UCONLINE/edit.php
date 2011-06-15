<?php // $Id$
/**
 * Who is onlin@?
 *
 * @version     UCONLINE 1.2.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_user_authenticated() ) claro_die( get_lang( 'Not allowed' ) );

FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' , 'display/layout.lib' , 'embed.lib' );
From::Module( 'UCONLINE' )->uses( 'skype.account.class' );

$skypeAccount = new SkypeAccount( claro_get_current_user_id() );

$userInput = Claro_UserInput::getInstance();

$dialogBox = new DialogBox;

$cmd = $userInput->get( 'cmd' );
$newSkypeName = $userInput->get( 'skypeName' );

if( $cmd == 'exUpdate' )
{
    if( ! $newSkypeName )
    {
        if( $skypeAccount->delete() )
        {
            $dialogBox->success( get_lang( 'Skype status notifier successfully deactivated.' ) );
        }
        else
        {
            $dialogBox->error( get_lang( 'Cannot save change.' ) );
        }
    }
    else
    {
        if( $skypeAccount->save( $newSkypeName ) )
        {
            $dialogBox->success( get_lang( 'Skype name successfully changed.') );
        }
        else
        {
            $dialogBox->error( get_lang( 'Cannot save change.' ) );
        }
    }
}


if ( $dialogBox )
{
    claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
}

$skypeEditView = new ModuleTemplate( 'UCONLINE' , 'skypeedit.tpl.php' );

$skypeEditView->assign( 'skypeName' , $skypeAccount->getSkypeName() );

Claroline::getInstance()->display->body->appendContent( $skypeEditView->render() );

echo Claroline::getInstance()->display->render();