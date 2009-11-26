<?php

class CLDOC_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLDOC';
    }
    
    public function getData( $course )
    {
        // 1. count files in directory
        // 2. count folders in directory
        // 3. total items = #files + #folders
        $courseData = claro_get_course_data( $course );
        
        $courseRootPath = get_conf( 'rootSys' ) . get_conf( 'coursesRepositoryAppend') . $courseData[ 'path' ] . '/document';
        $it = new RecursiveIteratorIterator(  new RecursiveDirectoryIterator($courseRootPath) );
        
        $cldoc_count_files = 0;
        $cldoc_count_folders = 0;
        
        foreach ( $it as $file )
        {
            if ( $file->isDir() && ! $file->isDot() )
            {
                $cldoc_count_folders++;
            }
            elseif ( $file->isFile() )
            {
                $cldoc_count_files++;
            }
        }
        
        // 4. count invisible files
        // 5. count invisible folders
        // 6. invisible items = #invis_files + #invis_folders
        // 7. visible files = #files - #invisible_files
        // 8. visible folders = #folders - #invisible_folders
        // 9. visible items = #items - #invis_items
        
        $cldoc_count_invisible_files = 0;
        $cldoc_count_invisible_folders = 0;
        
        $tables = $this->getCourseTables(array('document'),$course);
        
        $res = Claroline::getDatabase()->query("SELECT path FROM `{$tables['document']}` WHERE visibility ='i'");
        
        foreach ( $res as $file )
        {
            if ( preg_match( '/\w+\.\w+$/', $file['path'] ) )
            {
                $cldoc_count_invisible_files++;
            }
            else
            {
                $cldoc_count_invisible_folders++;
            }
        }
        
        //10. count comments
        
        $res = Claroline::getDatabase()->query("SELECT COUNT(*) AS `value` FROM `{$tables['document']}` WHERE `comment` IS NOT NULL");
        
        $cldoc_count_comments = $res->fetch();
        
        return array(
            'cldoc_count_invisible_files' => $cldoc_count_invisible_files,
            'cldoc_count_files' => $cldoc_count_files,
            'cldoc_count_invisible_folders' => $cldoc_count_invisible_folders,
            'cldoc_count_folders' => $cldoc_count_folders,
            'cldoc_count_comments' => $cldoc_count_comments['value']
        );
    }
}