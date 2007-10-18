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
 * @package CLAUTHOR
 *
 * @author Claroline team <info@claroline.net>
 *
 */
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
/*
  <item>
			<title>P1010025</title>

			<link>http://www.flickr.com/photos/10276634@N03/857860135/</link>
			<description>&lt;p&gt;&lt;a href=&quot;http://www.flickr.com/people/10276634@N03/&quot;&gt;paulbe_irc&lt;/a&gt; a posté une photo :&lt;/p&gt;

&lt;p&gt;&lt;a href=&quot;http://www.flickr.com/photos/10276634@N03/857860135/&quot; title=&quot;P1010025&quot;&gt;&lt;img src=&quot;http://farm2.static.flickr.com/1037/857860135_598cd078b2_m.jpg&quot; width=&quot;240&quot; height=&quot;180&quot; alt=&quot;P1010025&quot; /&gt;&lt;/a&gt;&lt;/p&gt;

</description>
			<pubDate>Fri, 20 Jul 2007 05:34:22 -0800</pubDate>
                        <dc:date.Taken>2007-07-20T05:34:22-08:00</dc:date.Taken>
			<author>nobody@flickr.com (paulbe_irc)</author>
			<guid isPermaLink="false">tag:flickr.com,2004:/photo/857860135</guid>
                        			<media:content url="http://farm2.static.flickr.com/1037/857860135_f02836c6af_o.jpg"
				       type="image/jpeg"
				       height="960"
				       width="1280"/>
			<media:title>P1010025</media:title>

			<media:text type="html">&lt;p&gt;&lt;a href=&quot;http://www.flickr.com/people/10276634@N03/&quot;&gt;paulbe_irc&lt;/a&gt; a posté une photo :&lt;/p&gt;

&lt;p&gt;&lt;a href=&quot;http://www.flickr.com/photos/10276634@N03/857860135/&quot; title=&quot;P1010025&quot;&gt;&lt;img src=&quot;http://farm2.static.flickr.com/1037/857860135_598cd078b2_m.jpg&quot; width=&quot;240&quot; height=&quot;180&quot; alt=&quot;P1010025&quot; /&gt;&lt;/a&gt;&lt;/p&gt;

</media:text>
			<media:thumbnail url="http://farm2.static.flickr.com/1037/857860135_598cd078b2_s.jpg" height="75" width="75" />
			<media:credit role="photographer">paulbe_irc</media:credit>
			<media:category scheme="urn:flickr:tags">elearning lettrage claroline paulbe</media:category>

		</item>
 */
    class FlickrComponent extends Component
    {
    	private $tag = '';
    	private $limit = 5;

		/**
		 * @see Component
		 */
    	function render()
    	{
    		if( !empty($this->tag) )
    		{
				require_once get_path('incRepositorySys') . '/lib/lastRSS/lastRSS.php';

				$out = '';
				$feed = $this->getFeed( 'http://www.flickr.com/services/feeds/photos_public.gne?tags='.urlencode($this->tag).'&format=rss_200' );

				if( !empty($feed) )
				{
					if( isset($feed['rss']['#']['channel'][0]['#']['item']) )
					{
						$items = $feed['rss']['#']['channel'][0]['#']['item'];
					}
					else
					{
						$items = array();
					}

					if( is_array($items) && !empty($items) )
					{
				    	$limit = $this->limit;
				    	$i = 0;

					    foreach( $items as $item )
					    {
					    	$thumbUrl = claro_utf8_decode($item['#']['media:thumbnail'][0]['@']['url']);

					    	if( !empty($thumbUrl) )
					    	{
						    	$thumbHeight = claro_utf8_decode($item['#']['media:thumbnail'][0]['@']['height']);
						    	$thumbWidth = claro_utf8_decode($item['#']['media:thumbnail'][0]['@']['width']);


						    	if( !empty($thumbHeight) ) $height = 'height="'.htmlspecialchars($thumbHeight).'"';
				    			else						$height = '';

				    			if( !empty($thumbWidth) ) $width = 'width="'.htmlspecialchars($thumbWidth).'"';
				    			else						$width = '';

								$link = claro_utf8_decode($item['#']['link'][0]['#']);

						        if( $i < $limit )
						        {
						            $out .= '<div style="float: left; border:1px solid #ccc; padding: 3px; margin: 5px;">' . "\n"
						            .	 '<a href="'.htmlspecialchars($link).'" >'
						    		.	 '<img src="'.htmlspecialchars($thumbUrl).'" '.$height.' '.$width.' alt="" />'
						    		.	 '</a>' . "\n"
						    		.	 '</div>' . "\n\n";
						            $i++;
						        }
						        else
						        {
						            break;
						        }
					    	}
					    } // foreach
						$out .= '<div class="spacer"></div>';
					} // is array
					else
					{
						$out .= '<p>' . get_lang('Problem reading feed.') . '</p>' . "\n";
					}
				}
				else
				{
				    if( claro_is_allowed_to_edit() )
				    {
				        $out .= '<p>' . get_lang('Error : cannot read RSS feed (Check that feed is accessible or ask your administrator if php setting "allow_url_fopen" is turned on).') . '</p>' . "\n";
				    }
				}

				return $out;
    		}
    		else
    		{
    			return '' . "\n";
    		}
    	}

		/**
		 * @see Component
		 */
    	function editor()
    	{
    		// use content in textarea
    		return '<label for="tag_'.$this->getId().'">' . get_lang('Tag') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="text" name="tag_'.$this->getId().'" id="tag_'.$this->getId().'" maxlength="255" value="'.htmlspecialchars($this->tag).'" /><br />' . "\n"
    		.	 '<label for="limit_'.$this->getId().'">' . get_lang('Number of displayed items ') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="text" name="limit_'.$this->getId().'" id="limit_'.$this->getId().'" size="5" maxlength="10" value="'.htmlspecialchars($this->limit).'" /><br />' . "\n"
    		;
    	}

		/**
		 * @see Component
		 */
    	function getEditorData()
    	{
    		$this->tag = $this->getFromRequest('tag_'.$this->getId());
    		$this->limit = $this->getFromRequest('limit_'.$this->getId());
    	}

		/**
		 * @see Component
		 */
    	function setData( $data )
    	{
  			$this->tag = $data['tag'];
  			$this->limit = $data['limit'];
    	}

		/**
		 * @see Component
		 */
    	function getData()
    	{
    		return array(
				'tag' => $this->tag,
				'limit' => $this->limit
			);
    	}

    	// this code is the same as Get method of lastRSS library
    	// this way cache can be shared between the two uses
    	private function getFeed($rss_url) {

			// configure parser
			$cache_dir = get_path('rootSys') . '/tmp/cache/';
			$cache_time = 3600;

			// If CACHE ENABLED
			if ($cache_dir != '') {
				$cache_file = $cache_dir . '/rsscache_' . md5($rss_url);
				$timedif = @(time() - filemtime($cache_file));
				if ($timedif < $cache_time) {
					// cached file is fresh enough, return cached array
					$result = unserialize(join('', file($cache_file)));
					// set 'cached' to 1 only if cached file is correct
					if ($result) $result['cached'] = 1;
				} else {
					// cached file is too old, create new
					$result = $this->parseFeed($rss_url);
					$serialized = serialize($result);
					if ($f = @fopen($cache_file, 'w')) {
						fwrite ($f, $serialized, strlen($serialized));
						fclose($f);
					}
					if ($result) $result['cached'] = 0;
				}
			}
			// If CACHE DISABLED >> load and parse the file directly
			else {
				$result = $this->parseFeed($rss_url);
				if ($result) $result['cached'] = 0;
			}
			// return result
			return $result;
		}

		private function parseFeed($rss_url)
		{
			if ($f = @fopen($rss_url, 'r'))
			{
				$rss_content = '';
				while (!feof($f))
				{
					$rss_content .= fgets($f, 4096);
				}
				fclose($f);

				$content = xmlize($rss_content);

				if( is_array($content) )
				{
					return $content;
				}
				else
				{
					return array();
				}
			}
    	}

    }

    PluginRegistry::register('flickr',get_lang('Flickr gallery'),'FlickrComponent', 'Externals', 'flickrIco');



    if( !function_exists('xmlize') )
    {
		function xmlize($data, $WHITE=1) {

		    $data = trim($data);
		    $vals = $index = $array = array();
		    $parser = xml_parser_create();
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $WHITE);
		    if ( !xml_parse_into_struct($parser, $data, $vals, $index) )
		    {
			    return sprintf("XML error: %s at line %d",
		                    xml_error_string(xml_get_error_code($parser)),
		                    xml_get_current_line_number($parser));

		    }
		    xml_parser_free($parser);

		    $i = 0;

		    $tagname = $vals[$i]['tag'];
		    if ( isset ($vals[$i]['attributes'] ) )
		    {
		        $array[$tagname]['@'] = $vals[$i]['attributes'];
		    } else {
		        $array[$tagname]['@'] = array();
		    }

		    $array[$tagname]["#"] = xml_depth($vals, $i);

		    return $array;
		}
    }



	if( !function_exists('xml_depth') )
    {
		function xml_depth($vals, &$i) {
		    $children = array();

		    if ( isset($vals[$i]['value']) )
		    {
		        array_push($children, $vals[$i]['value']);
		    }

		    while (++$i < count($vals)) {

		        switch ($vals[$i]['type']) {

		           case 'open':

		                if ( isset ( $vals[$i]['tag'] ) )
		                {
		                    $tagname = $vals[$i]['tag'];
		                } else {
		                    $tagname = '';
		                }

		                if ( isset ( $children[$tagname] ) )
		                {
		                    $size = sizeof($children[$tagname]);
		                } else {
		                    $size = 0;
		                }

		                if ( isset ( $vals[$i]['attributes'] ) ) {
		                    $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
		                }

		                $children[$tagname][$size]['#'] = xml_depth($vals, $i);

		            break;


		            case 'cdata':
		                array_push($children, $vals[$i]['value']);
		            break;

		            case 'complete':
		                $tagname = $vals[$i]['tag'];

		                if( isset ($children[$tagname]) )
		                {
		                    $size = sizeof($children[$tagname]);
		                } else {
		                    $size = 0;
		                }

		                if( isset ( $vals[$i]['value'] ) )
		                {
		                    $children[$tagname][$size]["#"] = $vals[$i]['value'];
		                } else {
		                    $children[$tagname][$size]["#"] = '';
		                }

		                if ( isset ($vals[$i]['attributes']) ) {
		                    $children[$tagname][$size]['@']
		                                             = $vals[$i]['attributes'];
		                }

		            break;

		            case 'close':
		                return $children;
		            break;
		        }

		    }

			return $children;

		}
    }

?>