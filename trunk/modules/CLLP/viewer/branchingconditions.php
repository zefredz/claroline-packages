<?php

$tlabelReq = 'CLLP';

require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_tool_allowed() )
{
    if ( claro_is_in_a_course() )
    {
        claro_die( get_lang( "Not allowed" ) );
    }
    else
    {
        claro_disp_auth_form( true );
    }
}

/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/item.class.php';
require_once dirname( __FILE__ ) . '/../lib/attempt.class.php';

require_once dirname( __FILE__ ) . '/../lib/scormInterface.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm12.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm13.lib.php';


$dialogBox = new DialogBox();

if(isset($_REQUEST['pathId'])) $pathId = (int) $_REQUEST['pathId']; else $pathId = null;
if(isset($_REQUEST['itemId'])) $itemId = (int) $_REQUEST['itemId']; else $itemId = null;

$path = new path();

if( ! $path->load( $pathId ) )
{
  claro_die(get_lang('Not allowed'));
}

$content = '';

if( is_null($pathId) || is_null($itemId) || !(isset($_SESSION['branchConditions']) && is_array($_SESSION['branchConditions']) )  )
{
  $dialogBox->error( get_lang('Not allowed') );
}
else
{
  if( $path->getVersion() == 'scorm12' )
  {
    $scormAPI = new Scorm12();
  }
  else
  {
  
    $scormAPI = new Scorm13();
  }
  
  $jsloader = JavascriptLoader::getInstance();
  $jsloader->load('jquery');
  $jsloader->load('jquery.json');
      
  $jsloader->load('CLLP');
  $jsloader->load('scormtime');
  $jsloader->load('claroline');
  
  $jsloader->load($scormAPI->getApiFileName());
  
  $htmlHeaders = "\n"
  .    '<script type="text/javascript">' . "\n"
  
  .	 '  var pathId = "'.(int) $pathId.'";' . "\n"
  .	 '  var cidReq = "'.claro_get_current_course_id().'";' . "\n"
  .	 '  var moduleUrl = "'.get_module_url('CLLP').'/";' . "\n"
  .  '  var debugMode = '.get_conf('scorm_api_debug').';' . "\n\n"  
  .	 '  var lpHandler = this.top.lpHandler;' . "\n"
  .	 '  var lp_top = this.top;' . "\n"
  .    '</script>' . "\n\n";
  
  $claroline->display->header->addHtmlHeader($htmlHeaders);
  
  $item = new item();
  $attempt = new attempt(); 
  
  if( ! $item->load( $itemId ) )
  {
    $dialogBox->error( get_lang('Unable to load item') );
  }
  elseif( ! $attempt->load( $pathId, claro_get_current_user_id()))
  {
    $dialogBox->error( get_lang('Unable to load attempt') );
  }
  else
  {
    $itemAttempt = new itemAttempt();
    if( ! $itemAttempt->load( $attempt->getId(), $itemId ) )
    {
      $dialogBox->error( get_lang('Unable to load item attempt') );
    }
    else
    {
      $content .= '<div style="font-weight: bold;">'.get_lang( 'Your score for this module is %score.', array('%score' => $itemAttempt->getScoreRaw() ) ).'</div>' . "\n"
      .   '<div>'. get_lang('Regarding your score, we propose you to choose the coresponding next module :') .'</div>' . "\n"      
      ;
      foreach($_SESSION['branchConditions'] as $sign => $branchConditions)
      {
        $bcContent = '';      
        switch( $sign )
        {
          case 0 :
          {
            $bcContent = '<div>'.get_lang('Your score is equal to :').'</div>';
          }
          break;
          case 1 :
          {
            $bcContent = '<div>'.get_lang('Your score is upper than :').'</div>';
          }
          break;
          case 2 :
          {
            $bcContent = '<div>'.get_lang('Your score is upper or equal to :').'</div>';
          }
          break;
          case 3 :
          {
            $bcContent = '<div>'.get_lang('Your score is lower or equal to :').'</div>';
          }
          break;
          case 4 :
          {
            $bcContent = '<div>'.get_lang('Your score is lower than :').'</div>';
          }
          break;
        }
        $bcContent .= '<ul>' . "\n";
        foreach( $branchConditions as $score => $bcItemId )
        {
          $thisItem = new item();
          if( $thisItem->load( $bcItemId ) )
          {
            $bcContent .= '<li>' . $score.'% : <a href="#" onclick="lpHandler.setContent('. $thisItem->getId().');">' .   $thisItem->getTitle() . '</a></li>';
          }          
        }
        $bcContent .= '</ul>' . "\n";
        $content .= $bcContent;
      }      
    }    
  }
}

$out = '';
$out .= $dialogBox->render()
.       $content;

$claroline->setDisplayType( Claroline::FRAME );
$claroline->display->body->appendContent($out);

echo $claroline->display->render();
