<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents an user's examination result
 * @property int $userId
 * @property array $resultList
 */
class UserExamination
{
    protected $userId;
    protected $resultList;
    
    /**
     * Constructor
     * @param int $userd
     */
    public function __construct( $userId )
    {
        $this->userId = $userId;
        
        $this->tbl = get_module_course_tbl( array( 'examination_session' , 'examination_score' ) );
    }
    
    /**
     * Loads the datas
     * This method is called by the constructor
     */
    public function load()
    {
        $result = Claroline::getDatabase()->query( "
            SELECT
                S.id,
                S.title,
                S.publication_date,
                S.max_score,
                R.score,
                R.comment
            FROM
                `{$this->tbl['examination_session']}` AS S,
                `{$this->tbl['examination_score']}`   AS R
            WHERE
                R.session_id = S.id
            AND
                R.user_id = " . Claroline::getDatabase()->escape( $this->userId ) . "
            ORDER BY
                S.publication_date" );
        
        $this->resultList = array();
        
        foreach( $result as $line )
        {
            $id = $line[ 'id' ];
            $this->resultList[ $id ][ 'title' ] = $line[ 'title' ];
            $this->resultList[ $id ][ 'score' ] = $line[ 'score' ] . '/' . $line[ 'max_score' ];
            $this->resultList[ $id ][ 'comment' ] = $line[ 'comment' ];
            $this->resultList[ $id ][ 'date' ] = $line[ 'publication_date' ];
            
        }
    }
    
    /**
     * Getter for result list
     * @return array $resultList
     */
    public function getResultList( $force = false )
    {
        if ( $force || ! isset( $this->resultList ) )
        {
            $this->load();
        }
        
        return $this->resultList;
    }
    
    /**
     * Control if the user has results
     * @return boolean true if results exist
     */
    public function hasResult()
    {
        return Claroline::getDatabase()->query( "
            SELECT
                user_id
            FROM
                `{$this->tbl['examination_score']}`
            WHERE
                user_id = " . Claroline::getDatabase()->escape( $this->userId )
        )->numRows();
    }
}