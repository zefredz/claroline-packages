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

class ReportGenerator
{
    protected $courseId;
    protected $title;
    protected $datas;
    
    public function __construct( $courseId , $datas )
    {
        $this->courseId = $courseId;
        $this->datas = $datas;
        
        $this->tbl = get_module_course_tbl( array( 'report_report' ) );
    }
    
    public function setTitle( $title )
    {
        $this->title = $title;
    }
    
    /**
     * Saves the reports
     * @return boolean true if successful
     */
    public function save()
    {
        if ( ! $this->title )
        {
            throw new Exception( 'Cannot save a report without title!' );
        }
        
        Claroline::getDatabase()->exec( "
            INSERT INTO
                `{$this->tbl['report_report']}`
            SET
                title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                datas = " . Claroline::getDatabase()->quote( serialize( $this->datas ) ) . ",
                publication_date = NOW()" );
        
        return Claroline::getDatabase()->insertId();
    }
}