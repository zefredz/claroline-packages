<?php

class PersistantVariablesRegistry
{
    protected $registry;
    protected $file, $autoSave;
    
    public function __construct( $file, $autoSave = true )
    {
        $this->registry = drupal_parse_info_file( $file );
        $this->file = $file;
        $this->autoSave = $autoSave ? true : false;
    }
    
    public function __destruct()
    {
        $this->save();
        unset ( $this->registry );
        unset ( $this->file );
    }
    
    public function get( $name, $default = null )
    {
        if ( !array_key_exists($name,$this->registry) )
        {
            // throw new Exception("Variable {$name} not found");
            
            return $default;
        }
        
        return unserialize( $this->registry[$name] );
    }
    
    public function set( $name, $value )
    {
        $this->registry[$name] = serialize( $value );
        
        $this->autoSave && $this->save();
    }
    
    public function delete( $name )
    {
        if ( !array_key_exists($name,$this->registry) )
        {
            throw new Exception("Variable {$name} not found");
        }
        
        $tmp =  $this->registry[$name];
        
        unset($this->registry[$name]);
        
        $this->autoSave && $this->save();
        
        return unserialize( $tmp );
    }
    
    public function save()
    {
        $contents = '';
        
        foreach ( $this->registry as $key => $value )
        {
            $value = addslashes( $value );
            $contents .= "{$key}=\"{$value}\"\n";
        }
        
        file_put_contents( $this->file, $contents );
        
        chmod( $this->file, CLARO_FILE_PERMISSIONS );
    }
}

class PersistantVariableStorage
{
    protected static $main = false;
    protected static $course = false;
    protected static $module = false;
    
    public static function main()
    {
        if ( ! self::$main )
        {
            self::$main = new PersistantVariablesRegistry( get_path('rootSys').'platform/registry.ini' );
        }
        
        return self::$main;
    }
    
    public static function course( $cid = null )
    {
        // std claroline magik !
        $cid = is_null($cid) ? claro_get_current_course_id() : $cid;
        
        if ( ! self::$course )
        {
            self::$course = array();
        }
        
        if ( ! isset( self::$course[$cid] ) )
        {
            self::$course[$cid] = new PersistantVariablesRegistry( claro_get_course_path($cid).'registry.ini' );
        }
        
        return self::$course[$cid];
    }
    
    public static function module( $label = null )
    {
        // std claroline magik !
        $label = is_null($label) ? claro_get_current_module_label() : $label;
        
        if ( ! self::$module )
        {
            self::$module = array();
        }
        
        if ( ! isset( self::$module[$label] ) )
        {
            self::$module[$label] = new PersistantVariablesRegistry( get_path('rootSys').'platform/'.$label.'.ini' );
        }
        
        return self::$module[$label];
    }
}

// Taken from Drupal code source :

function drupal_parse_info_file($filename) {
  $info = array();

  if (!file_exists($filename)) {
    return $info;
  }

  $data = file_get_contents($filename);
  if (preg_match_all('
    @^\s*                           # Start at the beginning of a line, ignoring leading whitespace
    ((?:
      [^=;\[\]]|                    # Key names cannot contain equal signs, semi-colons or square brackets,
      \[[^\[\]]*\]                  # unless they are balanced and not nested
    )+?)
    \s*=\s*                         # Key/value pairs are separated by equal signs (ignoring white-space)
    (?:
      ("(?:[^"]|(?<=\\\\)")*")|     # Double-quoted string, which may contain slash-escaped quotes/slashes
      (\'(?:[^\']|(?<=\\\\)\')*\')| # Single-quoted string, which may contain slash-escaped quotes/slashes
      ([^\r\n]*?)                   # Non-quoted string
    )\s*$                           # Stop at the next end of a line, ignoring trailing whitespace
    @msx', $data, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
      // Fetch the key and value string
      $i = 0;
      foreach (array('key', 'value1', 'value2', 'value3') as $var) {
        $$var = isset($match[++$i]) ? $match[$i] : '';
      }
      $value = stripslashes(substr($value1, 1, -1)) . stripslashes(substr($value2, 1, -1)) . $value3;

      // Parse array syntax
      $keys = preg_split('/\]?\[/', rtrim($key, ']'));
      $last = array_pop($keys);
      $parent = &$info;

      // Create nested arrays
      foreach ($keys as $key) {
        if ($key == '') {
          $key = count($parent);
        }
        if (!isset($parent[$key]) || !is_array($parent[$key])) {
          $parent[$key] = array();
        }
        $parent = &$parent[$key];
      }

      // Handle PHP constants
      if (defined($value)) {
        $value = constant($value);
      }

      // Insert actual value
      if ($last == '') {
        $last = count($parent);
      }
      $parent[$last] = $value;
    }
  }

  return $info;
}

