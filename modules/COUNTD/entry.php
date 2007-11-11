<?php // $Id$
/**
 *
 * @version 1.0 $Revision: 100 $
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Marc Lavergne <marc86.lavergne@gmail.com>
 *
 * @package Countd
 *
 */


if ( count( get_included_files() ) == 1 ) die( '---' );

include_once claro_get_conf_repository().'Countd.conf.php';

$html = '<script type="text/javascript">
var yr=' . get_conf('Year') . '
var mo=' . get_conf('Month') . '
var da=' . get_conf('Day') . '
var hr=' . get_conf('Hour') . '
var min=' . get_conf('Minute') . '
var sec=' . get_conf('Second') . '
var occasion="' . get_conf('Display') . '"
var message_on_occasion="' . get_conf('Alert') . '"
var message_passed_occasion="' . get_conf('Passed') . '"
var countdownwidth=\'640px\' // ou une valeur en % comme var countdownwidth=\'95%\'
var countdownheight=\'35px\'
var countdownbgcolor="' . get_conf('bgcolor') . '"
var opentags=\'<font face="Verdana"><small>\'
var closetags=\'</small></font>\'';

$html .= <<< EOF

var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec")
var crosscount=''

function start_countdown(){
	if (document.layers)
		document.countdownnsmain.visibility="show"
		else if (document.all||document.getElementById)
			crosscount=document.getElementById&&!document.all?document.getElementById("countdownie") : countdownie
			countdown()
}

if (document.all||document.getElementById)
document.write('<span id="countdownie" style="width:'+countdownwidth+'; background-color:'+countdownbgcolor+'"></span>')

window.onload=start_countdown


function countdown(){
	var today=new Date()
	var todayy=today.getYear()
	if (todayy < 1000)
	todayy+=1900
	var todaym=today.getMonth()
	var todayd=today.getDate()
	var todayh=today.getHours()
	var todaymin=today.getMinutes()
	var todaysec=today.getSeconds()
	var todaystring=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec
	futurestring=montharray[mo-1]+" "+da+", "+yr+" "+hr+":"+min+":"+sec
	dd=Date.parse(futurestring)-Date.parse(todaystring)
	dday=Math.floor(dd/(60*60*1000*24)*1)
	dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1)
	dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1)
	dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1)
//if on day of occasion
	if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=1&&todayd==da){
		if (document.layers){
			document.countdownnsmain.document.countdownnssub.document.write(opentags+message_on_occasion+closetags)
			document.countdownnsmain.document.countdownnssub.document.close()
		}
		else if (document.all||document.getElementById)
			crosscount.innerHTML=opentags+message_on_occasion+closetags
		return
	}
//if passed day of occasion
	else if (dday<=-1){
		if (document.layers){
		document.countdownnsmain.document.countdownnssub.document.write(opentags+message_passed_occasion+closetags)
		document.countdownnsmain.document.countdownnssub.document.close()
		}
		else if (document.all||document.getElementById)
		crosscount.innerHTML=opentags+message_passed_occasion+closetags
		return
	}
//else, if not yet
			else{
				if (document.layers){
				document.countdownnsmain.document.countdownnssub.document.write("Il reste "+opentags+dday+ " jours, "+dhour+" heures, "+dmin+" minutes, et "+dsec+" secondes avant "+occasion+closetags)
				document.countdownnsmain.document.countdownnssub.document.close()
				}
				else if (document.all||document.getElementById)
				crosscount.innerHTML="Il reste "+opentags+dday+ " jours, "+dhour+" heures, "+dmin+" minutes, et "+dsec+" secondes avant "+occasion+closetags
				}
			setTimeout("countdown()",1000)
			}
</script>

EOF;

$claro_buffer->append($html);

?>