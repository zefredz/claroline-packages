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

<fieldset id="accessResource">

    <legend><?php echo get_lang('Resource'); ?></legend>

<?php if ( $this->storageType == 'file' ) : ?>

    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDownload&resourceId='. $this->resourceId ) );?>">
        <?php echo get_lang( 'Download' ); ?>
    </a>

<?php else : ?>

    <a class="claroCmd" href="<?php echo htmlspecialchars( $this->url ); ?>">
        <?php echo get_lang( 'Link' ); ?>
    </a>

<?php endif; ?>

</fieldset>

<fieldset>
    
    <legend><?php echo get_lang( 'Metadatas' ); ?> :</legend>

    <dl id="metadataList">
        <dt>
            <?php echo get_lang( 'Title' ); ?> :
        </dt>
        <dd>
            <?php echo $this->title; ?>
        </dd>
        <dt>
            <?php echo get_lang( 'Description' ); ?> :
        </dt>
        <dd>
            <?php echo $this->description; ?>
        </dd>

    <?php foreach( $this->metadataList as $name => $metadata ): ?>

        <?php foreach( $metadata as $id => $value ): ?>

        <dt>
            <label>
                <?php echo get_lang( ucwords( $name ) ); ?> :
            </label>
        </dt>
        <dd>
            <?php echo htmlspecialchars( $value ); ?>
        </dd>

        <?php endforeach; ?>

    <?php endforeach; ?>

    </dl>

</fieldset>