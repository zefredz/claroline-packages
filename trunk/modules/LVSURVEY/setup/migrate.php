<?php
global $tlabelReq;
$tlabelReq = 'LVSURVEY';

require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';  
require_once get_path('incRepositorySys') . '/lib/utils/input.lib.php';  
require_once dirname(__FILE__) . '/../lib/util/surveyConstants.class.php';

if ( !claro_is_user_authenticated() )
{
    claro_disp_auth_form(true);
}
if( !claro_is_platform_admin())
{
    echo get_lang( 'Migration can only be done by platform admin' );
    die();
}


class LVSurveyUpgrader
{
    /** @var \Claroline_Database_Connection */
    private $db;
    private $migration_dir;
    
    private $currentDBVersion = '00000000000000';
    
    private $currentFileVersion = '00000000000000';
    
    private $availableMigrations = array();
    
    private $selectedMigrations = array();
    
    private $isUpgrading = true;
    
    private $targetVersion;
    
    
    public function __construct($db, $migration_dir) {
        $this->db = $db;
        $this->migration_dir = $migration_dir;
        $this->populateAvailableMigrations();
        $this->determineDBVersion();
        $this->determineFileversion();
        $this->targetVersion = $this->currentFileVersion;
    }
    
    private function populateAvailableMigrations()
    {
        $this->availableMigrations = array();
        $elements = scandir($this->migration_dir);
        foreach($elements as $element)
        {
            if(preg_match("/\d{14}/", $element))
            {
                $this->availableMigrations[] = $element;
            }
        }
    }
    
    private function determineDBVersion()
    {
        if($this->moduleHasNotBeenInstalled())
        {
            $this->currentDBVersion = '00000000000000';
            return;
        }
        if($this->noVersionTable())
        {
            $this->currentDBVersion = '00000000000001';
            return;
        }
        
        $query =  'SELECT V.`version` '
                . 'FROM `'.SurveyConstants::$VERSION_TBL.'` V '
                . 'ORDER BY V.`created_at` DESC '
                . 'LIMIT 1 ';
        
        $rs = $this->db->query($query);
        $record = $rs->fetch();
        $this->currentDBVersion = $record['version'];
    }
    
    private function moduleHasNotBeenInstalled()
    {
        return $this->tableNotExists('survey2_survey');
    }
    
    private function noVersionTable()
    {        
        return $this->tableNotExists('survey2_version');
    }
    
    private function tableNotExists($tableName)
    {
        $prefix = get_conf('mainTblPrefix');
        $fullName = "{$prefix}{$tableName}";
        $query =    "SHOW TABLES LIKE '{$fullName}' ;";
        $record = $this->db->query($query)->fetch();
        return empty($record);
    }    
    
    private function determineFileversion()
    {
        $this->currentFileVersion = end($this->availableMigrations);
    }
    
    public function selectMigrations()
    {
        foreach($this->availableMigrations as $migration)
        {
            $min = $this->isUpgrading() ? $this->currentDBVersion : $this->targetVersion;
            $max = $this->isUpgrading() ? $this->targetVersion : $this->currentDBVersion;
            
            $shouldBeKept =     strcmp($migration, $min) > 0
                            &&  strcmp($migration, $max) <= 0;
            
            if( $shouldBeKept)
            {
                $this->selectedMigrations[] = $migration;
            }
        }
        
    }
    
    public function run($target)
    {
        if ($target != null)
        {
            $this->targetVersion = $target;
        }
        $this->determineDirection();        
        $this->selectMigrations();        
        if( sizeof( $this->selectMigrations() ) == 0 )
        {
            echo "No upgrade available<br/>Current database and file version : " . $this->currentDBVersion;
            die();
        }
        $sortedMigrations = $this->sortMigrations();
                
        $direction = $this->isUpgrading ? 'upgrade' : 'downgrade';
        echo "Migrating from version {$this->currentDBVersion} to version {$this->targetVersion} ({$direction}) <br/>";
        foreach($sortedMigrations as $migration)
        {
            $this->executeMigration($migration);
        }
    }
    
    private function determineDirection()
    {
        $this->isUpgrading = strcmp($this->targetVersion, $this->currentDBVersion) > 0;
    }
    
    private function isUpgrading()
    {
        return $this->isUpgrading;
    }
    
    private function sortMigrations()
    {
        if($this->isUpgrading())
        {
            return $this->selectedMigrations;
        }
        return array_reverse($this->selectedMigrations); 
    }
    
    private function executeMigration($migration)
    {
        echo "Executing migration {$migration} ...";
        $filename = $this->isUpgrading() ? 'up.sql' : 'down.sql';
        
        $file = implode(DIRECTORY_SEPARATOR, array
                (
                    $this->migration_dir,
                    $migration,
                    $filename
                )
        );
        $sql = file_get_contents($file);
        // @see http://www.dev-explorer.com/articles/multiple-mysql-queries
        $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
        foreach($queries as $query)
        {
            if(ctype_space($query))
            {
                continue;
            }
            
            $query = str_replace ('__CL_MAIN__',get_conf('mainTblPrefix'), $query );
            
            $this->db->exec($query);
        }
        
        echo " ... Done ! <br/>";
    }
    
    
}
$target = Claro_UserInput::getInstance()->get('target');

$claroDB = Claroline::getDatabase();
$migrationDir = dirname(__FILE__);

$upgradeEngine = new LVSurveyUpgrader($claroDB, $migrationDir);
$upgradeEngine->run($target);