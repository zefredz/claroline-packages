<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
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
 * @property int $queryCount;
 */
class MultiSearch extends Search
{
    const OPERATOR_AND = 'AND';
    const OPERATOR_OR = 'OR';
    
    public static $itemList = array( 'author'
                                   , 'title'
                                   , 'keyword'
                                   , 'description' );
    
    protected $queryCount;
    
    /**
     * Search query
     * @param array $searchQuery
     */
    public function search( $searchQuery )
    {
        $this->queryCount = count( $searchQuery );
        
        $result = array();
        $matches = array();
        
        foreach( $searchQuery as $index => $item )
        {
            $resultSet = $this->database->query( "
                SELECT
                    M.resource_id    AS id,
                    T.metadata_value AS title,
                    M.metadata_name  AS name,
                    M.metadata_value AS value
                FROM
                    `{$this->tbl['library_metadata']}` AS M
                INNER JOIN
                    `{$this->tbl['library_metadata']}` AS T
                ON
                    M.resource_id = T.resource_id
                WHERE
                    T.resource_id = M.resource_id
                AND
                    T.metadata_name = " . $this->database->quote( Metadata::TITLE ) . "
                AND
                    M.metadata_name = " . $this->database->quote( $item[ 'name' ] ) . "
                AND
                    M.metadata_value LIKE " . $this->database->quote( '%' . $item[ 'value' ] . '%' ) );
            
            $itemResult = array();
            
            foreach( $resultSet as $line )
            {
                $id = 'resource' . $line[ 'id' ];
                $itemResult[ $id ][ 'id' ] = $line[ 'id' ];
                $itemResult[ $id ][ 'title' ] = $line[ 'title' ];
                $matches[ $id ][ $line[ 'name' ] ] = self::highlight( $item[ 'value' ] , $line[ 'value' ] );
            }
            
            $operator = $index
                      ? $item[ 'operator' ]
                      : self::OPERATOR_OR;
            
            if ( $operator == self::OPERATOR_OR )
            {
                $result = array_merge( $result , $itemResult );
            }
            else
            {
                $result = array_intersect( $result , $itemResult );
            }
        }
        
        foreach( array_keys( $result ) as $id )
        {
            $result[ $id ][ 'matches' ] = $matches[ $id ];
        }
        
        return $this->searchResult = $result;
    }
    
    /**
     * Prepares the datas collected by the search() method
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->searchResult as $line )
        {
            $id = $line[ 'id' ];
            
            $result[ $id ][ 'title' ] = $line[ 'title' ];
            $result[ $id ][ 'matches' ] =  $line[ 'matches' ];
            $result[ $id ][ 'score' ] = count( $line[ 'matches' ] ) * 60 / $this->queryCount;
        }
        
        $sortedResult = array();
        
        foreach( $result as $id => $datas )
        {
            $sortedResult[ $datas[ 'score' ] ][ $id ] = array( 'title'   => $datas[ 'title' ]
                                                             , 'matches' => $datas[ 'matches' ] );
        }
        
        krsort( $sortedResult );
        
        return $this->bakedResult = $sortedResult;
    }
}