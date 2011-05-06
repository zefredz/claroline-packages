<?php // $Id$

if ( count( get_included_files() ) == 1 ) die( basename(__FILE__) );

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 *
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

require_once dirname( __FILE__ ) . '/../videoPluginRegistry.lib.php';

$jsloader = JavascriptLoader::getInstance();
$jsloader->load('videoComponent');

class VideoComponent extends Component
{
    //Properties
    protected $videoType;
    protected $videoInput;
    protected $videoInputType;
    protected $videoParameterList;
    
    protected $videoRegister;
    
    protected $videoDataList;
    protected $videoDefaultValueList;
    protected $videoHtmlCodeList;
    
    protected $domIdList;
    
    /**
     * Constructor
     * Set properties default values
     * 
     */
    public function __construct()
    { 

        $this->videoType = 'automatic';
        $this->videoInput = '';
        $this->videoInputType = 'Url';
        $this->videoParameterList = array();
        
        $this->videoRegister = new videoPluginRegistry();
        
        $this->videoDataList = array();
        $this->videoDefaultValueList = array();
        $this->videoHtmlCodeList = array();
        
        $this->initDomIdList();
    }
    
    /**
     * Initiate the domIdList property structure based on the registered videos
     * 
     */
    protected function initDomIdList()
    {
        foreach ($this->videoRegister->getRegisteredVideoTypes() as $type)
        {
            $this->domIdList[$type] = array(
                                        'identifiers' => array(),
                                        'parameters' => array());
        }
    }

    /**
     * Create the videoComponent form HTML code (PHP and Javascript)
     *
     * @return string videoComponent form HTML code
     */
    public function editor()
    {
        $this->setVideoDefaultValueList();
        $this->setVideoDataList();
        $this->setVideoHtmlCodeList();

        $editor=
            
            '<script type="text/javascript">
            
                '.$this->setJSVideoHtmlCodeList().'
                '.$this->setJSVideoDataList().'
                
                var lastType = "'.$this->videoType.'";
                
                
            </script>' . "\n"
            
            .    '<label for="videoType_'.$this->getId().'">' . get_lang('Video type') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
            .    '<select name="videoType_'.$this->getId().'" id="videoType_'.$this->getId().'" size="1" onChange="setForm('.$this->getId().');">'. "\n"

            .        $this->getTypeOptions()           

            .    '</select><br /><br />'. "\n"
        
            .    '<label for="videoIdentifiers_'.$this->getId().'">' . get_lang('Video identifiers') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
            .    '<div class="videoIdentifiers" id="videoIdentifiers_'.$this->getId().'">'. "\n"

            .        $this->videoHtmlCodeList[$this->videoType]['identifiers']. "\n"
            
            .    '</div>'. "\n"
        
            .    '<label>' . get_lang('Video parameters') . '</label><br />' . "\n"
            .    '<div class ="videoParameters" id="videoParameters_'.$this->getId().'">'. "\n"
            
            .        $this->videoHtmlCodeList[$this->videoType]['parameters']. "\n"
            
            .    '</div>' ."\n";

        return $editor;
    }
    
    /**
     * Define the different available videos parameters and Identifiers default values
     * and save them in the videoDefaultValueList properties
     * videoDefaultValueList => ['identifiers' => ['youtube' => ['Url'],'DailyMotion' => ['Id'], ...],
     *                           'parameters' => ['youtube' => [['Size'] => 'medium', ...] ]
     */
    protected function setVideoDefaultValueList()
    {
        
        foreach($this->videoRegister->getRegisteredVideoTypes() as $videoType)
        {
            $video = $this->videoRegister->getVideoClassInstance( $videoType );
            $identifiers = $video->getIdentifiers();
            $this->videoDefaultValueList['identifiers'][$videoType]=$identifiers['default'];
            $parameters = $video->getParameters();
            
            foreach($parameters as $parameterName => $parameterData)
            {
                $this->videoDefaultValueList['parameters'][$videoType][$parameterName]=$parameterData['default'];
            }
            
        }
    }

