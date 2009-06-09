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

class TranslationManage{
  
  public function availableLanguages( $module_label, $returnPath = false )
  {
    $path = get_module_path( $module_label ) . '/lang';
    
    $availbleLanguages = array( 0 => '');
    
    $files = new DirectoryIterator( $path );
    
    foreach( $files as $file )
    {
      if( !( $file->isDot() || $file->isDir() ) && ( strpos( $file->getFilename(), 'incomplete' ) === false ) && $file->isFile() && substr($file->getFilename(), -4) == '.php' )
      {
        $lang = substr( $file->getFilename(), strpos( $file->getFilename(), '_' ) +1 );
        $lang = substr( $lang, 0, strpos( $lang, '.' ) );
        
        if( $returnPath )
        {
          $availbleLanguages[$lang] = $file->getPathname();
        }
        else
        {
          $availbleLanguages[$lang] = $lang;
        }
        
      }
    }
    
    return $availbleLanguages;
  }
  /**
   * Get the list of module installed on the platform
   *
   * @author Dimitri Rambout <dim@claroline.net>   *
   * @return array modules list
   */
  public function moduleList()
  {
    $tbl_name        = claro_sql_get_main_tbl();
    $tbl_module      = $tbl_name['module'];
    
    $sql = 'SELECT `label`, `name`
            FROM `' . $tbl_module . '`
            WHERE `activation` = "activated"
            ORDER BY `name`';
    
    $rows = claro_sql_query_fetch_all( $sql );
    
    $modules = array();
    foreach( $rows as $row )
    {
      $modules[$row['label']]['name'] = $row['name'];
      $moduleData = get_module_data( $row['label'] );
      
      if( is_null( $moduleData['icon'] ) )
      {
        if( file_exists( get_module_path( $row['label'] ) . '/' . 'icon.png' ) )
        {
          $icon = 'icon.png';
        }
        elseif( file_exists( get_module_path( $row['label'] ) . '/' . 'icon.gif' ) )
        {
          $icon = 'icon.gif';
        }
        else
        {
          $icon = '';
        }
      }
      else
      {
        $icon = $moduleData['icon'];
      }
      $modules[$row['label']]['icon'] = get_module_url( $row['label'] ) . '/' . $icon;
    }
    
    return $modules;
  }
  
  /**
   *
   *
   *
   */
  public function compareLang( $module_label, $lang )
  {
    $path = get_module_path( $module_label ) . '/lang/lang_' . $lang . '.php';
    
    if( !is_file ( $path ) )
    {
      return false;
    }
    
    require( $path );
    
    if( ! ( isset( $_lang ) && is_array( $_lang ) ) )
    {
      return false;
    }
    
    $extractedLangs = array_flip( $this->extractLangFromScripts( $module_label ) );
    
    $notUsedLangs = array();
    
    foreach( $_lang as $key => $value )
    {
      if( ! isset( $extractedLangs[$key] ) )
      {
        $notUsedLangs[$key] = $value;
      }
    }
    
    return $notUsedLangs;
  }
  
  /**
   * Return the number of translate sentence in the file
   *
   * @author Dimitri Rambout <dim@clarolinet.net>
   * @param string $lang_file path of the language file
   * @return int number of translate sentence in array $_lang
   *
   */
  private function getLangProgression( $lang_file, &$langs )
  {
    if( !is_file( $lang_file ) )
    {
      return 0;
    }
    
    require( $lang_file );
    if( isset( $_lang ) && is_array( $_lang ) )
    {
      $i = 0;
      
      foreach( $langs as $lang )
      {
        if(isset( $_lang[$lang] ))
        {
          $i++;
        }
      }
      return $i;
    }
    else
    {
      return 0;
    }
  }
  /**
   * Get the translation progression of a module
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param string $module_label Label of a module
   * @return array progressions of language files in %
   */
  public function getModuleProgression( $module_label )
  {
    $progression = array();
    
    $path = get_module_path( $module_label ) . '/lang';
    
    if( !is_dir( $path ) )
    {
      return $progression;
    }
    
    $languageVarList = $this->extractLangFromScripts( $module_label );
    $nbLanguageVarList = count( $languageVarList );
    
    $langs = $this->availableLanguages( $module_label, true);
    
    foreach( $langs as $lang => $lang_path )
    {
        $lang_progression = $this->getLangProgression( $lang_path, $languageVarList );
        
        $lang_progression = floor( 100 - (($nbLanguageVarList - $lang_progression) / $nbLanguageVarList * 100) );
        
        $progression[$lang] = $lang_progression;
    }
    
    return $progression;
    
  }  
  
