<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for simple database operations on a table
 * @property database object $database
 * @property string $table
 */
class DatabaseTable
{
    protected $database;
    protected $table;
    
    /**
     * Constructor
     * @param database object $database
     * @param string $table
     */
    public function __construct( $database , $table )
    {
        $this->database = $database;
        $this->table = $table;
    }
    
    /**
     * Performs a SELECT
     * @param array $nameList
     * @param array $whereList
     * @return ResultSet $result
     */
    public function select( $nameList , $whereList )
    {
        return $this->database->query( "
            SELECT
                " . implode( ",\n" , $nameList ) . "
            FROM
                " . $this->table . "
            WHERE
                " . $this->sqlString( $whereList , "\nAND" ) );
    }
    
    /**
     * Performs an INSERT
     * @param array $insertList
     * @return boolean true on success
     */
    public function insert( $insertList )
    {
        return $this->database->exec( "
            INSERT INTO
                " . $this->table . "
            SET
                " . $this->sqlString( $insertList , ",\n" ) );
    }
    
    /**
     * Performs an UPDATE
     * @param array $updateList
     * @param array $whereList
     * @return int $affectedRows
     */
    public function update( $updateList , $whereList )
    {
        $this->database->exec( "
            UPDATE
                " . $this->table . "
            SET
                " . $this->sqlString( $updateList , ",\n" ) ."
            WHERE
                " . $this->sqlString( $whereList , "\nAND" ) );
        
        return $this->database->affectedRows();
    }
    
    /**
     * Performs a DELETE
     * @param array $whereList
     * @return int $affectedRows
     */
    public function delete( $whereList )
    {
        $this->database->exec( "
            DELETE FROM
                " . $this->table . "
            WHERE
                " . $this->sqlString( $whereList , "\nAND" ) );
        
        return $this->database->affectedRows();
    }
    
    /**
     * Generates a string for the SQL query
     * @param array $valueList
     * @param string $glue
     * @return string $sqlString
     */
    protected function sqlString( $valueList , $glue )
    {
        $sqlString = array();
        
        foreach( $valueList as $name => $value )
        {
            $sqlString[] = $name
                         . " = "
                         . is_int( $value ) ? $this->database->escape( $value )
                                            : $this->database->quote( $value );
        }
        
        return implode( $glue , $sqlString );
    }
}