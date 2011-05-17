<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Yassine Hassaine <yassinehassaine@gmail.com>
 *
 * @package CLIM
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

// $tlabelReq = 'CLIM';

include_once claro_get_conf_repository().'CLIM.conf.php'; 

$tbl = claro_sql_get_tbl('user_online', array('course'=>null));

$sql = "SELECT COUNT(`id`) AS `user_id` FROM `".$tbl['user_online']."`";

$countOfUsers = claro_sql_query_get_single_value($sql);

//-- Output

$html = '';

$chatCSS = $_SERVER['PHP_SELF'] . '/css/chat.css';

$screenCSS = $_SERVER['PHP_SELF'] . '/../css/screen.css';

$html.= '
<link type="text/css" rel="stylesheet" media="all" href="'. $GLOBALS['rootWeb'] .'module/CLIM/css/chat.css" />
<link type="text/css" rel="stylesheet" media="all" href="'. $GLOBALS['rootWeb'] .'module/CLIM/css/screen.css" />
<style type="text/css">
* {margin: 0; padding: 0; outline: none;}

#footpanel {
	font: 10px normal Verdana, Arial, Helvetica, sans-serif;
	position: fixed;
	bottom: 0; left: 0;
	z-index: 9999; /*--Keeps the panel on top of all other elements--*/
	background: #e3e2e2;
	border: 1px solid #c3c3c3;
	border-bottom: none;
	width: 14%;
	margin: 0 82%;
}

*html #footpanel { /*--IE6 Hack - Fixed Positioning to the Bottom--*/
	margin-top: -1px; /*--prevents IE6 from having an infinity scroll bar - due to 1px border on #footpanel--*/
	position: absolute;
	top:expression(eval(document.compatMode &amp;&amp;document.compatMode==\'CSS1Compat\') ?documentElement.scrollTop+(documentElement.clientHeight-this.clientHeight) : document.body.scrollTop +(document.body.clientHeight-this.clientHeight));
}

#footpanel ul {
	padding: 0; margin: 0;
	float: left;
	width: 100%;
	list-style: none;
	border-top: 1px solid #fff; /*--Gives the bevel feel on the panel--*/
	font-size: 1.1em;
}
#footpanel ul li{
	padding: 0; margin: 0;
	float: left;
	position: relative;
}
#footpanel ul li a{
	padding: 5px;
	float: left;
	text-indent: -9999px;
	height: 16px; width: 16px;
	text-decoration: none;
	color: #333;
	position: relative;
}
html #footpanel ul li a:hover{	background-color: #fff; }
html #footpanel ul li a.active { /*--Active state when subpanel is open--*/
	background-color: #fff;
	height: 17px;
	margin-top: -2px; /*--Push it up 2px to attach the active button to subpanel--*/
	border: 1px solid #555;
	border-top: none;
	z-index: 200; /*--Keeps the active area on top of the subpanel--*/
	position: relative;
}
#footpanel a.home{	
	background: url(home.png) no-repeat 15px center;
	width: 50px;
	padding-left: 40px;
	border-right: 1px solid #bbb;
	text-indent: 0; /*--Reset text indent--*/
}
#footpanel a.chat{	
	background: url('. $GLOBALS['rootWeb'] .'module/CLIM/bar/balloon.png) no-repeat 15px center;
	width: 134px;
	padding-left: 40px;
	text-indent: 0; /*--Reset text indent--*/
}
a.alerts{	background: url(newspaper.png) no-repeat center center;	 }

#footpanel li#chatpanel, #footpanel li#alertpanel {	float: right; }  /*--Right align the chat and alert panels--*/

#footpanel a small {  /*--panel tool tip styles--*/
	text-align: center;
	width: 70px;
	background: url(pop_arrow.gif) no-repeat center bottom;
	padding: 5px 5px 11px;
	display: none; /*--Hide by default--*/
	color: #fff;
	font-size: 1em;
	text-indent: 0;
}
#footpanel a:hover small{
	display: block; /*--Show on hover--*/
	position: absolute;
	top: -35px; /*--Position tooltip 35px above the list item--*/
	left: 50%; 
	margin-left: -40px; /*--Center the tooltip--*/
	z-index: 9999;
}

