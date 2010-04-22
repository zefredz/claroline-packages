<?php 
JavascriptLoader::getInstance()->load('LVSURVEY');
$choiceNum = $this->choiceNum;

$choice = $this->choice;
$optionList = $choice->getOptionList();
$optionTextList = array_map(array('Functions', 'textOf'), $optionList);

$defaultOptions = empty($optionList)?"++;+;-;--":implode(';', $optionTextList);
?>
<div id="divquestionCh<?php echo $choiceNum; ?>">
	<span><?php echo $choiceNum; ?> : </span>
	<input 	name="questionCh<?php echo $choiceNum; ?>" 
			id="questionCh<?php echo $choiceNum; ?>" 
			type="text" 
			value="<?php echo htmlspecialchars($choice->text); ?>" />
	<input 	name="questionChId<?php echo $choiceNum; ?>" 
			id="questionChId<?php echo $choiceNum; ?>" 
			type="hidden" 
			value="<?php echo $choice->id; ?>" />
	<span id="optionBlocForChoice<?php echo $choiceNum; ?>"> 
		<span><?php echo get_lang('Options'); ?> : </span>
		<input 	name="optionsForChoice<?php echo $choiceNum; ?>" 
				id="optionsForChoice<?php echo $choiceNum; ?>" 
				type="text" 
				value="<?php echo htmlspecialchars($defaultOptions); ?>" 
				onFocus="clearText(this)" 
	            onBlur="clearText(this)"/>
	 </span>            
</div>