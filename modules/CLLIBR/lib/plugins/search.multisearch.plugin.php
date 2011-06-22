<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for multiple searches
 * @const TYPE_EQUAL
 * @const TYPE_LIKE
 * @const OPERATOR_AND
 * @const OPERATOR_OR
 * @static array $itemList
 */
class MultiSearch extends Search
{
    const OPERATOR_AND = 'AND';
    const OPERATOR_OR = 'OR';
    
    public static $itemList = array( 'author'
                                   , 'title'
                                   , 'keyword'
                                   , 'description' );
    
    /**
     * Search query
     * @param array $searchQuery
     */
    public function search( $searchQuery )
    {
        $sqlString1 = "SELECT\n"
                   . "    T.resource_id    AS id,\n"
                   . "    T.metadata_value AS title,\n"
                   . "    M.metadata_name  AS name,\n"
                   . "    M.metadata_value AS value\n"
                   . "FROM\n"
                   . "    `{$this->tbl['library_metadata']}` AS M,\n"
                   . "    `{$this->tbl['library_metadata']}` AS T\n";
        
        $sqlString2 = "WHERE\n";
        
        foreach( $searchQuery as $index => $item )
        {
            $operator = array_key_exists( 'operator' , $item )
                      ? $item[ 'operator' ]
                      : self::OPERATOR_AND;
            
            $sqlString1 .= $operator == self::OPERATOR_AND
                        ? "INNER JOIN\n"
                        : "LEFT JOIN\n";
                        
            $sqlString1 .= "    `{$this->tbl['library_metadata']}` AS M"
                        . $index . "\n"
                        . "ON\n"
                        . "    T.resource_id = M" . $index . ".resource_id\n";
            
            $sqlString2 .= $index
                        ? $operator
                        : "";
                        
            $sqlString2 .= "  M" . $index . ".metadata_name = "
                        . $this->database->quote( $item[ 'name' ] ) . "\n"
                        . "AND\n"
                        . "   M" . $index . ".metadata_value LIKE "
                        . $this->database->quote( '%' . $item[ 'value' ] . '%' )
                        . "\n";
        }
        
        $sqlString = $sqlString1 . $sqlString2;
        
        return $this->resultSet = $this->database->query( $sqlString );
    }
    
    /**
     * Prepares the datas collected by the search() method
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $id = $line[ 'id' ];
            
            if ( ! array_key_exists( $id , $result ) )
            {
                $result[ $id ][ 'count' ] = 0;
            }
            
            $match = array( 'name'  => $line[ 'name' ]
                          , 'value' => $line[ 'value' ] );
            
            $result[ $id ][ 'title' ] = $line[ 'title' ];
            $result[ $id ][ 'matches' ][] = $match;
            $result[ $id ][ 'count' ]++;
        }
        
        $sortedResult = array();
        
        foreach( $result as $id => $datas )
        {
            $sortedResult[ $datas[ 'count' ] ] = array( 'id' => $id
                                                      , 'title' => $datas[ 'title' ]
                                                      , 'matches' => $datas[ 'matches' ] );
        }
        
        krsort( $sortedResult );
        
        return $this->searchResult = $sortedResult;
    }
}