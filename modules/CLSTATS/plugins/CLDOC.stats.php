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
        $it = new RecursiveDirectoryIterator($courseRootPath);
        
        $cldoc_count_files = 0;
        $cldoc_count_folders = 0;
        
        foreach ( $it as $file )
        {
            if ( $file->isDir() && !preg_match('/^\.+/', $file->getFileName() ) )
            {
                $cldoc_count_folders++;
            }
            elseif ( $file->isFile() )
            {
                $cldoc_count_files++;
            }
        }
        
        $it2 = new DirectoryIterator($courseRootPath);
        $cldoc_count_items_at_first_level = 0;
        
        foreach ( $it as $file )
        {
            if ( !preg_match('/^\.+/', $file->getFileName() ) )
            {
                $cldoc_count_items_at_first_level++;
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
            'cldoc_count_items_at_first_level' => $cldoc_count_items_at_first_level,
            'cldoc_count_comments' => $cldoc_count_comments['value']
        );
    }
    
    public function getReportData( &$report, $itemStats, $nbCourses = 0 )
    {
        foreach( $itemStats as $itemName => $item )
        {
            parent::initReportData( $report, $itemName, $item );
            parent::setReportData( $report, $itemName, $item );
            parent::setReportMax( $report, $itemName, $item );
            //parent::setReportAverage( $report, $itemName, $item, $nbCourses );            
        }
        
        //Check if we need to substract invisible files or not
        return $itemStats[ 'cldoc_count_files' ]['value'];
    }
    
    public function getSummarizedReport( $items )
    {
        if(isset( $items['cldoc_count_files' ] ) )
        {
            $items['cldoc_count_files']['lessFive'] = $items['cldoc_count_files']['zero']
                                                            + $items['cldoc_count_files']['one']
                                                            + $items['cldoc_count_files']['two']
                                                            + $items['cldoc_count_files']['three']
                                                            + $items['cldoc_count_files']['four'];
            $items['cldoc_count_files']['moreFive'] += $items['cldoc_count_files']['five'];            
            return $items['cldoc_count_files' ];
        }
        else
        {
            return null;
        }
    }
}