    /**
     * Define the different available videos Identifiers and parameters
     * and save them in the videoDataList property
     * videoDataList = ['youtube' => ['identifiers' => ['Url' => 'Video Internet Adress',
     *                                                 'Id'=> 'Video identification']
     *                                'parameters' => ['type' => 'radio',
     *                                                 'display' => get_lang('Size'),
     *                                                 'default' => 'medium',
     *                                                 'data' => array('small' => 'Small',
     *                                                                 'medium' => 'Medium',
     *                                                                 'large' => 'Large') ] ], ... ]
     */
    protected function setVideoDataList()
    {

        foreach($this->videoRegister->getRegisteredVideoTypes() as $videoType)
        {
            $video = $this->videoRegister->getVideoClassInstance( $videoType );
            
            $this->videoDataList [$videoType] =
                array('identifiers' => $video->getIdentifiers(),
                      'parameters' => $video->getParameters());
        }
    }

    /*
     * Define the different available videos identifiers and parameters html codes
     * and save them in the videoHtmlCodeList property
     * videoHtmlCodeList is used to set specific video plugin form
     */
    protected function setVideoHtmlCodeList()
    {
        
        foreach( $this->videoDataList as $videoType => $videoData)
        {
            $this->videoHtmlCodeList [$videoType] =
                array ('identifiers' => $this->getIdentifiers($videoData['identifiers'],$videoType),
                       'parameters' => $this->getParameters($videoData['parameters'],$videoType));
        }
    }
    
    /**
     * Define the different available video identifiers and parameters html codes
     * and create a script sequence to enable them in javascript
     *
     * @return string The script sequence able to create the htmlCode Javascript variable
     */
    protected function setJSVideoHtmlCodeList()
    {
        $script_js = 'var videoHtmlCode = new Array();';

        foreach($this->videoDataList as $videoType => $videoData)
        {
            $htmlIdentifiersCode = $this->getIdentifiers($videoData['identifiers'],$videoType);
            $htmlParametersCode = $this->getParameters($videoData['parameters'],$videoType);
            
            $script_js .= 'videoData = new Array();';
            $script_js .= 'videoData[\'identifiers\'] =\''.$htmlIdentifiersCode.'\';';
            $script_js .= 'videoData[\'parameters\'] =\''.$htmlParametersCode.'\';';
            $script_js .= 'videoHtmlCode["'.$videoType.'"] = videoData;';
        }
        
        return $script_js;
    }
    
    /**
     * Define the different available videos identifiers and parameters types, id and values
     * and save them in the javascript videoDataList property
     * var videoDataList = ['youtube' => ['identifiers' => ['Url' => ['type' => radio,
     *                                                               'id' =>youtubeUrl_198,
     *                                                               'default' => ''],
     *                                                     'Id'  => ['type' => radio,
     *                                                               'id' => youtubeId_198,
     *                                                               'default' => ''],...]
     *                                    'parameters' => ['Size' => ['type' => inputBox,
     *                                                                'id' => Size_198,
     *                                                                'default' => 'medium'], ... ]]
     *
     * @return string The script sequence able to create the videoDataList Javascript variable
     */
    protected function setJSVideoDataList()
    {
        $script_js = 'var videoDataList = new Array();';
       
        foreach($this->domIdList as $videoType => $videoData)
        {
            $script_js .= 'videoDataList["'.$videoType.'"] = new Array();';
            $script_js .= 'videoDataList["'.$videoType.'"]["identifiers"] = new Array();';
            
            foreach($videoData['identifiers'] as $identifierName => $identifierData)
            {
                $script_js .= 'videoDataList["'.$videoType.'"]["identifiers"]["'.$identifierName.'"] = new Array();';
                $script_js .= 'videoDataList["'.$videoType.'"]["identifiers"]["'.$identifierName.'"]["type"] = "'.$identifierData['type'].'";';
                $script_js .= 'videoDataList["'.$videoType.'"]["identifiers"]["'.$identifierName.'"]["id"] = "'.$identifierData['id'].'";';
                $script_js .= 'videoDataList["'.$videoType.'"]["identifiers"]["'.$identifierName.'"]["value"] = "'.$identifierData['default'].'";';
            }
           
            $script_js .= 'videoDataList["'.$videoType.'"]["parameters"] = new Array();';
            
            foreach($videoData['parameters'] as $parameterName => $parameterData)
            {   
                $script_js .= 'videoDataList["'.$videoType.'"]["parameters"]["'.$parameterName.'"] = new Array();';
                $script_js .= 'videoDataList["'.$videoType.'"]["parameters"]["'.$parameterName.'"]["type"] = "'.$parameterData['type'].'";';
                $script_js .= 'videoDataList["'.$videoType.'"]["parameters"]["'.$parameterName.'"]["id"] = "'.$parameterData['id'].'";';
                $script_js .= 'videoDataList["'.$videoType.'"]["parameters"]["'.$parameterName.'"]["value"] = "'.$parameterData['default'].'";';
            }
        
        }
        
        return $script_js;
    } 
    
