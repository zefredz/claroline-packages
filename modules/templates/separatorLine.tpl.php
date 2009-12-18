<?php
$title = $this->surveyLine->title;
$description = $this->surveyLine->description;

$editIcon 		= claro_html_icon('edit', 		get_lang('Modify'), 		get_lang('Modify'));
$arrowUpIcon 	= claro_html_icon('move_up', 	get_lang('Move Up'), 		get_lang('Move Up'));
$arrowDownIcon 	= claro_html_icon('move_down', 	get_lang('Move Down'), 		get_lang('Move Down'));
$deleteIcon		= claro_html_icon('delete');

?>
<div class="LVSURVEYLine">
    <div class="LVSURVEYLineTitle">
        <?php
        if($this->editMode)
        {
            $urlMoveUp = 'show_survey.php?surveyLineId='.$this->surveyLine->id.'&amp;cmd=lineMoveUp&amp;surveyId='.$this->surveyLine->survey->id;
            echo claro_html_link($urlMoveUp, $arrowUpIcon);
            $urlMoveDown = 'show_survey.php?surveyLineId='.$this->surveyLine->id.'&amp;cmd=lineMoveDown&amp;surveyId='.$this->surveyLine->survey->id;
            echo claro_html_link($urlMoveDown, $arrowDownIcon);
            $urlEdit = 'edit_separator.php?surveyLineId='.$this->surveyLine->id.'&amp;surveyId='.$this->surveyLine->survey->id;
            echo claro_html_link($urlEdit, $editIcon);
            $urlRemove = 'show_survey.php?surveyLineId='.$this->surveyLine->id.'&amp;cmd=lineRemove&amp;surveyId='.$this->surveyLine->survey->id;
            echo claro_html_link($urlRemove, $deleteIcon);
        }
        echo htmlspecialchars($title);
        ?>
    </div>
    <div class="LVSURVEYLineDescription">
        <?php echo $description; ?>
    </div>
</div>

