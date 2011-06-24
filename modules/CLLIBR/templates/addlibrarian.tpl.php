<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.3 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<strong><?php echo get_lang( 'Add an user to this library' ); ?>:</strong>
<form id="searchUser" method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarian&option=add' ) ); ?>">
    <input type="hidden" name="libraryId" value="<?php echo $this->libraryId; ?>" />
    <input type="text" name="searchString" value="" />
    <input type="submit" value="<?php echo get_lang( 'Search' ); ?>" />
    <a href="<?php echo  htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqShowLibrarian&libraryId='. $this->libraryId ) );?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' );?>" />
    </a>
</form>
<br />
<?php if ( ! empty( $this->searchResult ) ): ?>
<strong><?php echo get_lang( 'Result' ); ?>:</strong>
<div>
    <?php foreach( $this->searchResult as $line ) : ?>
    <?php echo $line[ 'firstName' ] . ' ' . $line[ 'lastName' ]; ?>
    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAddLibrarian&libraryId=' . $this->libraryId . '&userId='. $line[ 'userId' ] ) );?>">
        <img src="<?php echo get_icon_url( 'add_librarian' ); ?>" alt="<?php echo get_lang( 'enroll' ); ?>"/>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>