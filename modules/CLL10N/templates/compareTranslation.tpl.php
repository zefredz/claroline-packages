<p>
  <?php echo get_lang( 'The Compare command allow you to check if there is difference between language script variables and language variables in your language file.' ); ?>
  <br />
  <?php echo get_lang( 'It will show you which variable can be deleted in your language file.'); ?>
</p>
<form name="selectLang" id="selectLang" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exCompare&module=' . $this->moduleLabel ) ); ?>" method="post" >
  <p>
    <?php echo get_lang( 'Select language you want to compare :' ); ?>
    <?php echo claro_html_form_select( 'lang', $this->availableLanguages, $this->selectedLanguage, array('id' => 'selectLangOptions'), true); ?>
    <input id="selectLangButton" type="submit" name="<?php echo get_lang( 'Select' ); ?>" />
  </p>
</form>
<?php
if( !is_null( $this->outdatedLangs ) && is_array( $this->outdatedLangs ) ) :
?>
<hr />
<?php
  if( !count( $this->outdatedLangs ) ) :    
?>
<p style="text-align: center;"><?php echo get_lang( 'No deprecated language variables.' ); ?></p>
<?php
  else :
    $this->dialogBox->question( get_lang( 'These language variables seem not used anymore in the module.' ) );
    $this->dialogBox->question( get_lang( 'Do you want to clean the file ? This will remove deprecated variables in the file.' ) . '<br />' . "\n"
    . '<a href="'. htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] .'?cmd=exCleanLangFile&module='. $this->moduleLabel .'&lang='.$this->selectedLanguage )) .'">' . get_lang( 'Yes' ) . '</a>'
    . ' | '
    . '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang( 'No' ) . '</a>' );
    echo $this->dialogBox->render();
?>
<p><?php echo get_lang( 'These language variables seem not used anymore in the module.' ); ?></p>
<table>
  <tbody>
<?php
    foreach( $this->outdatedLangs as $key => $value) :
?>
    <tr>
      <td><?php echo $key; ?></td>
      <td>=&gt;</td>
      <td><?php echo $value; ?></td>
    </tr>  
<?php
    endforeach;
?>
  </tbody>
</table>
<?php
  endif;
endif;
?>
<script type="text/javascript">
  $(document).ready( function()
  {
    $("#selectLangButton").hide();
    $("#selectLangOptions").change(function()
    {
      $("#selectLang").submit();
    });
  });
</script>