#footpanel ul li div a { /*--Reset link style for subpanel links--*/
	text-indent: 0;
	width: auto;
	height: auto;
	padding: 0;
	float: none;
	color: #00629a;
	position: static;
}
#footpanel ul li div a:hover {	text-decoration: underline; } /*--Reset link style for subpanel links--*/

#footpanel .subpanel {
	position: absolute;
	left: 0; bottom: 27px;
	display: none;	/*--Hide by default--*/
	width: 198px;
	border: 1px solid #555;
	background: #fff;
	overflow: hidden;
	padding-bottom: 2px;
}
#footpanel h3 {
	background: #526ea6;
	padding: 5px 10px;
	color: #fff;
	font-size: 1.1em;
	cursor: pointer;
}
#footpanel h3 span { 
	font-size: 1.5em;
	float: right;
	line-height: 0.6em;	
	font-weight: normal;
}
#footpanel .subpanel ul{
	padding: 0; margin: 0;
	background: #fff;
	width: 100%;
	overflow: auto;
}
#footpanel .subpanel li{ 
	float: none; /*--Reset float--*/
	display: block;
	padding: 0; margin: 0;
	overflow: hidden;
	clear: both;
	background: #fff;
	position: static;  /*--Reset relative positioning--*/
	font-size: 0.9em;
}

#chatpanel .subpanel li { background: url(dash.gif) repeat-x left center; } 
#chatpanel .subpanel li span {
	padding: 5px;
	background: #fff;
	color: #777;
	float: left;
}
#chatpanel .subpanel li a img {
	float: left;
	margin: 0 5px;
}
#chatpanel .subpanel li a{
	padding: 3px 0;	margin: 0;
	line-height: 22px;
	height: 22px;
	background: #fff;
	display: block;
}
#chatpanel .subpanel li a:hover {
	background: #3b5998;
	color: #fff;
	text-decoration: none;
}


