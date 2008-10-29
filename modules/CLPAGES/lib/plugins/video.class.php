<?php // $Id$

if ( count( get_included_files() ) == 1 ) die( basename(__FILE__) );

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2008 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 *
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
    
     require_once dirname( __FILE__ ) . '/../videoRegistry.lib.php';
    
    
class VideoComponent extends Component
{
    //Properties
    private $availableVideos;
    private $video;
    private $videosData;
    private $videoInput;
    private $videoType;
    private $videoInputType;
    private $videoParameters;
    private $defaultValues;
    private $htmlData;
    private $idList;
    
    function VideoComponent ()
    {
        //set default properties
        
        //Enable the automatic video type research
        $this->videoType = 'automatic';
        
        //Create a video oject and video parameters list from the available video class
        $this->availableVideos = array();
        $this->RegisterVideosType();
        
        //Set the video form input content as empty
        $this->videoInput = '';
        
        //Set the  videos data property as an empty array
        $this->videosData = array();
        $this->htmlData = array();
        
        //Set the  video parameters property as an empty array
        $this->videoParameters = array();

        //
        $this->idList = array();
    }
//VVVVVVVVVVVVVVVV
    /*
    Register all the available video class and fill the availableVideos property
    ex: $availableVideos = ['youtube' => ['displayName' => 'YouTube', 'videoObject' => YouTubeComponent Object], ...]
     */
    public function RegisterVideosType()
    {
        $videoRegistry = videoRegistry::getInstance();
        $videosList = $videoRegistry->getList();
        $videos = array();
        foreach( $videosList as $type => $details )
        {
            $videos[$details['category']][$type] = $details;
        }
        foreach( $videos as $category => $categoryVideos )
        {
            foreach( $categoryVideos as $type => $videoDetails )
            {
                $videoObject = new $videoDetails['className']();
                $data = array('displayName' => $videoDetails['displayName'],'videoObject' => $videoObject);
                $this->availableVideos[$type]= $data;
                $this->idList[$type] = array('references' => array(), 'parameters' => array());
            }
        }
    }
//VVVVVVVVVVVVVVVV    
    public function setDefaultValues()
    {
        
        $references = array();
        $references['automatic'] = 'Url';
        $parameters = array();
        $parameters['automatic'] = array( 'Size' =>'small');
        $this->defaultValues = array('references' => $references, 'parameters' => $parameters);
        
        foreach($this->availableVideos as $type => $data)
        {
            $videoObject = $data['videoObject'];
            $references = $videoObject->getReferences();
            $this->defaultValues['references'][$type]=$references['default'];
            $parameters = $videoObject->getParameters();
            foreach($parameters as $name => $data)
            {
                $this->defaultValues['parameters'][$type][$name]=$data['default'];
            }
        }
    }
//VVVVVVVVVVVVVVVV
    /*
    Create the video form HTML code
    */
    public function render()
    {
        //Recup the VideoComponent property values
        $input = $this->videoInput;
        $inputType = $this->videoInputType;
        $video = $this->video;
        
        if ($input != null && isset($video))
        {
            $video->setData(array('input' => $input, 'inputType' => $inputType , 'parameters' => $this->videoParameters));
            //Call the specific videoplayer html code of the selected video type
            return $this->video->setPlayer();
        }
    }
//VVVVVVVVVVVVVVVV
    /*
    Find the video the type from the input url reference and set the video property with the found video object
    */ 
    public function defineVideoType($url)
    {
        foreach( $this->availableVideos as $type => $videoData)
        {
            $videoTest = $videoData['videoObject'];
            if($videoTest->isValidUrl($url))
            {
                $this->videoType = $type;
                $this->video = $videoTest;
                return true;
            }
        }
        return false;
    }
//VVVVVVVVVVVVVVVV
    /*
    set the video property with the video object associated to the input type
    */
    public function setVideo($videoType)
    {
        foreach( $this->availableVideos as $type => $data)
        {
            if($type == $videoType)
            {
                $this->video = $data['videoObject'];
            }
        }
    }
//VVVVVVVVVVVVVVVV    
    /*
    Test the input to define correspondance and validation with some available video
    Return true if inputs are valide and error message if the validation abord
    */
    public function validate()
    {
        if($this->videoType == 'automatic' && $this->videoInputType == 'Url')
        {
            if($this->defineVideoType($this->videoInput))
            {
                $this->video->setData(array('input' => $this->videoInput, 'inputType' => $this->videoInputType,'parameters' => $this->videoParameters));
            }
            else
            {
                $errorMessage = get_lang('Not a correct available video web address').' :  <br> '.htmlspecialchars($this->videoInput) ;
                return $this->renderErrorMessage($errorMessage);
            }
        }
        else
        {
        $this->setVideo($this->videoType);
        $this->video->setData(array('input' => $this->videoInput, 'inputType' => $this->videoInputType,'parameters' => $this->videoParameters));
        }
        $test = $this->video->validate();
        if('true' == $test)
        {
            return 'true';
        }
        else
        {
            return $this->renderErrorMessage($test);
        }
    }
//VVVVVVVVVVVVVVVV
    /*
    Fill the video type listbox with the available videos display name
    */
    public function getTypeOptions()
    {
        $options =
        '<option value="automatic" '.$this->select($this->videoType,'automatic'). '>'.get_lang('Automatic').'</option>'. "\n";
        foreach( $this->availableVideos as $type => $data)
        {
            $options.='<option value="'.$type.'" '.$this->select($this->videoType,$type). '>'.$data['displayName'].'</option>'. "\n";
        }
        return $options;
    }
//VVVVVVVVVVVVVVVV
    /*
    
    */
    public function setVideosData()
    {
        $videosData = array ();
        foreach( $this->availableVideos as $type => $data)
        {
            $references = $data['videoObject']->getReferences();
            $parameters = $data['videoObject']->getParameters();
            $videosData [$type] = array('references' => $references , 'parameters' => $parameters);
        }
        $this->videosData = $videosData;
    }
//VVVVVVVVVVVVVVVV    
    /*
    
    */
    public function setHtmlData()
    {
        $htmlData = array();
        $script_js = 'var data = new Array();';
        
        //set automatic reference (url) and property (size)
        $automaticRef = array('Url' => 'Video Internet Adress');
        $references = array('default' => 'Url', 'references' => $automaticRef);
        $size = array('type' => 'radio', 'display' => get_lang('Size'),'default' => 'medium','data' => array('small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'));
        $parameters = array('Size' => $size);
        $htmlReferences = $this->getReferences($references,'automatic');
        $htmlParameters = $this->getParameters($parameters,'automatic');
        $htmlData ['automatic'] = array ('references' => $htmlReferences, 'parameters' => $htmlParameters);
        
        $script_js .= 'var videoData = new Array();';
        $script_js .= 'videoData[\'references\'] =\''.$htmlReferences.'\';';
        $script_js .= 'videoData[\'parameters\'] =\''.$htmlParameters.'\';';
        $script_js .= 'data["automatic"] = videoData;';

        //set video plugin references and  property
        foreach( $this->videosData as $type => $data)
        {
            $references = $data['references'];
            $parameters = $data['parameters'];
            $htmlReferences = $this->getReferences($references,$type);
            $htmlParameters = $this->getParameters($parameters,$type);
            $htmlData [$type] = array ('references' => $htmlReferences, 'parameters' => $htmlParameters);
            
            $script_js .= 'videoData = new Array();';
            $script_js .= 'videoData[\'references\'] =\''.$htmlReferences.'\';';
            $script_js .= 'videoData[\'parameters\'] =\''.$htmlParameters.'\';';
            $script_js .= 'data["'.$type.'"] = videoData;';
        }
        $this->htmlData = $htmlData;
        return $script_js;
    }
//VVVVVVVVVVVVVVVV

