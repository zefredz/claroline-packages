<?php
/**
 *  
 *  
 * CLAROLINE
 *
 * This  script  let the user choose a source course which he wants to duplicate.
 * The chosen course is placed in the session.
 *
 * Once done, the script redirects to next step of duplication (define target)
 * 
 *
 */
//=================================
// Include section
//=================================

require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/lib/LVDUPLIC.lib.php';
require_once get_path('incRepositorySys') . '/lib/pager.lib.php';

//=================================
// Security check
//=================================

// If you want to duplicate a course you need to be able to be an administrator
if ( ! claro_is_user_authenticated() )       	claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) 				claro_die( get_lang('Not allowed') );

//=================================
// Init
//=================================


$offsetCourse = isset($_REQUEST['offsetCourse']) ? $_REQUEST['offsetCourse'] : '0';
$validCmdList = array('duplicate',);
$cmd = (isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$validCmdList)? $_REQUEST['cmd'] : null);
$dupCode = isset($_REQUEST['dupCode']) ? $_REQUEST['dupCode'] : null;
$search = (isset($_REQUEST['search']))  ? $_REQUEST['search'] :'';
$addToURL = '';
$do=null;
$dialogBox = '';
$backUrl = get_module_entry_url('LVDUPLIC');

// Data from search form
$filterArray = array();
if (isset($_REQUEST['search'      ])) $filterArray['search'] 		= trim($_REQUEST['search'  ]);


// Deal with interbredcrumps
$interbredcrump[]= array ('url' => get_module_entry_url('LVDUPLIC') , 'name' => get_lang('Duplication'));
$nameTools = get_lang('Choose Source Course');


/**
 * PARSE COMMAND
 */

if ( 'duplicate' == $cmd )
{
    $sourceCourseData = claro_get_course_data( $dupCode );
    if ( $sourceCourseData ) $do = 'duplicate';
    else
    {
        switch(claro_failure::get_last_failure())
        {
            case 'course_not_found':
                $dialogBox = get_lang('Course not found');
                break;
            default  : $dialogBox = get_lang('Unknown error') . claro_failure::get_last_failure();
        }
    }
}

// EXECUTE
if ( 'duplicate' == $do )
{
    DUPSessionMgr::clearDupDataFromSession();
    DUPSessionMgr::setSourceCourseData( $sourceCourseData );
    claro_redirect( $backUrl . '?cmd='.DUPConstants::$DUP_STEP_DEFINE_TARGET);
    die;
}

/**
 * PREPARE DISPLAY
 *
 * Display contain 2 part
 *
 * * Filter/search panel
 * * List of datas
 */

$sqlCourseList = prepare_get_filtred_course_list($filterArray);
$myPager = new claro_sql_pager($sqlCourseList, $offsetCourse, get_conf('coursePerPage',20));
$sortKey = isset($_GET['sort']) ? $_GET['sort'] : 'officialCode';
$sortDir = isset($_GET['dir' ]) ? $_GET['dir' ] : SORT_ASC;
$myPager->set_sort_key($sortKey, $sortDir);
$myPager->set_pager_call_param_name('offsetCourse');
$courseList = $myPager->get_result_list();


/**
 * Prepare display of search/Filter panel
 */

$isSearched ='';

if ( !empty($_REQUEST['search']) ) $isSearched .= trim($_REQUEST['search']) . ' ';


$courseDataList=array();
// Now read datas and rebuild cell content to set datagrid to display.
//extra column : duplicate course
$moduleData 	= get_module_data('LVDUPLIC');
$url 			= get_module_url('LVDUPLIC');
$path 			= get_module_path('LVDUPLIC');   	    	
$icon = '<img src="' . $url . '/duplicate.png" alt="' . get_lang('Duplicate') . '" />';


foreach( $courseList as $numLine => $courseLine )
{
    if (    ! empty($filterArray['search'] ) )
    {
        $boldSearch = str_replace('*', '.*', $filterArray['search']);
        $courseLine['officialCode'] = preg_replace("/(".$boldSearch.")/i","<b>\\1</b>", $courseLine['officialCode']);
        $courseLine['intitule'] = preg_replace("/(".$boldSearch.")/i","<b>\\1</b>", $courseLine['intitule']);
    }

    // Official Code
    $courseDataList[$numLine]['officialCode'] = $courseLine['officialCode'];

    // Label
    $courseDataList[$numLine]['intitule'] =  '<a href="' . get_path('clarolineRepositoryWeb') . 'course/index.php?cid=' . htmlspecialchars($courseLine['sysCode']) . '">'
    .                                        $courseLine['intitule']
    .                                        '</a>';
    
    
    //Duplicate course     	
    $courseDataList[$numLine]['cmdDUP'] = 	'<a href="' . $_SERVER['PHP_SELF'] . '?cmd=duplicate&amp;dupCode=' . $courseLine['sysCode']. '" >'
    .										$icon
    .                                       '</a>'   	
    ;	    
   
}


