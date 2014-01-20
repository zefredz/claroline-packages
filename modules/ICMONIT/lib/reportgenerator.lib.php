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

class ReportGenerator
{
    protected $courseId;
    protected $title;
    protected $datas;
    
    public function __construct( $courseId , $datas )
    {
        $this->courseId = $courseId;
        $this->datas = $datas;
        
        foreach( $this->datas['users'] as $userId => $data )
        {
            if( ! $data[ 'active' ] )
            {
                unset( $this->datas[ 'users' ][ $userId ] );
                unset( $this->datas[ 'report' ][ $userId ] );
            }
        }
        
        $this->tbl = get_module_course_tbl( array( 'ICMONIT_report' ) );
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
                `{$this->tbl['ICMONIT_report']}`
            SET
                title = " . Claroline::getDatabase()->quote( $this->title ) . ",
                datas = " . Claroline::getDatabase()->quote( serialize( $this->datas ) ) . ",
                publication_date = NOW()" );
        
        return Claroline::getDatabase()->insertId();
    }
}
