<?xml version="1.0" encoding="utf-8"?>
<documentList>
<pubDate><?php echo Claro_Utils_Time::dateToDatetime(); ?></pubDate>
<iso8601><?php echo Claro_Utils_Time::dateToIso8601(); ?></iso8601>
<?php
foreach( $this->documents as $thisAction ):
    $thisDoc = $this->actionMapper->hasOne( $thisAction, 'document' ); 
	$courseData = claro_get_course_data( $thisAction->courseId );
    
    if ( $thisAction->action != 'delete' ):
    
?>
<document id="<?php echo $thisDoc->id; ?>">
<title><?php echo iconv( get_conf('charset'), 'utf-8', $thisDoc->title ); ?></title>
<hash><?php echo $thisAction->documentHash; ?></hash>
<path><?php echo $thisAction->documentLocalPath; ?></path>
<length><?php echo $thisDoc->length; ?></length>
<course code="<?php echo iconv( get_conf('charset'), 'utf-8', $courseData['officialCode'] ); ?>" 
        id="<?php echo $thisAction->courseId; ?>" 
        titular="<?php echo iconv( get_conf('charset'), 'utf-8', $courseData['titular'] ); ?>">
<?php
    echo iconv( get_conf('charset'), 'utf-8', $courseData['name'] );
?></course>
<action><?php echo $thisAction->action; ?></action>
<date><?php echo $thisAction->timestamp; ?></date>
<iso8601><?php echo Claro_Utils_Time::dateToIso8601($thisAction->timestamp); ?></iso8601>
<publisher><?php 
    $userData = user_get_properties( $thisAction->userId );
    echo iconv( get_conf('charset'), 'utf-8', $userData['firstname'] . ' ' . $userData['lastname'] ); 
?></publisher>
<downloadUrl><?php echo str_replace( get_conf('urlAppend').'/', '', get_path('rootWeb') ) 
    . $_SERVER['PHP_SELF'] . '?serviceKey='.$this->serviceKey.'&amp;cmd=get&amp;id='.$thisDoc->id.'&amp;cidReq='.$thisDoc->courseId; ?></downloadUrl>
</document><?php

    else: 

?>

<document id="<?php echo $thisAction->documentId; ?>">
<hash><?php echo $thisAction->documentHash; ?></hash>
<path><?php echo $thisAction->documentLocalPath; ?></path>
<course code="<?php echo iconv( get_conf('charset'), 'utf-8', $courseData['officialCode'] ); ?>"
        id="<?php echo $thisAction->courseId; ?>"
        titular="<?php echo iconv( get_conf('charset'), 'utf-8', $courseData['titular'] ); ?>">
<?php
    echo iconv( get_conf('charset'), 'utf-8', $courseData['name'] );
?></course>
<action><?php echo $thisAction->action; ?></action>
<date><?php echo $thisAction->timestamp; ?></date>
<iso8601><?php echo Claro_Utils_Time::dateToIso8601($thisAction->timestamp); ?></iso8601>
<publisher><?php 
    $userData = user_get_properties( $thisAction->userId );
    echo iconv( get_conf('charset'), 'utf-8', $userData['firstname'] . ' ' . $userData['lastname'] ); 
?></publisher>
</document>
<?php 

    endif;
    
endforeach; 
?>
</documentList>