// CONFIG DATAGRID.
$sortUrlList = $myPager->get_sort_url_list( $_SERVER['PHP_SELF'] );

$courseDataGrid = new claro_datagrid( $courseDataList );

$courseDataGrid->set_colTitleList(array ( 'officialCode' => '<a href="' . $sortUrlList['officialCode'] . '">' . get_lang('Course code') . '</a>'
                                        , 'intitule'     => '<a href="' . $sortUrlList['intitule'    ] . '">' . get_lang('Course title'). '</a>'                                        
                                        , 'cmdDUP'    	=> get_lang('Duplicate')));
                                        
$courseDataGrid->set_colAttributeList( array ( 'cmdDUP' 	=> array ('align' => 'center')
                                             )
                                    );


$courseDataGrid->set_idLineType('none');
$courseDataGrid->set_colHead('officialCode') ;

$courseDataGrid->set_noRowMessage( get_lang('There is no course matching such criteria') . '<br />');

/** ***********************************************************************************
 ************************************** DISPLAY *************************************
 ************************************************************************************/

/** DISPLAY : Common Header */
include get_path('incRepositorySys') . '/claro_init_header.inc.php';
echo claro_html_tool_title($nameTools);
if ( !empty($dialogBox) ) echo claro_html_message_box($dialogBox);

if ( !empty($isSearched) )
{
    echo claro_html_message_box ( '<b>' . get_lang('Search on') . '</b> : <small>' .$isSearched . '</small>' );
}

/**
 * DISPLAY : Search/filter panel
 */
echo '<table width="100%">' . "\n\n"
.    '<tr>' . "\n"
.    '<td align="left" valign="top">' . "\n"
.    '</td>' . "\n"
.    '<td align="right"  valign="top">' . "\n\n"
.    '<form action="' . $_SERVER['PHP_SELF'] . '">' . "\n"
.    '<label for="search">' . get_lang('Make new search') . ' : </label>'."\n"
.    '<input type="text" value="' . htmlspecialchars($search) . '" name="search" id="search" />' . "\n"
.    '<input type="submit" value=" ' . get_lang('Ok') . ' " />' . "\n"
.    '<input type="hidden" name="newsearch" value="yes" />' . "\n"
.    '</form>'  . "\n\n"
.    '</td>'    . "\n"
.    '</tr>'    . "\n\n"
.    '</table>' . "\n\n"
;

/** DISPLAY : LIST of data */

echo $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF'])
.    $courseDataGrid->render()
.    $myPager->disp_pager_tool_bar($_SERVER['PHP_SELF']);
;

/** DISPLAY : Common footer */
include get_path('incRepositorySys') . '/claro_init_footer.inc.php';


/**
 * Return a list of courses based on given criteria
 * 
 * @param $filter : an array contaning the different filters the different possible  keys are :
 * search : String only the courses which the name,the category or the code contains this value
 
 * 
 * @return a String containing the SQL request for selecting `officialCode`, `intitule`,`faculte`,`sysCode`,`repository`
 * of the subset of wanted courses
 * 
 * @see claroline/admin/admin_courses.php#prepare_get_filtred_course_list()
 */
function prepare_get_filtred_course_list($filter = array())
{
    $tbl_mdb_names       = claro_sql_get_main_tbl();

    $sqlFilter = array();
    // Prepare filter deal with KEY WORDS classification call
    if (isset($filter['search']))
        $sqlFilter[] = "(  co.`intitule`  LIKE '%". claro_sql_escape(pr_star_replace($filter['search'])) ."%'" . "\n"
                     . "   OR co.`administrativeNumber` LIKE '%". claro_sql_escape(pr_star_replace($filter['search'])) ."%'" . "\n"
                     . ")";
    
    
    // Create the WHERE clauses
    $sqlFilter = sizeof($sqlFilter) ? "WHERE " . implode(" AND ",$sqlFilter)  : "";
    
    // Build the complete SQL request
    $sql = "SELECT co.`cours_id`      AS `id`, " . "\n"
         . "co.`administrativeNumber` AS `officialCode`, " . "\n"
         . "co.`intitule`             AS `intitule`, " . "\n"
         . "co.`code`                 AS `sysCode`, " . "\n"
         . "co.`sourceCourseId`       AS `sourceCourseId`, " . "\n"
         . "co.`isSourceCourse`       AS `isSourceCourse`, " . "\n"
         . "co.`visibility`           AS `visibility`, " . "\n"
         . "co.`access`               AS `access`, " . "\n"
         . "co.`registration`         AS `registration`, " . "\n"
         . "co.`registrationKey`      AS `registrationKey`, " . "\n"
         . "co.`directory`            AS `repository`, " . "\n"
         . "co.`status`               AS `status` " . "\n"
         . "FROM  `" . $tbl_mdb_names['course'] . "` AS co " . "\n"
         . $sqlFilter ;
    
    return $sql;
}

?>