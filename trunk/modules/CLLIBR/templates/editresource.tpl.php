<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <input type="hidden"
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />
    <input type="hidden"
           name="resourceId"
           value="<?php echo $this->resourceId; ?>" />
    <input type="hidden"
           name="context"
           value="catalogue" />
    <?php if ( $this->urlAction == 'exAddResource' ): ?>
    <?php echo get_lang( 'Resource type' ) . ' : '; ?>
    <select id="resourceType" name="type">
        <?php foreach( $this->typeList as $type ) : ?>
        <option value="<?php echo $type; ?>">
            <?php echo get_lang( strtolower( $type ) ); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <h4><?php echo get_lang( 'Browse your file' ); ?></h4>
    <input type="file"
           name="uploadedFile" /><br />
    <?php endif; ?>
    <h4><?php echo get_lang( 'Metadatas' ); ?></h4>
    <?php if ( $this->urlAction == 'exAddResource' ) : ?>
        <?php foreach( $this->defaultMetadataList as $property ) : ?>
            <?php echo get_lang( ucwords( $property ) ) . ' : '; ?>
    <input type="text"
           name="metadata[<?php echo $property; ?>]" /><br />
        <?php endforeach; ?>
    <?php else : ?>
        <?php foreach( $this->metadataList as $name => $metadata )
              {
                foreach( $metadata as $id => $value )
                {
                    echo get_lang( ucwords( $name ) ) . ' : <input type="text" name="metadata[' . $id . ']" value="' . $value . '" /><br />';
                }
              }
        ?>
    <?php endif; ?>
    <input type="hidden" name="storage" value="file" />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                  . '?context=catalogue&libraryId='
                                                  . $this->libraryId ) ) , get_lang( 'Cancel' ) ); ?>
</form>