    /**
     * Create and return the specific "$videoType" video identifiers html codes from the "$videoData"
     *
     * @param array $videoIdentifiers The specified video type identifiers list
     * @param string $videoType A video type
     * @return string The specific video identifiers html code
     */
    protected function getIdentifiers($videoIdentifiers,$videoType)
    {
        $htmlCode = '';
        $identifierInputType ='radio';
        
        $identifiers = $videoIdentifiers['identifiers'];
        $defaultIdentifier = $videoIdentifiers['default'];
    
        //To hide the radio button if only one identifier choice exist
        if(count($identifiers)==1)
        {
            $identifierInputType = 'hidden';
        }
        
        foreach( $identifiers as $identiferName => $identifierDisplayName)
        {
            $htmlCode .= $this->CreateIdentifierInput ($identifierInputType, $identiferName, $identifierDisplayName,$defaultIdentifier,$videoType);
        }
        
        return $htmlCode;
    }
    
    /**
     * Create and return the specific "$videoType" video parameters html codes from the "$videoData"
     *
     * @param array $videoParameters The specified video type parameters list
     * @param string $videoType A video type
     * @return string The specific video parameters html code
     */
    protected function getParameters($videoParameters,$videoType)
    {
        $htmlCode ='';
        
        foreach($videoParameters as $parameterName => $parameterData)
        {
            $parameterInputType = $parameterData['type'];
            $parameterDisplayName = $parameterData['display'];
            $specificParameterData = $parameterData['data'];
            $defaultParameterValue = $parameterData['default'];
            $htmlCode .= $this->createParameter($parameterInputType,$parameterName,$parameterDisplayName,$defaultParameterValue,$specificParameterData,$videoType);
        }
        
        return $htmlCode;
    }
  
    /**
     * Create and return a specified identifier html codes
     *
     * @param string $identifierInputType The form input type used for the parameter
     * @param string $identifierName The identifier name
     * @param string $identifierDisplayName The identifier display name
     * @param string $defaultIdentifier The videoType default identifier type
     * @param string $videoType A video type
     * @return string The specified video identifier form sequence html code
     */
    protected function createIdentifierInput ($identifierInputType, $identifierName, $identifierDisplayName,$defaultIdentifier,$videoType)
    {
        $defaultValue = "";
        
        if($identifierName == $defaultIdentifier)
        {
            $defaultValue = "checked";
        }
        
        $this->domIdList[$videoType]['identifiers']['radio'.$identifierName]= array('type'=>'radio','id' =>'#videoInput'.$identifierName.'_'.$this->getId(),'default' => $defaultValue);
        $this->domIdList[$videoType]['identifiers']['text'.$identifierName]= array('type'=>'textBox','id' =>'#video'.$identifierName.'_'.$this->getId(), 'default' => '');
        
        $identifier=
                '<input type ="'.$identifierInputType.'" name="videoInputType_'.$this->getId().'" id="videoInput'.$identifierName.'_'.$this->getId().'" value="'.$identifierName.'" '.$this->checkIdentifier($videoType,$identifierName).'>'
        .       '<label for="video'.$identifierName.'_'.$this->getId().'">'. get_lang($identifierDisplayName). '</label>&nbsp;'
        .       '<input type="text" name="video'.$identifierName.'_'.$this->getId().'" id="video'.$identifierName.'_'.$this->getId().'" maxlength="255" '.$this->setIdentifierValue($videoType,$identifierName).'/><br /><br />';   
        
        return $identifier;
    }
  
