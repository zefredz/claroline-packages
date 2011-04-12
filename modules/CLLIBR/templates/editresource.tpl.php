<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.4 $Revision$ - Claroline 1.9
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
           name="resourceId"
           value="<?php echo $this->resourceId; ?>" />
    <input type="hidden"
           name="context"
           value="<?php echo $this->context; ?>" />
    <input type="hidden"
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />
    <?php if ( $this->urlAction == 'exAddResource' ): ?>
    <strong><?php echo get_lang( 'Resource type' ) . ' : '; ?></strong>
    <select id="resourceType" name="type">
        <?php foreach( $this->typeList as $type ) : ?>
        <option value="<?php echo $type; ?>">
            <?php echo get_lang( strtolower( $type ) ); ?>
        </option>
        <?php endforeach; ?>
    </select><br />
    <strong><?php echo get_lang( 'Storage type' ) . ' : '; ?></strong>
    <select id="resourceStorage" name="storage">
        <option value="file">
            <?php echo get_lang( 'Local storage of a file' ); ?>
        </option>
        <option value="url">
            <?php echo get_lang( 'External link' ); ?>
        </option>
    </select>
    <span id="resourceSelect">
        <input type="file"
               name="uploadedFile" />
    </span>
    <!--
    <div id="resourceSelect">
        <strong><?php echo get_lang( 'Browse your file' ); ?></strong>
        <input type="file"
               name="uploadedFile" />
    </div>
    <div id="resourceUrl">
        <strong><?php echo get_lang( 'Url of the resource' ); ?></strong>
        <input type="text"
               name="resourceUrl"
               value="" />
    </div>
    -->
    <?php endif; ?>
    <h4><?php echo get_lang( 'Metadatas' ); ?></h4>
    <div id="metadataList">
        <?php if ( $this->urlAction == 'exAddResource' ) : ?>
            <?php foreach( $this->defaultMetadataList as $property ) : ?>
                <?php echo get_lang( ucwords( $property ) ) . ' : '; ?>
        <input type="text"
               name="metadata[<?php echo $property; ?>]" value="" /><br />
            <?php endforeach; ?>
        <?php else : ?>
            <?php foreach( $this->metadataList as $name => $metadata )
                  {
                    foreach( $metadata as $id => $value )
                    {
                        echo get_lang( ucwords( $name ) ) . ' : <input type="text" name="metadata[' . $id . ']" value="' . htmlspecialchars( $value ) . '" /><br />' . "\n";
                    }
                  }
            ?>
        <?php endif; ?>
        <a id="addMetadata" href="#claroBody">
            <span class="claroCmd"><?php echo get_lang( 'Add a new metadata' ); ?></span>
        </a><br />
    </div>
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                  . '?context=' . $this->context . '&'
                                                  . $this->refName . '=' . $this->refId ) ) , get_lang( 'Cancel' ) ); ?>
</form>