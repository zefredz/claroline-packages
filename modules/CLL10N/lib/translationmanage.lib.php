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
    if( $module_label == 'PLATFORM' )
    {
      $path = get_path( 'clarolineRepositorySys' ) . '/lang';
    }
    else
    {
      $path = get_module_path( $module_label ) . '/lang';
    }
    
    $availbleLanguages = array( 0 => '');
    
    $files = new DirectoryIterator( $path );
    
    foreach( $files as $file )
    {
      if( $module_label == 'PLATFORM' )
      {
        if( $file->isDir() && !$file->isDot() && !(substr($file->getBasename(),0,1) == '.') )
        {
          $lang = $file->getBasename();
          $availbleLanguages[$lang] = $lang;
        }
      }elseif( !( $file->isDot() || $file->isDir() ) && ( strpos( $file->getFilename(), 'incomplete' ) === false ) && $file->isFile() && substr($file->getFilename(), -4) == '.php' )
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
    if( $module_label == 'PLATFORM' )
    {
      $path = get_path( 'clarolineRepositorySys' ) . '/lang/' . $lang . '/complete.lang.php';
      $path_install = get_path( 'clarolineRepositorySys' ) . '/lang/' . $lang . '/install.lang.php';
    }
    else
    {
      $path = get_module_path( $module_label ) . '/lang/lang_' . $lang . '.php';
    }
    
    if( !( is_file ( $path ) ) )
    {
      return false;
    }
    
    require( $path );
    
    if( ! ( isset( $_lang ) && is_array( $_lang ) ) )
    {
      return false;
    }
    else
    {
      $_lang_tmp = $_lang;
    }
    
    $extractedLangs = array_flip( $this->extractLangFromScripts( $module_label ) );
    
    //skip install lang
    /*if( $module_label == 'PLATFORM' )
    {
      if( is_file( $path_install ) )
      {
        require( $path_install );
        foreach( $_lang as $key => $value )
        {
          if( isset( $extractedLangs[ $key ] ) )
          {
            unset( $extractedLangs[ $key ] );
          }
        }
      }      
    }*/
    
    $_lang = $_lang_tmp;
    unset( $_lang_tmp );
    
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
    
    if( $module_label == 'PLATFORM' )
    {
      $path = get_path( 'clarolineRepositorySys' ) . '/lang/';
    }
    else
    {
      $path = get_module_path( $module_label ) . '/lang';
    }
    
    if( !is_dir( $path ) )
    {
      return $progression;
    }
    
    $languageVarList = $this->extractLangFromScripts( $module_label );
    $nbLanguageVarList = count( $languageVarList );
    
    //skip install lang
    /*if( $module_label == 'PLATFORM' )
    {
      $path_install = $path . 'english/install.lang.php';
      if( is_file( $path_install ) )
      {
        $languageVarList = array_flip( $languageVarList );
        require( $path_install );
        foreach( $_lang as $key => $value )
        {
          if( isset( $languageVarList[ $key ] ) )
          {
            var_dump( $languageVarList[ $key ] );
            unset( $languageVarList[ $key ] );
          }
        }
        $languageVarList = array_flip( $languageVarList );
      }      
    }*/
    $langs = $this->availableLanguages( $module_label, true);
    
    if( isset( $langs[0] ) && empty( $langs[0] ) )
    {
      unset( $langs[0] );
    }
    
    foreach( $langs as $lang => $lang_path )
    {
        if( $module_label == 'PLATFORM' )
        {
          $lang_path = $path . $lang_path . '/complete.lang.php';
          $lang_progression = $this->getLangProgression( $lang_path, $languageVarList );
        }
        else
        {
          $lang_progression = $this->getLangProgression( $lang_path, $languageVarList );
        }
        
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
   *
   *
   */
  private function extractLangFromDefConfFile( $file )
  {
    require_once( $file );
    
    $languageVarList = array();
    $rejectValues = array( 'boolean', 'enum', 'integer', 'multi', 'relpath', 'string', 'syspath', 'urlpath' );
    
    if( isset( $conf_def ) && is_array( $conf_def ) )
    {
      if( isset( $conf_def[ 'config_name' ] ) )
      {
        $languageVarList[] = $conf_def['config_name'];
      }
      foreach( $conf_def['section'] as $section )
      {
        if( is_array( $section ) )
        {
          if( isset( $section['label'] ) )
          {
            $languageVarList[] = $section['label'];
          }
          if( isset( $section['description'] ) )
          {
            $languageVarList[] = $section['description'];
          }
        }
      }
      if( isset( $conf_def['section']['main']['label'] ) )
      {
        $languageVarList[] = $conf_def['section']['main']['label'];
      }
      if( isset( $conf_def['section']['quota']['description'] ) )
      {
        $languageVarList[] = $conf_def['section']['quota']['description'];
      }      
    }
    
    if( isset( $conf_def_property_list ) && is_array( $conf_def_property_list ) )
    {
      foreach( $conf_def_property_list as $key => $prop )
      {
        if( isset( $prop[ 'label' ] ) && $prop[ 'label' ] )
        {
          $languageVarList[] = $prop[ 'label' ];
        }
        if( isset( $prop[ 'description' ] ) && $prop[ 'description' ] )
        {
          $languageVarList[] = $prop[ 'description' ];
        }
        if( isset( $prop[ 'acceptedValue' ] ) && is_array( $prop['acceptedValue'] ) )
        {
          foreach( $prop[ 'acceptedValue' ] as $acceptedValue )
          {
            if( ! is_int( $acceptedValue ) || !in_array( $acceptedValue, $rejectValues ) )
            {
              $languageVarList[] = $acceptedValue;
            }
          }
        }
      }
    }
    
    return array_unique( $languageVarList );
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
    $rejectValues = array( 'boolean', 'enum', 'integer', 'multi', 'relpath', 'string', 'syspath', 'urlpath' );
    
    $content = '<?php ' . "\n\n";
    foreach( $langs as $lang){      
      if( !in_array( $lang, $rejectValues ) )
      {
        $lang = str_replace( "'", "\'", $lang);
        $content .= '$_lang[\''. $lang .'\'] = \'' . $lang . '\';' . "\n";
      }
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
   * create a install lang file
   *
   * @param array $langs Langs that we need to put in the file
   * @param string $path Directory path of the lang file
   *
   * @return boolean True of False
   *
   */  
  public function createInstallLangFile( $langs, $path, $lang )
  {
    $file = $path . '/' . $lang . '/install.lang.php';
    $file_incomplete = $path . '/' . $lang . '/missing.install.lang.php';
    
    if( is_file( $file ) )
    {
      include( $file );
      
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
   * create a lang file
   *
   * @param array $langs Langs that we need to put in the file
   * @param string $path Directory path of the lang file
   *
   * @return boolean True of False
   *
   */  
  public function createLangFile( $langs, $path, $lang, $isPlatform = false )
  {
    if( $isPlatform )
    {
      $file = $path . '/' . $lang . '/complete.lang.php';
      $file_install = $path . '/' . $lang . '/install.lang.php';
      $file_incomplete = $path . '/' . $lang . '/missing.lang.php';
    }
    else
    {
      $file = $path . '/lang_'. $lang .'.php';
      $file_incomplete = $path . '/lang_'. $lang .'.incomplete.php';      
    }
    
    if( isset( $file_install) && is_file( $file_install) )
    {
      include( $file_install );
    }
    
    if( is_file( $file ) )
    {
      include( $file );
      
      if( !empty( $langs) && is_array( $langs ) && !empty( $_lang ) && is_array( $_lang ) )
      {
        foreach( $langs as $key => $value)
        {
          if( array_key_exists( $value, $_lang ) )
          {
            unset( $langs[ $key ] );
          }
          elseif( $value == '' || is_numeric( $value ) )
          {
            unset( $langs[ $key ] );
          }
        }
      }
      
      unset( $_lang );
      /*$tmp_langs = array();
      if( !empty( $_lang ) && is_array( $_lang ) )
      {
       $langs = array_flip( $langs );
       foreach( $_lang as $id => $l)
       {
          if( isset( $langs[ $id ] ) )
          {
            unset( $langs[ $id ] );
          }
          elseif( $id == '' || is_numeric( $id ) )
          {
            unset( $langs[ $id ] );
          }
          else
          {
            $tmp_langs[] = $id;
          }
       }
       var_dump( $tmp_langs );
       $langs = $tmp_langs;
       unset( $tmp_langs );
       unset($_lang);
      }*/
    }
    else
    {
      touch( $file );
    }
    // build incomplete file
    return $this->buildIncompleteLangFile( $langs, $file_incomplete );
  }
  
  /**
   * Create all install language files (minimum english)
   *
   * @author Dimtiri Rambout <dim@claroline.net>
   * @param array $langs Langs extracted from scrpts
   * @param string $path Lang path in the module
   * 
   * @return boolean
   */
  public function createInstallLangFiles( $langs, $path )
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
    
    if( ! $this->createInstallLangFile( $langs, $path, 'english' ) )
    {
      $error = true;
    }
    else
    {
      $langs_exported[ 'english' ] = 'english';
    }
    
    foreach( $files as $file )
    {
      if( !( $file->isDot() ) && $file->isDir() && substr( $file->getBasename(), 0, 1 ) != '.' )
      {
        $lang = $file->getBasename();
        //$path = $path . '/' . $lang;
        
        $files = new DirectoryIterator( $path );
        
        foreach( $files as $file )
        {
          if( !isset( $langs_exported[ $lang ] ) )
          {
            if( ! $this->createInstallLangFile( $langs, $path, $lang ) )
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
    }
  }
  /**
   * Create all language files (minimum english)
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param array $langs Langs exracted from scripts
   * @param string $path Lang path in the module
   * @param boolean $isPlatform True if plaftorm module
   * 
   * @return boolean
   */
  public function createLangFiles( $langs, $path, $isPlatform )
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
    
    if( ! $this->createLangFile( $langs, $path, 'english', $isPlatform ) )
    {
      $error = true;
    }
    else
    {
      $langs_exported[ 'english' ] = 'english';
    }
    
    foreach( $files as $file )
    {
      if( $isPlatform )
      {
        if( !( $file->isDot() ) && $file->isDir() && substr( $file->getBasename(), 0, 1 ) != '.' )
        {
          $lang = $file->getBasename();
          //$path = $path . '/' . $lang;
          
          $files = new DirectoryIterator( $path );
          
          foreach( $files as $file )
          {
            if( !isset( $langs_exported[ $lang ] ) )
            {
              if( ! $this->createLangFile( $langs, $path, $lang, $isPlatform ) )
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
      }
      elseif( !( $file->isDot() || $file->isDir() ) )
      {
        $lang = substr( $file->getFilename(), strpos( $file->getFilename(), '_' ) +1 );
        $lang = substr( $lang, 0, strpos( $lang, '.' ) );
        if( ! isset( $langs_exported[ $lang ] ) )
        {
          if( ! $this->createLangFile( $langs, $path, $lang, $isPlatform ) )
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
   *
   */
  public function cleanLangFile( $moduleLabel, $lang )
  {
    $deprecatedLang = $this->compareLang( $moduleLabel, $lang );
    
    $langFile = get_module_path( $moduleLabel ) . '/lang/lang_' . $lang . '.php';
    if( is_file( $langFile ) )
    {
      require( $langFile );
      if( !empty( $_lang ) && is_array( $_lang ) )
      {
        foreach( $deprecatedLang as $key => $value )
        {
          if( isset( $_lang[$key] ) )
          {
            unset( $_lang[$key] );
          }
        }
        $content = '<?php ' . "\n\n";
        foreach( $_lang as $key => $value){
          $key = str_replace( "'", "\'", $key);
          $value = str_replace( "'", "\'", $value);
          $content .= '$_lang[\''. $key .'\'] = \'' . $value . '\';' . "\n";
        }
        $content .= "\n" . '?>';
        return file_put_contents( $langFile, $content);
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Extract language strings from scripts
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @param string $moduleId Module ID
   * @return array language strings
   */
  public function extractLangFromScripts( $moduleId, $restrictedDirectory = null ){
    if( $moduleId == 'PLATFORM' )
    {
      $module_path = get_path( 'clarolineRepositorySys' ) . '/';
      if( ! is_null( $restrictedDirectory ) )
      {
        $module_path .= $restrictedDirectory . '/';
      }
    }
    else
    {
      $module_path = get_module_path( $moduleId );
      if( ! is_null( $restrictedDirectory ) )
      {
        $module_path .= '/' . $restrictedDirectory . '/';
      }
    }
    
    set_time_limit(0);
    $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $module_path ), RecursiveIteratorIterator::SELF_FIRST );
    
    $languageVarList = array();
    foreach( $files as $file )
    {
      $fileIsOk = true;
      if( $moduleId == 'PLATFORM' && $restrictedDirectory != 'install' )
      {
        if( !( strpos( $file->getPathname(), 'install\\' ) === false ) )
        {
          $fileIsOk = false;
          continue; 
        }
        elseif( ! ( strpos( $file->getPathname(), 'install/' ) === false ) )
        {
          $fileIsOk = false;
          continue;
        }
      }
      
      if( $fileIsOk && $file->isFile() && substr($file->getFilename(), -4) == '.php' )
      {
        if( strpos( $file->getFilename(), '.def.conf.inc.php' ) === false )
        {
          $languageVarList = array_merge( $languageVarList, array_flip($this->extractLangFromFile( $file->getPathName() ) ));
        }
        else
        {
          //extract from def.conf.inc file
          $languageVarList = array_merge( $languageVarList, array_flip( $this->extractLangFromDefConfFile( $file->getPathName() ) ));
        }
      }
    }
    
    // get langs from core
    $core_lang = get_path( 'clarolineRepositorySys' ) . 'lang/english/complete.lang.php';
    if( $moduleId != 'PLATFORM' && is_file( $core_lang ) )
    {
      require( $core_lang );
      if( !empty($_lang)  && is_array( $_lang ) )
      {
        foreach( $languageVarList as $key => $value)
        {
          if( isset( $_lang[$key] ) )
          {
            unset( $languageVarList[$key] );
          }
        }
        unset( $_lang );
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

class InstallFilter extends FilterIterator
{
  public function accept()
  {
    if( strpos( parent::current(), 'install/' ) === false )
    {
      return true;
    }
    
    return false;
  }
}

?>