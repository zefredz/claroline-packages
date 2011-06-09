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

<form class="msform" method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <input type="hidden"
           name="resourceId"
           value="<?php echo $this->resourceId; ?>" />
    <input type="hidden"
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />

    <fieldset>
    <legend><?php echo get_lang( 'Metadatas' ); ?> :</legend>

    <dl id="metadataList">
        <dt>
            <?php echo get_lang( 'Title' ); ?> :
        </dt>
        <dd>
            <input type="text" size="48" name="title" value="<?php echo $this->title; ?>" />
        </dd>
        <dt>
            <?php echo get_lang( 'Description' ); ?> :
        </dt>
        <dd>
            <textarea cols="60" rows="8" name="description"><?php echo $this->description; ?></textarea>
        </dd>
        <!-- when adding a new resource -->
        <?php if ( ! empty( $this->defaultMetadataList ) ) : ?>

            <?php foreach( $this->defaultMetadataList as $property ) : ?>

                <dt><?php echo get_lang( ucwords( $property ) ); ?> :</dt>
                <dd><input type="text"
                           size="32"
                           name="add[<?php echo $property; ?>]" value="" /></dd>

            <?php endforeach; ?>

        <!-- when editing an existing resource -->
        <?php else : ?>

            <?php 
                foreach( $this->metadataList as $name => $metadata ):
                    foreach( $metadata as $id => $value ):
            ?>

        <dt id="label<?php echo $id; ?>">
            <label><?php echo get_lang( ucwords( $name ) ); ?> :</label>
        </dt>
        <dd id="value<?php echo $id; ?>">
            <input id="metadata<?php echo $id; ?>"
                   type="text"
                   size="32"
                   name="metadata[<?php echo $id; ?>]"
                   value="<?php echo htmlspecialchars( $value ); ?>" />
            <a id="del<?php echo $id; ?>" class="delMetadata claroCmd" href="#metadata<?php echo $id; ?>">
                    <?php echo get_lang( 'Delete' ); ?>
            </a>
        </dd>

            <?php
                    endforeach;
                endforeach;
            ?>

        <?php endif; ?>
        
        <dt>
            <a id="addKeyword" href="#claroBody">
            <span class="claroCmd"><?php echo get_lang( 'Add an existing keyword' ); ?></span>
            </a>
        </dt>
        <dd>
            <select id="addKeyword" name="keyword">
                <option selected="selected" value=""></option>
            <?php foreach ( $this->keywordList as $keyword ) : ?>
                <option value="<?php echo $keyword[ 'value' ]; ?>"><?php echo $keyword[ 'value' ]; ?></option>
            <?php endforeach; ?>
            </select>
        <dd>
        <dt>
            <span class="claroCmd"><?php echo get_lang( '... or new ones (separated by a comma)' ); ?></span>
        </dt>
        <dd>
            <input type="text" name="keywords" value="" />
        </dd>
        
        <dt>
            <a id="addMetadata" href="#claroBody">
            <span class="claroCmd"><?php echo get_lang( 'Add a new metadata' ); ?></span>
            </a>
        </dt>
        <dt class="invisible">
            <strong><?php echo get_lang( 'Metadata\'s name' ); ?></strong>
        </dt>
        <dd class="invisible">
            <strong><?php echo get_lang( 'Metadata\'s content' ); ?></strong>
        </dd>

    </dl>

    </fieldset>

    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $this->libraryId ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>