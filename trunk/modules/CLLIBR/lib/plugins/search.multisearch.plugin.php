<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.4 $Revision$ - Claroline 1.9
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
        $searchString = "";
        
        foreach( $searchQuery as $index => $item )
        {
            
            if ( $index )
            {
                $searchString .= $item[ 'operator' ] . " ";
            }
            
            $searchString .= "M.name = "
                           . $this->database->quote( $item[ 'name' ] )
                           . "AND M.value LIKE "
                           . $this->database->quote( '%' . $item[ 'value' ] . '%' )
                           . "\n";
        }
        
        return $this->resultSet = $this->database->query( "
            SELECT
                R.title,
                R.id,
                M.name,
                M.value
            FROM
                `{$this->tbl['library_metadata']}` AS M
            INNER JOIN
                `{$this->tbl['library_resource']}` AS R
            ON
                R.id = M.resource_id
            WHERE
                " . $searchString );
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
            
            $match = array( 'name' => $line[ 'name' ]
                          , 'value' => $line[ 'value' ] );
            
            $result[ $id ][ 'matches' ][] = $match;
            $result[ $id ][ 'count' ]++;
            $result[ $id ][ 'title' ] = $line[ 'title' ];
        }
        
        $sortedResult = array();
        
        foreach( $result as $id => $datas )
        {
            $sortedResult[ $datas[ 'count' ] ][ $id ] = array( 'title' => $datas[ 'title' ]
                                                             , 'matches' => $datas[ 'matches' ] );
        }
        
        krsort( $sortedResult );
        
        $searchResult = array();
        
        foreach( $sortedResult as $score => $resources )
        {
            foreach( $resources as $id => $datas )
            {
                if ( ! array_key_exists( $id , $searchResult ) )
                {
                    $searchResult[ $id ] = $datas;
                    $searchResult[ $id ][ 'score' ] = 0;
                }
                $searchResult[ $id ][ 'score' ] += $score;
            }
        }
        
        return $this->searchResult = $searchResult;
    }
    
    /**
     *
     */
    public function render()
    {
        return null; //Nothing there yet;
    }
}