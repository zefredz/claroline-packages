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
           name="libraryId"
           value="<?php echo $this->libraryId; ?>" />

    <fieldset id="resourseFile">
        <legend><?php echo get_lang("Resource"); ?> : </legend>
        <dl>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'Resource type' ); ?> :</label>
            </dt>
            <dd>
                <select id="resourceType" name="type">
                <?php foreach( $this->typeList as $type ) : ?>
                <option value="<?php echo $type; ?>">
                    <?php echo get_lang( strtolower( $type ) ); ?>
                </option>
                <?php endforeach; ?>
                </select>
            </dd>
            <dt>
                <label for="resourceStorage"><?php echo get_lang( 'Storage type' ); ?> :</label>
            </dt>
            <dd>
                <select id="resourceStorage" name="storage">
                    <option value="file">
                        <?php echo get_lang( 'Local storage of a file' ); ?>
                    </option>
                    <option value="url">
                        <?php echo get_lang( 'External link' ); ?>
                    </option>
                </select>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <span id="resourceSelect">
                    <input type="file"
                           name="uploadedFile" />
                </span>
            </dd>
        </dl>
        
    <dl>
        <dt>
            <?php echo get_lang( 'Title' ); ?> :
        </dt>
        <dd>
            <input type="text" size="48" name="title" value="" />
        </dd>
        <dt>
            <?php echo get_lang( 'Description' ); ?> :
        </dt>
        <dd>
            <textarea cols="60" rows="8" name="description"></textarea>
        </dd>
    </dl>
    </fieldset>
    
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $this->libraryId ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>
