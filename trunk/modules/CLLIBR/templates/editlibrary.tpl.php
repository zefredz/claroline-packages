<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditLibrary' ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <?php if ( $this->libraryId ) : ?>
    <input type="hidden"
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />
    <?php endif; ?>
    <?php echo get_lang( 'Title' ) . ' : '; ?><input type="text" name="title" value="<?php echo $this->title; ?>" /><br />
    <input type="radio"
           name="status"
           value="<?php echo Library::LIB_PRIVATE; ?>"
           <?php if ( $this->status == Library::LIB_PRIVATE ) echo 'checked="checked"'; ?> />
    <?php echo get_lang( '_private' ); ?><br />
    <input type="radio"
           name="status"
           value="<?php echo Library::LIB_RESTRICTED; ?>"
           <?php if ( $this->status == Library::LIB_RESTRICTED ) echo 'checked="checked"'; ?> />
    <?php echo get_lang( '_restricted' ); ?><br />
    <input type="radio"
           name="status"
           value="<?php echo Library::LIB_PUBLIC; ?>"
           <?php if ( $this->status == Library::LIB_PUBLIC ) echo 'checked="checked"'; ?> />
    <?php echo get_lang( '_public' ); ?><br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>