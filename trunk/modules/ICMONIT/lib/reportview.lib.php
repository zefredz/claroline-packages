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
 * A abstract class for report view managment
 * @property Report object $report
 * @property int $userId
 * @property boolean $is_admin
 */
abstract class ReportView
{
    protected $report;
    protected $userId;
    protected $is_admin;
    
    /**
     * Contructor
     * @param Report object $report
     * @param int $userId
     */
    public function __construct( $report , $userId , $is_admin = false )
    {
        $this->report = $report;
        $this->userId = $userId;
        $this->is_admin = $is_admin;
    }
    
    /**
     * Renders the report view
     */
    abstract public function render();
}
