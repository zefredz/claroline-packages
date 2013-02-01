<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<form id="updateResource"
      class="msform"
      method="post"
      enctype="multipart/form-data"
      action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exUpdateResource' ) ); ?>" >
    <input type="hidden"
           name="userId"
           value="<?php echo $this->userId; ?>" />
    <input type="hidden"
           name="resourceId"
           value="<?php echo $this->resource->getId(); ?>" />

    <fieldset id="resourseFile">
        <legend><?php echo get_lang( 'Resource' ); ?> : </legend>
        <dl>
        <?php if ( $this->resource->getStorageType() == Resource::TYPE_FILE ) : ?>
            <dt>
                    <label for="resourceSelect">
                        <span id="storageType">
                            <?php echo get_lang( 'File' ); ?> :
                        </span>
                    </label>
            </dt>
            <dd>
                <span id="resourceSelect">
                    <input type="file"
                           name="uploadedFile" />
                </span>
            </dd>
        </dl>
        <?php else : ?>
            <dt>
                    <label for="resourceSelect">
                        <span id="storageType">
                            <?php echo get_lang( 'Url' ); ?> :
                        </span>
                    </label>
            </dt>
            <dd>
                <span id="resourceSelect">
                    <input type="text"
                           size="60"
                           name="resourceUrl"
                           value="<?php echo $this->resource->getName(); ?>"/>
                </span>
            </dd>
        </dl>
        <?php endif; ?>
    </fieldset>
    
    <div id="update">
        <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
        <a style="text-decoration: none;" href="<?php echo claro_htmlspecialchars( Url::Contextualize(
            $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $this->resource->getId() ) ); ?>">
            <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
        </a>
    </div>
</form>