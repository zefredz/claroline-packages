<?php // $Id$

/**
 * Claroline Translation Tool
 *
 * @version     CLL10N 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLL10N
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class TranslationRenderer{
  
  /**
   * Display modules list
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param array $list modules list
   *
   * @return string html content
   */
  public static function moduleList( $list )
  {
    $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/moduleList.tpl.php' );
    
    $tpl->assign('moduleList', $list);
    
    return $tpl->render();
  }
  
  /**
   *
   *
   */
  public static function compareTranslation( $moduleLabel, $availableLanguages, $selectedLanguage = null, $outdatedLangs = null )
  {
    $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/compareTranslation.tpl.php' );
    
    $tpl->assign( 'availableLanguages', $availableLanguages);
    $tpl->assign( 'moduleLabel', $moduleLabel);
    $tpl->assign( 'selectedLanguage', $selectedLanguage);
    $tpl->assign( 'outdatedLangs', $outdatedLangs);
    
    $dialogBox = new DialogBox();
    
    $tpl->assign( 'dialogBox', $dialogBox);
    
    return $tpl->render();
  }
  /**
   * Display module translation progression
   *
   * @author Dimitri Rambout<dim@claroline.net>
   * @param array $progression array of progression for each language found in the lang directory
   *
   * @return string html content
   */
  public static function moduleProgression( $moduleLabel, $progression )
  {
    $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/moduleProgression.tpl.php' );
    
    $cmdMenu[] = claro_html_cmd_link( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ), '<img src="' . get_icon('translate') . '" alt="" />' . get_lang('Go back to the list'));
    $cmdMenu[] = claro_html_cmd_link( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=' . $moduleLabel ) ), '<img src="' . get_icon('translate_add') . '" alt="" />' . get_lang('Generate language files'));
    
    $tpl->assign( 'cmdMenu', $cmdMenu);
    $tpl->assign( 'progression', $progression);
    
    return $tpl->render();
  }
  
  // Singleton constructor
    
  private static $instance = false;
  
  public static function getInstance()
  {
    if ( !self::$instance )
    {
        self::$instance = new self();
    }
    
    return self::$instance;
  }
}

?>