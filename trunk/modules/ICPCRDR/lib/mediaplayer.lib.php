<?php // $Id$

// vim: set expandtab tabstop=4 shiftwidth=4:

class Claro_Html_Mediaplayer
{
    protected static $htmlHeaderSent = false;
    protected $type, $src, $claroline, $displayDowloadLink;
    
    public function __construct( $src, $type, $displayDownloadLink = true )
    {
        $this->src = $src;
        $this->type = $type;
        $this->displayDowloadLink = $displayDownloadLink ? true : false;
    }
    
    public function hideDownloadLink()
    {
        $this->displayDowloadLink = false;
    }
    
    public function showDownloadLink()
    {
        $this->displayDowloadLink = true;
    }
    
    public function render ()
    {
        self::sendHtmlHeader();
        
        return $this->getView()->render();
    }
    
    public static function sendHtmlHeader()
    {
        if ( ! self::$htmlHeaderSent )
        {
            Claroline::getDisplay()->header->addHtmlHeader('
                <script src="'.get_module_url('ICPCRDR').'/mediaelementjs/mediaelement-and-player.min.js"></script>
                <link rel="stylesheet" href="'.get_module_url('ICPCRDR').'/mediaelementjs/mediaelementplayer.css" />
            ');
            
            self::$htmlHeaderSent = true;
        }
    }
    
    public function getView()
    {
        $mediaplayer = new ModuleTemplate('ICPCRDR','mediaplayer.tpl.php');
        $mediaplayer->assign( 'src', $this->src );
        $mediaplayer->assign( 'type', $this->type );
        $mediaplayer->assign( 'displayDowloadLink', $this->displayDowloadLink );
        
        return $mediaplayer;
    }
}