  /**
   * Extract language strings from get_lang & get_block in a file
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param string $file path of a file
   * @return array Array of found strings
   */
  private function extractLangFromFile( $file )
  {
    $languageVarList = get_lang_vars_from_file( $file );
    
    return $languageVarList;
  }
  
  /**
   * Create a file with non translated sentence
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param array $langs array of sentence to build the $_lang array in the file
   * @param string $file path of the incomplete file
   *
   * @return boolean True if content put successfully in the file or False if it's failed
   *
   */
  private function buildIncompleteLangFile( $langs , $file )
  {
    $content = '<?php ' . "\n\n";
    foreach( $langs as $lang){
      $lang = str_replace( "'", "\'", $lang);
      $content .= '$_lang[\''. $lang .'\'] = \'' . $lang . '\';' . "\n";
    }
    $content .= "\n" . '?>';
    
    if( file_put_contents( $file, $content) )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * create a lang file
   *
   * @param array $langs Langs that we need to put in the file
   * @param string $path Directory path of the lang file
   *
   * @return boolean True of False
   *
   */  
  public function createLangFile( $langs, $path, $lang)
  {
    
    $file = $path . '/lang_'. $lang .'.php';
    $file_incomplete = $path . '/lang_'. $lang .'.incomplete.php';
    if( is_file( $file ) )
    {
      include_once( $file );
      
      if( !empty( $_lang ) && is_array( $_lang ) )
      {
       
       $langs = array_flip( $langs );
       foreach( $_lang as $id => $l)
       {
          if( isset( $langs[ $id ] ) )
          {
            unset( $langs[ $id ] );
          }
       }
       $langs = array_flip( $langs );
       unset($_lang);
      }
    }
    else
    {
      touch( $file );
    }
    
    // build incomplete file
    return $this->buildIncompleteLangFile( $langs, $file_incomplete );
  }
  
  /**
   * Create all language files (minimum english)
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param array $langs Langs exracted from scripts
   * @param string $path Lang path in the module
   * @return boolean
   */
  public function createLangFiles( $langs, $path )
  {
    if(!is_dir( $path ) )
    {
      if( ! claro_mkdir( $path ) )
      {
        claro_die( get_lang('Unable to create lang directory.' ) );
      }
    }
    
    $files = new DirectoryIterator( $path );
    
    $langs_exported = array();
    $error = false;
    
    if( ! $this->createLangFile( $langs, $path, 'english' ) )
    {
      $error = true;
    }
    else
    {
      $langs_exported[ 'english' ] = 'english';
    }
    
    foreach( $files as $file )
    {
      if( !( $file->isDot() || $file->isDir() ) )
      {
        $lang = substr( $file->getFilename(), strpos( $file->getFilename(), '_' ) +1 );
        $lang = substr( $lang, 0, strpos( $lang, '.' ) );
        if( ! isset( $langs_exported[ $lang ] ) )
        {
          if( ! $this->createLangFile( $langs, $path, $lang) )
          {
            $error = true;
            break;
          }
          else
          {
            $langs_exported[ $lang ] = $lang;
          }
        }
      }      
    }
    
    return !$error;
  }
  
  
  /**
   * Extract language strings from scripts
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param string $moduleId Module ID
   * @return array language strings
   */
  public function extractLangFromScripts( $moduleId ){
    $module_path = get_module_path( $moduleId );
    $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $module_path ), RecursiveIteratorIterator::SELF_FIRST );
    
    $languageVarList = array();
    
    foreach( $files as $file )
    {
      if( $file->isFile() && substr($file->getFilename(), -4) == '.php' )
      {
        $languageVarList = array_merge( $languageVarList, array_flip($this->extractLangFromFile( $file->getPathName() ) ));
        
      }
    }
    ksort($languageVarList);
    $languageVarList = array_keys( $languageVarList );
    
    return $languageVarList;
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