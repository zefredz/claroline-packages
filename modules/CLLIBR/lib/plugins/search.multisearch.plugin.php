<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.3 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for multiple searches
 */
class MultiSearch extends Search
{
    /**
     * Search query
     * @param array $searchQuery
     */
    public function search( $searchQuery )
    {
        $searchString = "";
        
        foreach( $searchQuery as $index => $item )
        {
            $searchString .= $item[ 'name' ]
                          . " = "
                          . $this->database->quote( $item[ 'value' ] )
                          . "\n";
            
            if ( $index )
            {
                $searchString .= $item[ 'operator' ] . " ";
            }
        }
        
        return $this->resultSet = $this->database->query( "
            SELECT
                resource_id,
                name,
                value
            FROM
                `{$this->tbl['library_metadata']}`
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
            $id = $line[ 'resource_id' ];
            
            if ( ! array_key_exists( $result[ $id ] ) )
            {
                $result[ $id ][ 'count' ] = 0;
            }
            
            $match = array( 'name' => $line[ 'name' ]
                          , 'value' => $line[ 'value' ] );
            
            $result[ $id ][ 'matches' ][] = $match;
            $result[ $id ][ 'count' ]++;
        }
        
        $sortedResult = array();
        
        foreach( $result as $id => $datas )
        {
            $sortedResult[ $datas[ 'count' ] ][ $id ] = $data[ 'matches' ];
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