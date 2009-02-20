<?php

require_once dirname(__FILE__) . '/webserviceauth.lib.php';
require_once dirname(__FILE__) . '/webservice.lib.php';

class Simple_Lti
{
    // Targets
    const TARGET_IFRAME = 'iframe';
    const TARGET_WIDGET = 'widget';
    const TARGET_POST = 'post';
    
    // Post formats
    const POST_FORMAT_XML = 'xml';
    const POST_FORMAT_JSON = 'json';
    
    protected static function output( $target, $content, $format = self::POST_FORMAT_XML )
    {
        $output = '';
        
        switch ( $target )
        {
            case self::TARGET_IFRAME:
                    $output = HttpResponse::getHeader()
                        . HttpResponse::body( $content->toHtml() )
                        . HttpResponse::getFooter()
                        ;
                break;
            
            case self::TARGET_WIDGET:
                    $output = HttpResponse::div( $content->toHtml() );
                break;
            
            case self::TARGET_POST:
                
            default:
                    switch( $format )
                    {
                        case self::POST_FORMAT_JSON:
                            $output = Json::encodeArray( $content->toArray() );
                            break;
                        
                        case self::POST_FORMAT_XML:
                            
                        default:
                            $output = XML::serializeArray( $content->toArray() );
                            break;
                    }
                break;
        }
        
        return $output;
    }
}

class Simple_Lti_Sec
{
    public  $digest,
            $nonce,
            $created,
            $org_digest;
}

class Simple_Lti_User
{
    public  $id,
            $role,
            $firstname,
            $lastname,
            $email, $eid, $displayid, $roster, $locale;
}

class Simple_Lti_Course
{
    public  $id,
            $name,
            $title;
}

class Simple_Lti_Org
{
    public  $id,
            $title,
            $name,
            $url;
}

class Simple_Lti_Launch
{
    public  $resource_id,
            $resource_url,
            $targets,
            $width,
            $height,
            $tool_name,
            $tool_id,
            $tool_title;
}

class Simple_Lti_Request
{
    public  $action,
            $sec,
            $user,
            $course,
            $org,
            $launch
            ;
    
    public function __construct()
    {
        $this->sec = new Simple_Lti_Sec;
        $this->user = new Simple_Lti_User;
        $this->course = new Simple_Lti_Course;
        $this->org =  new Simple_Lti_Org;
        $this->launch = new Simple_Lti_Launch;
    }
}


class Simple_Lti_Client extends WebService_Client
{
    
}

class Simple_Lti_Server extends WebService_Server
{
    
}
