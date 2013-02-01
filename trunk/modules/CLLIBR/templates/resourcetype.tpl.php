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

<?php if ( $this->edit ) : ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(".invisible").hide();
        var nbToAdd=0;
        $("#addMetadata").click(function(){
            nbToAdd++;
            var content="<li id=\"newName"+nbToAdd+"\">"+
                        "    <input type=\"text\" name=\"name[n"+nbToAdd+"]\" value=\"\" \/>"+
                        "    <select name=type[n"+nbToAdd+"]>"+
                        "        <option value=\"short\"><?php echo get_lang( 'type_short' ); ?></option>"+
                        "        <option value=\"long\"><?php echo get_lang( 'type_long' ); ?></option>"+
                        "        <option value=\"image\"><?php echo get_lang( 'type_image' ); ?></option>"+
                        "        <option value=\"url\"><?php echo get_lang( 'type_url' ); ?></option>"+
                        "    </select>"+
                        "</li>";
            
            $(".invisible").show();
            $("#metadataList").append(content);
        });
        
        $(".delMetadata").click(function(){
            var index=$(this).attr("id").substr(3);
            $("#metadata"+index).remove();
        });
    });
</script>

<form method="post"
      enctype="multipart/form-data"
      action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exEditResourceType' ) ); ?>">
    <fieldset>
        <legend>
        <?php if ( $this->typeName ) : ?>
            <?php echo get_lang( 'Edition' ) . ' : ' . ucwords( $this->typeName ); ?>
        <?php else : ?>
            <?php echo get_lang( 'New resource type' ); ?>
        <?php endif; ?>
        </legend>
        <?php if ( $this->typeName ) : ?>
        <input type="hidden" name="typeName" value="<?php echo $this->typeName; ?>" />
        <?php else : ?>
        <dl>
            <dt><?php echo get_lang( 'Name' ); ?></dt>
            <dd><input type="text"
                       name="typeName"
                       value="" />
            </dd>
        <?php endif; ?>
        <dl>
            <dt><?php echo get_lang( 'Accepted extensions' ); ?></dt>
            <dd>
                <input type="text"
                       name="extensions"
                       size="80"
                       value="<?php echo implode( ', ' , $this->authorizedFileList ); ?>" />
            </dd>
        </dl>
        <dl>
            <dt><?php echo get_lang( 'Default metadata fields' ); ?></dt>
            <dd>
                <ul id="metadataList"
                    style="list-style: none;">
        <?php $index = 0; ?>
        <?php foreach( $this->defaultMetadataList as $name => $type ) : ?>
                    <li id="metadata<?php echo $index; ?>">
                        <input type="text"
                               name="name[<?php echo $index; ?>]"
                               value="<?php echo $name; ?>" />
                        <select name="type[<?php echo $index; ?>]">
            <?php foreach( array( 'short' , 'long' , 'image' , 'url' ) as $option ) : ?>
                            <option value="<?php echo $option; ?>"
                <?php if ( $option == $type ) : ?>
                                selected="selected"
                <?php endif; ?>>
                            <?php echo get_lang( 'type_' . $option ); ?>
                            </option>
            <?php endforeach; ?>
                        </select>
                        <a id="del<?php echo $index; ?>" class="delMetadata claroCmd" href="#metadata<?php echo $index; ?>">
                                <?php echo get_lang( 'Delete' ); ?>
                        </a>
            <?php $index++; ?>
        <?php endforeach; ?>
                </ul>
                 <a id="addMetadata" href="#claroBody">
                    <span class="claroCmd"><?php echo get_lang( 'Define a new metadata' ); ?></span>
                </a>
            </dd>
        </dl>
    </fieldset>
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo claro_htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'] . '?cmd=rqShowResourceType' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>

<?php elseif ( $this->typeName ) : ?>

<fieldset>
    <legend>
        <?php echo ucwords( get_lang( $this->typeName ) ); ?>
    <?php if ( $this->edit_allowed ) : ?>
        <a title="<?php echo get_lang( 'Edit resource type definition' ); ?>" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditResourceType&typeName='. $this->typeName ) );?>">
            <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
        </a>
    <?php endif; ?>
        </legend>
    <dl>
        <dt><?php echo get_lang( 'Accepted extensions' ); ?></dt>
        <dd><?php echo implode( ', ' , $this->authorizedFileList ); ?></dd>
    </dl>
    <dl>
        <dt><?php echo get_lang( 'Default metadata fields' ); ?></dt>
        <dd>
            <ul style="list-style: none;">
    <?php foreach( $this->defaultMetadataList as $name => $type ) : ?>
                <li><?php echo get_lang( ucwords( $name ) ); ?>    <span class="typeDescription">[<?php echo get_lang( 'type_' . $type ); ?>]</span></li>
    <?php endforeach; ?>
            </ul>
        </dd>
    </dl>
</fieldset>

<?php else : ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Resource type' ); ?></th>
    <?php if( $this->edit_allowed ) : ?>
            <th><?php echo get_lang( 'Actions' ); ?></th>
        </tr>
    <?php endif; ?>
    </thead>
    <tbody>
    <?php foreach( $this->resourceTypeList as $name ) : ?>
        <tr>
            <td><a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowResourceType&typeName=' . $name ) ); ?>"><?php echo get_lang( $name ); ?></a></td>
        <?php if ( $this->edit_allowed ) :?>
            <td align="center">
                <a title="<?php echo get_lang( 'Delete this resource type' ); ?>" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteResourceType&typeName=' . $name ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                <a title="<?php echo get_lang( 'Edit resource type definition' ); ?>" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditResourceType&typeName='. $name ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
            </td>
        <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>