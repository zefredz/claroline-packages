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
    }
    
    return $quizList;
}

function MOODLEEX_get_document_list()
{
    $docRepoPath = get_path('coursesRepositorySys') . claro_get_course_path() . '/document';
    
    $itemList = array();
    
    $tbl = get_module_course_tbl ( array ( 'document' ) );
    
    $data = Claroline::getDatabase()->query(
        "SELECT
            id,path,visibility,comment
        FROM
            `{$tbl['document']}`"
    );
    
    if( $data->numRows() )
    {
        foreach( $data as $line )
        {
            $documentList[ $line['id'] ] = array(
                'id' => $line['id'],
                'title' => $line[ 'path' ],
                'comment' => $line['comment']
            );
        }
    }
    
    return $itemList;
}

/**
 *
 */
function MOODLEEX_get_forum_list()
{
    $forumList = array();
    
    $tbl = get_module_course_tbl ( array ( 'bb_forums', 'bb_categories' ) );
    
    $data = Claroline::getDatabase()->query(
        "SELECT
            F.forum_id, C.cat_title, F.forum_name, F.forum_desc
        FROM
            `{$tbl['bb_forums']}` AS F
        INNER JOIN `{$tbl['bb_categories']}` AS C
        ON C.cat_id = F.cat_id"
    );
    
    if( $data->numRows() )
    {
        foreach( $data as $line )
        {
            $forumList[ $line['forum_id'] ] = array(
                'id' => $line['forum_id'],
                'title' => $line[ 'forum_name' ],
                'description' => $line['forum_desc'],
                'category' => $line['cat_name'],
            );
        }
    }
    
    return $forumList;
}

/**
 *
 */
function MOODLEEX_get_lp_list()
{
    
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
    return MOODLEEX_remove_tinymce_tags( MOODLEEX_process_spoilers( MOODLEEX_convert_smart_quotes( $string ) ) );
}

/**
 * Replaces src content for img by base64 encoded data
 * @param string $string : the html content
 * @return string : the 'same' html content with integrated images 
 */
function MOODLEEX_convert_img_src( $string )
{
    preg_match_all('/<img(.*)src(.*)=(.*)"(.*)"(.*)>/U', $string, $imgTagList );
    
    foreach( $imgTagList[ 4 ] as $index => $imageSrc )
    {
        if( substr( $imageSrc , 0 , 5 ) != 'data:' )
        {
            if( str_replace( '/claroline/backends/download.php' , '' , $imageSrc ) != $imageSrc )
            {
                preg_match('/\?url=(.*)&/U' , $imageSrc . '&', $url );
                
                if( ! empty( $url ) )
                {
                    $filePath = get_path( 'coursesRepositorySys' )
                    . claro_get_course_path()
                    . '/document'
                    .  base64_decode( $url[ 1 ] );
                }
                else
                {
                    $filePath = $imageSrc;
                }
            }
            else
            {
                $filePath = html_entity_decode( $imageSrc );
            }
            
            if( substr( $imageSrc , 0 , 7 ) == 'http://' )
            {
                $fileName = pathinfo( $imageSrc , PATHINFO_BASENAME );
                $filePath = get_path( 'rootSys' ) . 'tmp/' . $fileName;
                
                $ch = curl_init( $imageSrc );
                
                if( $ch !== FALSE )
                {
                    $fp = fopen( $filePath , 'wb' );
                    curl_setopt( $ch , CURLOPT_FILE , $fp );
                    curl_setopt( $ch , CURLOPT_HEADER , 0 );
                    curl_exec( $ch );
                    curl_close( $ch ); 
                    fclose( $fp );
                }
            }
            
            if( file_exists( $filePath ) )
            {
                $imageData = file_get_contents( $filePath );
                $fileInfo = new finfo( FILEINFO_MIME );
                $mimeType = $fileInfo->buffer( $imageData );
                
                $newImageSrc = 'data:' . $mimeType . ';base64,' . base64_encode( $imageData );
                $string = str_replace( $imageSrc , $newImageSrc , $string );
            }
            else
            {
                $string = str_replace( $imgTagList[ 0 ][ $index ], get_lang( 'MISSING IMAGE : ' . $filePath ) , $string );
            }
        }
    }
    
    return $string;
}

/**
 * Prepares html content in order to make if suitable for MOODLE xml
 * i.e. : - wraps html content in CDATA tags
 *        - removes tinyMCE shits
 * @param string $string
 * @return string : the baked content
 */
function MOODLEEX_bake( $string )
{
    $output = MOODLEEX_clear( $string );
    
    if( MOODLEEX_is_html( $output ) )
    {
        return '<![CDATA[' . MOODLEEX_process_images( $output ) . ']]>';
    }
    else
    {
        return $output;
    }
}

/**
 * Checks if passed string contains html tags
 * @param string $string : the string to check
 * @return boolean : true if has html stuff in it
 */
function MOODLEEX_is_html( $string )
{
    return preg_match("/<[^<]+>/", $string, $m )
        || strpos( $string , '<' );
}

/**
 * Gets the extension of a file name
 * @param string : $fileName
 * @return string : the isloated extension (like 'gif' or 'txt')
 */
function MOODLEEX_getFileExtension( $fileName )
{
    return strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
}

/*
 * Checks if the passed file name matches with an image file type (jpg, png, gif)
 * @param $fileName
 * @return boolean : true if it's (supposely) an image
 */
