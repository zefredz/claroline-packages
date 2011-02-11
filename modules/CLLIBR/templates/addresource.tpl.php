<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exAddResource' ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <input type="hidden"
           name="LibraryId"
           value="<?php echo $this->libraryId; ?>" />
    <input type="hidden"
           name="context"
           value="Catalogue" />
    <?php echo get_lang( 'Title'); ?> : <input type="text" name="title" value="" /><br />
    <?php echo get_lang( 'Author(s)' ); ?> : <input type="text" name="author[0]" value="" /><br />
    <input type="hidden" name="type" value="Book" />
    <input type="hidden" name="storage" value="file" />
    <input type="file"
           name="uploadedFile" /><br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?context=Catalogue' ) ) , get_lang( 'Cancel' ) ); ?>
</form>