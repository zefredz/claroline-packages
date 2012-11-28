<h3><?php echo get_lang( 'Step 1 : choose the dates' ); ?></h3>
<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' ) .'/timeslot.php?step=1&sessionId=' . $this->sessionId ) ); ?>">
<input id="datePicker"
       type="text"
       class="auto-kal"
       data-kal="lang: '<?php echo get_lang( '_lang_code' ); ?>', mode: 'multiple'"
       name="data"
       value=""
       size="80" />

<input id="dateList" type="hidden" name="data" value="" />
<div id="nextStep">
<input type="submit"
       value="<?php echo get_lang( 'Next step' ); ?>""
</div></form>