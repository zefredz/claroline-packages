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
 * A VERY simple class for single fulltext search
 */
class FulltextSearch extends Search
{
    /**
     * @param array $searchQuery
     */
    public function search( $searchString )
    {
        return $this->resultSet = $this->database->query( "
            SELECT
                T1.resource_id,
                T1.name,
                T1.value,
                T2.value as title,
                T3.value as author,
                MATCH (T1.name,T1.value) AGAINST (" . $this->database->quote( $searchString ) . ") AS score
            FROM
                `{$this->tbl['library_metadata']}` AS T1
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS T2
            ON
                T1.resource_id = T2.resource_id
            AND
                T2.name = 'author'
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS T3
            ON
                T1.resource_id = T3.resource_id
            AND
                T3.name = 'title'
            WHERE
                MATCH (T1.name,T1.value) AGAINST (" . $this->database->quote( $searchString ) . ")"
        );
        /*
        return $this->resultSet = $this->database->query( "
            SELECT
                resource_id,
                name,
                value,
                MATCH (name,value) AGAINST (" . $this->database->quote( $searchString ) . ") AS score
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                MATCH (name,value) AGAINST (" . $this->database->quote( $searchString ) . ")"
        );*/
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $id = $line[ 'resource_id' ];
            $score = $line[ 'score' ];
            $match = array( 'name' => $line[ 'name' ]
                          , 'value' => $line[ 'value' ] );
            /////// NOT SURE
            $title = $line[ 'title' ];
            $author = $line[ 'author' ];
            ///////
            $result[ $score ][ $id ] = array( 'title' => $title
                                            , 'author' => $author
                                            , 'match' => $match );
            //$result[ $score ][ $id ] = $match;
            
        }
        
        krsort( $result );
        
        return $this->searchResult = $result;
    }
}