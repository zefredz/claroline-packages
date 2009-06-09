<p>
  <?php echo claro_html_menu_horizontal( $this->cmdMenu ); ?>
</p>
<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
  <thead>
    <tr class="headerX" align="center" valign="top">
      <th scope="col" id="c0" ><?php echo get_lang('Lnaguage'); ?></th>
      <th scope="col" id="c1" colspan="2" ><?php echo get_lang('Progression'); ?></th>
    </tr>
  </thead>
  <tbody>
<?php
if( !empty( $this->progression ) ) :
foreach( $this->progression as $lang => $progress) :
?>
    <tr>
      <td headers="c0">
      <?php echo $lang; ?>
      </td>
      <td style="text-align: right;" headers="c1">
        <?php echo claro_html_progress_bar( $progress, 1); ?>
      </td>
      <td headers="c1">
        <?php echo $progress."%"; ?>
      </td>
    </tr>
<?php
endforeach;
else:
?>
    <tr colspan="2">
      <td><?php echo get_lang('Nothing translated yet.'); ?></td>
    </tr>
<?php
endif;
?>
  </tbody>
</table>