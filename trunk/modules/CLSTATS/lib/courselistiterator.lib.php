<?php

class CourseList_Iterator implements Iterator, Countable
{
    protected $limit, $courseCount, $iterationCount;
    protected $table;
    protected $current, $idx;
    
    public function __construct( $limit = 50 )
    {
        $this->limit = $limit;
        
        if ( $this->limit == 0 )
        {
            throw new OutOfRangeException("Limit must be >= 0 !");
        }
        
        $this->table = get_module_main_tbl( array('courses_stats') );
        
        $this->courseCount = $this->countCourses();
        
        $this->iterationCount = $this->countIterations();
    }
    
    public function updateCourseStatus( $courseCode, $status = 'done' )
    {
        Claroline::getDatabase()->exec( "
            UPDATE `{$this->table['courses_stats']}`
            SET `status` = '" . Claroline::getDatabase()->escape( $status ) . "'
            WHERE `code_course` = '" . Claroline::getDatabase()->escape( $courseCode ) . "';
        " );
    }
    
    public function countCourses()
    {
        /*$result = $this->dbCourses->querySingle( "
            SELECT COUNT(*)
            FROM `{$this->table}`
            WHERE status = 'pending'
            ORDER BY code_course;
        ");
        return (int) $result;*/
        $result = Claroline::getDatabase()->query("
            SELECT COUNT(*)
            FROM `{$this->table['courses_stats']}`
            WHERE status = 'pending'
            ORDER BY code_course ASC;
        ");
        
        return (int) $result->fetch(Database_ResultSet::FETCH_VALUE);
    }
    
    public function countIterations()
    {
        if ( $this->courseCount == 0 )
        {
            return 0;
        }
        
        if ( $this->limit == 0 )
        {
            throw new OutOfRangeException("Limit must be >= 0 !");
        }
        
        $quot = floor( $this->courseCount / $this->limit );
        $rem = $this->courseCount % $this->limit;
        
        if ( $rem == 0 )
        {
            return $quot;
        }
        else
        {
            return $quot + 1;
        }
    }
    
    public function getNextBunchOfCourses()
    {
        /*$result = $this->dbCourses->query("
            SELECT *
            FROM `{$this->table}`
            WHERE status = 'pending'
            LIMIT " . $this->limit . "
            OFFSET " . $this->offset . ";
            ");
        */
        $result = Claroline::getDatabase()->query("
            SELECT *
            FROM `{$this->table['courses_stats']}`
            WHERE status = 'pending'
            LIMIT ".Claroline::getDatabase()->escape($this->limit).";
        ");
        
        return $result;
    }
    
    public function count()
    {
        return $this->iterationCount;
    }
    
    // --- Iterator ---
    
    /**
     * Check if the current position in the result set is valid
     * @see     Iterator
     * @return  boolean
     */
    public function valid()
    {
        return ($this->courseCount > 0);            
    }
    
    /**
     * Return the current row
     * @see     Iterator
     * @return  mixed, current row
     */
    public function current()
    {
        $this->current = $this->getNextBunchOfCourses();
        return $this->current;
    }
    
    /**
     * Advance to the next row in the result set
     * @see     Iterator
     */
    public function next()
    {
        $this->idx++;
        $this->courseCount -= $this->limit;        
    }
    
    /**
     * Rewind to the first row
     * @see     Iterator
     */
    public function rewind()
    {
        $this->idx = 0;
    }
    
    /**
     * Return the index of the current row
     * @see     Iterator
     * @return  int
     */
    public function key()
    {
        return $this->idx;
    }
}
