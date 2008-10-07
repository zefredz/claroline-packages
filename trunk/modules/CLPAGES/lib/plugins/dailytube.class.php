<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );

/**
* CLAROLINE
*
* $Revision$
*
* @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
*
* @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
*
* @package CLPAGES
*
* @author Claroline team <info@claroline.net>
*
*/
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class DailyTubeComponent extends Component
{

private $videoUrl= '';
private $videoId='';

//default
private $videoInputType = 'url';
private $videoSize = 'small';
private $videoIdType = 'YouTube';

public function render()
{
    $inputError = false;
    $inputType = $this->videoInputType;
    $idType = $this->videoIdType;
    $videoSize = $this->videoSize;
    
    //default = small
    $width = 380;
    $height = 320;

    //input reception
    $input = '';
    if('url' == $inputType)
    {
        $input = $this->videoUrl;
    }
    else if ('id' == $inputType)
    {
        $input = $this->videoId;
    }

    //size dimensions
    if('medium' == $videoSize)
    {
        $width = 550;
        $height = 460;
    }
    if('large' == $videoSize)
    {
        $width = 750;
        $height = 625;
    }

    
    if ($input != null)
    {
        if('url' == $inputType)
        {
            if($this->isYouTubeUrl($input))
            {
                return $this->setYouTubePlayer($input,'url',$width, $height);
            }
            else if($this->isDailyMotionUrl($input))
            {
                $urlType = $this->defineUrlType($input);
                return $this->setDailyMotionPlayer($input,$urlType,$width,$height);
            }
            else
            {
                $inputError = true;
                $message = get_lang('Not a correct video address');
            }
        }
        else if ('id' == $inputType)
        {
            if('YouTube' == $idType)
            {
                return $this->setYouTubePlayer($input,'id',$width, $height);
            }
            else if ('DailyMotion' == $idType)
            {
                return $this->setDailyMotionPlayer($input,'id',$width, $height);
            }
        }
        if($inputError)
        {
            $dialogBox = new DialogBox();
            $dialogBox->error($message);
            return '<center>' . "\n"
                    .     $dialogBox->render(). "\n"
                    .     '</center>' . "\n";
        }
    }
}

 /**
 * Check if input equals correct and active YouTube url
 */

private function isYouTubeUrl($url)
{
    if (preg_match('/youtube.com\/watch\?v=/',$url))
    {
        return true;
    }
    else
    {   
        return false;
    }
}

 /**
 * Check if input equals correct and active DailyMotion url 
 */

private function isDailyMotionUrl($url)
{
    if (preg_match('/dailymotion.com/',$url))
    {
        return true;
    }
    else
    {   
        return false;
    } 
}

 /**
 * Check if input equals correct and active DailyMotion url 
 */

private function defineUrlType($url)
{
    if (preg_match('/swf/',$url))
    {
        return 'videoUrl';
    }
    else if (preg_match('/video/',$url))
    {   
        return 'webPageUrl';
    }
    else
    {
        return 'error';
        $error = true;
        $message = get_lang('Not a correct video address');
    }
}

 /**
 * Set YouTube web player from YouTube url or id
 */

private function setYouTubePlayer($input,$type,$width,$height)
{
    $id='';
    if('id' == $type)
    {
        $id=$input;
    }
    else if('url' == $type)
    {
        $id = $input;
        $id = preg_replace('/(.*)youtube.com\/watch\?v=/','',$id);
    }

    return '<center>' . "\n"
        .     '<object width="'.$width.'" height="'.$height.'">' . "\n"
        .     '<param name="movie" value="http://www.youtube.com/v/'.htmlspecialchars($id).'&rel=1"></param>' . "\n"
        .     '<param name="wmode" value="transparent"></param>' . "\n"
        .     '<embed src="http://www.youtube.com/v/'.$id.'&rel=0" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed>' . "\n"
        .     '</object>' . "\n"
        .     '</center>' . "\n";

}

/**
 * Set DailyMotion web player from correct DailyMotion id, webPage url or video url
 */

private function setDailyMotionPlayer($input, $type,$width,$height)
{
    $id ='';
    if ('id' == $type)
    {
        $id = $input;
    }
    else if ('videoUrl' == $type)
    {
        $id = $input;
        $id = preg_replace('/(.*)www.dailymotion.com\/swf\//','',$id);
        $id = preg_replace('/&(.*)/','',$id);
    }
    else if ('webPageUrl' == $type)
    {
        $id = $input;
        $id = preg_replace('/(.*)www.dailymotion.com\/(.*)\/video\//','',$id);
        $id = preg_replace('/_(.*)/','',$id);
    }                      
    return '<center>' . "\n"
        .     '<object width="'.$width.'" height="'.$height.'">' . "\n"
        .    '<param name="movie" value="http://www.dailymotion.com/swf/'.htmlspecialchars($id).'&related=1"></param>' . "\n"
        .    '<param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param>' . "\n" //allowScriptAccess="always"
        .    '<embed src= "http://www.dailymotion.com/swf/'.$id.'&related=0" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'" allowFullScreen="true"></embed>' . "\n"
        .    '</object>' . "\n"
        .    '</center>' . "\n";
}

public function editor()
{
    return

        '<script type="text/javascript">
        
            function disableId()
            {
                $("#videoUrl_'.$this->getId().'").removeAttr("disabled"); 
                $("#videoId_'.$this->getId().'").attr("disabled","disabled");
                $("#videoIdType_'.$this->getId().'").attr("disabled","disabled");
            }
            function disableUrl()
            {
                $("#videoId_'.$this->getId().'").removeAttr("disabled");
                $("#videoIdType_'.$this->getId().'").removeAttr("disabled");
                $("#videoUrl_'.$this->getId().'").attr("disabled","disabled");
            }
        
        </script>' . "\n"
          
    .     '<label for="videoRef">' . get_lang('Video reference') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    .     '<input type ="radio" name="videoInputType_'.$this->getId().'" id="videoInputUrl_'.$this->getId().'" value="url" '.$this->check($this->videoInputType,'url').' onchange="disableId();"><label for="videoInputUrl_'.$this->getId().'">'. get_lang('Video internet address') . '</label>' . "\n"
    .     '<input type="text" name="videoUrl_'.$this->getId().'" id="videoUrl_'.$this->getId().'" maxlength="255" value=" '.htmlspecialchars($this->videoUrl).'" '.$this->initEnable($this->videoInputType,'id').'/><br /><br />' . "\n"
    .     '<input type ="radio" name="videoInputType_'.$this->getId().'" id="videoInputId_'.$this->getId().'" value="id" '.$this->check($this->videoInputType,'id'). ' onchange="disableUrl();"><label for="videoInputId_'.$this->getId().'">'. get_lang('Video identification') . '</label>' . "\n"
    .     '<input type="text" name="videoId_'.$this->getId().'" id="videoId_'.$this->getId().'" maxlength="255" value=" '.htmlspecialchars($this->videoId).'" '.$this->initEnable($this->videoInputType,'url').'/>' . "\n"
    .     '<select name="videoIdType_'.$this->getId().'" id="videoIdType_'.$this->getId().'" size="1" '.$this->initEnable($this->videoInputType,'url').'>
              <option value="YouTube" selected>YouTube</option>
              <option value="DailyMotion">DailyMotion</option>
           </select> <br /><br />' . "\n"
    .     '<label for="videoSize">' . get_lang('Video size') . '<br />' . "\n"
    .     '<input type ="radio" name="videoSize_'.$this->getId().'" id="videoSizeS_'.$this->getId().'" value="small" '.$this->check($this->videoSize,'small').'><label for="videoSizeS_'.$this->getId().'">'. get_lang('Small') . '</label><br/>' . "\n"
    .     '<input type ="radio" name="videoSize_'.$this->getId().'" id="videoSizeM_'.$this->getId().'" value="medium" '.$this->check($this->videoSize,'medium').'><label for="videoSizeM_'.$this->getId().'">'. get_lang('Medium') . '</label><br/>' . "\n"
    .     '<input type ="radio" name="videoSize_'.$this->getId().'" id="videoSizeL_'.$this->getId().'" value="large" '.$this->check($this->videoSize,'large').'><label for="videoSizeL_'.$this->getId().'">'. get_lang('Large') . '</label><br/>' . "\n";
}

private function check($var,$value)
{
    if($var == $value)
    {
        return 'checked="checked"';
    } 
}

private function initEnable($var,$value)
{
    if($var == $value)
    {
        return 'disabled="disabled"';
    } 
}

/**
 * @see Component
 */
public function getEditorData()
{
    $this->videoUrl = $this->getFromRequest('videoUrl_'.$this->getId());
    $this->videoId = $this->getFromRequest('videoId_'.$this->getId());
    $this->videoInputType = $this->getFromRequest('videoInputType_'.$this->getId());
    $this->videoIdType = $this->getFromRequest('videoIdType_'.$this->getId());
    $this->videoSize = $this->getFromRequest('videoSize_'.$this->getId());
}

/**
 * @see Component
 */
public function setData( $data )
{
    $this->videoUrl = trim($data['videoUrl']);
    $this->videoId = trim($data['videoId']);
    $this->videoInputType = $data['videoInputType'];
    $this->videoIdType = $data['videoIdType'];
    $this->videoSize = $data['videoSize'];
}

/**
 * @see Component
 */
public function getData()
{
    return array('videoId' => $this->videoId,'videoUrl' => $this->videoUrl,'videoInputType' => $this->videoInputType, 'videoIdType' => $this->videoIdType, 'videoSize' => $this->videoSize);
}

}

PluginRegistry::register('dailytube',get_lang('YouTube and DailyMotion video'),'DailyTubeComponent', 'Externals', 'youtubeIco');
?>