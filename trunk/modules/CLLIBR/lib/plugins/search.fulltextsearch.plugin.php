<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.0 $Revision$ - Claroline 1.9
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
        $this->searchString = $searchString;
        
        return $this->resultSet = $this->database->query( "
            SELECT
                R.id,
                R.title,
                R.description,
                M.name,
                M.value,
                MATCH (R.title) AGAINST (" . $this->database->quote( $searchString ) . ") AS score1,
                MATCH (R.description) AGAINST (" . $this->database->quote( $searchString ) . ") AS score2,
                MATCH (M.name,M.value) AGAINST (" . $this->database->quote( $searchString ) . ") AS score3
            FROM
                `{$this->tbl['library_resource']}` AS R
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS M
            ON
                R.id = M.resource_id
            WHERE
                MATCH (R.title) AGAINST (" . $this->database->quote( $searchString ) . ")
            OR
                MATCH (R.description) AGAINST (" . $this->database->quote( $searchString ) . ")
            OR
                MATCH (M.name,M.value) AGAINST (" . $this->database->quote( $searchString ) . ")"
        );
        
        /*$this->result_main = $this->database->query( "
            SELECT
                id,
                title,
                description,
                MATCH (title) AGAINST (" . $this->database->quote( $searchString ) . ") AS score1,
                MATCH (description) AGAINST (" . $this->database->quote( $searchString ) . ") AS score2
            FROM
                `{$this->tbl['library_resource']}`
            WHERE
                MATCH (title) AGAINST (" . $this->database->quote( $searchString ) . ")
            OR
                MATCH (description) AGAINST (" . $this->database->quote( $searchString ) );
        
        $this->result_metadata = $this->database->query( "
            SELECT
                R.id,
                R.title
                M.name,
                M.value,
                MATCH (M.name,M.value) AGAINST (" . $this->database->quote( $searchString ) . ") AS score3
            FROM
                `{$this->tbl['library_resource']}` AS R
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS M
            ON
                R.id = M.resource_id
            WHERE
                MATCH (R.title) AGAINST (" . $this->database->quote( $searchString ) );*/
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $score1 = $line[ 'score1' ];
            $score2 = $line[ 'score2' ];
            $score3 = $line[ 'score3' ];
            
            $matches = array();
            $score = $score1 + $score2 + $score3;
            
            if ( $score1 )
            {
                $matches[ 'title' ] = $line[ 'title' ];
            }
            
            if ( $score2 )
            {
                $description = $line[ 'description' ];
                $position = strrpos( $description , $this->searchString );
                $matches[ 'description' ] = substr( $description , $position - 30 , 60 );
            }
            
            if ( $score3 )
            {
                $matches[ $line[ 'name' ] ] = $line[ 'value' ];
            }
            
            $id = $line[ 'id' ];
            $title = $line[ 'title' ];
            $result[ (string)$score ][ $id ] = array( 'title' => $title , 'matches' => $matches );
        }
        
        krsort( $result );
        
        return $this->searchResult = $result;
    }
}