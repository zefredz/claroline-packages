<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.1 $Revision$ - Claroline 1.9
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
                $matches[ 'description' ] = substr( $description , $position - 30 , 60 ); //TODO find a best way
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
        
        $searchResult = array();
        
        foreach( $result as $score => $resources )
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
     * Display the search form
     */
    public function render()
    {
        $html  = '<form id="searchForm" method="post" action="' . $this->cmd . '">' . "\n";
        $html .= '    <input type="submit" value="' . get_lang( 'Quick search' ) . '" />' . "\n";
        $html .= '    <input type="text" name="searchString" value="" />' . "\n";
        $html .= '</form>';
        
        return $html;
    }
}