<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * OPML Generator Class
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLOPML
 */

/**
 * OPML Generator Class
 */
class Opml
{
    /**
     * Generate OPML file content from an array
     * @param   array data content of the opml in an array
     * @return  string opml file content
     */
    public function generate( $data )
    {
        $opml = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?'.'>'."\n"
            . '<opml version="1.1">' . "\n"
            . '<head>' . "\n"
            . ( array_key_exists( 'title', $data )
                ? '<title>'.claro_htmlspecialchars($data['title']).'</title>'
                : '' )
            . ( array_key_exists( 'dateCreated', $data )
                ? '<dateCreated>'.claro_htmlspecialchars($data['dateCreated']).'</dateCreated>'
                : '' )
            . ( array_key_exists( 'dateModified', $data )
                ? '<dateModified>'.claro_htmlspecialchars($data['dateModified']).'</dateModified>'
                : '' )
            . ( array_key_exists( 'ownerName', $data )
                ? '<ownerName>'.claro_htmlspecialchars($data['ownerName']).'</ownerName>'
                : '' )
            . ( array_key_exists( 'ownerEmail', $data )
                ? '<ownerEmail>'.claro_htmlspecialchars($data['ownerEmail']).'</ownerEmail>'
                : '' )
            . '</head>' . "\n"
            . '<body>' ."\n"
            ;

        if ( array_key_exists ( 'outlines', $data )
            && is_array( $data['outlines'] ) )
        {
            foreach ( $data['outlines'] as $outline )
            {
                $opml .= '<outline type="rss"'
                    . ( array_key_exists ( 'text', $outline )
                        ? ' text="'.claro_htmlspecialchars($outline['text']).'"'
                        : '' )
                    . ( array_key_exists ( 'count', $outline )
                        ? ' count="'.(int)$outline['count'].'"'
                        : '' )
                    . ' xmlUrl="' . claro_htmlspecialchars($outline['xmlUrl']) . '"/>'
                    ;
            }
        }

        $opml .= '</body>'."\n" .'</opml>';

        return $opml;
    }
}