    /**
     * Set the checked attribute to the identifier radio input if
     * the specific videoType equals the current videoInputType property
     *
     * @param string $videoType A video type
     * @param string $identifierName A identifier name
     * @return string The checked attribute of an identifier radio input html element
     */
    protected function checkIdentifier($videoType,$identifierName)
    {
        if($videoType == $this->videoType)
        {
            if($identifierName == $this->videoInputType)
            {
                return 'checked="checked"';
            }
        }
        else
        { 
            if($identifierName == $this->videoDefaultValueList['identifiers'][$videoType])
            {
                return 'checked="checked"';
            }            
        }
    }
    
    /**
     * Return the identifier value attribute of the identifier html element if
     * the identifier name equals the current video input type
     *
     * @param string $identifierName A identifier name
     * @param string $identifierValue A identifier input value
     * @return string The value attribute of an identifier hmtl element
     */
    protected function setIdentifierValue($videoType,$identifierName)
    {
        if($videoType == $this->videoType)
        {
            if($identifierName == $this->videoInputType)
            {
                return 'value="'.htmlspecialchars(trim($this->videoInput)).'"';
            }
        }
    }
  
    /**
     * Create and return a specified parameter html codes
     *
     * @param string $parameterInputType A video parameter form input type
     * @param string $paramerterName The specific video parameter name
     * @param string $parameterDisplayName The specific video parameter display name
     * $param string $parameterDefaultValue The specific video parameter default value 
     * @param array  $parameterData The specific video parameter data
     * @param string $videoType A video type
     * @return string The specified video parameter form sequence html code
     */
    protected function createParameter($parameterInputType,$parameterName,$parameterDisplayName,$parameterDefaultValue,$parameterData,$videoType)
    {
        $htmlCode ='';
        
        if($parameterInputType =='radio')
        {
            $htmlCode .=
                '<div class ="parameter'.$parameterName.'_'.$this->getId().'">'
            .   '<label for="video'.$parameterName.'_'.$this->getId().'">'.get_lang($parameterDisplayName).'<br />';
            
            foreach( $parameterData as $radioValue => $radioDisplay)
            {
                if($radioValue == $parameterDefaultValue)
                {
                    $parameterChecked = "checked";
                }
                else
                {
                    $parameterChecked = "";
                }    
                $this->domIdList[$videoType]['parameters'][$parameterName.'_'.$radioValue]= array('type'=>'radio','id' =>'#video'.$parameterName.'_'.$radioValue.'_'.$this->getId(), 'default' => $parameterChecked);
                
                $htmlCode .=
                    '<input type ="radio" name="video'.$parameterName.'_'.$this->getId().'" id="video'.$parameterName.'_'.$radioValue.'_'.$this->getId().'" value="'.$radioValue.'" '.$this->checkParameter($videoType,$parameterName,$radioValue).'>'
                .   '<label for="video'.$parameterName.'_'.$radioValue.'_'.$this->getId().'">'.get_lang($radioDisplay).'</label><br />';
            }
            
            $htmlCode .=
                '</div><br/>';
            
            return $htmlCode;
        
        }
        else if($parameterInputType =='select')
        {
            $htmlCode .=
                '<div class ="parameter'.$parameterName.'_'.$this->getId().'">'
            .   '<label for="video'.$parameterName.'_'.$this->getId().'">'.get_lang($parameterDisplayName).'<br />'
            .   '<select name="video'.$parameterName.'_'.$this->getId().'" id="video'.$parameterName.'_'.$this->getId().'" size="1">';
            
            foreach( $parameterData as $selectOptionValue => $selectOptionDisplay)
            {
                if($selectOptionValue == $parameterDefaultValue)
                {
                    $parameterDefaultValue = "selected";
                }
                else
                {
                    $parameterDefaultValue = "";
                }
                
                $this->domIdList[$videoType]['parameters']['select'.$parameterName.$selectOptionValue]= array('type'=>'select','id' =>'#video'.$parameterName.'_'.$selectOptionValue.'_'.$this->getId(), 'default' => $parameterDefaultValue);
                
                $htmlCode .=
                    '<option value="'.$selectOptionValue.'" id="video'.$parameterName.'_'.$selectOptionValue.'_'.$this->getId().'"' .$this->selectParameterOption($videoType,$parameterName,$selectOptionValue).'>'.get_lang($selectOptionDisplay).'</option>';
            }
            
            $htmlCode .=
                '</select></div><br />';
           
            return $htmlCode;
        
        }
        else if($parameterInputType =='textBox')
        {
            $this->domIdList[$videoType]['parameters']['text'.$parameterName]= array('type'=>'textBox','id' =>'#video'.$parameterName.'_'.$this->getId(), 'default' => $parameterDefaultValue);
            
            $htmlCode .=
                '<div class ="parameter'.$parameterName.'_'.$this->getId().'">'
            .   '<label for="video'.$parameterName.'_'.$this->getId().'">'.get_lang($parameterDisplayName).'</label>'
            .   '<input type="'.$parameterData['type'].'" name="video'.$parameterName.'_'.$this->getId().'" id="video'.$parameterName.'_'.$this->getId().'" maxlength="255"  '.$this->setParameterValue($videoType,$parameterName).'"/></div><br/>';
            
            return $htmlCode;
        
        }
    }
    
