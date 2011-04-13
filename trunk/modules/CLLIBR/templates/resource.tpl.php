<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<div id="accessResource">
<?php if ( $this->storageType == 'file' ) : ?>
<a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDownload&resourceId='. $this->resourceId ) );?>">
    <?php echo get_lang( 'Download' ); ?>
</a>
<?php else : ?>
<a class="claroCmd" href="<?php echo htmlspecialchars( $this->url ); ?>">
    <?php echo get_lang( 'Link' ); ?>
</a>
<?php endif; ?>
</div><br />

<fieldset>
<legend><?php echo get_lang( 'Metadatas' ); ?> :</legend>

<dl id="metadataList">
        <?php 
            foreach( $this->metadataList as $name => $metadata ):
                foreach( $metadata as $id => $value ):
        ?>

    <dt><label><?php echo get_lang( ucwords( $name ) ); ?> :</label></dt>
    <dd>
        <?php echo htmlspecialchars( $value ); ?>
    </dd>

        <?php
                endforeach;
            endforeach;
        ?>

</dl>
</fieldset>