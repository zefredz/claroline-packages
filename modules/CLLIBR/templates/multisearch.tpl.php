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
        var itemNb=0;
        $("#addItem").click(function(){
            itemNb++;
            var content='<select id="operator'+itemNb+'" name="searchQuery['+itemNb+'][operator]" >'+
                        '    <option value="AND"><?php echo get_lang( 'AND' ); ?></option>'+
                        '    <option value="OR"><?php echo get_lang( 'OR' ); ?></option>'+
                        '</select><br />'+
                        '<select id="item'+itemNb+'" name="searchQuery['+itemNb+'][name]" >'+
    <?php foreach( MultiSearch::$itemList as $name ) : ?>
                        '    <option value="<?php echo $name; ?>">'+
                        '        <?php echo get_lang( $name ); ?>'+
                        '    </option>'+
    <?php endforeach; ?>
                        '</select>'+
                        '<span> = </span>'+
                        '<input type="text" name="searchQuery['+itemNb+'][value]" value="" />';
            
            $("#items").append(content);
        });
    });
</script>
<strong><?php echo get_lang( 'Advanced search' ); ?>:</strong><br />
<form id="multiSearch" class="multisearch"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqSearch' ) ); ?>"
      method="post">
    <div id="items">
        <select id="item0" name="searchQuery[0][name]" >
        <?php foreach( MultiSearch::$itemList as $name ) : ?>
        <option value="<?php echo $name; ?>">
            <?php echo get_lang( $name ); ?>
        </option>
        <?php endforeach; ?>
        </select>
        <span> = </span>
        <input type="text" name="searchQuery[0][value]" value="" />
    </div>

    <a id="addItem" href="#claroBody">
        <span class="claroCmd"><?php echo get_lang( 'Add an item' ); ?></span>
    </a>
    <br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarylist' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>