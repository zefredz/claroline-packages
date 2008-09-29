<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * PDOMapperSchema
     *
     * @version     $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     pdocrud
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Parse a XML schema describing a mapper Object and give access
     * to the schema contents
     */
    class PDOMapperSchema
    {
        protected $schema;
        
        protected $className;
        protected $tableName;
        protected $tableKey;
        
        // raw attributes
        protected $attributes = array();
        
        protected $fields = array();
        protected $requiredAttributes = array();
        protected $defaultValues = array();
        protected $editableAttributes = array();
        protected $allowedValues = array();
        
        protected $hasOne = array();
        protected $hasMany = array();
        protected $hasAndBelongsTo = array();
        
        /**
         * Constructor
         * @param   string $schema XML schema
         */
        public function __construct( $schema )
        {
            $this->schema = $schema;
            
            $this->parse();
        }
        
        /**
         * Check if the given attribute is required in the schema
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function isRequired( $attribute )
        {
            return in_array( $attribute, $this->requiredAttributes );
        }
        
        /**
         * Check if the given value is allowed for the given attribute in the schema
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function isAllowed( $attribute, $value )
        {
            return !array_key_exists( $attribute, $this->allowedValues )
                || in_array( $value, $this->allowedValues[$attribute] ) ;
        }
        
        /**
         * Check if the given attribute is editable in the schema
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function isEditable( $attribute )
        {
            return in_array( $attribute, $this->editableAttributes );
        }
        
        /**
         * Check if the given attribute has a list of allowed values in the schema
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function hasAllowedValues( $attribute )
        {
            return array_key_exists( $attribute, $this->allowedValues );
        }
        
        /**
         * Get the allowed values for the attribute in the schema
         * @param   string $attribute name of the attribute
         * @return  array
         * @throws  Exception if no allowed values for this attribute
         */
        public function getAllowedValues( $attribute )
        {
            if ( ! $this->hasAllowedValues( $attribute ) )
            {
                throw new Exception( 'No default value for attribute ' . $attribute );
            }
            
            return $this->allowedValues[$attribute];
        }
        
        /**
         * Check if the given attribute has a default value in the schema
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function hasDefaultValue( $attribute )
        {
            return array_key_exists( $attribute, $this->defaultValues );
        }
        
        /**
         * Get the default value of the given attribute in the schema
         * @param   string $attribute name of the attribute
         * @return  string value
         * @throws  Exception if the attribute has no default value
         */
        public function getDefaultValue( $attribute )
        {
            if ( ! $this->hasDefaultValue( $attribute ) )
            {
                throw new Exception( 'No default value for attribute ' . $attribute );
            }
            
            return $this->defaultValues[$attribute];
        }
        
        public function getAttributes()
        {
            return $this->attributes;
        }
        
        /**
         * Check if the given attribute is a member of an has one relation
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function hasOne( $attribute )
        {
            return array_key_exists( $attribute, $this->hasOne );
        }
        
        /**
         * Get the has one relation for the given attribute
         * @param   string $attribute name of the attribute
         * @return  array has one relation
         * @throws  Exception if the attribute is not part of a has one relation
         */
        public function getHasOneRelation( $attribute )
        {
            if ( ! $this->hasOne( $attribute ) )
            {
                throw new Exception( 'No has one relation for attribute ' . $attribute );
            }
            
            return $this->hasOne[$attribute];
        }
        
        /**
         * Get the number of has many relations
         * @return  int
         */
        public function countHasOneRelations()
        {
            return count( $this->hasOne );
        }
        
        /**
         * Get the list of has many relations
         * @return  array
         */
        public function getHasOneRelationList()
        {
            return $this->hasOne;
        }
        
        /**
         * Check if the given attribute is a member of an has many relation
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function hasMany( $attribute )
        {
            return array_key_exists( $attribute, $this->hasMany );
        }
        
        /**
         * Get the has many relation for the given attribute
         * @param   string $attribute name of the attribute
         * @return  array has many relation
         * @throws  Exception if the attribute is not part of a has many relation
         */
        public function getHasManyRelation( $attribute )
        {
            if ( ! $this->hasMany( $attribute ) )
            {
                throw new Exception( 'No has many relation for attribute ' . $attribute );
            }
            
            return $this->hasMany[$attribute];
        }
        
        /**
         * Get the number of has many relations
         * @return  int
         */
        public function countHasManyRelations()
        {
            return count( $this->hasMany );
        }
        
        /**
         * Get the list of has many relations
         * @return  array
         */
        public function getHasManyRelationList()
        {
            return $this->hasMany;
        }
        
        /**
         * Check if the given attribute is a member of an has and belongs to relation
         * @param   string $attribute name of the attribute
         * @return  boolean
         */
        public function hasAndBelongsTo( $attribute )
        {
            return array_key_exists( $attribute, $this->hasAndBelongsTo );
        }
        
        /**
         * Get the has and belongs to relation for the given attribute
         * @param   string $attribute name of the attribute
         * @return  array has and belongs to relation
         * @throws  Exception if the attribute is not part of a has and belongs to relation
         */
        public function getHasAndBelongsToRelation( $attribute )
        {
            if ( ! $this->hasAndBelongsTo( $attribute ) )
            {
                throw new Exception( 'No has and belongs to relation for attribute ' . $attribute );
            }
            
            return $this->hasAndBelongsTo[$attribute];
        }
        
        /**
         * Get the number of has and belongs to relations
         * @return  int
         */
        public function countHasAndBelongsToRelations()
        {
            return count( $this->hasAndBelongsTo );
        }
        
        /**
         * Get the list of has and belongs to relations
         * @return  array
         */
        public function getHasAndBelongsToRelationList()
        {
            return $this->hasAndBelongsTo;
        }
        
        /**
         * Get the database field name for the given attribute name
         * @param   string $attribute
         * @return  string field name
         */
        public function getField( $attribute )
        {
            if ( array_key_exists( $attribute, $this->fields ) )
            {
                return $this->fields[$attribute];
            }
            else
            {
                throw new Exception('No mapping for given attribute ' . $attribute . ' in ' . $this->getClass());
            }
        }
        
        /**
         * Get the list of fields in the schema
         * @param   string $attribute name of the attribute
         * @return  array field list arranged by attributeName => fieldName
         */
        public function getFieldList()
        {
            return $this->fields;
        }
        
        /**
         * Get the list of attributes in the schema
         * @param   string $attribute name of the attribute
         * @return  array attribute list
         */
        public function getAttributeList()
        {
            return array_keys( $this->fields );
        }
        
        /**
         * Get the class name for the schema
         * @return  string
         */
        public function getClass()
        {
            return $this->className;
        }
        
        /**
         * Get the database table name for the schema
         * @return  string
         */
        public function getTable()
        {
            return $this->tableName;
        }
        
        /**
         * Get the name of the key attribute for the schema
         * @return  string
         */
        public function getKey()
        {
            return $this->tableKey;
        }

        /**
         * Load the mapping schema
         * @throws  Exception
         */
        protected function parse()
        {
            $xml = simplexml_load_string( $this->schema );

            // parse class and table
            
            if ( ! ( $classElem = $xml->class->attributes() ) )
            {
                throw new Exception('Malformed Schema : missing class declaration');
            }
            
            if ( ! $classElem['name'] )
            {
                throw new Exception('Malformed schema : missing class name');
            }
            
            $this->className = "{$classElem['name']}";
            
            if ( ! $classElem['table'] )
            {
                // use pluralizer here ?
                throw new Exception('Malformed schema : missing table name');
            }
            
            $this->tableName = "{$classElem['table']}";
            
            // parse key
            
            if ( ! ( $key = $xml->key ) || ! $key['name'] )
            {
                throw new Exception('Malformed schema : missing key declaration');
            }
            
            $this->tableKey = "{$key['name']}";

            // parse attributes
            
            foreach ( $xml->attribute as $attribute )
            {
                $attr = $attribute->attributes();
                
                // attribute-field mapping
                
                if ( ! $attr['name'] )
                {
                    throw new Exception('Malformed schema : missing attribute name');
                }
                
                $this->attributes["{$attr['name']}"] = array();
                $this->attributes["{$attr['name']}"]['name'] = "{$attr['name']}"; 
                
                if ( ! $attr['field'] )
                {
                    $this->fields["{$attr['name']}"] = "{$attr['name']}";
                    $this->attributes["{$attr['name']}"]['field'] = "{$attr['name']}";
                }
                else
                {                
                    $this->fields["{$attr['name']}"] = "{$attr['field']}";
                    $this->attributes["{$attr['name']}"]['field'] = "{$attr['field']}";
                }
                
                // is attribute required ?
                
                if ( isset( $attr['required'] )
                    && "{$attr['required']}" == 'true' )
                {
                    $this->requiredAttributes[] = "{$attr['name']}";
                    $this->attributes["{$attr['name']}"]['required'] = true;
                }
                else
                {
                    $this->attributes["{$attr['name']}"]['required'] = false;
                }
                
                // attribute has a default value ?
                
                if ( isset( $attr['default'] ) )
                {
                    $this->defaultValues["{$attr['name']}"] = "{$attr['default']}";
                    $this->attributes["{$attr['name']}"]['default'] = "{$attr['default']}";
                }
                
                if ( isset( $attr['editable'] )
                    && "{$attr['editable']}" == 'false' )
                {
                    $this->editableAttributes[] = "{$attr['name']}";
                    $this->attributes["{$attr['name']}"]['editable'] = false;
                }
                else
                {
                    $this->attributes["{$attr['name']}"]['editable'] = true;
                }
                
                if ( isset( $attr['values'] ) )
                {
                    $this->allowedValues["{$attr['name']}"] = explode( '|', "{$attr['values']}" );
                    $this->attributes["{$attr['name']}"]['values'] = explode( '|', "{$attr['values']}" );
                }
            }
            
            // parse hasone relations
            
            foreach ( $xml->hasone as $hasone )
            {
                $rel = $hasone->attributes();
                
                $this->parseRel( $rel, 'hasOne' );
            }
            
            // parse hasmany relations
            
            foreach ( $xml->hasmany as $hasmany )
            {
                $rel = $hasmany->attributes();
                
                $this->parseRel( $rel, 'hasMany' );
            }
            
            // parse has and belongs to many
            foreach ( $xml->hasandbelongsto as $habt )
            {
                $rel = $habt->attributes();
                
                $this->parseRel( $rel, 'hasAndBelongsTo' );
                
                if (! $rel['table'] )
                {
                    throw new Exception( "Missing relation table for {$rel['name']}" );
                }
                
                $this->hasAndBelongsTo["{$rel['name']}"]['table'] = "{$rel['table']}";
                
                if (! $rel['cols'] )
                {
                    throw new Exception( "Missing relation columns for {$rel['name']}" );
                }
                
                $colsArr = explode(':',"{$rel['cols']}");
                
                if ( count( $colsArr ) != 2 )
                {
                    throw new Exception( "Invalid relation columns {$rel['cols']} for {$rel['name']}" );
                }
                
                $this->hasAndBelongsTo["{$rel['name']}"]['cols'] = array();
                $this->hasAndBelongsTo["{$rel['name']}"]['cols']['left'] = $colsArr[0];
                $this->hasAndBelongsTo["{$rel['name']}"]['cols']['right'] = $colsArr[1];
                
//                var_dump( $this->hasAndBelongsTo );
            }
        }
        
        /**
         * Parse a relation
         * @param   domxmlelement $rel relation
         * @param   string $attrName name of the attribute of the relation 
         */
        protected function parseRel( $rel, $attrName )
        {
            if ( ! isset ( $this->$attrName ) )
            {
                throw new Exception ( 'Invalid relation name : ' . $attrName );
            }
            
            $relArray =& $this->$attrName;
            $xmlElemName = strtolower($attrName);
            
            if ( ! $rel['name'] )
            {
                throw new Exception("Malformed schema : missing {$xmlElemName} relation attribute name");
            }
            
            $relArray["{$rel['name']}"] = array();
            
            if ( ! $rel['class'] )
            {
                throw new Exception("Malformed schema : missing {$xmlElemName} relation class name");
            }
            
            $relArray["{$rel['name']}"]['class'] = "{$rel['class']}";
            
            if ( $attrName != 'hasAndBelongsTo' )
            {
                // parse triggers
                if ( ! $rel['ondelete'] )
                {
                    $relArray["{$rel['name']}"]['ondelete'] = 'keep';
                }
                else
                {
                    if ( "{$rel['ondelete']}" == 'keep' 
                        || "{$rel['ondelete']}" == 'delete' )
                    {
                        $relArray["{$rel['name']}"]['ondelete'] = "{$rel['ondelete']}";
                    }
                    else
                    {
                        throw new Exception('Malformed schema : invalid ondelete trigger');
                    }
                }
            }
            
            $relArray["{$rel['name']}"]['rel'] = array();
            
            if ( $rel['rel'] )
            {
                $horel = "{$rel['rel']}";
                
                $lr = explode( ':', $horel );
                
                // "Class1.attr:Class2.attr" or ":Class2.attr" or "Class1.attr:"
                if ( count( $lr ) == 2 )
                {
                    $relArray["{$rel['name']}"]['rel']['left'] = $this->parseRelKey( $lr[0] );
                    $relArray["{$rel['name']}"]['rel']['right'] = $this->parseRelKey( $lr[1] );
                }
                // "Class1.attr"
                elseif ( count( $lr ) == 1 )
                {
                    $relArray["{$rel['name']}"]['rel']['left'] = $this->parseRelKey( $lr[0] );
                    $relArray["{$rel['name']}"]['rel']['right'] = '';
                }
                // too many classes in rel
                else
                {
                    throw new Exception( 'Invalid relation descriptor ' . $horel );
                }
            }
            // rel not declared or rel=""
            else
            {
                $relArray["{$rel['name']}"]['rel']['left'] = '';
                $relArray["{$rel['name']}"]['rel']['right'] = '';
            }
        }
        
        /**
         * Parse a relation key 
         * @param   string $expr key to parse
         * @return  string $key
         */
        protected function parseRelKey( $expr )
        {
            $keys = explode( '.', $expr );
            
            if ( count($keys) == 1 )
            {
                $key = $keys[0];
            }
            elseif ( count($keys) == 2 )
            {
                $key = $keys[1];
            }
            else
            {
                throw new Exception('Invalid relation key ' . $expr );
            }
            
            return $key;
        }
        
        // static factories
        
        /**
         * Create a schema from a XML file
         * @param   string $filePath path of the schema file
         * @return  PDOMapperSchema
         */
        public static function fromFile( $filePath )
        {
            if ( ! file_exists( $filePath ) )
            {
                throw new Exception('File not found ' . $filePath );
            }
            else
            {
                $schema = file_get_contents( $filePath );
                
                return self::fromString( $schema );
            }
        }
        
        /**
         * Create a schema from a XML string 
         * same as calling new PDOMapperSchema( $schema )
         * @param   string $schema xml schema
         * @return  PDOMapperSchema
         */
        public static function fromString( $schema )
        {
            $schemaObj = new PDOMapperSchema( $schema );
                
            return $schemaObj;
        }
        
        
        /**
         * Create a schema from a database table
         * @param   PDO $pdo connection to the database
         * @param   string $tableName name of the table to map in database
         * @return  PDOMapperSchema
         */
        public static function fromTable( $pdo, $tableName )
        {
            $schema= self::getXMLSchemaFromTable( $pdo, $tableName );
            
            return PDOMapperSchema::fromString( $schema );
        }
        
        public static function getXMLSchemaFromTable( $pdo, $tableName )
        {
            $sql = "SHOW FIELDS FROM `{$tableName}`";
            $stmt = $pdo->query( $sql );
            
            $ret = '<schema>' . "\n";
            $key = '';
            
            foreach ( $stmt as $field )
            {
                $ret .= '<attribute name="'.$field['Field'].'"'
                    . ' field="'.$field['Field'].'"'
                    . ( $field['Default'] 
                        ? ' default="'.$field['Default'].'"' 
                        : ( $field['Null'] == 'YES' ? '' : ' required="true"' ) )
                    . ' />' . "\n"
                    ;
                    
                if ( $field['Key'] == 'PRI' )
                {
                    $key = '<key name="'. $field['Field'] .'" />' . "\n";
                }
            }
            
            $ret .= $key;
            
            $ret .= '</schema>';
            
            return $ret;
        }
    }
?>