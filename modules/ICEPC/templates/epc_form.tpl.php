<form class="msform" action="<?php echo $this->actionUrl; ?>" method="post" id="epcQueryForm">
    <?php echo claro_form_relay_context (); ?>
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION[ 'csrf_token' ]; ?>" />
    <input type="hidden" id="cmd" name="cmd" value="preview" />
    <fieldset id="epcStudentSearch">
        <legend>
            <?php echo get_lang ( 'Search for students in EPC' ); ?>
        </legend>
        <dl>
            <dt><label for="epcAcadYear"><?php echo get_lang ( 'Academic year' ); ?></label></dt>
            <dd><input type="text" name="epcAcadYear" id="epcAcadYear" value="<?php echo htmlspecialchars ( $this->epcAcadYear ); ?>" /><br />
                <em><?php echo get_lang ( 'The year the wanted academic year started on. For instance, if the academic year started on september 2012, enter 2012.' ); ?></em>
            </dd>
            <dt><label for="epcSearchString"><?php echo get_lang ( 'Course or program code' ); ?></label></dt>
            <dd><input type="text" name="epcSearchString" id="epcSearchString" value="<?php echo htmlspecialchars ( $this->epcSearchString ); ?>" /><br />
                <em><?php echo get_lang ( 'The complete official code of the course or program (see help below), for instance : LBIO1111A, LMAPR2016, BIRA21MS/G, BIR13BA' ); ?></em>
                <div class="collapsible collapsed">
                    <a href="#" class="doCollapse"><?php echo get_lang ( 'Help' ) . ' ' . get_lang ( '(click to show/hide)' ); ?></a>
                    <div class="collapsible-wrapper">
                        <blockquote class="info">
                        <?php echo get_lang ( 'UCL course codes are made of 3 elements :  
<ol>
<li>SSSSS : Sigle : up to 5 upper case letters</li>
<li>NNNN : numerical code : 4 digits</li>
<li>D : Subdivision : 0 or 1 upper case letter</li>
</ol>
Sample course code : LBIO1111A, LMAPR2016' ); ?>
                        </blockquote>
                        <blockquote class="info">
                        <?php echo get_lang ( 'The program codes are made of 5 elements :  
<ol>
<li>SSSSS : Sigle : up to 5 upper case letters</li>
<li>C : cycle : 1 digit</li>
<li>N : level (year in the cycle) : 1 digit</li>
<li>LF : final letters : BA, MA, MS, etc : 2 upper case letters</li>
<li>OR : orientation : 0, 1 or 2 uper case letters</li>
</ol>
Usualy a \'/\' is added before the orientation digits : SSSSCNLF/OR<br />
Sample program codes : BIRA21MS/G, BIR13BA' ); ?>
                        </blockquote>
                    </div>
                </div>
            </dd>
            <dt><?php echo get_lang ( 'The given code is' ); ?></dt>
            <dd>&nbsp;</dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcSearchFor" id="epcIsCourse" 
                    <?php echo $this->epcSearchFor == 'course' ? 'checked="checked"' : ''; ?>
                    value="course" /> 
                <label for="epcIsCourse"><?php echo get_lang ( 'a course' ); ?></label>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcSearchFor" id="epcIsProgram"
                    <?php echo $this->epcSearchFor == 'program' ? 'checked="checked"' : ''; ?>
                    value="program" /> 
                <label for="epcIsProgram"><?php echo get_lang ( 'a program' ); ?></label>
            </dd>
        </dl>
    </fieldset>
    <fieldset class="collapsible collapsed" id="advancedOptions">
        <legend>
            <a href="#" class="doCollapse">
                <?php echo get_lang ( 'Advanced options' ) . ' ' . get_lang ( '(click to show/hide)' ); ?>
            </a>
        </legend>
        <dl class="collapsible-wrapper">
            <dt><?php echo get_lang ( 'Link already enroled students' ); ?></dt>
            <dd>
                <em>
                    <?php echo get_lang( 'By doing this students witch are already enroled in your course will be linked to their EPC registration. This will allow you to delete them when deleting an EPC list from your course.If not, they will remain in your course when deleting an EPC list.' ); ?>
                </em>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcLinkExistingStudentsToClass" id="epcLinkExistingStudentsToClass_Yes" 
                    <?php echo $this->epcLinkExistingStudentsToClass == 'yes' ? 'checked="checked"' : ''; ?>
                    value="yes" /> 
                <label for="epcLinkExistingStudentsToClass_Yes"><?php echo get_lang ( 'Yes' ); ?></label> <em>(<?php echo get_lang('recommended'); ?>)</em>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcLinkExistingStudentsToClass" id="epcLinkExistingStudentsToClass_No"
                    <?php echo $this->epcLinkExistingStudentsToClass == 'no' ? 'checked="checked"' : ''; ?>
                    value="no" /> 
                <label for="epcLinkExistingStudentsToClass_No"><?php echo get_lang ( 'No' ); ?></label>
            </dd>
            <dt><?php echo get_lang ( 'Validate pending user enrollments' ); ?></dt>
            <dd>
                <em>
                    <?php echo get_lang( 'Imported users already in your course but with a pending enrollment (i.e. not validated yet) will have their enrollment validated.' ); ?>
                </em>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcValidatePendingUsers" id="epcValidatePendingUsers_Yes" 
                    <?php echo $this->epcValidatePendingUsers == 'yes' ? 'checked="checked"' : ''; ?>
                    value="yes" /> 
                <label for="epcValidatePendingUsers_Yes"><?php echo get_lang ( 'Yes' ); ?></label> <em>(<?php echo get_lang('recommended'); ?>)</em>
            </dd>
            <dt>&nbsp;</dt>
            <dd>
                <input 
                    type="radio" name="epcValidatePendingUsers" id="epcValidatePendingUsers_No"
                    <?php echo $this->epcValidatePendingUsers == 'no' ? 'checked="checked"' : ''; ?>
                    value="no" /> 
                <label for="epcValidatePendingUsers_No"><?php echo get_lang ( 'No' ); ?></label>
            </dd>
        </dl>
    </fieldset>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <input type="submit" name="epcSubmitSearch" value="<?php echo get_lang ( 'Import' ); ?>" />
            <a href="<?php echo Url::Contextualize(get_module_url('ICEPC')); ?>">
                <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'Cancel' ); ?>" />
            </a>
        </dd>
    </dl>
</form>