    /*
    
    */
    public function setJSdata()
    {
        $script_js = 'var temp = new Array();';
        foreach($this->idList as $videoType => $data)
        {
            $script_js .= 'temp["'.$videoType.'"] = new Array();';
            $script_js .= 'temp["'.$videoType.'"]["references"] = new Array();';
            foreach($data['references'] as $nom => $info)
            {
                $script_js .= 'temp["'.$videoType.'"]["references"]["'.$nom.'"] = new Array();';
                $script_js .= 'temp["'.$videoType.'"]["references"]["'.$nom.'"]["type"] = "'.$info['type'].'";';
                $script_js .= 'temp["'.$videoType.'"]["references"]["'.$nom.'"]["id"] = "'.$info['id'].'";';
                $script_js .= 'temp["'.$videoType.'"]["references"]["'.$nom.'"]["value"] = "'.$info['default'].'";';
            }
            $script_js .= 'temp["'.$videoType.'"]["parameters"] = new Array();';
            foreach($data['parameters'] as $nom => $info)
            {   
                $script_js .= 'temp["'.$videoType.'"]["parameters"]["'.$nom.'"] = new Array();';
                $script_js .= 'temp["'.$videoType.'"]["parameters"]["'.$nom.'"]["type"] = "'.$info['type'].'";';
                $script_js .= 'temp["'.$videoType.'"]["parameters"]["'.$nom.'"]["id"] = "'.$info['id'].'";';
                $script_js .= 'temp["'.$videoType.'"]["parameters"]["'.$nom.'"]["value"] = "'.$info['default'].'";';
            }
        }
        return $script_js;
    }
    /*
    
    */
    public function getReferences($data,$videoType)
    {
        $htmlCode = '';
        $type ='radio';
        
        $references = $data['references'];
        $default = $data['default'];
    
        if(count($references)==1)
        {
            $type = 'hidden';
        }
        foreach( $references as $name => $displayName)
        {
            $htmlCode .= $this->CreateReferenceInput ($type, $name, $displayName,$default,$videoType);
        }
        return $htmlCode;
    }
//VVVVVVVVVVVVVVVV    
    public function getParameters($parameters,$videoType)
    {
        $htmlCode ='';
        foreach($parameters as $name => $property)
        {
            $type = $property['type'];
            $display = $property['display'];
            $data = $property['data'];
            $default = $property['default'];
            $htmlCode .= $this->createParameter($type,$name,$display,$default,$data,$videoType);
        }
        return $htmlCode;
    }
//VVVVVVVVVVVVVVVV    
    /*
    Create the $type reference input form HTML code
    */
    public function createReferenceInput ($type, $name, $display,$default,$videoType)
    {
        if($name == $default)
        {
            $defaultValue = "checked";
        }
        else
        {
            $defaultValue = "";
        }    
        $this->idList[$videoType]['references']['radio'.$name]= array('type'=>'radio','id' =>'#videoInput'.$name.'_'.$this->getId(),'default' => $defaultValue);
        $this->idList[$videoType]['references']['text'.$name]= array('type'=>'textBox','id' =>'#video'.$name.'_'.$this->getId(), 'default' => '');
        $reference=
                '<input type ="'.$type.'" name="videoInputType_'.$this->getId().'" id="videoInput'.$name.'_'.$this->getId().'" value="'.$name.'" '.$this->checkReference($videoType,$name).'>'
        .       '<label for="videoInput'.$name.'_'.$this->getId().'">'. get_lang($display). '</label>'
        .       '<input type="text" name="video'.$name.'_'.$this->getId().'" id="video'.$name.'_'.$this->getId().'" maxlength="255" '.$this->setReferenceValue($this->videoInputType,$name).'/><br /><br />';   
        return $reference;
    }
//VVVVVVVVVVVVVVVV    
    /*
    Create parameter input HTML code
    */
    public function createParameter($type,$name,$display,$default,$data,$videoType)
    {
        $param ='';
        if($type=='radio')
        {
            $param .=
                '<div class ="parameter'.$name.'_'.$this->getId().'">'
            .   '<label for="video'.$name.'_'.$this->getId().'">'.get_lang($display).'<br />';
            
            foreach( $data as $radioValue => $radioDisplay)
            {
                if($radioValue == $default)
                {
                    $defaultValue = "checked";
                }
                else
                {
                    $defaultValue = "";
                }    
                $this->idList[$videoType]['parameters'][$name.'_'.$radioValue]= array('type'=>'radio','id' =>'#video'.$name.'_'.$radioValue.'_'.$this->getId(), 'default' => $defaultValue);
                $param .=
                '<input type ="radio" name="video'.$name.'_'.$this->getId().'" id="video'.$name.'_'.$radioValue.'_'.$this->getId().'" value="'.$radioValue.'" '.$this->checkParameter($videoType,$name,$radioValue).'>'
            .   '<label for="video'.$name.'_'.$radioValue.'_'.$this->getId().'">'.get_lang($radioDisplay).'</label><br />';
            }
            $param .= '</div><br/>';
            return $param;
        }
        else if($type =='select')
        {
            $param .=
                '<div class ="parameter'.$name.'_'.$this->getId().'">'
            .   '<label for="video'.$name.'_'.$this->getId().'">'.get_lang($display).'<br />'
            .   '<select name="video'.$name.'_'.$this->getId().'" id="video'.$name.'_'.$this->getId().'" size="1">';
            foreach( $data as $selectValue => $selectDisplay)
            {
                if($selectValue == $default)
                {
                    $defaultValue = "selected";
                }
                else
                {
                    $defaultValue = "";
                }    
                $this->idList[$videoType]['parameters']['select'.$name.$selectValue]= array('type'=>'select','id' =>'#video'.$name.'_'.$selectValue.'_'.$this->getId(), 'default' => $defaultValue);
                $param .=
                '<option value="'.$selectValue.'" id="video'.$name.'_'.$selectValue.'_'.$this->getId().'"' .$this->selectParameter($videoType,$name,$selectValue).'>'.get_lang($selectDisplay).'</option>';
            }
            $param .=
                '</select></div><br />';
           return $param;
        }
        else if($type =='textBox')
        {
            $this->idList[$videoType]['parameters']['text'.$name]= array('type'=>'textBox','id' =>'#video'.$name.'_'.$this->getId(), 'default' => $default);
            $param.=
                '<div class ="parameter'.$name.'_'.$this->getId().'">'
            .   '<label for="video'.$name.'_'.$this->getId().'">'.get_lang($display).'</label>'
            .   '<input type="'.$data['type'].'" name="video'.$name.'_'.$this->getId().'" id="video'.$name.'_'.$this->getId().'" maxlength="255"  '.$this->setParameterValue($videoType,$name).'"/></div><br/>';
            return $param;
        }
    }
//VVVVVVVVVVVVVVVV //
    public function editor()
    {
            $this->setDefaultValues();
            $this->setVideosData();
            $editor=
            
            '<script type="text/javascript">
            
            '.$this->setHtmlData().'
            '.$this->setJSdata().'
                
                var lastType = "'.$this->videoType.'";
            
                 function setForm(id)
                {
                    saveTemp(lastType);
                    var divType = "#videoType_" + id;
                    var type = $(divType).val();
                    var divReferences = "#videoReferences_" + id;
                    $(divReferences).empty();
                    $(divReferences).append(data[type]["references"]);
                    var divParameters = "#videoParameters_" + id;
                    $(divParameters).empty();
                    $(divParameters).append(data[type]["parameters"]);
                    loadTemp(type);
                    lastType = type;
                }
                
                function saveTemp(type)
                {
                    for (var reference in temp[type]["references"])
                    {
                        var typeRef = temp[type]["references"][reference]["type"];
                        var id = temp[type]["references"][reference]["id"];
                        var value = temp[type]["references"][reference]["value"];
                        
                        if (typeRef == "radio" )
                        {
                            if($(id).attr("checked"))
                            {
                                temp[type]["references"][reference]["value"] = "checked";
                            }
                            else
                            {
                                temp[type]["references"][reference]["value"] = "";
                            }
                        }
                        
                        if (typeRef == "textBox" )
                        {
                            temp[type]["references"][reference]["value"] = $(id).attr("value");
                        }
                        
                        
                    }
                    
                    for (var parameter in temp[type]["parameters"])
                    {
                        var typeParam = temp[type]["parameters"][parameter]["type"];
                        var id = temp[type]["parameters"][parameter]["id"];
                        var value = temp[type]["parameters"][parameter]["value"];
                        
                        if (typeParam == "radio" )
                        {
                            if($(id).attr("checked"))
                            {
                                temp[type]["parameters"][parameter]["value"] = "checked";
                            }
                            else
                            {
                                temp[type]["parameters"][parameter]["value"] = "";
                            }
                        }
                        
                        if (typeParam == "select" )
                        {
                            
                            if($(id).attr("selected"))
                            {
                                temp[type]["parameters"][parameter]["value"] = "selected";
                            }
                            else
                            {
                                temp[type]["parameters"][parameter]["value"] = "";
                            }
                        }
                        
                        if (typeParam == "textBox" )
                        {
                            temp[type]["parameters"][parameter]["value"] = $(id).attr("value");
                        }
                        
                    }
                }

                
                function loadTemp(type)
                {
                    for (var reference in temp[type]["references"])
                    {
                        var typeRef = temp[type]["references"][reference]["type"];
                        var id = temp[type]["references"][reference]["id"];
                        var value = temp[type]["references"][reference]["value"];
                        
                        if (typeRef == "radio" )
                        {
                            if(value == "checked")
                            {
                                $(id).attr("checked","checked");
                            }
                            else
                            {
                                $(id).removeAttr("checked");
                            }
                        }
                        if (typeRef == "textBox" )
                        {
                            $(id).val(value);
                        }
                    }

                    for (var parameter in temp[type]["parameters"])
                    {
                        var typeParam = temp[type]["parameters"][parameter]["type"];
                        var id = temp[type]["parameters"][parameter]["id"];
                        var value = temp[type]["parameters"][parameter]["value"];

                        if (typeParam == "radio" )
                        {
                            if(value == "checked")
                            {
                                $(id).attr("checked","checked");
                            }
                            else
                            {
                                $(id).removeAttr("checked");
                            }
                        }
                        if (typeParam == "select" )
                        {
                            if(value == "selected")
                            {
                                $(id).attr("selected","selected");
                            }
                            else
                            {
                                $(id).removeAttr("selected");
                            }
                        }
                        if (typeParam == "textBox" )
                        {
                            $(id).val(value);
                        }
                    }
                }
            </script>' . "\n"
            
        
        //Video type list box
        .    '<label for="videoType">' . get_lang('Video type') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .    '<select name="videoType_'.$this->getId().'" id="videoType_'.$this->getId().'" size="1" onChange="setForm('.$this->getId().');">'. "\n"
        .    $this->getTypeOptions()           
        .    '</select><br /><br />'. "\n"
        
        //Video reference input and radio box
        .    '<label for="videoRef">' . get_lang('Video reference') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        .    '<div class="videoReferences" id="videoReferences_'.$this->getId().'">';
        //Insert reference html code depends video type
        //Default = automatic reference
        $editor .=
        
            $this->htmlData[$this->videoType]['references']
             .'</div>'. "\n"
        
        //Video parameters inputs
        .    '<label>' . get_lang('Video parameters') . '</label><br />' . "\n"
        .    '<div class ="videoParameters" id="videoParameters_'.$this->getId().'">';
        //Insert parameter html code depends video type
            $editor .=
            
            $this->htmlData[$this->videoType]['parameters']
            .'</div>' ."\n";
        return $editor;
    }
//VVVVVVVVVVVVVVVV    
    /**
     * @set the checked status if inputs equal
     */
    private function checkReference($videoType,$inputType)
    {
        if($videoType != 'automatic')
        {
            $video = $this->availableVideos[$videoType]['videoObject'];
            $data = $video->getReferences();
            $references = $data['references'];
        
            if(isset($this->videoInputType) && isset($references[$this->videoInputType]))
            {
                if($this->videoInputType == $inputType)
                {
                return 'checked="checked"';
                }
            }
            else
            {
                if($this->defaultValues['references'][$videoType] == $inputType )
                {
                    return 'checked="checked"';
                }
            }
        }
    }
//VVVVVVVVVVVVVVVV    
    /**
     * @set the checked status if inputs equal
     */
    private function checkParameter($videoType,$name,$value)
    { 
        if(isset($this->videoParameters[$name]))
        {
            if($this->videoParameters[$name] == $value)
            {
            return 'checked="checked"';
            }
        }
        else
        {
            if($this->defaultValues['parameters'][$videoType][$name] == $value )
            {
                return 'checked="checked"';
            }
        }
    }   
//VVVVVVVVVVVVVVVV        
    /**
     * @set the selected status if inputs equal
     */
     private function selectParameter($videoType,$name,$selectedValue)
    {
        if(isset($this->videoParameters[$name]))
        {
            if($this->videoParameters[$name] == $selectedValue)
            {
            return 'selected="selected"';
            }
        }
        else
        {
            if($this->defaultValues['parameters'][$videoType][$name] == $selectedValue )
            {
                return 'selected="selected"';
            }
        }
    }
    
//VVVVVVVVVVVVVVVV    
    private function setReferenceValue($var,$value)
    {
        if($var == $value)
        {
            return 'value="'.htmlspecialchars($this->videoInput).'"';
        }
        else
        {
            return 'value=""';
        }
    }
//VVVVVVVVVVVVVVVV    
    private function setParameterValue($videoType,$name)
    {
        if(isset($this->videoParameters[$name]))
        {
            return 'value="'.htmlspecialchars($this->videoParameters[$name]).'"';
        }
        else
        {
            return 'value="'.htmlspecialchars($this->defaultValues['parameters'][$videoType][$name]).'"';
            
        }
    }
    
//VVVVVVVVVVVVVVVV        
    /**
     * @set the selected status if inputs equal
     */
     private function select($var,$selectedValue)
    {
        if($var == $selectedValue)
        {
            return 'selected="selected"';
        }
    }
//VVVVVVVVVVVVVVVV        
    /**
     * @set the disabled status if inputs equal
     */
    private function initEnable($var,$value)
    {
        if($var == $value)
        {
            return 'disabled="disabled"';
        } 
    }
//VVVVVVVVVVVVVVVV    
    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->videoInputType = $this->getFromRequest('videoInputType_'.$this->getId());
        $this->videoInput = $this->getFromRequest('video'.$this->videoInputType.'_'.$this->getId());
        $this->videoType = $this->getFromRequest('videoType_'.$this->getId());
        $this->getVideoParameters();
    }
