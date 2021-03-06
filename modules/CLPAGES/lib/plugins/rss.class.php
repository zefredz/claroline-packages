<?php

// $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class RssComponent extends Component
{

    protected $url = '';
    protected $limit = 5;

    /**
     * @see Component
     */
    public function render()
    {
        if (!empty($this->url))
        {
            $rss_content = file_get_contents($this->url);

            $out = '';

            if ( $rss_content )
            {
                $xml = simplexml_load_string($rss_content);
                
                $out .= '<p>' . "\n";

                $limit = $this->limit;
                $i = 0;
                
                if ( $xml->item )
                {
                    foreach ( $xml->item as $item )
                    {
                        if ($i < $limit)
                        {
                            $out .= '<div class="componentRssItem">'
                                . '<div class="componentRssItemHeader"><a href="' . claro_utf8_decode((string) $item->link) . '">' . claro_utf8_decode((string)$item->title) . '</a></div>' . "\n"
                                . '<div  class="componentRssItemContent">' . "\n" . html_entity_decode(claro_utf8_decode((string)$item->description)) . '</div>' . "\n"
                                . '</div>' . "\n";
                            $i++;
                        }
                        else
                        {
                            break;
                        }
                    }
                }
                elseif ( $xml->entry )
                {
                    foreach ( $xml->entry as $item )
                    {
                        if ($i < $limit)
                        {
                            $out .= '<div class="componentRssItem">'
                                . '<div class="componentRssItemHeader"><a href="' . claro_utf8_decode((string) $item->link['href']) . '">' . claro_utf8_decode((string)$item->title) . '</a></div>' . "\n"
                                . '<div  class="componentRssItemContent">' . "\n" . html_entity_decode(claro_utf8_decode((string)$item->content)) . '</div>' . "\n"
                                . '</div>' . "\n";
                            $i++;
                        }
                        else
                        {
                            break;
                        }
                    }
                }

                $out .= '</p>' . "\n";
            }
            else
            {
                if (claro_is_allowed_to_edit())
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
    public function editor()
    {
        // use content in textarea
        return '<label for="url_' . $this->getId() . '">' . get_lang('Url of rss feed') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        . '<input type="text" name="url_' . $this->getId() . '" id="url_' . $this->getId() . '" maxlength="255" value="' . htmlspecialchars($this->url) . '" /><br />' . "\n"
        . '<label for="limit_' . $this->getId() . '">' . get_lang('Number of displayed items ') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
        . '<input type="text" name="limit_' . $this->getId() . '" id="limit_' . $this->getId() . '" size="5" maxlength="10" value="' . htmlspecialchars($this->limit) . '" /><br />' . "\n"
        ;
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        $this->url = $this->getFromRequest('url_' . $this->getId());
        $this->limit = $this->getFromRequest('limit_' . $this->getId());

        $this->limit = max(0, $this->limit);
    }

    /**
     * @see Component
     */
    public function setData($data)
    {
        $this->url = $data['url'];
        $this->limit = $data['limit'];
    }

    /**
     * @see Component
     */
    public function getData()
    {
        return array (
            'url' => $this->url,
            'limit' => $this->limit
        );
    }

}

PluginRegistry::register('rss', get_lang('Rss'), 'RssComponent', 'Externals', 'rssIco');
