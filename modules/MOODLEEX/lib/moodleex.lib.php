<?php // $Id$

/**
 * Moodle Resource Exporter
 *
 * @version     MOODLEEX 1.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2015 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     MOODLEEX
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Gets the current course's exercise list
 * @return array : the exercise list
 */
function MOODLEEX_get_quiz_list()
{
    $quizList = array();
    
    $tbl = get_module_course_tbl ( array ( 'qwz_exercise' ) );
    
    $data = Claroline::getDatabase()->query(
        "SELECT
            id,title, description, shuffle
        FROM
            `{$tbl['qwz_exercise']}`"
    );
    
    if( $data->numRows() )
    {
        foreach( $data as $line )
        {
            $quizList[ $line['id'] ] = array(
                'id' => $line['id'],
                'title' => $line[ 'title' ],
                'description' => $line['description'],
                'shuffle' => $line['shuffle'] == '1' ? true : false,
            );
        }
        
        return $quizList;
    }
    else
    {
        throw new Exception( 'Invalid id' );
    }
}

/**
 * Removes unwanted chars and accents
 * @param string $string : the string to clean
 * @return string : the cleaned up string
 */
function MOODLEEX_clean( $string )
{
    $string = str_replace( ' ' , '_' , $string );
    $string = str_replace( '\'' , '' , $string );
    $string = str_replace( '"' , '' , $string );
    
    $string = preg_replace( '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i'
        , '$1'
        , htmlentities( claro_utf8_encode( $string ) , ENT_QUOTES , 'UTF-8' ) );
    
    if( strlen( $string ) > 64 )
    {
        $string = substr( $string , 0 , 64 );
    }
    
    return strtolower( $string );
}

/**
 * Removes shit TinyMCE puts in its content
 * @param string $string : the string to clear
 * @return string : the clear up string
 */
function MOODLEEX_clear( $string )
{
    $string_to_remove = array(
        '<br /><!-- content: html tiny_mce -->',
        '<!-- content: html tiny_mce -->',
        '<!-- content: imsqti -->',
    );
    
    return str_replace( $string_to_remove , '' , trim( $string ) );
}

/**
 * Replaces src content by base64 encoded data
 * @param string $string 
 * @return string
 */
function MOODLEEX_convertImageSrc( $string )
{
    return $string;
    /*$content = new DOMDocument();
    $content->loadHTML( $string );
    $xpath = new DOMXPath( $content );
    $imageSrcList = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"*/
    
    $imageSrcList = array();
    
    //preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $string, $imgTagList );
    
    preg_match('/<img[^>]+>/i', $string , $imgTagList );
    
    foreach( $imgTagList as $index => $imgTag )
    {
        preg_match('/src="([^"]+)/i', $imgTag , $image );
        $imageSrc = str_ireplace( 'src="', '',  $image[0]);
        
        if( substr( $imageSrc , 0 , 5 ) != 'data:' )
        {
            $imageSrcList[] = $imageScr;
        }
    }
    
    if( ! empty( $imageSrcList) )
    {
        $newImageSrcList = array();
        
        foreach( $imageSrcList as $index => $imageSrc )
        {
            if( $imageData = file_get_contents( $imageSrc ) )
            {
                $extension = pathinfo( $imageSrc , PATHINFO_EXTENSION );
                
                $newImageSrcList[ $index ] = 'data:image/' . $imageSrc . ';base64,' . base64_encode( $imageData );
            }
        }
        
        str_replace( $imageSrcList , $newImageSrcList , $string );
    }
    
    return $string;
}

/**
 * Prepares html content in order to make if suitable for MOODLE xml
 */
function MOODLEEX_bake( $string )
{
    //return MOODLEEX_clear( $string );
    $output = MOODLEEX_clear( $string );
    
    if( MOODLEEX_is_html( $string ) )
    {
        return '![CDATA[' . MOODLEEX_convertImageSrc( $output ) . ']]';
    }
    else
    {
        return $output;
    }
}

function MOODLEEX_is_html( $string )
{
    return preg_match("/<[^<]+>/", $string, $m );
}

function MOODLEEX_getFileExtension( $fileName )
{
    return strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
}

function MOODLEEX_is_image( $fileName )
{
    $fileExtension = strtolower( MOODLEEX_getFileExtension( $fileName ) );
    $extensionList = array( 'gif','jpg','png' );
    
    return in_array( $fileExtension , $extensionList );
}
