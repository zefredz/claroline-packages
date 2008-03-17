<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * PDOMapper
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
     * PDOMapper class : map object to database using a xml schema
     *
     * The mapped object must have public attributes for the attributes
     * corresponding to fields in the database
     */
    class PDOMapper
    {
        protected $schema;
        protected $db;
        protected $builder;

        /**
         * Constructor
         * @param   PDO $pdo PDO database connection
         * @param   PDOMapperSchema $schema
         */
        public function __construct( $pdo, $schema, $builder )
        {
            $this->schema = $schema;
            $this->db = $pdo;
            $this->builder = $builder;
            
            // use exception to report error
            if ( $this->db->getAttribute(PDO::ATTR_ERRMODE) != PDO::ERRMODE_EXCEPTION )
            {
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }
        
        /**
         * Select one or more objects from the database matching the optional
         * clause. If no clause given, returns all the objects from DB
         * @param   string $clause
         * @param   array $params values to put in the clause string
         *          (see PDOStatement)
         * @return  PDOStatement
         */
        public function select( $clause = '1', $params = null )
        {
            $mapping = array();

            foreach ( $this->schema->getFieldList() as $name => $field )
            {
                $mapping[] = $field . " AS " . $name;
            }

            $sql = "SELECT \n"
                . implode( ",\n", $mapping ) . "\n"
                . "FROM " . $this->schema->getTable() . "\n"
                . "WHERE " . $clause
                ;
                
//            var_dump( $sql );

            $statement = $this->executeQuery( $sql, $params );
            
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->schema->getClass());

            return $statement;
        }
        
        /**
         * Select one object of the given schema matching the given clause
         * @param   string $clause
         * @param   array $params values to put in the clause string
         *          (see PDOStatement)
         * @return  object or false
         */
        public function selectOne( $clause = "1", $params = null )
        {
            $stmt = $this->select( $clause, $params );
            $obj = $stmt->fetch();
            $stmt->closeCursor();
            
            if ( ! $obj )
            {
                $obj = null;
            }
            
            return $obj;
        }
        
        /**
         * Select one object of the given schema matching the given clause
         * @param   string $clause
         * @param   array $params values to put in the clause string
         *          (see PDOStatement)
         * @return  array of object or empty array
         */
        public function selectAll( $clause = "1", $params = null )
        {
            $stmt = $this->select( $clause, $params );
            $objList = array();
            
            while ( $obj = $stmt->fetch() )
            {
                $objList[] = $obj;
            }
            
            $stmt->closeCursor();
            return $objList;
        }
        
        /**
         * Update the given object in the database
         * @param   Object $obj object matching the mapping schema
         * @return  Object $obj
         */
        public function update( $obj )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );      
            
            $key = $this->schema->getKey();
            $setFields = array();
            $params = array();
            
            if ( ! isset( $obj->$key ) )
            {
                throw new Exception('Cannot update object : missing object key !');
            }

            foreach ( $this->schema->getFieldList() as $attr => $field )
            {
                if ( ! isset( $obj->$attr )
                    && $this->schema->isRequired( $attr ) )
                {
                    throw new Exception('Cannot update object : missing required argument ' . $attr);
                }
                
                $setFields[] = $field . ' = :' . $attr;
                $params[':'.$attr] = $obj->$attr;
            }
            
            $sql = "UPDATE " . $this->schema->getTable() . "\n"
                . "SET "
                . implode( ",\n", $setFields ) . "\n"
                . "WHERE " . $this->schema->getField($key) . " = :".$key
                ;
                
            $this->executeQuery( $sql, $params );
            
            return $obj;
        }
        
        /**
         * Add the given object to the database
         * @param   Object $obj object matching the mapping schema
         * @return  Object $obj
         */
        public function create( $obj )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );
                    
            $key = $this->schema->getKey();
            $insertFields = array();
            $insertValues = array();
            $params = array();
            
            foreach ( $this->schema->getFieldList() as $attr => $field )
            {
                if ( isset( $obj->$attr ) )
                {
                    $insertFields[] = $field;
                    $insertValues[] = ':' . $attr;
                    $params[':'.$attr] = $obj->$attr;
                }
                elseif ( $this->schema->hasDefaultValue( $attr ) )
                {
                    $insertFields[] = $field;
                    $insertValues[] = ':' . $attr;
                    $params[':'.$attr] = $this->schema->getDefaultValue( $attr );
                }
                elseif ( $this->schema->isRequired( $attr ) )
                {
                    throw new Exception('Cannot create object : missing required argument ' . $attr);
                }
                else
                {
                    continue;
                }
            }
            
            $sql = "INSERT INTO " . $this->schema->getTable() . "\n"
                . '(' . implode( ', ', $insertFields ) . ')' . "\n"
                . 'VALUES(' . implode( ', ', $insertValues ) . ')'
                ;
                
            $this->executeQuery( $sql, $params );
            
            if ( ! isset( $obj->$key ) )
            {
                $obj->$key = $this->db->lastInsertId();
            }
            
            return $obj;
        }
        
        /**
         * Add or update the given object in the database
         * @param   Object $obj object matching the mapping schema
         * @return  Object $obj
         */
        public function save( $obj )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );
            
            $key = $this->schema->getKey();
            
            if ( ! isset( $obj->$key ) )
            {
                return $this->create( $obj );
            }
            else
            {
                $clause = $this->schema->getField($key) . " = :".$key;
                $params = array(
                    ':'.$key => $obj->$key
                );
                // if an object with the same key already exists : update
                
                if ( $this->selectOne( $clause, $params ) != false )
                {
                    return $this->update( $obj );
                }
                // else : insert
                else
                {
                    return $this->create( $obj );
                }
            }
        }
        
        /**
         * Delete the given object from the database
         * @param   Object $obj object matching the mapping schema
         * @return  bool
         */
        public function delete( $obj )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );
            
            $key = $this->schema->getKey();
            
            if ( ! isset( $obj->$key ) )
            {
                throw new Exception('Cannot delete object : missing object key !');
            }
            
            // delete related            
            if ( 0 < $this->schema->countHasOneRelations() )
            {
                foreach ( $this->schema->getHasOneRelationList() as $attr => $horel )
                {
                    if ( $horel['ondelete'] === 'delete' )
                    {
                        $this->deleteHasOne( $obj, $attr );
                    }
                }
            }
            
            if ( 0 < $this->schema->countHasManyRelations() )
            {
                foreach ( $this->schema->getHasManyRelationList() as $attr => $hmrel )
                {
                    if ( $hmrel['ondelete'] === 'delete' )
                    {
                        $this->deleteHasMany( $obj, $attr );
                    }
                }
            }
            
            // delete object
            
            $params = array(
                ':'.$key => $obj->$key
            );
            
            $clause = $this->schema->getField($key) . " = :".$key;
                
            return $this->deleteWhere( $clause, $params );
        }
        
        /**
         * Delete all the objects of the current schema from the database
         * @return  bool
         */
        public function deleteAll()
        {
            return $this->deleteWhere( "1" );
        }
        
        /**
         * Delete the given objects from the database matching the given clause
         * @param   string $clause
         * @param   array $params values to put in the clause string
         *          (see PDOStatement)
         * @return  bool
         */
        public function deleteWhere( $clause = "1", $params = null )
        {
            $sql = "DELETE FROM " . $this->schema->getTable() . "\n"
                . "WHERE " . $clause
                ;
                
            $this->executeQuery( $sql, $params );
            
            return true;
        }
        
        // relations
        
        /**
         * Resolve has one relation, bind the associated object to the current object
         * and return the associated object
         * @param   Object $obj current object
         * @param   string $name name of the relation
         * @return  object or false
         */
        public function hasOne( $obj, $name )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );
            
            if ( $this->schema->hasOne( $name ) )
            {
                $rel = $this->schema->getHasOneRelation( $name );
                
                $otherMapper = $this->builder->getMapper( $rel['class'] );
                $otherSchema = $otherMapper->getSchema();
                
                $thiskey = empty( $rel['rel']['left'] )
                    ? $this->schema->getKey()
                    : $rel['rel']['left']
                    ;
                    
                $otherkey = empty( $rel['rel']['right'] )
                    ? $otherSchema->getKey()
                    : $otherSchema->getField($rel['rel']['right'])
                    ;
                
                $clause = $otherkey . " = :{$rel['rel']['left']}";
                $params = array( ":{$rel['rel']['left']}" => $obj->$thiskey );
                
//                var_dump( $this->schema->getClass().' has one '.$otherSchema->getClass() );
//                var_dump( $clause );
//                var_dump( $params );
                
                $hoobj = $otherMapper->selectOne( $clause, $params );
                $obj->$name = $hoobj;
                return $hoobj;
            }
            else
            {
                throw new Exception($this->schema->getClass()
                    .' schema do not have hasone relation named '
                    . $name );
            }
        }
        
        /**
         * Delete the object related through the given has one relation to the 
         * given object
         * @param   Object $obj, current object
         * @param   string $name, name of the relation
         * @return  bool
         */
        public function deleteHasOne( $obj, $name )
        {
            $this->checkObject( $obj );
            
            if ( $this->schema->hasOne( $name ) )
            {
                $rel = $this->schema->getHasOneRelation( $name );
                
                $otherMapper = $this->builder->getMapper( $rel['class'] );
                $otherSchema = $otherMapper->getSchema();
                
                $thiskey = empty( $rel['rel']['left'] )
                    ? $this->schema->getKey()
                    : $rel['rel']['left']
                    ;
                    
                $otherkey = empty( $rel['rel']['right'] )
                    ? $otherSchema->getKey()
                    : $otherSchema->getField($rel['rel']['right'])
                    ;
                
                $clause = $otherkey . " = :{$rel['rel']['left']}";
                $params = array( ":{$rel['rel']['left']}" => $obj->$thiskey );
                
//                var_dump( $this->schema->getClass().' has one '.$otherSchema->getClass() );
//                var_dump( $clause );
//                var_dump( $params );
                
//                if ( $otherMapper->deleteWhere( $clause, $params ) )
//                {
//                    $obj->$name = null;
//                    return true;
//                }
//                else
//                {
//                    return false;
//                }

                return $otherMapper->deleteWhere( $clause, $params );
            }
            else
            {
                throw new Exception($this->schema->getClass()
                    .' schema do not have hasone relation named '
                    . $name );
            }
        }
        
        /**
         * Resolve has many relation, bind the associated object to the current object
         * and return the associated object
         * @param   Object $obj current object
         * @param   string $name name of the relation
         * @return  aray of objects or empty array
         */
        public function hasMany( $obj, $name )
        {
            // throws an exception if not valid
            $this->checkObject( $obj );
            
            if ( $this->schema->hasMany( $name ) )
            {
                $rel = $this->schema->getHasManyRelation( $name );
                
                $otherMapper = $this->builder->getMapper( $rel['class'] );
                $otherSchema = $otherMapper->getSchema();
                
                $thiskey = empty( $rel['rel']['left'] )
                    ? $this->schema->getKey()
                    : $rel['rel']['left']
                    ;
                    
                $otherkey = empty( $rel['rel']['right'] )
                    ? $otherSchema->getKey()
                    : $otherSchema->getField($rel['rel']['right'])
                    ;
                
                $clause = $otherkey . " = :{$rel['rel']['left']}";
                $params = array( ":{$rel['rel']['left']}" => $obj->$thiskey );
                
//                var_dump( $this->schema->getClass().' has many '.$otherSchema->getClass() );
//                var_dump( $clause );
//                var_dump( $params );
                
                $hmObjList = $otherMapper->selectAll( $clause, $params );
                $obj->$name = $hmObjList;
                return $hmObjList;
            }
            else
            {
                throw new Exception($this->schema->getClass()
                    .' schema do not have hasone relation named '
                    . $name );
            }
        }
        
        /**
         * Delete all the objects related through the given has many relation to the 
         * given object
         * @param   Object $obj, current object
         * @param   string $name, name of the relation
         * @return  bool
         */
        public function deleteHasMany( $obj, $name )
        {
            $this->checkObject( $obj );
            
            if ( $this->schema->hasMany( $name ) )
            {
                $rel = $this->schema->getHasManyRelation( $name );
                
                $otherMapper = $this->builder->getMapper( $rel['class'] );
                $otherSchema = $otherMapper->getSchema();
                
                $thiskey = empty( $rel['rel']['left'] )
                    ? $this->schema->getKey()
                    : $rel['rel']['left']
                    ;
                    
                $otherkey = empty( $rel['rel']['right'] )
                    ? $otherSchema->getKey()
                    : $otherSchema->getField($rel['rel']['right'])
                    ;
                
                $clause = $otherkey . " = :{$rel['rel']['left']}";
                $params = array( ":{$rel['rel']['left']}" => $obj->$thiskey );
                
//                var_dump( $this->schema->getClass().' has many '.$otherSchema->getClass() );
//                var_dump( $clause );
//                var_dump( $params );
                
//                if ( $otherMapper->deleteWhere( $clause, $params ) )
//                {
//                    $obj->$name = null;
//                    return true;
//                }
//                else
//                {
//                    return false;
//                }

                return $otherMapper->deleteWhere( $clause, $params );
            }
            else
            {
                throw new Exception($this->schema->getClass()
                    .' schema do not have hasone relation named '
                    . $name );
            }
        }
        
        /**
         * Get the PDOMapperSchema associated with this Mapper
         * @return  PDOMapperObject
         */
        public function getSchema()
        {
            return $this->schema;
        }
        
        /**
         * Check if a given object is Valid
         * @throws  Exception if not valid
         */
        protected function checkObject( $obj )
        {
            /* 
                instance does not allow to compare directely an object and a  
                string so we have to assign the class name to a variable...
             */
            $className = $this->schema->getClass();
            if ( ! $obj instanceof $className )
            {
                throw new Exception( 'Given object is not of the expected class : '
                    . $className . ' expected, ' . get_class( $obj ) . ' given' );
            }
            
            $objArr = (array) $obj;
            
            foreach ( $objArr as $attribute => $value )
            {
                if ( ! $this->schema->isAllowed( $attribute, $value ) )
                {
                    throw new Exception( 'Value ' . $value . ' not allowed for attribute ' . $attribute );
                }
            }
        }
        
        protected static $queryCounter = 1;
        
        protected function executeQuery( $sql, $params = null )
        {
            $sql = toClaroQuery( $sql );
            
            if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
            {
                $start = microtime();
            }
            
            if ( ! is_array( $params ) || empty( $params ) )
            {
                $statement = $this->db->query( $sql );
            }
            else
            {
                $statement = $this->db->prepare( $sql );
                $statement->execute( $params );
            }
            
            if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
            {
                $duration = microtime()-$start;
                $info = 'execution time : ' . ($duration > 0.001 ? '<b>' . round($duration,4) . '</b>':'&lt;0.001')  . '&#181;s'  ;
                $info .= ': affected rows :' . claro_sql_affected_rows();
                pushClaroMessage( '<br>Query counter : <b>pdo_' . self::$queryCounter++ . '</b> : ' . $info . '<br />'
                    . '<code><span class="sqlcode">' . nl2br($sql) . '</span></code>'
                    , 'pdo');
            }
            
            return $statement;
        }
    }
?>