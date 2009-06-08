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