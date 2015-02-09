<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class MoodleQuiz
{
    protected $id;
    
    public $title;
    public $questionList;
    public $output;
    
    /**
     * Constructor
     * @param int $quizId : the exercise's id
     * @param string $title : the exercise's title
     * @param string $description : the exercise's description
     * @param boolean $shuffle : defines if the questions' order must be randomized or not
     * @return void
     */
    public function __construct( $quizId , $title , $description = '' , $shuffle = false )
    {
        $this->id = $quizId;
        $this->title = $title;
        $this->description = $description;
        $this->shuffle = $shuffle;
        
        $this->tbl = get_module_course_tbl ( array ( 'qwz_rel_exercise_question' ) );
        $this->load();
    }
    
    /**
     * Loads Exercise's datas
     * @return void
     */
    public function load()
    {
        $data = Claroline::getDatabase()->query(
            "SELECT
                questionId
            FROM
                `{$this->tbl['qwz_rel_exercise_question']}`
            WHERE
                exerciseId = " . Claroline::getDatabase()->escape( $this->id ) . "
            ORDER BY rank DESC"
        );
        
        $this->questionList = array();
        
        foreach( $data as $line )
        {
            $this->questionList[] = new MoodleQuestion( $line[ 'questionId' ] );
        }
    }
    
    /**
     * Exports Exercise's datas into and Moddle XML
     * @return bitstream (file to download)
     */
    public function export()
    {
        $template = new ModuleTemplate( 'MOODLEEX' , 'exercise.tpl.php' );
        $template->assign( 'questionList' , $this->questionList );
        $template->assign( 'title' , $this->title );
        
        if( $output = $template->render() )
        {
            header("Content-type: aplication/xml" );
            header('Content-Disposition: attachment; filename="' . MOODLEEX_clean( $this->title ) . '.xml"');
            header('Content-Enoding: UTF-8');
            echo claro_utf8_encode( $output );
            exit();
        }
        else
        {
            return false;
        }
    }
}