//VVVVVVVVVVVVVVVV    
    public function getVideoParameters()
    {
        $videoParameters = array();
        $videoType = $this->videoType;
        if ($videoType == 'automatic')
        {
        $id = 'videoSize_'.$this->getId();
        $this->videoParameters['Size']= $this->getFromRequest($id);
        }
        
        foreach( $this->availableVideos as $type => $data)
        {
            if($videoType == $type)
            {
                $parameters = $data['videoObject']->getParameters();
                $videoParam = array();
                foreach($parameters as $name => $data)
                {
                    $id = 'video'.$name.'_'.$this->getId();
                    $this->videoParameters[$name]= $this->getFromRequest($id);
                }
            }
        }
    }
//VVVVVVVVVVVVVVVV    
    /**
     * @see Component
     */
    public function setData( $data )
    {
        $this->videoInput = trim($data['videoInput']);
        $this->videoInputType = $data['videoInputType'];
        $this->videoType = $data['videoType'];
        $this->video = $data['video'];
        $this->videoParameters = $data['parameters'];
        
    }
//VVVVVVVVVVVVVVVV    
    /**
     * @see Component
     */
    public function getData()
    {
        return array('videoInput' => $this->videoInput,'videoInputType' => $this->videoInputType, 'videoType' => $this->videoType, 'video' =>$this->video, 'parameters' =>$this->videoParameters);
    }
}

PluginRegistry::register('video',get_lang('Web video'),'VideoComponent', 'Externals', 'youtubeIco');
