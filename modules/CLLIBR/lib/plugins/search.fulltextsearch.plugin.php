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
        
        return $this->searchResult = $this->database->query( "
            SELECT
                M.resource_id    AS id,
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
                T.resource_id = M.resource_id
            AND
                T.metadata_name = " . $this->database->quote( Metadata::TITLE ) . "
            AND
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
        
        foreach( $this->searchResult as $line )
        {
            $id = $line[ 'id' ];
            $title = $line[ 'title' ];
            $score = $line[ 'score' ];
            $name = $line[ 'name' ];
            $value = self::highlight( $this->searchString , $line[ 'value' ] );
            
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
            $sortedResult[ $datas[ 'score' ] ][ $id ] = array( 'title' => $datas[ 'title' ]
                                                             , 'matches' => $datas[ 'matches' ] );
        }
        
        krsort( $sortedResult );
        
        return $this->bakedResult = $sortedResult;
    }
}