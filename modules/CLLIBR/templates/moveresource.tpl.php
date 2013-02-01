<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<strong><?php echo get_lang( 'Select the library' ); ?>:</strong>
<div>
    <?php foreach( $this->libraryList[ 'user' ] as $id => $datas ) : ?>
    <form id="moveForm<?php echo $id; ?>"
          method="post"
          action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveResource&libraryId=' . $id ) );?>">
        <?php echo $datas[ 'title' ]; ?>
        <?php foreach( array_keys( $this->resourceList ) as $resourceId ) : ?>
        <input type="hidden" name="resource[<?php echo $resourceId; ?>]" value="on" />
        <?php endforeach; ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#formSubmit<?php echo $id; ?>").click(function () {
                $("#moveForm<?php echo $id; ?>").submit();
            });
        });
    </script>
    <a id="formSubmit<?php echo $id; ?>" href="#">
        <img src="<?php echo get_icon_url( 'move' ); ?>" alt="<?php echo get_lang( 'Move' ); ?>"/>
    </a><br />
    </form>
    <?php endforeach; ?>
</div>