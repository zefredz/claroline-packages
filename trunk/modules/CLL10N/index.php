<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 1.0-alpha $Revision$
 *
 * @copyright (c) 2001-2009 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLL10N
 *
 * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
 *
 */

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

//SECURITY CHECK

if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib');
From::Module('CLL10N')->uses('translationmanage.lib','translationrenderer.lib');

require_once (get_path('incRepositorySys') . '/../admin/xtra/sdk/lang/language.lib.php');

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Administration'), get_path('rootAdminWeb') );

$dialogBox = new DialogBox();

try {
  $manage = TranslationManage::getInstance();
  $moduleList = $manage->moduleList();

  $userInput = Claro_UserInput::getInstance();
  
  $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
      'list', 'rqGenerate', 'exGenerate'
  ) ) );
  
  $cmd = $userInput->get( 'cmd','list' );
  
  $out = '';
  
  switch( $cmd )
  {
    case 'list' :
      {
        ClaroBreadCrumbs::getInstance()->setCurrent( get_lang( 'Translations'), get_module_url('CLL10N') );
        $out .= TranslationRenderer::moduleList( $moduleList );
      }
      break;
    
    case 'exGenerate' :
      {
        if( empty( $_REQUEST['module'] ) )
        {
          claro_die( get_lang('Module is missing') );
        }
        
        if( !isset( $moduleList[$_REQUEST['module']] ) )
        {
          claro_die( get_lang( 'Module doesn\'t exist' ) );
        }
        
        if( empty( $_SESSION['CLL10N']['langs'] ) || !is_array( $_SESSION['CLL10N']['langs'] ) )
        {
          claro_die( get_lang( 'Nothing to export' ) );
        }
        
        ClaroBreadCrumbs::getInstance()->append( get_lang( 'Translations'), get_module_url('CLL10N') );
        ClaroBreadCrumbs::getInstance()->append( $moduleList[$_REQUEST['module']]['name'], htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=' . $_REQUEST['module'] ) ) );
        
        // get existing $_lang for existing language files
        $path = get_module_path( $_REQUEST['module']) . '/lang';
        $return = $manage->createLangFiles( $_SESSION['CLL10N']['langs'], $path);
        
        if( $return )
        {
          $dialogBox->success( get_lang( 'Language files created.' ) );
        }
        else
        {
          $dialogBox->error( get_lang( 'Unable to create language files' ) );
        }
        
        //unset( $_SESSION['CLL10N']['langs'] );
        
        $out .= $dialogBox->render();
      }
      break;
    
    case 'rqGenerate' :
      {
        if( empty( $_REQUEST['module'] ) )
        {
          claro_die( get_lang('Module is missing') );
        }
        
        if( !isset( $moduleList[$_REQUEST['module']] ) )
        {
          claro_die( get_lang( 'Module doesn\'t exist' ) );
        }
        ClaroBreadCrumbs::getInstance()->append( get_lang( 'Translations'), get_module_url('CLL10N') );
        ClaroBreadCrumbs::getInstance()->append( $moduleList[$_REQUEST['module']]['name'], htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=' . $_REQUEST['module'] ) ) );
        
        $_SESSION['CLL10N']['langs'] = array();
        
        $_SESSION['CLL10N']['langs'] = $manage->extractLangFromScripts( $_REQUEST['module'] );
        
        if( empty( $_SESSION['CLL10N']['langs'] ) )
        {
          $dialogBox->info( get_lang('Nothing to translate in this module.') . '<br /><br />' . "\n\n"
          . '<a href="'.$_SERVER['PHP_SELF'].'">' . get_lang( 'Go back to the list' ) . '</a>' );
        }
        else
        {
          $dialogBox->question( get_lang( 'The script found %langsNb strings in this module files.', array( '%langsNb' => count($_SESSION['CLL10N']['langs']))) . '<br /><br />' ."\n\n"
          . get_lang( 'Do you want to export it ?') . '<br />' . "\n"
          . '<a href="'. htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] .'?cmd=exGenerate&module='. $_REQUEST['module'])) .'">' . get_lang( 'Yes' ) . '</a>'
          . ' | '
          . '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang( 'No' ) . '</a>'
          );
        }
        $out .= $dialogBox->render();
      }
      break;
    
  }
  
  Claroline::getDisplay()->body->appendcontent( $out ); 
}
catch(Exception $e )
{
  if ( claro_debug_mode() )
  {
    $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
  }
  else
  {
    $dialogBox->error( $e->getMessage() );
  }
  
  Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}
  
echo $claroline->display->render();

?>