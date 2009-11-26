<?php

class CLLNP_Stats extends ClaroStats_CourseTask
{
    public function getLabel()
    {
        return 'CLLNP';
    }
    
    public function getData($course)
    {
        $tables = $this->getCourseTables(array('lp_learnPath','lp_asset','lp_module'),$course);
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_learnPath']}` WHERE learnPath_id != 1"
        );
        
        $cllp_count_learningpaths = $res->fetch();
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_module']}` WHERE module_id > 2"
        );
        
        $cllp_count_modules = $res->fetch();
        
        $res = Claroline::getDatabase()->query(
            "SELECT COUNT(*) AS `value` FROM `{$tables['lp_asset']}` WHERE asset_id > 2"
        );
        
        $cllp_count_assets = $res->fetch();
        
        return array(
            'cllp_count_learningpaths' => $cllp_count_learningpaths['value'],
            'cllp_count_modules' => $cllp_count_modules['value'],
            'cllp_count_assets' => $cllp_count_assets['value']
        );
    }
}