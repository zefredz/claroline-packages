<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
  <thead>
    <tr class="headerX" align="center" valign="top">
      <th scope="col" id="c0" ><?php echo get_lang('Module name'); ?></th>
      <th scope="col" id="c1" ><?php echo get_lang('Generate'); ?></th>
      <th scope="col" id="c2" ><?php echo get_lang('Progression'); ?></th>
    </tr>
  </thead>
  <tbody>
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
        <a href="<?php echo htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqGenerate&module=' . $id) ); ?>"><img src="<?php echo get_icon('translate_add'); ?>" alt="<?php echo get_lang('Generate language files'); ?>" title="<?php echo get_lang('Generate language files'); ?>" /></a>
      </td>
      <td style="text-align: center;" headers="c2">
        <a href="<?php echo htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=rqProgression&module=' . $id) ); ?>"><img src="<?php echo get_icon('translate_progression'); ?>" alt="<?php echo get_lang('Progression'); ?>" title="<?php echo get_lang('Progression'); ?>" /></a>
      </td>
    </tr>
<?php
endforeach;
else:
?>
    <tr colspan="3">
      <td><?php echo get_lang('No results'); ?></td>
    </tr>
<?php
endif;
?>
  </tbody>
</table>