function MOODLEEX_is_image( $fileName )
{
    $fileExtension = strtolower( MOODLEEX_getFileExtension( $fileName ) );
    $extensionList = array( 'gif','jpg', 'jpeg', 'png' );
    
    return in_array( $fileExtension , $extensionList );
}

/**
 * Removes TeX code from a passed string
 * @param string $string : the string to clean up
 * @return string : the cleaned up string
 */
function MOODLEEX_clean_tex_content( $string )
{
    preg_match_all('/<img[^>]+>/i', $string , $imgTagList );
    
    foreach( $imgTagList as $imgTag )
    {
        if( ! empty( $imgTag ) && preg_match( '/class="latexFormula"/i' , $imgTag[0] ) )
        {
            preg_match('/<img(.*)alt(.*)=(.*)"(.*)"/U', $imgTag[0] , $texContent );
        }
    }
    
    return str_replace( $imgTagList, $texContent , $string );
}

function MOODLEEX_extract_tex_content( $string )
{
    $mimeTexPath = get_conf( 'claro_texRendererUrl' );
    $stringToMatch = '/' . preg_quote( $mimeTexPath , "/" ) . '?(.*)\"/U';
    preg_match_all( '/\[tex\](.*)\[\/tex\]/U', $string, $texCodeList1 );
    preg_match_all( $stringToMatch, $string, $texCodeList2 );
    $texCodeList = array_merge( $texCodeList1 , $texCodeList2 );
    
    return $texCodeList;
}

/**
 * Processes TeX code in a passed html
 * @param string $string : the html string to process
 * @param boolean $imgEncode : processing mode (false -> render into MoodleTex code / true -> render into a base64 image )
 * @param $string : processed html code
 */
function MOODLEEX_process_tex_content( $string , $imgEncode = false )
{
    $texCodeList = MOODLEEX_extract_tex_content( $string );
    
    if( $imgEncode === true )
    {
        preg_match_all( '/\[tex\](.*)\[\/tex\]/U' , $string, $texCodeList );
        
        $mimeTexPath = get_conf( 'claro_texRendererUrl' );
        $imgTagList = array();
        
        foreach( $texCodeList[ 1 ] as $index => $texCode )
        {
            $imageData = file_get_contents( $mimeTexPath . '?' . $texCode );
            $fileInfo = new finfo( FILEINFO_MIME );
            $mimeType = $fileInfo->buffer( $imageData );
            $imgTagList[] = '<img src="data:'
                . $mimeType
                . ';base64,'
                . base64_encode( $imageData )
                .'" alt="'
                . $texCode
                . '" />';
        }
        
        return str_replace( $texCodeList[ 0 ] , $imgTagList , $string );
    }
    else
    {
        $texTag = array( '[tex]' , '[/tex]' );
        
        return str_replace( $texTag , '$$' , $string );
    }

}

/**
 * Processes links to images within html content
 * It's an helper for two other functions : MOODLEEX_convert_img_src() and MOODLEEX_process_tex_content()
 * @param string $string : the string to process
 * @return string : the processed string
 */
function MOODLEEX_process_images( $string )
{
    return MOODLEEX_convert_img_src( MOODLEEX_process_tex_content( MOODLEEX_clear( $string ) ) );
}

/**
 * Removes spoilers from a passed string or extract them
 * @param string $string : the string to clean up
 * @param boolean $getContent : treatment mode (false -> removes spoilers, true -> extracts spoilers into a array)
 * @return string or array ...depending of second parameter
 */
function MOODLEEX_process_spoilers( $string , $getContent = false )
{
    preg_match_all( '/\[spoiler \/(.*)\/\](.*)\[\/spoiler\]/Ums' , $string , $spoilerList );
    
    if( $getContent === true )
    {
        return $spoilerList[ 2 ];
    }
    else
    {
        return str_replace( $spoilerList[ 0 ] , '' , $string );
    }
}

/**
 * Removes shit tinyMCE puts in its content
 * @param string $string : the string to clean up
 * @return string : a cleaned up string
 */
function MOODLEEX_remove_tinymce_tags( $string )
{
    $string_to_remove = array(
        '<br /><!-- content: html tiny_mce -->',
        '<!-- content: html tiny_mce -->',
        '<!-- content: imsqti -->',
    );
    
    return trim( str_replace( $string_to_remove , '' , html_entity_decode( $string  ) ) );
}

/**
 * Removes M$-encoded quotes
 * @param string $string : the string to clean up
 * @return string : a cleaned up string
 */
function MOODLEEX_convert_smart_quotes( $string ) 
{ 
    $search = array( chr(145), chr(146), chr(147), chr(148), chr(151) );
    $replace = array( "'", "'", '"', '"', '-' );
    
    return str_replace( $search, $replace, $string ); 
}

/**
 * Check if an url does exist
 * @param string $url
 * @return bolean true if exists
 */
function MOODLEEX_url_exists( $url )
{
    $url = str_replace( "http://", "", $url );
    
    if ( strstr( $url, "/" ) )
    {
        $url = explode( "/", $url, 2 );
        $url[1] = "/" . $url[1];
    }
    else
    {
        $url = array( $url, "/" );
    }

    $fh = fsockopen( $url[0] , 80 );
    
    if( $fh )
    {
        fputs( $fh, "GET " . $url[1] . " HTTP/1.1\nHost:" . $url[0] . "\n\n");
        
        $content = fread( $fh, 22 );
        
        if( $content == "HTTP/1.1 404 Not Found" )
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }

    }
    else
    {
        return FALSE;
    }
}