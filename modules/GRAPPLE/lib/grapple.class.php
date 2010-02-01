<?php

require_once get_path('incRepositorySys') . '/lib/user.lib.php';

class grapple{
  
  private $soapClient;
  
  private $identifier;
  
  public function __construct()
  {
    echo $this->identifier = get_conf( 'platformId' );
  }
  
  /**
   * Connection to the Grapple Event Bus
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return boolean True if connection is ok, false on the other cases
   */
  private function connect()
  {
    $wsdl = 'http://dyn070.win.tue.nl:8080/GrappleEventBus/eventGEBListenerService?wsdl';
    $option = array("trace" => 1, "exceptions" => 1);
    
    $soapClient = new SoapClient( $wsdl, $option );
    
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
  
  private function sendData( $xml, $previousId )
  {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <statement>
              <origin>' . $xml . '</origin>
            </statement>';
            
    $params = array(  'eventListenerID' => $previousId,
                      'method'=>'setUMData',
                      'body'=> $xml
                    );
    
    $result = $this->soapClient->__soapCall( 'eventGEBListenerOperation', array($params) );
    
    return $result;
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
  public function courseAccess( $userId, $courseId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateXMLCourseAccess( $userId, $courseId );
      return $this->sendData( $xml, $previousId );
    }
    else
    {
      return false;
    }
  }
  /**
   * Generate XML for an access to a course
   *
   * @param int $userId Id of the user
   * @param int $courseId Id of course
   *
   * @author Dimitri Rambout <dim@clarolinet.net>
   * @return string XML content
   */
  private function generateXMLCourseAccess( $userId, $courseId)
  {
    $courseData = claro_get_course_data( $courseId );
    $userData = user_get_properties( $userId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
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
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier . '</id>
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
          <short xml:lang="en">' . claro_utf8_encode( $courseData[ 'name' ], get_conf( 'charset' ) ) . '</short>
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
  public function learningActivityAddition( $userId, $courseId, $pathId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateLearningActivityAddition( $userId, $pathId );
      return $this->sendData( $xml, $previousId );
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
   * @return object Soap object
   */
  private function generateLearningActivityAddition( $userId, $pathId )
  {
    $userData = user_get_properties( $userId );
    
    $path = new path();
    $path->load( $pathId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
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
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier .'</id>
            </sourcedid>
          </referential>
        </contentype>
        <activity>
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $path->getTitle(), get_conf( 'charset' ) ) . '</short>
            <long xml:lang="en">' . claro_utf8_encode( $path->getDescription(), get_conf( 'charset' ) ) . '</long>
          </description>
        </activity>
      </activity>
    </learnerinformation>';
    
    return $xml;
  }
  /**
   * Not yet supporter
   */
  public function learningActivityChange( $userId )
  {
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>imc_super</fielddata>
        </keyfields>
      </securitykey>
      <activity>
        <contentype>
          <referential>
            <sourcedid>
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier . '</id>
            </sourcedid>
          </referential>
        </contentype>
        <activity>
          <description>
            <short xml:lang="en">KursbuchungInCLIX</short>
            <long xml:lang="en">standard course booking</long>
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
      return $this->sendData( $xml, $previousId );
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
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier . '</id>
            </sourcedid>
          </referential>
        </contentype>
        <evaluation>
          <date>
            <typename>
              <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
              <tyvalue xml:lang="en">StartDate</tyvalue>
            </typename>
            <datetime>' . date( 'c', $startTime ) . '</datetime>
          </date>
          <date>
            <typename>
              <tysource sourcetype="list">AccessDate,CreationDate,StartDate,StopDate,BirthDate</tysource>
              <tyvalue xml:lang="en">StopDate</tyvalue>
            </typename>
            <datetime>' . date( 'c', $stopTime ) . '</datetime>
          </date>
          <noofattempts>' . (int) $attempt->getAttemptNumber() . '</noofattempts>
          <result>
            <score>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">Total</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreRaw() . '</fielddata>
            </score>
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">MinScore</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreMin() . '</fielddata>
            </interpretscore>
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">MaxScore</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $itemAttempt->getScoreMax() . '</fielddata>
            </interpretscore>
            <interpretscore>
              <fieldlabel>
                <typename>
                  <tyvalue xml:lang="en">Treshold</tyvalue>
                </typename>
              </fieldlabel>
              <fielddata>' . (float) $item->getCompletionThreshold() . '%</fielddata>
            </interpretscore>
          </result>
          <description>
            <short xml:lang="en">' . claro_utf8_encode( $item->getTitle(), get_conf( 'charset' ) ) . '</short>
          </description>
        </evaluation>
        <description>
          <short xml:lang="en">' . claro_utf8_encode( $path->getTitle(), get_conf( 'charset' ) ) . '</short>
          <long xml:lang="en">' . claro_utf8_encode( $path->getDescription(), get_conf( 'charset' ) ) . '</long>
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
  public function studentEnrollment( $userId, $previousId = 0 )
  {
    if( $this->connect() )
    {
      $xml = $this->generateStudentEnrollment( $userId );
      return $this->sendData( $xml, $previousId );
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
  private function generateStudentEnrollment( $userId )
  {
    $userData = user_get_properties( $userId );
    
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
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
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier . '</id>
            </sourcedid>
          </referential>
        </contentype>
      </activity>
      <identification>
        <ext_identification>LEARNER</ext_identification>
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
      return $this->sendData( $xml, $previousId );
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
              <source>LMS-CLAROLINE-ID</source>
              <id>' . $this->identifier . '</id>
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
      return $this->sendData( $xml, $previousId );
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
        <ext_identification></ext_identification>
      </identification>
      <identification>
        <ext_identification>Learner</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }
  
  /**
   * Not yet implemented
   */
  public function userRoleChange( $userId )
  {
    $xml =
    '<learnerinformation xml:lang="en" xmlns="http://www.imsglobal.org/xsd/imslip_v1p0">
      <securitykey>
        <keyfields>
          <fieldlabel>
            <typename>
              <tyvalue xml:lang="en">UserName</tyvalue>
            </typename>
          </fieldlabel>
          <fielddata>kclixlearn</fielddata>
        </keyfields>
      </securitykey>
      <identification>
        <ext_identification>Admin</ext_identification>
      </identification>
    </learnerinformation>';
    
    return $xml;
  }

}

?>