    /**
     * Set the checked attribute to the parameter radio input if
     * the relatif videoParameterList value or the parameter default value equals the specified parameter value
     * 
     * @param string $videoType A video type
     * @param string $parameterName A identifier name
     * @param string $parameterValue A parameter value
     * @return string The checked attribute of a parameter radio input html element
     */
    protected function checkParameter($videoType,$parameterName,$parameterValue)
    {
        if($videoType == $this->videoType && $videoType != 'automatic')
        {
            if(isset($this->videoParameterList[$parameterName]))
            {
                if($this->videoParameterList[$parameterName] == $parameterValue)
                {
            
                    return 'checked="checked"';
                }
            }
        }
        else
        {
            if($this->videoDefaultValueList['parameters'][$videoType][$parameterName] == $parameterValue )
            {
                return 'checked="checked"';
            }
        }
    }
    
    /**
     * Set the selected attribute to the parameter select option if
     * the relatif videoParameterList value or the parameter default value equals the specified parameter value
     * 
     * @param string $videoType A video type
     * @param string $parameterName A identifier name
     * @param string $parameterValue A parameter value
     * @return string The selected attribute of a parameter select option html element
     */
     protected function selectParameterOption($videoType,$parameterName,$selectOptionValue)
    {
        if($videoType == $this->videoType)
        {
            if(isset($this->videoParameterList[$parameterName]))
            {
                if($this->videoParameterList[$parameterName] == $selectOptionValue)
                {
                
                    return 'selected="selected"';
                }
            }
        }
        else
        {
            if($this->videoDefaultValueList['parameters'][$videoType][$parameterName] == $selectOptionValue)
            {
            
                return 'selected="selected"';
            }
        }
    }
    
    /**
     * Return the identifier value attribute of the parameter html element if
     * the parameter name equals the current video input type
     *
     * @param string $videoType A video type
     * @param string $parameterName A identifier name
     * @return string The value attribute of an identifier hmtl element
     */
    protected function setParameterValue($videoType,$parameterName)
    {
        if($videoType == $this->videoType)
        {
            if(isset($this->videoParameterList[$parameterName]))
            {
                
                return 'value="'.htmlspecialchars(trim($this->videoParameterList[$parameterName])).'"';
            }
        }
        else
        {
            return 'value="'.htmlspecialchars(trim($this->videoDefaultValueList['parameters'][$videoType][$parameterName])).'"';
        }
    }
    
    /**
     * Create and return the html code to fill the video type listbox with the available videos display name
     *
     * @return string The option select form element html code
     */
    protected function getTypeOptions()
    {
        $options = '';
        
        foreach($this->videoRegister->getRegisteredVideoTypes() as $videoType)
        {
            $options.='<option value="'.$videoType.'" '.$this->selectVideoTypeOption($videoType). '>'.$this->videoRegister->getVideoDisplayName( $videoType ).'</option>'. "\n";
        }
        return $options;
    }

