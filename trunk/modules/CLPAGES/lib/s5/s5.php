<?php // $Id$

/*
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */

// load Claroline kernel
$tlabelReq = 'CLPAGES';

require_once dirname(__FILE__) . '/../../../../claroline/inc/claro_init_global.inc.php';

// load required class
require_once dirname( __FILE__ ) . '/../clpages.lib.php';
require_once dirname( __FILE__ ) . '/../pluginRegistry.lib.php';

if( isset($_REQUEST['pageId']) && is_numeric($_REQUEST['pageId']) )
{
    $pageId = (int) $_REQUEST['pageId'];
}
else
{
    $pageId = null;
}

if( isset($_REQUEST['componentId'])&& is_numeric($_REQUEST['pageId']))
{
    $componentId = $_REQUEST['componentId'];
}
else
{
    $componentId = 'all';
}

//Error redirections
if( is_null($pageId) ) 
{
    header("Location: ./../../index.php");
    exit();
}
else
{
    $page = new Page();

    if( !$page->load($pageId) )
    {
        // required
        header("Location: ./../../index.php");
        exit();
    }
}

//Set S5 properties    
$pageTitle = $page->getTitle();
$s5Date = claro_date("Y-m-d");

//Set slides or slide preview xhtml S5 code    
$componentList = $page->getComponentList();
$slides = '';
if ($componentId == 'all') //For all slides
{
    foreach( $componentList as $component )
    {
        $slides .= $component->s5RenderBlock();
    }
}
else  //For only one slide preview
{
    foreach( $componentList as $component )
    {
        if ($componentId == $component->getId())
        {
            $slides .= $component->s5RenderBlock();
        }
    }
}

//S5 first part xhtml code
$head =
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'. "\n" .

'<html xmlns="http://www.w3.org/1999/xhtml">'. "\n" .

    '<head>'. "\n" . 
        '<title>S5 presentation</title>'. "\n" .
        
        '<!-- metadata -->'. "\n" .
        '<meta name="generator" content="S5" />'. "\n" .
        '<meta name="version" content="S5 1.1" />'. "\n" .
        '<meta name="presdate" content="20050728" />'. "\n" .
        '<meta name="author" content="Eric A. Meyer" />'. "\n" .
        '<meta name="company" content="Complex Spiral Consulting" />'. "\n" .
            
        '<!-- configuration parameters -->'. "\n" .
        '<meta name="defaultView" content="slideshow" />'. "\n" .
        '<meta name="controlVis" content="hidden" />'. "\n" .

        '<!-- style sheet links -->'. "\n" .
        '<link rel="stylesheet" href="ui/default/slides.css" type="text/css" media="projection" id="slideProj" />'. "\n" .
        '<link rel="stylesheet" href="ui/default/outline.css" type="text/css" media="screen" id="outlineStyle" />'. "\n" .
        '<link rel="stylesheet" href="ui/default/print.css" type="text/css" media="print" id="slidePrint" />'. "\n" .
        '<link rel="stylesheet" href="ui/default/opera.css" type="text/css" media="projection" id="operaFix" />'. "\n" .

        '<!-- S5 JS -->'. "\n" .
        '<script src="ui/default/slides.js" type="text/javascript"></script>'. "\n" .
        '</head>'. "\n" ;

//S5 layout xhtml code        
$body =
    '<body>'. "\n" .
        '<div class="layout">'. "\n" .
            '<div id="controls"></div>'. "\n" .
            '<div id="currentSlide"></div>'. "\n" .
            '<div id="header">'. "\n" .
            '</div>'. "\n" .
            '<div id="footer">'. "\n" .
                '<h1>'.$s5Date.'</h1>'. "\n" .
                '<h2>'.$pageTitle.'</h2>'. "\n" .
            '</div>'. "\n" .
        '</div>'. "\n" .
        
        '<div class="presentation">'. "\n" ;

//S5 slide 0 xhtml code
$slide0 =
    '<div class="slide">'. "\n" .
        '<h1>'.$pageTitle.'</h1>'. "\n" .
        '<h2>Claroline S5 viewer</h2>'. "\n" .
    '</div>'. "\n" ;

//S5 xhtml last part code
$end =
        '</div>'. "\n" .
    '</body>'. "\n" .
'</html>'. "\n" 
;

//S5 xhtml parts concatenation
$out = $head . $body ;
if($componentId == 'all')
{
    $out .= $slide0;
}
$out .= $slides . $end;

echo($out);
