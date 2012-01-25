<?php
    From::module('LVSURVEY')->uses( 'util/functions.class');

    $surveyLine = $this->surveyLine;
    $question = $surveyLine->question;
    $answer = $this->participation->getAnswerForSurveyLine($surveyLine);
    $selectedChoiceList = $answer->getSelectedChoiceList();
    $selectedOptionList = $answer->getSelectedOptionList();
    
    $editIcon       = claro_html_icon('edit',       get_lang('Modify'),         get_lang('Modify'));
    $arrowUpIcon    = claro_html_icon('move_up',    get_lang('Move Up'),        get_lang('Move Up'));
    $arrowDownIcon  = claro_html_icon('move_down',  get_lang('Move Down'),      get_lang('Move Down'));
    $deleteIcon     = claro_html_icon('delete');
?>

<div class="LVSURVEYLine<?php echo $answer->isValid() ? '' : ' invalid'; ?>">
    <input type="hidden" name="questionId<?php echo $question->id; ?>" value="<?php echo $question->id; ?>" />
    <input type="hidden" name="answerId<?php echo $surveyLine->id; ?>" value="<?php echo $answer->id; ?>" />
    <div class="LVSURVEYLineTitle">
        <?php
            if($this->editMode)
            { 
                $urlMoveUp = 'show_survey.php?surveyId='.$surveyLine->survey->id.'&amp;surveyLineId='.$surveyLine->id.'&amp;cmd=lineMoveUp';             
                echo claro_html_link($urlMoveUp, $arrowUpIcon);
                $urlMoveDown = 'show_survey.php?surveyId='.$surveyLine->survey->id.'&amp;surveyLineId='.$surveyLine->id.'&amp;cmd=lineMoveDown';             
                echo claro_html_link($urlMoveDown, $arrowDownIcon);
                $urlEdit = 'edit_question.php?questionLineId='.$surveyLine->id.'&amp;surveyId='.$surveyLine->survey->id;
                echo claro_html_link($urlEdit, $editIcon);
                $urlRemove = 'show_survey.php?surveyId='.$surveyLine->survey->id.'&amp;surveyLineId='.$surveyLine->id.'&amp;cmd=lineRemove';
                echo claro_html_link($urlRemove, $deleteIcon);
            }
            if($surveyLine->isRequired())
            {
                echo '<span class="required">*</span>';
            }
            echo htmlspecialchars($question->text). ' ';
                ?>
                </div>
    <div class="LVSURVEYQuestionContent">
        <?php if ('OPEN' == $question->type) : ?>
            <textarea
                name="choiceText<?php  echo $surveyLine->id; ?>"
                id="choiceText<?php  echo $surveyLine->id; ?>"
                rows="3"
                cols="40"
                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
                ><?php
                        $answerText = empty($selectedChoiceList)?'':reset($selectedChoiceList)->text;
                        echo htmlspecialchars($answerText);
                    ?></textarea>
        <?php endif; ?>
        <?php if ('MCSA' == $question->type) : ?>
            <ul>
                <?php foreach($question->getChoiceList() as $choice) : ?>
                    <li>
                        <input  name="choiceId<?php  echo $surveyLine->id; ?>" 
                                type="radio" 
                                value="<?php  echo $choice->id; ?>" 
                                id="choiceId<?php  echo $surveyLine->id; ?>_<?php  echo $choice->id; ?>"
                                <?php echo in_array($choice->id, array_map(array('Functions', 'idOf'),$selectedChoiceList))?'checked="checked"':''; ?> 
                                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
                        />
                        <label  for="choiceId<?php  echo $surveyLine->id; ?>_<?php  echo $choice->id; ?>">
                            <?php echo htmlspecialchars($choice->text); ?>
                        </label>
                    </li>
                <?php endforeach;?>
             </ul>
        <?php endif; ?>
        <?php if ('MCMA' == $question->type) : ?>
            <ul>
                <?php foreach($question->getChoiceList() as $choice) : ?>
                    <li>
                        <input  name="choiceId<?php  echo $surveyLine->id; ?>[]" 
                                type="checkbox" 
                                value="<?php  echo $choice->id; ?>" 
                                id="choiceId<?php  echo $surveyLine->id; ?>[]_<?php  echo $choice->id; ?>"
                                <?php echo in_array($choice->id, array_map(array('Functions', 'idOf'),$selectedChoiceList))?'checked="checked"':''; ?> 
                                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
                        />
                        <label for="choiceId<?php  echo $surveyLine->id; ?>[]_<?php  echo $choice->id; ?>">
                            <?php echo htmlspecialchars($choice->text); ?>
                        </label>
                    </li>
                <?php endforeach;?>
            </ul>
        <?php endif; ?>
        <?php if ('LIKERT' == $question->type) : ?>
            <ul>
                <?php for( $i = 1; $i <= 5; $i++ ) : ?>
                    <li>
                        <input  name="predefined<?php  echo $surveyLine->id; ?>" 
                                type="radio" 
                                value="__LIKERT_LEVEL_<?php  echo $i ?>__" 
                                id="likertLevel<?php  echo $surveyLine->id; ?>_<?php  echo $i; ?>"
                                <?php echo ( '__LIKERT_LEVEL_' . $i . '__' == $answer->getPredefinedValue() ) ?'checked="checked"':''; ?> 
                                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
                        />
                        <label  for="likertLevel<?php  echo $surveyLine->id; ?>_<?php  echo $i; ?>">
                            <?php echo htmlspecialchars( get_lang( '__LIKERT_LEVEL_' . $i . '__' ) ); ?>
                        </label>
                    </li>
                <?php endfor;?>
             </ul>
        <?php endif; ?>
        <?php if ('ARRAY' == $question->type) : ?>
            <table>
                <?php foreach($question->getChoiceList() as $choice) : ?>
                    <tr>
                        <td><span>
                            <?php echo htmlspecialchars($choice->text); ?> : 
                        </span></td>
                        <?php foreach($choice->getOptionList() as $option) : ?>
                            <td><span>
                                <input  name="choiceId<?php  echo $surveyLine->id; ?>_<?php  echo $choice->id; ?>" 
                                        type="radio" 
                                        value="<?php  echo $option->getId(); ?>"
                                        id="choiceId<?php  echo $surveyLine->id; ?>_<?php  echo $choice->id; ?>_optionId<?php  echo $option->getId(); ?>"
                                        <?php echo in_array($option->getId(), array_map(array('Functions', 'idOf'),$selectedOptionList))?'checked="checked"':''; ?>
                                        <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
                                />
                                <label  for="choiceId<?php  echo $surveyLine->id; ?>_<?php  echo $choice->id; ?>_optionId<?php  echo $option->getId(); ?>">
                                    <?php echo htmlspecialchars($option->getText()); ?>
                                </label>
                            </span></td>
                        <?php endforeach;?>
                    </tr>
                <?php endforeach;?>
            </table>
        <?php endif; ?>
    </div>
    <div class="answerCommentBlock" id="answerCommentBlock<?php echo $surveyLine->id; ?>">
    <?php  if ( $this->editMode) : ?>
        <?php if ($surveyLine->maxCommentSize == 0) : ?>
            <a href="show_survey.php?surveyId=<?php echo $surveyLine->survey->id; ?>&amp;surveyLineId=<?php echo $surveyLine->id; ?>&amp;cmd=setCommentSize&amp;commentSize=200">
                 <?php echo get_lang('Enable comments')?> 
            </a>
        <?php else : ?>
            <a href="show_survey.php?surveyId=<?php echo $surveyLine->survey->id; ?>&amp;surveyLineId=<?php echo $surveyLine->id; ?>&amp;cmd=setCommentSize&amp;commentSize=0">
                 <?php echo get_lang('Disable comments')?>  
            </a>
        <?php  endif; ?>
    <?php endif; ?>
        <?php echo get_lang('Comment'); ?> : 
        <?php if ($surveyLine->maxCommentSize == 0) : ?>
            <?php echo get_lang('No Comments'); ?>
            <input 
                type="hidden"
                name="answerComment<?php echo $surveyLine->id; ?>"
                value="<?php echo $answer->comment; ?>"
                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
            />
        <?php else : ?>
            <input
                maxlength="<?php echo $surveyLine->maxCommentSize; ?>"
                type="text"
                size="70"
                name="answerComment<?php echo $surveyLine->id; ?>"
                id="answerComment<?php echo $surveyLine->id; ?>"
                value="<?php echo $answer->comment; ?>"
                <?php echo $this->allowChange?"":"disabled='disabled'"; ?>
            />
            <span id="commentCharLeft<?php echo $surveyLine->id; ?>" class="commentCharLeft"></span>
            <?php echo get_lang('char(s) left'); ?>
        <?php endif;?>
        </div>
</div>

<script type="text/javascript">
$('#answerComment<?php echo $surveyLine->id; ?>').limit(<?php echo $surveyLine->maxCommentSize; ?>, '#commentCharLeft<?php echo $surveyLine->id; ?>');
</script>