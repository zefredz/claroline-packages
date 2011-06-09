<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<strong><?php echo get_lang( 'Advanced search' ); ?>:</strong><br />
<form class="multisearch"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqSearch' ) ); ?>"
      method="post">
<?php for( $itemNb = 0; $itemNb <= $this->itemNb; $itemNb++ ) : ?>
    <?php if ( $itemNb ) : ?>
    <select id="operator<?php echo $itemNb; ?>" name="searchQuery[<?php echo $itemNb; ?>][operator]" >
        <option value="AND"><?php echo get_lang( 'AND' ); ?></option>
        <option value="OR"><?php echo get_lang( 'OR' ); ?></option>
    </select><br />
    <?php endif; ?>
    
    <select id="item<?php echo $itemNb; ?>" name="searchQuery[<?php echo $itemNb; ?>][name]" >
    <?php foreach( MultiSearch::$itemList as $name ) : ?>
    <option value="<?php echo $name; ?>">
        <?php echo get_lang( $name ); ?>
    </option>
    <?php endforeach; ?>
    </select>
    
    <input type="text" name="searchQuery[<?php echo $itemNb; ?>][value]" value="" />

<?php endfor; ?>
    
    <a id="addItem" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?option=multisearch&itemNb=' . ++$this->itemNb ) ); ?>">
        <span class="claroCmd"><?php echo get_lang( 'Add an item' ); ?></span>
    </a>
    <br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;" href="<?php echo htmlspecialchars( Url::Contextualize(
        $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarylist' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>