<?php // $Id$

// vim: set expandtab tabstop=4 shiftwidth=4:

// +----------------------------------------------------------------------+
// | PHP version 5.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997, 1998, 1999, 2000, 2001 The PHP Group             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ned Tore <naxxtor@hackervoice.co.uk>                        |
// |          Claroline adaptation by ZeFredz <zefredz@claroline.net>     |
// +----------------------------------------------------------------------+
//


require_once dirname(__FILE__) . '/urlgetcontents.lib.php';

class PodcastParser
{
    private $feed_url;
    private $items;
    private $channel;
    
    public function __construct() 
    {
        $this->channel     = array();
        $this->items    = array();
    }
    
    /**
    * Gets the feed and parses it
    */
    public function parseFeed($url)
    {
        $this->feed_url = $url;
        
        $file = url_get_contents($url);
        
        if ( ! $file )
        {
            throw new Exception( get_lang( "Podcast feed not found" ) );
        }
        
        $xml_parser = simplexml_load_string($file);
        
        $this->channel = array(                            // Maps SimpleXML elements into the object
            'title'=>(string) $xml_parser->channel->title,
            'link'=> (string) $xml_parser->channel->link,
            'description'=>(string) $xml_parser->channel->description,
            'pubDate'=>(string) $xml_parser->channel->pubDate
        );
    
        foreach ( $xml_parser->channel->children()->item as $item ) 
        {                    // pushes on PodcastItem objects into the items array for each <item> tag
            $this->items[]  = new PodcastItem($item);
        }
        
        unset($xml_parser);
        
        return true;
    }
    
    public function getChannelInfo()
    {
        return $this->channel;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    
    /**
        Resets the object ready to parse another feed.
    */
    public function reset()
    {
        $this->__construct();
    }
    
    public function getHash()
    {
        return md5(serialize($this));
    }
}

class PodcastItem
{
    public $metadata;
    public $enclosure;
    
    /**
        Converts from SimpleXMLElement to a normal user object
        This means you can serialise/cache it if you wish
    */
    public function __construct($xml_obj)
    {
        $this->metadata = array(
            'title'=>        (string) $xml_obj->title,
            'description'=>    (string) $xml_obj->description,
            'link'=>        (string) $xml_obj->link,
            'guid'=>        (string) $xml_obj->guid,
            'pubDate'=>        (string) $xml_obj->pubDate,
        );
        
        $enc_tmp =  $xml_obj->enclosure->attributes();
        
        $this->enclosure = array(
            'url'=>         (string) $enc_tmp->url,
            'length'=>         (string) $enc_tmp->length,
            'type'=>         (string) $enc_tmp->type,
        );
    }
}
