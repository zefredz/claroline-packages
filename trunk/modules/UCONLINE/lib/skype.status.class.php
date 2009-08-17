<?php // $Id$

/**
 * Who is onlin@?
 *
 * @version     UCONLINE 0.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCONLINE
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

/**
 * Class for an user's Skype account management
 * @property private int $userId
 * @property private string $skypeName : the name of the Skype account
 */
class SkypeStatus
{
    private $userId;
    private $skypeName;
    
    private $tbl;
    
    /**
     * Constructor
     * @param int $UserId
     */
    public function __construct( $userId )
    {
        $this->userId = $userId;
        
        $this->tbl = claro_sql_get_tbl( array( 'user_property' ) );
        
        $this->load();
    }
    
    /**
     * This function is called by the constructor
     * Loads the skype account name from database ( if any ),
     * then put the value into the $skypeName attribute
     */
    private function load()
    {        
        $result = Claroline::getDatabase()->query( "
                SELECT
                    `propertyValue`
                FROM
                    `{$this->tbl[ 'user_property' ]}`
                WHERE
                    `propertyId` = 'skypeName'
                AND
                    `userId` = " . Claroline::getDatabase()->escape( $this->userId )
        )->fetch( Database_ResultSet::FETCH_VALUE );
        
        $this->skypeName = $result;
    }
    
    /**
     * Creates the entry in the database when entering the skype account name
     */
    private function create()
    {
        return Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl[ 'user_property' ]}`
            SET `userId` = ". Claroline::getDatabase()->escape( $this->userId ) . ",
                `propertyId` = 'skypeName',
                `propertyValue` = ". Claroline::getDatabase()->quote( $this->skypeName )
        );
    }
    
    /**
     * Updates the entry in database when changing the skype account
     */
    private function update()
    {
        return Claroline::getDatabase()->exec( "
            UPDATE
            `{$this->tbl[ 'user_property' ]}`
            SET
                `propertyId` = 'skypeName',
                `propertyValue` = " . Claroline::getDatabase()->quote( $this->skypeName ) ."
            WHERE
                `userId` = " . Claroline::getDatabase()->escape( $this->userId )
        );
    }
    
    /**
     * The public function called to save the skype account name into database
     * @param string $newSkypeName
     */
    public function save( $newSkypeName )
    {
        if ( $this->skypeName )
        {
            $this->skypeName = $newSkypeName;
            
            return $this->update();
        }
        else
        {
            $this->skypeName = $newSkypeName;
            
            return $this->create();
        }
    }
    
    /**
     * Removes the entry in database when deleting the skype account
     */
    public function delete()
    {
        Claroline::getDatabase()->exec( "
            DELETE FROM
                `{$this->tbl[ 'user_property' ]}`
            WHERE
                `userId` = " . Claroline::getDatabase()->escape( $this->userId )
        );
        
        $this->skypeName = '';
        
        return $this;
    }
    
    /**
     * Common getter fot $skypeName
     */
    public function getSkypeName()
    {
        return $this->skypeName;
    }
}