    /**
     * Set the selected attribute to the video type select option if
     * the specified videotype equals the current videoType video property
     * 
     * @param string $videoType A video type
     * @return string The selected attribute of a parameter select option html element
     */
     protected function selectVideoTypeOption($videoType)
    {
        if($videoType == $this->videoType)
        {
            return 'selected="selected"';
        }
    }

    /**
     * Set the video properties with the form inputs after submition
     */
    public function getEditorData()
    {
        $this->videoInputType = $this->getFromRequest('videoInputType_'.$this->getId());
        $this->videoInput = $this->getFromRequest('video'.$this->videoInputType.'_'.$this->getId());
        $this->videoType = $this->getFromRequest('videoType_'.$this->getId());
        $this->getVideoParameterList();
    }
 
    /**
     * Set the videoParameter property with the selected video parameters inputs after submition
     */
    protected function getVideoParameterList()
    {
        $videoParameterList = array();

        foreach($this->videoRegister->getRegisteredVideoTypes() as $videoType)
        {
            if($videoType == $this->videoType)
            {
                $videoParam = array();

                foreach($this->videoRegister->getVideoClassInstance($videoType)->getParameters() as $parameterName => $parameterData)
                {
                    $id = 'video'.$parameterName.'_'.$this->getId();
                    $this->videoParameterList[$parameterName]= $this->getFromRequest($id);
                }
            }
        }
    }
    
   /**
    * Test the submited inputs to define the validation status
    * Return true if inputs are validate and error message if the validation abord
    *
    * @return string The validation result, true or an error message
    */
    public function validate()
    {
        $video = '';
        
        if($this->videoType == 'automatic' && $this->videoInputType == 'Url')
        {
            $definedVideoType = $this->videoRegister->defineVideoType($this->videoInput);
            
            if($definedVideoType != false)
            {
                $this->videoType = $definedVideoType;
                $video = $this->videoRegister->getVideoClassInstance($definedVideoType);
                $video->setData(array('input' => $this->videoInput, 'inputType' => $this->videoInputType,'parameters' => $this->videoParameterList));
            }
            else
            {
                $errorMessage = get_lang('Not a correct available%videoType video web address : <br> %wrongInput',array("%videoType" => '', "%wrongInput" => htmlspecialchars($this->videoInput)));
                return $this->renderErrorMessage($errorMessage);
            }
        }
        else
        {
            $video = $this->videoRegister->getVideoClassInstance($this->videoType);
            $video->setData(array('input' => $this->videoInput, 'inputType' => $this->videoInputType,'parameters' => $this->videoParameterList));
        }
        
        $validation = $video->validate();
        
        if('true' == $validation)
        {
            
            return 'true';
        }
        else
        {
            
            return $this->renderErrorMessage($validation);
        }
    }
    
    /**
     * Create the current video type video player HTML code
     *
     * @return string The video player HTML code
     */
    public function render()
    {
        if ($this->videoInput != null && $this->videoType!= 'automatic')
        {
            $video = $this->videoRegister->getVideoClassInstance($this->videoType);
            
            $video->setData(array('input' => $this->videoInput, 'inputType' => $this->videoInputType , 'parameters' => $this->videoParameterList));

            return $video->setPlayer();
        }
    }
    
    /**
     * @see Component
     */
    public function setData( $data )
    {
        $this->videoInput = trim($data['videoInput']);
        $this->videoInputType = $data['videoInputType'];
        $this->videoType = $data['videoType'];
        $this->videoParameterList = $data['parameters'];
        
    }
   
    /***
     * see Component
     */
    public function getData()
    {
        return array('videoInput' => $this->videoInput,'videoInputType' => $this->videoInputType, 'videoType' => $this->videoType, 'parameters' =>$this->videoParameterList);
    }
}

PluginRegistry::register('video',get_lang('Web video'),'VideoComponent', 'Externals', 'videoIco');
