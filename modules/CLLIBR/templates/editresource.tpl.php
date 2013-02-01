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

<script type="text/javascript">
    $(document).ready(function(){
        $(".invisible").hide();
        var nbToAdd=0;
        $("#addMetadata").click(function(){
            nbToAdd++;
            var content="<dl><dt><input id=\"newName"+nbToAdd+"\" type=\"text\" name=\"newName["+nbToAdd+"]\" value=\"\" size=\"32\" \/></dt>"+
                        "<dd><input id=\"value"+nbToAdd+"\" type=\"text\" name=\"value["+nbToAdd+"]\" value=\"\" size=\"32\" \/>"+
                        "<a id=\"delx"+nbToAdd+"\" class=\"claroCmd\" href=\"#delx"+nbToAdd+"\">"+
                        " <?php echo get_lang( 'Delete' ); ?>"+
                        "<\/a></dd>"+
                        "<script>"+
                        "    $(\"#delx"+nbToAdd+"\").click(function(){"+
                        "    $(this).parent().parent().remove();"+
                        "    });"+
                        "<\/script></dl>";
            
            $(".invisible").show();
            $("#metadataList").append(content);
        });
        
        $(".delMetadata").click(function(){
            var metadataId = $(this).attr("id").substr(3);
            $("#metadata"+metadataId).attr({name:"del["+metadataId+"]"});
            $("#label"+metadataId).hide();
            $("#value"+metadataId).hide();
        });
        
        $(".delKeyword").click(function(){
            var keywordId = $(this).attr("id").substr(4);
            $("#keyword"+keywordId).attr({name:"kdel["+keywordId+"]"});
            $("#kvalue"+keywordId).hide();
        });
    });
</script>

<form class="msform" method="post"
      enctype="multipart/form-data"
      action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
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
        <dl>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'Resource type' ); ?> :</label>
            </dt>
            <dd>
                <select id="resourceType" name="type">
                <option value="_none_">------</option>
                <?php foreach( $this->typeList as $type ) : ?>
                <option value="<?php echo $type; ?>" <?php if ( $this->resourceType == $type ) echo 'selected="on"'; ?>>
                    <?php echo get_lang( str_replace( '_' , ' ' , $type ) ); ?>
                </option>
                <?php endforeach; ?>
                </select>
            </dd>
        </dl>
        <dl id="metadataList">
            <dt>
                <?php echo get_lang( 'Title' ); ?> :
            </dt>
            <dd>
                <input type="text" size="48" name="title" value="<?php echo isset( $this->metadataList[ 'title' ] ) ? $this->metadataList[ 'title' ] : '' ; ?>" />
            </dd>
            <dt>
                <?php echo get_lang( 'Description' ); ?> :
            </dt>
            <dd>
                <textarea cols="60" rows="8" name="description"><?php echo isset( $this->metadataList[ 'description' ] ) ? $this->metadataList[ 'description' ] : ''; ?></textarea>
            </dd>

<?php $index = 0; ?>

<?php foreach( $this->metadataList as $name => $value ): ?>
    <?php if ( $name != Metadata::TITLE && $name != Metadata::DESCRIPTION && $name != Metadata::KEYWORD && $name != Metadata::COLLECTION && $name != Metadata::TYPE ) : ?>
        <dt id="label<?php echo $index; ?>">
            <label><?php echo get_lang( ucwords( $name ) ); ?> :</label>
            <input id="metadata<?php echo $index; ?>"
                   type="hidden" name="name[<?php echo $index; ?>]" value="<?php echo $name; ?>" />
        </dt>
        <dd id="value<?php echo $index; ?>">
            <?php if ( isset( $this->defaultMetadataList[ $name ] ) && $this->defaultMetadataList[ $name ] == ResourceType::TYPE_LONG ) : ?>
            <textarea cols="60"
                      rows="8"
                      name="metadata[<?php echo $index; ?>]"><?php echo claro_htmlspecialchars( $value ); ?></textarea>
            <?php else : ?>
            <input type="text"
                   size="32"
                   name="metadata[<?php echo $index; ?>]"
                   value="<?php echo claro_htmlspecialchars( $value ); ?>" />
            <?php endif; ?>
            <a id="del<?php echo $index; ?>" class="delMetadata claroCmd" href="#metadata<?php echo $index; ?>">
                    <?php echo get_lang( 'Delete' ); ?>
            </a>
        </dd>
    <?php $index++ ?>
    <?php endif; ?>
<?php endforeach; ?>

<?php foreach( $this->defaultMetadataList as $name => $type ) : ?>
    <?php if( ! array_key_exists( $name , $this->metadataList ) ) : ?>
        <dt id="label<?php echo $index; ?>">
            <label><?php echo get_lang( ucwords( $name ) ); ?> :</label>
            <input id="metadata<?php echo $index; ?>"
                   type="hidden" name="name[<?php echo $index; ?>]" value="<?php echo $name; ?>" />
        </dt>
        <dd id="value<?php echo $index; ?>">
        <?php if ( $type == ResourceType::TYPE_LONG ) : ?>
            <textarea cols="60"
                      rows="8"
                      name="metadata[<?php echo $index; ?>]"></textarea>
        <?php else : ?>
            <input type="text"
                   size="32"
                   name="metadata[<?php echo $index; ?>]" value="" />
            <span class="typeDescription">[<?php echo get_lang( 'type_' . $type ); ?>]</span>
        <?php endif; ?>
        </dd>
    <?php $index++; ?>
    <?php endif; ?>
<?php endforeach; ?>
    </dl>
    <dl>
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
    <legend><?php echo get_lang( 'Keywords' ); ?> :</legend>
    <dl>
<?php if ( array_key_exists( Metadata::KEYWORD , $this->metadataList ) ) : ?>
        <dt>
            <?php echo get_lang( 'Keywords' ); ?> :
        </dt>
        <dd>

    <?php $index = 0; ?>

    <?php foreach( $this->metadataList[ Metadata::KEYWORD ] as $keyword ) : ?>
        <div id="kvalue<?php echo $index; ?>" class="keyword">
            <input id="keyword<?php echo $index; ?>"
                   type="text"
                   size="32"
                   name="keyword[<?php echo $index; ?>]"
                   value="<?php echo claro_htmlspecialchars( $keyword ); ?>" />
            <a id="kdel<?php echo $index; ?>" class="delKeyword claroCmd" href="#keyword<?php echo $index; ?>">
                    <?php echo get_lang( 'Delete' ); ?>
            </a>
        </div>
        <?php $index++; ?>
    <?php endforeach; ?>
        </dd>

<?php endif; ?>
    <?php if ( $this->tagCloud ) : ?>
        <dt>
            <span class="claroCmd"><?php echo get_lang( 'Add an existing keyword' ); ?></span> :
        </dt>
        <dd id="tagCloudAdd">
            <?php echo $this->tagCloud; ?>
        <dd>
    <?php endif; ?>
        <dt>
            <span class="claroCmd"><?php echo get_lang( 'Add new keywords (separated by commas)' ); ?></span> :
        </dt>
        <dd>
            <input type="text"
                   size="60"
                   name="keywords" value="" />
        </dd>
    </dl>
    </fieldset>

    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php if ( $this->libraryId ) : ?>
    <a style="text-decoration: none;" href="<?php echo claro_htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'] . '?cmd=rqShowCatalogue&libraryId='. $this->libraryId ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
    <?php else : ?>
    <a style="text-decoration: none;" href="<?php echo claro_htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'] . '?cmd=rqView&resourceId=' . $this->resourceId ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
    <?php endif; ?>
</form>