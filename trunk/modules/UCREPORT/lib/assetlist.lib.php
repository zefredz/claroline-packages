<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
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
    const ACCESS_PUBLIC = 'PUBLIC';
    const ACCESS_PRIVATE = 'PRIVATE';
    
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
                    confidentiality,
                    visibility
                FROM `{$this->tableName}`" );
        }
        
        return $this->assetList;
    }
    
    /**
     * Set the confidentiality parameter of an asset
     * @param int assetId
     * @param string $confidentiality
     * @return true on success
     */
    public function setConfidentiality( $assetId , $is_private = true )
    {
        $confidentiality = $is_private ? self::ACCESS_PRIVATE : self::ACCESS_PUBLIC;
        
        return Claroline::getDatabase()->exec( "
            UPDATE `{$this->tableName}`
            SET confidentiality = " . Claroline::getDatabase()->quote( $confidentiality ) . "
            WHERE id = " . Claroline::getDatabase()->escape( $assetId ) );
    }
    
    /**
     * Helpers
     */
    public function setPrivate( $assetId )
    {
        return $this->setConfidentiality( $assetId , true );
    }
    
    public function setPublic( $assetId )
    {
        return $this->setConfidentiality( $assetId , false );
    }
    
    /**
     * Check the confidentiality of an asset
     * @param int $assetId
     * @return boolean true if private
     */
    public function isPublic( $assetId )
    {
        return Claroline::getDatabase()->query( "
            SELECT confidentiality
            FROM `{$this->tableName}`
            WHERE id = " . Claroline::getDatabase()->escape( (int)$assetId ) . "
            AND confidentiality = " . Claroline::getDatabase()->quote( self::ACCESS_PUBLIC )
        )->numRows(); 
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
                confidentiality = " . Claroline::getDatabase()->quote( self::ACCESS_PRIVATE ) . ",
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