#alertpanel .subpanel { right: 0; left: auto; /*--Reset left positioning and make it right positioned--*/ }
#alertpanel .subpanel li {
	border-top: 1px solid #f0f0f0;
	display: block;
}
#alertpanel .subpanel li p {padding: 5px 10px;}
#alertpanel .subpanel li a.delete{
	background: url(delete_x.gif) no-repeat;
	float: right;
	width: 13px; height: 14px;
	margin: 5px;
	text-indent: -9999px;
	visibility: hidden; /*--Hides by default but still takes up space (not completely gone like display:none;)--*/
}
#alertpanel .subpanel li a.delete:hover { background-position: left bottom; }
#footpanel #alertpanel li.view {
	text-align: right;
	padding: 5px 10px 5px 0;
}
</style>
<script src="bar/jquery-1.3.2.min.js" type="text/javascript"></script>
<script language="Javascript">
var rootWeb ="'. $GLOBALS['rootWeb'] .'";
</script>
<script src="'. $GLOBALS['rootWeb'] .'module/CLIM/js/chat.js" type="text/javascript"></script>
<script type="text/javascript"> 
$(document).ready(function(){

	//Adjust panel height
	$.fn.adjustPanel = function(){ 
		$(this).find("ul, .subpanel").css({ \'height\' : \'auto\'}); //Reset subpanel and ul height
		
		var windowHeight = $(window).height(); //Get the height of the browser viewport
		var panelsub = $(this).find(".subpanel").height(); //Get the height of subpanel	
		var panelAdjust = windowHeight - 100; //Viewport height - 100px (Sets max height of subpanel)
		var ulAdjust =  panelAdjust - 25; //Calculate ul size after adjusting sub-panel (27px is the height of the base panel)
		
		if ( panelsub >= panelAdjust ) {	 //If subpanel is taller than max height...
			$(this).find(".subpanel").css({ \'height\' : panelAdjust }); //Adjust subpanel to max height
			$(this).find("ul").css({ \'height\' : ulAdjust}); //Adjust subpanel ul to new size
		}
		else if ( panelsub < panelAdjust ) { //If subpanel is smaller than max height...
			$(this).find("ul").css({ \'height\' : \'auto\'}); //Set subpanel ul to auto (default size)
		}
	};
	
	//Execute function on load
	$("#chatpanel").adjustPanel(); //Run the adjustPanel function on #chatpanel
	$("#alertpanel").adjustPanel(); //Run the adjustPanel function on #alertpanel
	
	//Each time the viewport is adjusted/resized, execute the function
	$(window).resize(function () { 
		$("#chatpanel").adjustPanel();
		$("#alertpanel").adjustPanel();
	});
	
	//Click event on Chat Panel + Alert Panel	
	$("#chatpanel a:first, #alertpanel a:first").click(function() { //If clicked on the first link of #chatpanel and #alertpanel...
		if($(this).next(".subpanel").is(\':visible\')){ //If subpanel is already active...
			$(this).next(".subpanel").hide(); //Hide active subpanel
			$("#footpanel li a").removeClass(\'active\'); //Remove active class on the subpanel trigger
		}
		else { //if subpanel is not active...
			$(".subpanel").hide(); //Hide all subpanels
			$(this).next(".subpanel").toggle(); //Toggle the subpanel to make active
			$("#footpanel li a").removeClass(\'active\'); //Remove active class on all subpanel trigger
			$(this).toggleClass(\'active\'); //Toggle the active class on the subpanel trigger
		}
		return false; //Prevent browser jump to link anchor
	});
	
	//Click event outside of subpanel
	$(document).click(function() { //Click anywhere and...
		$(".subpanel").hide(); //hide subpanel
		$("#footpanel li a").removeClass(\'active\'); //remove active class on subpanel trigger
	});
	$(\'.subpanel ul\').click(function(e) { 
		e.stopPropagation(); //Prevents the subpanel ul from closing on click
	});
	
	//Delete icons on Alert Panel
	$("#alertpanel li").hover(function() {
		$(this).find("a.delete").css({\'visibility\': \'visible\'}); //Show delete icon on hover
	},function() {
		$(this).find("a.delete").css({\'visibility\': \'hidden\'}); //Hide delete icon on hover out
	});
});
</script>
<div id="footpanel">
	<ul id="mainpanel">
        <li id="chatpanel">
        	<a class="chat" href="#">';
if( $countOfUsers > 1 )
{
    $html.= get_lang('%countOfUsers users connected', array('%countOfUsers'=> $countOfUsers));
}
elseif( $countOfUsers == 1 )
{
    $html.= get_lang('1 user connected');
}
else // $countOfUsers < 1
{
    $html.= get_lang('No user connected');
}	
$html .=    '</a>
			<div class="subpanel" style="height: 314px; display: none;">
            <h3><span> &ndash; </span>Utilisateurs en Ligne</h3>
            <ul style="height: 289px;">';
require_once dirname(__FILE__) . '/../../claroline/inc/lib/pager.lib.php';
require_once dirname(__FILE__) . '/../../claroline/inc/lib/user.lib.php';
$userPerPage = get_conf('usersPerPage');
$tbl = claro_sql_get_tbl(array('user_online','user'), array('course'=>null));
$sql = "SELECT U.`nom`                  AS `lastname`,
               U.`prenom`               AS `firstname`,
               U.`email`                AS `email`,
			   U.`username`             AS `username`,
               U.`user_id`              AS `user_id`,
               U.`isCourseCreator`      AS `isCourseCreator`,
               O.`last_action`          AS `last_action`
          FROM `" . $tbl['user_online'] . "` AS O,
               `" . $tbl['user'] . "`        AS U
         WHERE U.`user_id` = O.`user_id`";
$userIdChat = claro_get_current_user_id();
$mainTblList = claro_sql_get_main_tbl();
$tbl_user    = $mainTblList['user'];
$qry = "SELECT `username`
				FROM `".$tbl_user."`
				WHERE `user_id` = $userIdChat";
		$userNameChat = claro_sql_query_get_single_value($qry);
$_SESSION['username'] = $userNameChat;
$offset       = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0 ;
$myPager      = new claro_sql_pager($sql, $offset, $userPerPage);
$pagerSortKey = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'lastname';
$pagerSortDir = isset($_REQUEST['dir' ]) ? $_REQUEST['dir' ] : SORT_ASC;
$myPager->set_sort_key($pagerSortKey, $pagerSortDir);
$userList = $myPager->get_result_list();
$sortUrlList = $myPager->get_sort_url_list($_SERVER['PHP_SELF']);
foreach($userList as $user)
{
	$html.= '<li><a onclick="javascript:chatWith(\'' . $user['username'] . '\')" href="javascript:void(0)">' . $user['username'] . '</a></li>' . "\n";
}
				$html.= '
            </ul>
            </div>
        </li>
	</ul>
</div>';
$claro_buffer->append($html);

?>
