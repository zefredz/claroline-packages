<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
  <thead>
    <tr class="headerX" align="center" valign="top">
      <th scope="col" id="c0" ><?php echo get_lang('Module name'); ?></th>
      <th scope="col" id="c1" ><?php echo get_lang('Generate'); ?></th>
      <th scope="col" id="c2" ><?php echo get_lang('Compare'); ?></th>
      <!--th scope="col" id="c3" ><?php echo get_lang('Edit'); ?></th-->
      <th scope="col" id="c4" ><?php echo get_lang('Progression'); ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td headers="c0">
        <img src="<?php echo get_icon( 'claroline' ); ?>" alt="" />
        <?php echo get_lang( 'Platform' ); ?>
      </td>
      <td style="text-align: center;" headers="c1">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=PLATFORM' ) ); ?>"><img src="<?php echo get_icon('translate_add'); ?>" alt="<?php echo get_lang('Generate language files'); ?>" title="<?php echo get_lang('Generate language files'); ?>" /></a>
      </td>
      <td style="text-align: center;" headers="c2">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqCompare&module=PLATFORM' ) ); ?>"><img src="<?php echo get_icon('translate_compare'); ?>" alt="<?php echo get_lang('Compare script and language files'); ?>" title="<?php echo get_lang('Compare script and language files'); ?>" /></a>
      </td>
      <!--td style="text-align: center;" headers="c3">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqEdit&module=PLATFORM' ) ); ?>"><img src="<?php echo get_icon('translate_edit'); ?>" alt="<?php echo get_lang('Edit language files'); ?>" title="<?php echo get_lang('Edit language files'); ?>" /></a>
      </td-->
      <td style="text-align: center;" headers="c4">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqProgression&module=PLATFORM' ) ); ?>"><img src="<?php echo get_icon('translate_progression'); ?>" alt="<?php echo get_lang('Progression'); ?>" title="<?php echo get_lang('Progression'); ?>" /></a>
      </td>
<?php
if( !empty( $this->moduleList ) ) :
foreach( $this->moduleList as $id => $module) :
?>
    <tr>
      <td headers="c0">
      <img src="<?php echo $module['icon']; ?>" alt="" />
      <?php echo $module['name']; ?>
      </td>
      <td style="text-align: center;" headers="c1">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=' . $id) ); ?>"><img src="<?php echo get_icon('translate_add'); ?>" alt="<?php echo get_lang('Generate language files'); ?>" title="<?php echo get_lang('Generate language files'); ?>" /></a>
      </td>
      <td style="text-align: center;" headers="c2">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqCompare&module=' . $id ) ); ?>"><img src="<?php echo get_icon('translate_compare'); ?>" alt="<?php echo get_lang('Compare script and language files'); ?>" title="<?php echo get_lang('Compare script and language files'); ?>" /></a>
      </td>
      <!--td style="text-align: center;" headers="c3">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqEdit&module=' . $id ) ); ?>"><img src="<?php echo get_icon('translate_edit'); ?>" alt="<?php echo get_lang('Edit language files'); ?>" title="<?php echo get_lang('Edit language files'); ?>" /></a>
      </td-->
      <td style="text-align: center;" headers="c4">
        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqProgression&module=' . $id) ); ?>"><img src="<?php echo get_icon('translate_progression'); ?>" alt="<?php echo get_lang('Progression'); ?>" title="<?php echo get_lang('Progression'); ?>" /></a>
      </td>
    </tr>
<?php
endforeach;
else:
?>
    <tr colspan="5">
      <td><?php echo get_lang('No results'); ?></td>
    </tr>
<?php
endif;
?>
  </tbody>
</table>