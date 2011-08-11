<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A abstract class that represents a list of assets
 * @const VISIBLE
 * @const INVISIBLE
 * @property string $tableName
 * @property string $dataName
 * @property array $assetList
 */
class AssetList
{
    const VISIBLE = 'VISIBLE';
    const INVISIBLE = 'INVISIBLE';
    
    protected $tableName;
    protected $dataName;
    
    protected $assetList;
    
    /**
     * Constructor
     */
    public function __construct( $tableName , $dataName )
    {
        $this->tableName = $tableName;
        $this->dataName  = $dataName;
    }
    
    /**
     * Gets the list of assets
     */
    public function getList( $force = false )
    {
        if ( ! $this->assetList || $force )
        {
            $this->assetList = Claroline::getDatabase()->query( "
                SELECT
                    id,
                    title,
                    publication_date,
                    visibility
                FROM `{$this->tableName}`" );
        }
        
        return $this->assetList;
    }
    
    /**
     * Set the visibility of a specified asset
     * @param int $assetId
     * @param boolean $is_visible ( default = true )
     * @return boolean true on success
     */
    public function setVisibility( $assetId , $is_visible = true )
    {
        $visibility = $is_visible ? self::VISIBLE : self::INVISIBLE;
        
        return Claroline::getDatabase()->exec( "
            UPDATE `{$this->tableName}`
            SET visibility = " . Claroline::getDatabase()->quote( $visibility ) . "
            WHERE id = " . Claroline::getDatabase()->escape( $assetId ) );
    }
    
    /**
     * Checks the visibility of an asset
     * @param int $assetId
     * @return boolean true if it's visible
     */
    public function isVisible( $assetId )
    {
        return Claroline::getDatabase()->query( "
            SELECT visibility
            FROM `{$this->tableName}`
            WHERE id = " . Claroline::getDatabase()->escape( $assetId ) . "
            AND visibility = " . Claroline::getDatabase()->quote( self::VISIBLE )
        )->numRows();
    }
    
    /**
     * Adds a examination in the database
     * @param string $title
     * @param string $data
     * @return int $assetId
     */
    public function add( $title , $data )
    {
        if ( Claroline::getDatabase()->exec( "
            INSERT INTO `{$this->tableName}`
            SET
                title = " . Claroline::getDatabase()->quote( $title ) . ",\n"
                . $this->dataName . " = " . Claroline::getDatabase()->quote( $data ) . ",
                publication_date = NOW(),
                visibility = " . Claroline::getDatabase()->quote( self::VISIBLE ) ) )
        {
            return Claroline::getDatabase()->insertId();
        }
    }
    
    /**
     * Deletes an asset
     * @param int $assetId
     * @return boolean true on success
     */
    public function delete( $assetId )
    {
        return Claroline::getDatabase()->exec( "
            DELETE FROM `{$this->tableName}`
            WHERE id = " . Claroline::getDatabase()->escape( $assetId ) );
    }
}