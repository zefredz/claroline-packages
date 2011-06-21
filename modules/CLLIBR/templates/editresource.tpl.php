<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".invisible").hide();
        var nbToAdd=0;
        $("#addMetadata").click(function(){
            nbToAdd++;
            var content="<dt><input id=\"name"+nbToAdd+"\" type=\"text\" name=\"name["+nbToAdd+"]\" value=\"\" size=\"32\" \/></dt>"+
                        "<dd><input id=\"value"+nbToAdd+"\" type=\"text\" name=\"value["+nbToAdd+"]\" value=\"\" size=\"32\" \/></dd>"+
                        "<a id=\"delx"+nbToAdd+"\" class=\"claroCmd\" href=\"#delx"+nbToAdd+"\">"+
                        "<\/a>"+
                        "<script>"+
                        "    $(\"#delx"+nbToAdd+"\").click(function(){'"+
                        "    $(this).parent().remove();'"+
                        "    });"+
                        "<\/script>";
            
            $(".invisible").show();
            $("#metadataList").append(content);
        });
        
        $(".delMetadata").click(function(){
            var metadataId = $(this).attr("id").substr(3);
            $("#metadata"+metadataId).attr({name:"del["+metadataId+"]"});
            $("#label"+metadataId).hide();
            $("#value"+metadataId).hide();
        });
    });
</script>

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
                <?php var_dump( $this->resourceId ); ?>
                <input type="text" size="48" name="title" value="<?php echo $this->metadataList[ 'title' ]; ?>" />
            </dd>
            <dt>
                <?php echo get_lang( 'Description' ); ?> :
            </dt>
            <dd>
                <textarea cols="60" rows="8" name="description"><?php echo $this->metadataList[ 'description' ]; ?></textarea>
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
    <?php foreach( $this->metadataList as $name => $value ): ?>
        <?php if ( $name != Metadata::TITLE && $name != Metadata::DESCRIPTION && $name != Metadata::KEYWORD && $name != Metadata::COLLECTION ) : ?>
            <dt id="label<?php echo ucwords( $name ); ?>">
                <label><?php echo get_lang( ucwords( $name ) ); ?> :</label>
            </dt>
            <dd id="value<?php echo ucwords( $name ); ?>">
                <input id="metadata<?php echo ucwords( $name ); ?>"
                       type="text"
                       size="32"
                       name="metadata[<?php echo $name; ?>]"
                       value="<?php echo htmlspecialchars( $value ); ?>" />
                <a id="del<?php echo ucwords( $name ); ?>" class="delMetadata claroCmd" href="#metadata<?php echo ucwords( $name ); ?>">
                        <?php echo get_lang( 'Delete' ); ?>
                </a>
            </dd>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
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
    
    <fieldset>
    <legend><?php echo get_lang( 'Metadatas' ); ?> :</legend>
    <dl>
<?php if ( array_key_exists( Metadata::KEYWORD , $this->metadataList ) ) : ?>
        <dt>
            <?php echo get_lang( 'Keywords' ); ?>
        </dt>
    <?php foreach( $this->metadataList[ Metadata::KEYWORD ] as $keyword ) : ?>
        <dd id="keyword<?php echo ucwords( $keyword ); ?>">
            <input id="keyword<?php echo ucwords( $keyword ); ?>"
                   type="text"
                   size="32"
                   name="keyword[<?php echo $keyword; ?>]"
                   value="<?php echo htmlspecialchars( $keyword ); ?>" />
            <a id="del<?php echo ucwords( $keyword ); ?>" class="delMetadata claroCmd" href="#metadata<?php echo ucwords( $keyword ); ?>">
                    <?php echo get_lang( 'Delete' ); ?>
            </a>
        </dd>
    <?php endforeach; ?>
<?php endif; ?>
    <?php if ( $this->tagCloud ) : ?>
        <dt>
            <span class="claroCmd"><?php echo get_lang( 'Add an existing keyword' ); ?></span>
        </dt>
        <dd id="tagCloudAdd">
            <?php echo $this->tagCloud; ?>
        <dd>
    <?php endif; ?>
        <dt>
            <span class="claroCmd"><?php echo get_lang( 'Add new keywords (separated by commas)' ); ?></span>
        </dt>
        <dd>
            <input type="text"
                   size="60"
                   name="keywords" value="" />
        </dd>
    </dl>
    </fieldset>

    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $this->libraryId ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>