<?php
From::module('LVSURVEY')->uses( 'util/surveyConstants.class', 
                                'model/surveyLine.class');

class SeparatorLine extends SurveyLine
{
    public $title;
    public $description;
    
    
    public function __construct($survey, $title)
    {
        parent::__construct($survey);
        $this->title = $title;
        $this->description = '';
    }
    
    static function __set_state($array)
    {
        if(empty($array))
        {
            return false;
        }
        
        $res = new SeparatorLine($array['survey'], $array['title']);
        
        foreach ($array as $akey => $aval)
        {
            $res->{$akey} = $aval;
        }
        
        return $res;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function checkConsistency()
    {
        parent::checkConsistency();
        
        if(empty($this->title))
        {
            throw new Exception("Survey line (Separator) not consistent : empty title");
        }
    }
    
    protected function insertConcreteLine()
    {
        $dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlInsertRel = "
            INSERT INTO     `".SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL."`
                SET         `id`            = ".(int) $this->id.",
                            `title`         = ".$dbCnx->quote($this->title).",
                            `description`   = ".$dbCnx->quote($this->description)." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlInsertRel);
    }
    
    protected function updateConcreteLine()
    {
        $dbCnx = ClaroLine::getDatabase();
        //add a relation survey-question
        $sqlUpdate = "
            UPDATE      `".SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL."`
            SET         `title`         = ".$dbCnx->quote($this->title).",
                                `description`   = ".$dbCnx->quote($this->description)."
            WHERE       `id`                = ".(int) $this->id." ; ";
        // execute the creation query and get id of inserted assignment
        $dbCnx->exec($sqlUpdate);
    }
    
    protected function deleteConcreteLine()
    {
        $dbCnx = ClaroLine::getDatabase();
        $sql = "
                    DELETE FROM `".SurveyConstants::$SURVEY_LINE_SEPARATOR_TBL."`
                    WHERE           `id`                = ".(int) $this->id." ; ";
        $dbCnx->exec($sql);
    }
    
    public function render($editMode, $participation, $allowChange = true)
    {
        $separatorLineTpl = new PhpTemplate(get_module_path('LVSURVEY').'/templates/separatorLine.tpl.php');
        $separatorLineTpl->assign('surveyLine', $this);
        $separatorLineTpl->assign('editMode', $editMode);
        $separatorLineTpl->assign('allowChange', $allowChange);
        return $separatorLineTpl->render();
    }
}