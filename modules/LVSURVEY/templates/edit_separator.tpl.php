<form method="post" action="./edit_separator.php" >
    <input type="hidden" name="separatorId" value="<?php  echo $this->separator->id; ?>" />
    <?php if (isset($this->surveyId)):?>
        <input type="hidden" name="surveyId" value="<?php echo $this->surveyId; ?>" />
    <?php endif;?>
    <input type="hidden" name="claroFormId" value="<?php  echo uniqid(''); ?>">
    <input type="hidden" name="cmd" value="SeparatorSave">
    
     <table border="0" cellpadding="5" id="formTable">
        
    <!--  SEPARATOR TITLE -->
    <tr>
        <td valign="top">
            <label for="separatorTitle">
                <?php echo get_lang('Title'); ?>&nbsp;
                <span class="required">*</span>&nbsp;:
            </label>
        </td>
        <td>
            <input  type="text" name="separatorTitle" id="separatorTitle" size="60" maxlength="200" 
                value="<?php echo htmlspecialchars($this->separator->title); ?>" />
        </td>
    </tr>
    
    <!-- SEPARATOR DESCRIPTION -->
     <tr>
         <td valign="top">
             <label for="separatorDescription"><?php echo get_lang('Description'); ?> &nbsp;:</label>
         </td>
         <td>
             <?php echo claro_html_textarea_editor('separatorDescription', $this->separator->description); ?>
         </td>
     </tr>
    
    <!-- SUBMIT -->
    <tr>
        <td>
            &nbsp;
        </td>
        <td>
            <input type="submit" value="<?php echo get_lang('Finish'); ?>" />
        </td>
    </tr>
</table>
</form>