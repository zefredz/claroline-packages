<?php 
    $dialogBox = new DialogBox();
    $dialogBox->question( get_lang('Are you sure you want to delete results of this survey?')
                            . '<br />' .get_lang('Title of the survey') . ' : '. htmlspecialchars($this->survey->title));
    echo $dialogBox->render();
?>

<table>
    <tr>
        <td>
            <form method="post" action="show_results.php?surveyId=<?php echo $this->survey->id; ?>">
                <input type="submit" name="submit" value="<?php  echo get_lang('Cancel'); ?>" />
             </form>
        </td>
        <td>
            <form method="post" action="show_results.php?surveyId=<?php echo $this->survey->id; ?>">
                <input type="hidden" name="claroFormId" value="<?php  echo uniqid(''); ?>" />
                <input type="hidden" name="cmd" value="reset" />
                <input type="submit" name="submit" value="<?php  echo get_lang('Confirm'); ?>" />
            </form>
        </td>
    </tr>
</table>