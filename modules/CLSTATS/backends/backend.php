<?php

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

//SECURITY CHECK

if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

FromKernel::uses('utils/input.lib','utils/validator.lib','user.lib');
From::Module('CLSTATS')->uses('stats.lib','courselistiterator.lib');

$userInput = Claro_UserInput::getInstance();
  
$userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
    'generateStats', 'resetCoursesStatus'
) ) );

$cmd = $userInput->get( 'cmd' );

switch( $cmd )
{
    case 'generateStats' :
    {
        $reset = (bool) $userInput->get( 'reset', true );
        $bunchCourses = $userInput->get( 'bunchCourses', null );
        $claroStats = new ClaroStats;
        if( $claroStats->execute( $reset, $bunchCourses ) )
        {
            $json['response'] = get_lang( 'Statistics generated successfully.' );
            $json['success'] = 1;
        }
        else
        {
            $json['response'] = get_lang( 'An error occured during statistics\' generation.');
            $json['success'] = 0;
        }
        
        echo json_encode( $json );
        exit();
    }
    break;
    case 'resetCoursesStatus' :
    {
        Stats_CourseList::init( true );
        echo json_encode( array(   'response' => get_lang( 'Courses\' status reseted' ),
                        'success' => 1
                    )
            );
        exit();
    }
    break;
}


exit();
?>