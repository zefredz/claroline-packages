<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class that represents a published (and therefore stored) report
 * @property int $id
 * @property boolean $is_allowed
 * @property string $courseId
 * @property string $title
 * @property string $date
 * @property array $datas
 * @property boolean $is_visible
 */
class StoredReport
{
    protected $id;
    protected $is_allowed;
    
    protected $courseId;
    protected $title;
    protected $date;
    protected $is_visible;
    
    protected $datas;
    
    /**
     * Constructor
     */
    public function __construct( $id , $is_allowed = false )
    {
        $this->id = $id;
        $this->is_allowed = $is_allowed;
        
        $this->tbl = get_module_course_tbl( array( 'ICMONIT_report' ) );
        $this->load();
    }
    
    /**
     * Loads the report's datas
     * This method is called by the constructor
     */
    public function load()
    {
        if ( $result = Claroline::getDatabase()->query( "
            SELECT
                title, datas, publication_date, visibility
            FROM
                `{$this->tbl['ICMONIT_report']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->id )
        )->fetch() )
        {
            try
            {
                $this->title = $result[ 'title' ];
                $this->date = $result[ 'publication_date' ];
                $this->is_visible = $result[ 'visibility' ] == AssetList::VISIBLE;
                
                $this->datas = unserialize( $result[ 'datas' ] );
            }
            catch( Exception $e )
            {
                echo 'invalid datas : ' . $e->getMessage();
            }
        }
    }
    
    /**
     * Getter for report's title
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Getter for publication date
     * @return string $date
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Getter for report's datas
     * @return array $reportDatas
     */
    public function getDatas()
    {
        return $this->datas;
    }
    
    /**
     * Alias for getDatas()
     */
    public function export()
    {
        return $this->datas;
    }
    
    /**
     * Verifies if access to the report is allowed
     * @return boolean true if visible
     */
    public function isAllowed()
    {
        return $this->is_visible || $this->is_allowed;
    }
}
