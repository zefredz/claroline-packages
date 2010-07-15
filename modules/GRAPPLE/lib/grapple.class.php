<?php

require_once get_path('incRepositorySys') . '/lib/user.lib.php';

class grapple{
  
  private $soapClient;
  
  private $identifier;
  
  private $tblGrappleResources;
  
  public function __construct()
  {
    load_module_config('GRAPPLE');
    $this->identifier = get_conf( 'platformId' );
    
    $tblNameList = array(
        'lp_grapple_resources'
    );

    // convert to Claroline course table names
    $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
    $this->tblGrappleReources = $tbl_lp_names['lp_grapple_resources'];
  }
  
  /**
   * Connection to the Grapple Event Bus
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return boolean True if connection is ok, false on the other cases
   */
  private function connect()
  {
    
    $wsdl = get_conf('geb_wsdl');
    $option = array("trace" => 1, "exceptions" => 1);
    $soapClient = new SoapClient( $wsdl, $option );
    //$soapClient = '';
    if( $soapClient )
    {
      $this->soapClient = $soapClient;
      return true;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Generic method to send data to GEB with the setUMData method
   *
   * @param string $xml Formated XML String
   * @param int $previousId Previous Grapple Id
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  
  private function sendData( $xml, $previousId, $calledFunction )
  {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <statement>
              <origin>' . $xml . '</origin>
            </statement>';
    //$this->log( $xml );
    $params = array(  'eventListenerID' => $previousId,
                      'method'=>'setUMData',
                      'body'=> $xml
                    );
    
    $result = $this->soapClient->__soapCall( 'eventGEBListenerOperation', array($params) );
    
    $this->log(date('Y-m-d H:i') . "\t" . $previousId . "\t" . $calledFunction . "\t" . $result->idAssignedEvent . "\n" );    

    return $result;
  }
  
  /**
   * Log informations
   *
   * @param text
   */
  private function log( $msg )
  {
    $file = get_module_path( 'GRAPPLE' ) . '/log.txt';
    file_put_contents( $file, $msg, FILE_APPEND );
  }
  
  /**
   * Send data for an access to a course to GEB
   *
   * @param int $userId Id of user
   * @param int $courseId Id of course
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function courseAccess( $userId, $courseId, $itemId, $previousId = 0 )
  {
    if( $this->connect() )
    {      
      $xml = $this->generateXMLCourseAccess( $userId, $courseId, $itemId );
      return $this->sendData( $xml, $previousId, 'courseAccess' );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for an access to a course (Grapple Module)
   *
   * @param int $userId Id of the user
   * @param int $courseId Id of course
   *
   * @author Dimitri Rambout <dim@clarolinet.net>
   * @return string XML content
   */
  private function generateXMLCourseAccess( $userId, $courseId, $itemId )
  {
    $courseData = claro_get_course_data( $courseId );
    $userData = user_get_properties( $userId );
    
    $item = new item();
    $item->load( $itemId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Access to a course</comment>
      <contentype>
        <referential>
          <sourcedid>
            <source>CLAROLINE</source>
            <id>' . $courseId . '_' . $itemId . '</id>
          </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>GrappleDR_0</source>
              <id>' . ( $item->getType() == 'GRAPPLE' ? $item->getSysPath() : '' ) . '</id>
            </sourcedid>
          </referential>
        </contentype>
        <date>
          <typename>
            <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
            <tyvalue xml:lang="en">AccessDate</tyvalue>
          </typename>
          <datetime>' . date( 'c' ) . '</datetime>
        </date>
        <date>
          <typename>
            <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
            <tyvalue xml:lang="en">CreationDate</tyvalue>
          </typename>
          <datetime>' . date( 'c', $courseData[ 'publicationDate' ] ) . '</datetime>
        </date>
        <description>
          <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
        </description>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  /**
   * Send data when a new learning path is created to GEB
   *
   * @param int $userId Id of a user
   * @param int $courseId Id of a course
   * @param int $pathId Id of a learning path
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimtiri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function learningActivityAddition( $userId, $courseId, $itemId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateLearningActivityAddition( $userId, $itemId, $courseId );
      return $this->sendData( $xml, $previousId, 'learningActivityAddition' );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for a learning path creation
   *
   * @param int $userId Id of the user
   * @param int $courseId Id of a course
   * @param int $pathId Id of a learning path
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  private function generateLearningActivityAddition( $userId, $itemId, $courseId )
  {
    $userData = user_get_properties( $userId );
    
    $item = new item();
    $item->load( $itemId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Learning activity addition</comment>
      <contentype>
        <referential>
            <sourcedid>
                <source>CLAROLINE</source>
                <id>' . $courseId . '_' . $itemId .'</id>
            </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>GrappleDR</source>
              <id>' . ( $item->getType() == 'GRAPPLE' ? $item->getSysPath() : '' ) . '</id>
            </sourcedid>            
          </referential>
        </contentype>
        <status>
          <typename>
            <tysource sourcetype="list">Changed, Added, Removed</tysource> 
            <tyvalue>Added</tyvalue>
          </typename>
        </status>
        <activity>
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
            <long xml:lang="en">' . claro_utf8_encode( $item->getDescription(), get_conf( 'charset' ) ) . '</long>
          </description>
        </activity>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  
  /**
   * Send data when an item of a learning path is change to GEB
   *
   * @param int $userId Id of a user
   * @param int $courseId Id of a course
   * @param int $itemId Id of an item in a learning path
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimtiri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function learningActivityChange( $userId, $courseId, $itemId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateLearningActivityChange( $userId, $itemId, $courseId );
      return $this->sendData( $xml, $previousId, 'learningActivityChange' );
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Generate XML for a learning path change
   *
   * @param int $userId Id of the user
   * @param int $courseId Id of a course
   * @param int $itemId Id of an item in a learning path
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  public function generateLearningActivityChange( $userId, $itemId, $courseId )
  {
    $userData = user_get_properties( $userId );
    
    $path = new path();
    $path->load( $pathId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Learning activity change</comment>
      <contentype>
        <referential>
            <sourcedid>
                <source>CLAROLINE</source>
                <id>' . $courseId . '_' . $itemId .'</id>
            </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>GrappleDR</source>
              <id>' . ( $item->getType() == 'GRAPPLE' ?  $item->getSysPath() : '' ) .'</id>
            </sourcedid>
          </referential>
        </contentype>
        <status>
          <typename>
            <tysource sourcetype="list">Changed, Added, Removed</tysource> 
            <tyvalue>Changed</tyvalue>
          </typename>
        </status>
        <activity>
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
            <long xml:lang="en">' . claro_utf8_encode( $item->getDescription(), get_conf( 'charset' ) ) . '</long>
          </description>
        </activity>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  /**
   * Send data when an item of a learning path is deleted to GEB
   *
   * @param int $userId Id of a user
   * @param int $courseId Id of a course
   * @param int $itemId Id of an item in a learning path
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimtiri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function learningActivityDeleted( $userId, $courseId, $itemId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateLearningActivityDeleted( $userId, $itemId, $courseId );
      return $this->sendData( $xml, $previousId, 'learningActivityChange' );
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Generate XML for a item of a learning path deleted
   *
   * @param int $userId Id of the user
   * @param int $courseId Id of a course
   * @param int $itemId Id of a item in a learning path
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  public function generateLearningActivityDeleted( $userId, $itemId, $courseId )
  {
    $userData = user_get_properties( $userId );
    
    $item = new item();
    $item->load( $itemId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Learning activity change</comment>
      <contentype>
        <referential>
            <sourcedid>
                <source>CLAROLINE</source>
                <id>' . $courseId . '_' . $itemId .'</id>
            </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>GrappleDR</source>
              <id>' . ( $item->getType() == 'GRAPPLE' ?  $item->getSysPath() : '' ) .'</id>
            </sourcedid>
          </referential>
        </contentype>
        <status>
          <typename>
            <tysource sourcetype="list">Changed, Added, Removed</tysource> 
            <tyvalue>Removed</tyvalue>
          </typename>
        </status>
        <activity>
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
            <long xml:lang="en">' . claro_utf8_encode( $item->getDescription(), get_conf( 'charset' ) ) . '</long>
          </description>
        </activity>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  /**
   * Send data for a completion of a quiz
   *
   * @param int $userId Id of a user
   * @param int $courseId Id of a course
   * @param int $previousId Previous Grapple Event id
   * @param int $pathId Id of the LP
   * @param int $itemId Id of the item in the LP
   * @param int $attemptId Id of the attempt linked to this itemId
   * @param int $startTime Timestamp of the start of the quiz attempt
   * @param int $stopTime Timestamp of the stop of the quiz attempt
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function quizCompletion( $userId, $courseId, $previousId = 0, $pathId, $itemId, $attemptId, $startTime, $stopTime )
  {
    if( $this->connect() )
    {
      $xml = $this->generateQuizCompletion( $userId, $courseId, $pathId, $itemId, $attemptId, $startTime, $stopTime );
      return $this->sendData( $xml, $previousId, 'quizCompletion' );
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Generate XML for completion of a quiz
   *
   * @param int $userId Id of a user
   * @param int $courseId Id of a course
   * @param int $pathId Id of the LP
   * @param int $itemId Id of an item in the LP
   * @param int $attemptId Id of an attempt
   * @param int $startTime Timestamp of the start of the quiz attempt
   * @param int $stopTime Timestam of the stop of the quiz attempt
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  private function generateQuizCompletion( $userId, $courseId, $pathId, $itemId, $attemptId, $startTime, $stopTime )
  {
    $courseData = claro_get_course_data( $courseId );
    $userData = user_get_properties( $userId );
    
    $itemAttempt = new itemAttempt();
    $itemAttempt->load( $attemptId, $itemId);
    
    $attempt = new attempt();
    $attempt->load( $pathId, $userId );
    
    $item = new item();
    $item->load( $itemId );
    
    $path = new path();
    $path->load( $pathId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Tests/quizzes</comment>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>CLAROLINE</source>
              <id>' . $courseId . '_' . $itemId . '</id>
            </sourcedid>
          </referential>
        </contentype>
        <evaluation>'
        ;
        if( get_conf('grapple_privacy_quiz_starttime') === true )
        {
          $xml .= '
          <date>
            <typename>
              <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
              <tyvalue xml:lang="en">StartDate</tyvalue>
            </typename>
            <datetime>' . date( 'c', $startTime ) . '</datetime>
          </date>
          ';
        }
        if( get_conf('grapple_privacy_quiz_stoptime') === true )
        {
          $xml .= '
          <date>
            <typename>
              <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
              <tyvalue xml:lang="en">StopDate</tyvalue>
            </typename>
            <datetime>' . date( 'c', $stopTime ) . '</datetime>
          </date>
          ';
        }
        if( get_conf('grapple_privacy_quiz_attemptnb') === true )
        {
          $xml .= '
          <noofattempts>' . (int) $attempt->getAttemptNumber() . '</noofattempts>
          ';
        }
        $xml .= '
          <result>
          ';
        if( get_conf('grapple_privacy_quiz_score_total') === true )
        {
            $xml .= '
            <score>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">Total</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreRaw() . '</fielddata>
            </score>
            ';
        }
        if( get_conf('grapple_privacy_quiz_score_min') === true )
        {
            $xml .'
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">MinScore</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreMin() . '</fielddata>
            </interpretscore>
            ';
        }
        if( get_conf('grapple_privacy_quiz_score_max') === true )
        {
            $xml .= '
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">MaxScore</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreMax() . '</fielddata>
            </interpretscore>
            ';
        }
        if( get_conf('grapple_privacy_quiz_score_treshold') === true )
        {
            $xml .= '
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">Treshold</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $item->getCompletionThreshold() . '%</fielddata>
            </interpretscore>
            ';
        }
          $xml .= '
          </result>
          ';
        if( get_conf('grapple_privacy_quiz_title') === true )
        {
          $xml .= '
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
          </description>
          ';
        }
        $xml .= '
        </evaluation>
        <description>
        ';
        if( get_conf('grapple_privacy_quiz_title') === true )
        {
          $xml .= '
          <short xml:lang="en">' . claro_utf8_encode( strtolower( $item->getTitle() ), get_conf( 'charset' ) ) . '.quizScore</short>
          ';
        }
        if( get_conf('grapple_privacy_quiz_description') === true )
        {
          $xml .= '
          <long xml:lang="en">' . claro_utf8_encode( $path->getDescription(), get_conf( 'charset' ) ) . '</long>
          ';
        }
        $xml .='
        </description>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  
  /**
   * Send data when a student is enrolled to a course
   *
   * @param int $userId Id of a user
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function studentEnrollment( $userId, $courseId, $userType, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateStudentEnrollment( $userId, $courseId, $userType );
      return $this->sendData( $xml, $previousId, 'studentEnrollment' );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for course enrolling
   *
   * @param int $userId Id of a user
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  private function generateStudentEnrollment( $userId, $courseId, $userType = 'LEARNER' )
  {
    $userTypesAccepted = array( 'TEACHER', 'LEARNER', 'TUTOR', 'SYSTEM ADMINISTRATOR' );
    
    if( ! in_array( $userType, $userTypesAccepted ) )
    {
      $userType = 'LEARNER';
    }
    
    $userData = user_get_properties( $userId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Student enrollment</comment>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>CLAROLINE</source>
              <id>' . $courseId . '</id>
            </sourcedid>
          </referential>
        </contentype>
      </activity>
      <identification>
        <ext_identification>'. claro_utf8_encode( $userType ) .'</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }
  
  
  /**
   * Send data when a  user login
   *
   * @param int $userId Id of a user
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   *
   */
  public function userLogin( $userId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateUserLogin( $userId );
      return $this->sendData( $xml, $previousId, 'userLogin' );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for a user login
   *
   * @param int $userId Id of a user
   * 
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  private function generateUserLogin( $userId )
  {
    $userData = user_get_properties( $userId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>User login</comment>
      <contentype>
        <referential>
          <sourcedid>
            <source>CLAROLINE</source>
            <id>' . $this->identifier . '</id>
          </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>GrappleURL_0</source>
              <id></id>
            </sourcedid>
          </referential>
        </contentype>
      </activity>      
      <identification>
        <comment xml:lang="en">IPADRESS</comment>
        <ext_identification>' . claro_utf8_encode( $_SERVER[ 'REMOTE_ADDR' ] ) . '</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }
  
  /**
   * Send data when a user create an account
   *
   * @param int $userId Id of a user
   * @param int $previousId Previous Grapple Event Id
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return object Soap object
   */
  public function userRegistration( $userId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateUserRegistration( $userId );
      return $this->sendData( $xml, $previousId, 'userRegistration' );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for a user registration
   *
   * @param int $userId Id of a user
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return string XML content
   */
  private function generateUserRegistration( $userId )
  {
    $userData = user_get_properties( $userId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Registration</comment>
      <contentype>
        <referential>
          <sourcedid>
            <source>CLAROLINE</source>
            <id>' . $this->identifier . '</id>
          </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>
      <identification>
        <name>
          <partname>
            <typename>
              <tysource sourcetype="imsdefault">First,Last,Organisation</tysource>
              <tyvalue xml:lang="en">First</tyvalue>
            </typename>
            <text xml:lang="en">' . claro_utf8_encode( $userData['firstname'], get_conf( 'charset' ) ) . '</text>
          </partname>
          <partname>
            <typename>
              <tysource sourcetype="imsdefault">First,Last,Organisation</tysource>
              <tyvalue xml:lang="en">Last</tyvalue>
            </typename>
            <text xml:lang="en">' . claro_utf8_encode( $userData['lastname'], get_conf( 'charset' ) ) . '</text>
          </partname>
          <partname>
            <typename>
              <tysource sourcetype="imsdefault">First,Last,Organisation</tysource>
              <tyvalue xml:lang="en">Organisation</tyvalue>
            </typename>
            <text xml:lang="en"></text>
          </partname>
        </name>
        <contactinfo>
          <email>' . claro_utf8_encode( $userData['email'], get_conf( 'charset' ) ) . '</email>
        </contactinfo>
        <address>
          <street>
            <streetname xml:lang="en"></streetname>
          </street>
          <city xml:lang="en"></city>
          <region xml:lang="en"></region>
          <country xml:lang="en"></country>
          <postcode xml:lang="en"></postcode>
        </address>
        <demographics>
          <gender gender=""/>
          <date>
            <typename>
              <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
              <tyvalue xml:lang="en">BirthDate</tyvalue>
            </typename>
            <datetime></datetime>
          </date>
        </demographics>
      </identification>
      <accessibility>
        <language>
          <typename>
            <tysource sourcetype="imsdefault">English,Dutch,French,Spanish,German,Italian</tysource>
            <tyvalue xml:lang="en">' . claro_utf8_encode( $userData['language'], get_conf( 'charset' ) ) . '</tyvalue>
          </typename>
        </language>
      </accessibility>
      <identification>
        <comment xml:lang="en">IPADRESS</comment>
        <ext_identification>' . claro_utf8_encode( $_SERVER[ 'REMOTE_ADDR' ] ) . '</ext_identification>
      </identification>
      <identification>
        <ext_identification>Learner</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }
  
  public function userRoleChange( $userId, $courseId, $userType, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateUserRoleChange( $userId, $courseId, $userType );
      return $this->sendData( $xml, $previousId, 'userRoleChange' );
    }
    else
    {
      return false;
    }
  }
  
  private function generateUserRoleChange( $userId, $courseId, $userType )
  {
    $userData = user_get_properties( $userId );
    
    $userTypesAccepted = array( 'TEACHER', 'LEARNER', 'TUTOR', 'SYSTEM ADMINISTRATOR' );
    
    if( ! in_array( $userType, $userTypesAccepted ) )
    {
      $userType = 'LEARNER';
    }
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <comment>Role change</comment>
      <contentype>
        <referential>
          <sourcedid>
            <source>CLAROLINE</source>
            <id>' . $courseId . '</id>
          </sourcedid>
        </referential>
      </contentype>
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>' . claro_utf8_encode( $userData['username'], get_conf( 'charset' ) ) . '</fielddata>
        </keyfields>
      </securitykey>      
      <identification>
        <ext_identification>'. claro_utf8_encode( $userType ) .'</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }
  
  public function requestCoursesList()
  {
    
    //Need to set the time limit to 0 to be sure to get result (will be change when Ajax will be used)
    set_time_limit( 0 );
    
    $courseWsdl = get_conf( 'courses_wsdl' );
    $options=array("trace" => 1, "exceptions" => 1);
    
    $client = new SoapClient( $courseWsdl, $options );
    
    $coursesList = array();
    $coursesList['success'] = false;
    
    try {
      $res = $client->__soapCall(
              'sendmessage',
              array(
                  array(  'request_method' => 'getCourses',
                          'body' => '',
                          'answer_method' => 'getCoursesResponse ',
                          'blocking' => true,
                          'timeout' => 30,
                          'waittimeout' => false
                      )
                  )
              );
    }catch (SoapFault $exception) {
            
      $coursesList['error'] = $client->__getLastResponse();
      //print_r( $exception );
    }
    
    if( ! is_soap_fault( $res ) )
    {
        if( ! empty( $res ) )
        {
            $xml=simplexml_load_string( (string) $res[0] );
            
            $coursesList['success'] = true;
            $coursesList['courses'] = array();
            
            $i = 0;
            
            foreach($xml->activity as $activity){
              $gid = $activity->contentype->temporal->temporalfield->fielddata;
              $uri = $activity->contentype->referential->sourcedid->source;
              $name = $activity->contentype->referential->sourcedid->id;
              $gid = (string) $gid[0];
              
              $coursesList['courses'][ $gid ]['uri'] = (string) $uri[0];
              $coursesList['courses'][ $gid ]['name']  = (string) $name[0];
              $coursesList['courses'][ $gid ]['gid']  = $gid;
              $coursesList['courses'][ $gid ]['path']  = substr( $coursesList['courses'][ $gid ]['uri'] , 0, strpos( $coursesList['courses'][ $gid ]['uri'] , "://", 10 ) );
              
              $i++;
            }
        }
        else
        {
          $coursesList['error'] = get_lang( 'Unable to get response from GEB' );
        }
    }
    else
    {
      $coursesList['error'] = get_lang( "SOAP Fault: (faultcode: %faultcode, faultstring: %faultstring)", array( '%faultcode' => $res->faultcode, '%faultstring' => $res->faultstring ) );
    }
    
    return $coursesList;
  }

}

class grappleResource
{
  private $tblGrappleResources;
  
  private $id;
  private $uri;
  private $name;
  private $path;
  
  public function __construct()
  {
    $tblNameList = array(
        'lp_grapple_resources'
    );

    // convert to Claroline course table names
    $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
    $this->tblGrappleResources = $tbl_lp_names;
    
    $this->id = null;
    $this->uri = null;
    $this->name = null;
    $this->path = null;
  }
  
  public function load( $id )
  {
    $resource = Claroline::getDatabase()->query("
      SELECT
        *
      FROM
        `" . $this->tblGrappleResources['lp_grapple_resources'] . "`
      WHERE
        `grappleId` = '" . Claroline::getDatabase()->escape( $id ) . "'
      ");
    
    if( $resource->numRows() )
    {
      $resourceData = $resource->fetch();
      
      $this->id = $resourceData[ 'grappleId' ];
      $this->uri = $resourceData[ 'uri' ];
      $this->name = $resourceData[ 'name' ];
      $this->path = $resourceData[ 'path' ];
      
      return true;
    }
    
    return false;
  }
  
  public function save()
  {
    //check if need to insert a new resource or update an existing one.
    $result = Claroline::getDatabase()->query("
      SELECT
        `grappleId`
      FROM
        `" . $this->tblGrappleResources['lp_grapple_resources'] . "`
      WHERE
        `grappleId` = '" . Claroline::getDatabase()->escape( $this->id ) . "'
    ");
    
    if( $result->numRows() )
    {
      //Update
      $result = Claroline::getDatabase()->exec("
        UPDATE
          `" . $this->tblGrappleResources['lp_grapple_resources'] . "`
        SET
          `uri` = '" . Claroline::getDatabase()->escape( $this->uri ) . "',
          `name` = '" . Claroline::getDatabase()->escape( $this->name ) . "',
          `path` = '" . Claroline::getDatabase()->escape( $this->path ) . "'
        WHERE
          `grappleId` = '" . Claroline::getDatabase()->escape( $this->id ) . "'
      ");
      
      if( $result )
      {
        return true;
      }
      else
      {
        return false;
      }      
    }
    else
    {
      //Insert
      $result = Claroline::getDatabase()->exec("
        INSERT
          INTO `" . $this->tblGrappleResources['lp_grapple_resources'] . "`
        SET
          `grappleId` = '" . Claroline::getDatabase()->escape( $this->id ) . "',
          `uri` = '" . Claroline::getDatabase()->escape( $this->uri ) . "',
          `name` = '" . Claroline::getDatabase()->escape( $this->name ) . "',
          `path` = '" . Claroline::getDatabase()->escape( $this->path ) . "'
      ");
      
      if( $result )
      {
        return true;
      }
      else
      {
        return false;
      }      
    }
  }
  
  public function getId()
  {
    return $this->id;
  }
  
  public function setId( $id )
  {
    $this->id = $id;
    
    return $this;
  }
  
  public function getUri()
  {
    return $this->uri;
  }
  
  public function setUri( $uri )
  {
    $this->uri = $uri;
    
    return $this;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function setName( $name )
  {
    $this->name = $name;
    
    return $this;
  }
  
  public function getPath()
  {
    return $this->path;
  }
  
  public function setPath( $path )
  {
    $this->path = $path;
    
    return $this;
  }
}

?>