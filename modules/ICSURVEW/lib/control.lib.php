<?php

class ICSURVEW_Control
{
    public function __construct( $userId )
    {
        $this->userId = $userId;
    }
    /**
     * Searchs for course which user is manager
     */
    public function getManagerCourseList()
    {
        $tbl_mdb_names = claro_sql_get_main_tbl();
        
        return Claroline::getDatabase()->query( "
            SELECT
                code AS id,
                administrativeNumber AS code
                intitule AS title
            FROM
                `{$tbl_mdb_names['cours']}` AS C
            INNER JOIN
                `{$tbl_mdb_names['rel_course_user']}` AS U
            ON
                U.code_cours = C.id
            WHERE
                U.user_id =" . Claroline::getDatabase()->escape( $this->userId ) . "
            AND
                U.isCourseManager = TRUE" );
    }
}