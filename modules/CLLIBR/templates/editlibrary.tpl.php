<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditLibrary&context=LibraryList' ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <?php if ( $this->libraryId ) : ?>
    <input type="hidden"
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />
    <?php endif; ?>
    <?php echo get_lang( 'Title' ) . ' : '; ?><input type="text" name="title" value="<?php echo $this->title; ?>" /><br />
    <input type="checkbox" name="is_public" <?php if ( $this->is_public ) echo 'checked="checked"'; ?> />
    <?php echo get_lang( 'Public' ); ?><br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?context=LibraryList' ) ) , get_lang( 'Cancel' ) ); ?>
</form>