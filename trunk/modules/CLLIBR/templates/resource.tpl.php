<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<fieldset id="accessResource">
    <legend><?php echo get_lang('Resource'); ?></legend>
<?php if ( $this->is_deleted ) : ?>
    <span class="empty"><?php echo get_lang( 'This resource has been deleted' ); ?></span>
<?php elseif ( $this->read_allowed ) : ?>
    <?php if ( $this->viewer ) : ?>
    <div id="imageView">
        <?php echo $this->viewer->render(); ?>
    </div>
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

<fieldset id="metadatas">
    <legend><?php echo get_lang( 'Metadatas' ); ?></legend>
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
<?php foreach( $this->metadataList as $name => $value ): ?>
    <?php if ( $name != Metadata::TITLE && $name != Metadata::DESCRIPTION && $name != Metadata::KEYWORD && $name != Metadata::TYPE ) : ?>
        <dt>
            <?php echo get_lang( ucwords( $name ) ); ?> :
        </dt>
        <dd>
        <?php if ( isset( $this->defaultmetadataList[ $name ] ) && $this->defaultMetadataList[ $name ] == ResourceType::TYPE_IMAGE ) : ?>
            <img src="<?php echo $value; ?>" alt="<?php echo $name; ?>" />
        <?php elseif ( isset( $this->defaultmetadataList[ $name ] ) && $this->defaultMetadataList[ $name ] == ResourceType::TYPE_URL ) : ?>
            <a href="<?php echo $value; ?>"><?php echo $value; ?></a>
        <?php else : ?>
            <?php echo htmlspecialchars( is_array( $value ) ? implode( ',' , $value ) : $value ); ?>
        <?php endif; ?>
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

<?php if ( ! is_null( $this->userNote ) ) : ?>
<fieldset id="userNote">
    <legend><?php echo get_lang( 'My personnal notes' ); ?></legend>
    <form method="post"
          action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exNote' ) ); ?>" >
        <input type="hidden"
               name="userId"
               value="<?php echo $this->userId; ?>" />
        <input type="hidden"
               name="resourceId"
               value="<?php echo $this->resourceId; ?>" />
        <textarea rows="20" name="content"><?php echo $this->userNote; ?></textarea>
        <br />
        <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'Save' ); ?>" />
    </form>
</fieldset>
<?php endif; ?>