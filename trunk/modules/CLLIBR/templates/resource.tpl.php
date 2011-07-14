<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<fieldset id="accessResource">
    <legend><?php echo get_lang('Resource'); ?></legend>
<?php if ( $this->read_allowed ) : ?>
    <?php if ( $this->viewer ) : ?>
    <?php echo $this->viewer->render(); ?>
    <?php endif; ?>
    <?php if ( $this->storageType == 'file' ) : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDownload&resourceId='. $this->resourceId ) );?>">
        <?php echo get_lang( 'Download' ); ?>
    </a>
    <?php else : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( $this->url ); ?>">
        <?php echo get_lang( 'Link' ); ?>
    </a>
    <?php endif; ?>
<?php else : ?>
    <strong><?php echo get_lang( 'You are not allowed to access this resource!' ); ?></strong>
<?php endif; ?>
</fieldset>

<fieldset>
    <legend><?php echo get_lang( 'Metadatas' ); ?> :</legend>
    <dl id="metadataList">
        <dt>
            <?php echo get_lang( 'Title' ); ?> :
        </dt>
        <dd>
            <?php echo isset( $this->metadataList[ 'title' ] ) ? $this->metadataList[ 'title' ] : ''; ?>
        </dd>
        <dt>
            <?php echo get_lang( 'Description' ); ?> :
        </dt>
        <dd>
            <?php echo isset( $this->metadataList[ 'description' ] ) ? $this->metadataList[ 'description' ] : ''; ?>
        </dd>
<?php foreach( $this->metadataList as $name => $values ): ?>
    <?php if ( $name != Metadata::TITLE && $name != Metadata::DESCRIPTION && $name != Metadata::KEYWORD ) : ?>
        <dt>
            <label>
                <?php echo get_lang( ucwords( $name ) ); ?> :
            </label>
        </dt>
        <dd>
            <?php echo htmlspecialchars( $values ); ?>
        </dd>
    <?php endif; ?>
<?php endforeach; ?>
<?php if ( array_key_exists( Metadata::KEYWORD , $this->metadataList ) ) : ?>
        <dt>
            <label>
                <?php echo get_lang( 'Keywords' ); ?> :
            </label>
        </dt>
        <dd>
    <?php foreach( $this->metadataList[ Metadata::KEYWORD ] as $keyword ) : ?>
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqSearch&keyword=' . $keyword ) ); ?>">
                <?php echo $keyword; ?>
            </a>&nbsp;
    <?php endforeach; ?>
        </dd>
<?php endif; ?>
    </dl>
</fieldset>