<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.5.0 $Revision: 1319 $ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for access and right management
 */
class CLLIBR_ACL
{
    protected $userId;
    protected $is_platform_admin;
    protected $is_course_creator;
    
    protected $database;
    
    /**
     * Constructor
     */
    public function __construct( $database , $userId , $is_course_creator = false , $is_platform_admin = false )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array ( 'library_collection'
                                                , 'library_library'
                                                , 'library_librarian'
                                                , 'rel_course_user' ) );
        $this->userId = $userId;
        $this->is_course_creator = $is_course_creator;
        $this->is_platform_admin = $is_platform_admin;
    }
    
    /**
     *
     */
    public function accessGranted( $resourceId )
    {
        $in_bookmark = $this->database->query( "
            SELECT
                resource_id
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                type = 'bookmark'
            AND
                ref_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
        
        $in_bibliography = $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            INNER JOIN
                `{$this->tbl['rel_course_user']}` AS U
            ON
                C.ref_id = U.code_cours
            WHERE
                C.type = 'bibliography'
            AND
                U.user_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
        
        $in_library = $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            LEFT JOIN
                `{$this->tbl['library_library']}` AS P
            ON
                C.ref_id = P.id
            AND
                P.is_public = TRUE
            RIGHT JOIN
                `{$this->tbl['library_librarian']}` AS L
            ON
                C.ref_id = L.library_id
            AND
                L.user_id = " . $this->database->escape( $this->userId ) . "
            WHERE
                C.type = 'catalogue'
            AND
                resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
        
        return $this->is_platform_admin
            || $in_bookmark
            || $in_bibliography
            || $in_library;
    }
}