<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.5.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class for access and right management
 * @const ACCESS_EDIT
 * @const ACCESS_READ
 * @const ACCESS_SEARCH
 * @property int $userId;
 * @property boolean $is_platform_admin
 * @property boolean $is_course_manager
 */
class CLLIBR_ACL
{
    const ACCESS_EDIT = 'edit';
    const ACCESS_READ = 'read';
    const ACCESS_SEARCH = 'search';
    
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
                                                , 'library_course_library'
                                                , 'rel_course_user'
                                                , 'cours' ) );
        $this->userId = (int)$userId;
        $this->is_course_creator = $is_course_creator;
        $this->is_platform_admin = $is_platform_admin;
    }
    
    /**
     * Controls if user is allowed to acess the specified resource
     * @param int $resourceId
     * @param string $access
     * @return boolean
     */
    public function accessGranted( $resourceId , $access = self::ACCESS_SEARCH )
    {
        return $this->is_platform_admin
            || $this->in_bookmark( $resourceId )
            || $this->in_bibliography( $resourceId )
            || $this->in_library( $resourceId )
            || $this->in_course_library( $resourceId );
    }
    
    /**
     * Controls if user is allowed to edit the specified resource
     * @param int $resourceId
     * @return boolean
     */
    public function editGranted( $resourceId )
    {
        return $this->is_platform_admin
            || $this->is_librarian( $resourceId );
    }
    
    /**
     * Controls if resource is in user's bookmark
     * @return boolean
     */
    private function in_bookmark( $resourceId )
    {
        return $this->database->query( "
            SELECT
                resource_id
            FROM
                `{$this->tbl['library_collection']}`
            WHERE
                collection_type = 'bookmark'
            AND
                ref_id = " . $this->database->escape( $this->userId ) . "
            AND
                resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
    }
    
    /**
     * Controls if resource is in a bibliography of a course which user has access
     * @return boolean
     */
    private function in_bibliography( $resourceId )
    {
        return $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            INNER JOIN
                `{$this->tbl['cours']}` AS P
            ON
                P.code = C.ref_id
            INNER JOIN
                `{$this->tbl['rel_course_user']}` AS U
            ON
                U.code_cours = C.ref_id
            WHERE
                C.collection_type = 'bibliography'
            AND
                ( C.is_visible = TRUE
            AND
                U.isCourseManager = TRUE )
            AND
                ( P.access = 'public'
            OR
                U.user_id = " . $this->database->escape( $this->userId ) . ")
            AND
                C.resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
    }
    
    /**
     * Controls if resource is in a library of a course which user has access
     * @return boolean
     */
    private function in_course_library( $resourceId )
    {
        return $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            INNER JOIN
                `{$this->tbl['library_course_library']}` AS L
            ON
                L.library_id = C.ref_id
            INNER JOIN
                `{$this->tbl['rel_course_user']}` AS U
            ON
                U.code_cours = L.course_id
            WHERE
                C.collection_type = 'catalogue'
            AND
                U.user_id = " . $this->database->escape( $this->userId ) . "
            AND
                C.resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
    }
    
    /**
     * Controls if resource is in a library which user has access
     * @return boolean
     */
    private function in_library( $resourceId )
    {
        $cond = $access == self::ACCESS_READ
              ? " = 'public'"
              : " != 'private'";
        
        $sql = $access != self::ACCESS_EDIT
             ? "\nAND
                ( P.status" . $cond . "
            OR
                L.user_id = " . $this->database->escape( $this->userId ) . ")"
             : "";
        
        return $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            INNER JOIN
                `{$this->tbl['library_library']}` AS P
            ON
                P.id = C.ref_id
            INNER JOIN
                `{$this->tbl['library_librarian']}` AS L
            ON
                L.library_id = C.ref_id
            WHERE
                C.collection_type = 'catalogue'". $sql . "
            AND
                C.resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
    }
    
    /**
     * Controls if resource is in a library which user is librarian
     * @return boolean
     */
    private function is_librarian( $resourceId )
    {
        return $this->database->query( "
            SELECT
                C.resource_id
            FROM
                `{$this->tbl['library_collection']}` AS C
            INNER JOIN
                `{$this->tbl['library_librarian']}` AS L
            ON
                C.ref_id = L.library_id
            AND
                L.user_id = " . $this->database->escape( $this->userId ) . "
            WHERE
                C.collection_type = 'catalogue'
            AND
                C.resource_id = " . $this->database->escape( $resourceId )
        )->numRows();
    }
}