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
 * A VERY simple class for single fulltext search
 */
class FulltextSearch extends Search
{
    /**
     * Performs the search
     * @param array $searchQuery
     */
    public function search( $searchString )
    {
        $this->searchString = $searchString;
        
                return $this->resultSet = $this->database->query( "
            SELECT
                T.resource_id    AS id,
                T.metadata_value AS title,
                M.metadata_name  AS name,
                M.metadata_value AS value,
                MATCH (M.metadata_name,M.metadata_value) AGAINST ("
                . $this->database->quote( $searchString ) . ") AS score
            FROM
                `{$this->tbl['library_metadata']}` AS T
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS M
            ON
                T.resource_id = M.resource_id
            WHERE
                MATCH (M.metadata_name,M.metadata_value) AGAINST ("
                . $this->database->quote( $searchString ) . ")"
        );
    }
    
    /**
     * Bakes the search result
     * @return array $result
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $id = $line[ 'id' ];
            $title = $line[ 'title' ];
            $score = $line[ 'score' ];
            $name = $line[ 'name' ];
            $value = str_ireplace( $this->searchString
                                 , '<strong>' . $this->searchString . '</strong>'
                                 , $line[ 'value' ] );
            
            if ( ! array_key_exists( $id , $result ) )
            {
                $result[ $id ][ 'score' ] = 0;
            }
            
            $result[ $id ][ 'title' ] = $title;
            $result[ $id ][ 'matches' ][ $name ] = $value;
            $result[ $id ][ 'score' ] += $score;
        }
        
        $sortedResult = array();
        
        foreach( $result as $id => $datas )
        {
            $sortedResult[ $datas[ 'score' ] ] = array( 'id' => $id
                                                       ,'title' => $datas[ 'title' ]
                                                       , 'matches' => $datas[ 'matches' ] );
        }
        
        krsort( $sortedResult );
        
        return $this->searchResult = $sortedResult;
    }
}