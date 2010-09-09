<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class subscription
{
  /**
   * @var int id Subscription's id
   */
  private   $id,
  /**
   * @var string $title Title
   */
            $title,
  /**
   * @var string $description Description
   */
            $description,
  /**
   * @var string $context Context (user, group)
   */
            $context,
  /**
   * @var string $type Type (unique, multiple, preference, ...)
   */
            $type,
  /**
   * @var string $modifiable Modifiable (modifiable, not_modifiable)
   */
            $modifiable,
  /**
  /**
   * @var string $visibility Visiblity (visibile, invisible)
   */
            $visibility,
  /**
   * @var int $visibilityFrom Visibility start date (timestamp)
   */
            $visibilityFrom,
  /**
   * @var int $visibilityTo Visibility stop date (timestamp)
   */
            $visibilityTo,
  /**
   * @var string $lock Lock (open, close)
   */
            $lock,
  /**
   * @var int $slotsAvailable Number of slots still available
   */
            $slotsAvailable,
  /**
   * @var int $totalSlotsAvailable Slots available
   */
            $totalSlotsAvailable;
  /**
   * @var array $table Database tables
   */
  private $table,
  /**
   * @var string $errors Errors
   */
          $errors;
  /**
   * Constructor
   *
   * @author Dimitri Rambout <dim@claroline.net>
   */
  public function __construct()
  {
    $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
    
    $this->errors = array();
    
    $this->id = null;
    $this->context = 'user';
    $this->type = 'unique';
    $this->visibility = 'visible';
    $this->modifiable = 'modifiable';
    $this->visibilityFrom = null;
    $this->visibilityTo = null;
    $this->lock = 'open';    
  }
  
  /**
   * Save a subscription
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * @return int Affected rows
   */
  public function save()
  {
    $visibilityFrom = isset( $this->visibilityFrom )
                    ? (int) Claroline::getDatabase()->escape( $this->visibilityFrom )
                    : 'NULL';
                    
    $visibilityTo = isset( $this->visibilityTo )
                    ? (int) Claroline::getDatabase()->escape( $this->visibilityTo )
                    : 'NULL';
                    
    $query_fields = "`title` = " . Claroline::getDatabase()->quote( $this->title ) . ",
                    `description` = " . Claroline::getDatabase()->quote( $this->description ) . ",
                    `context` = " . Claroline::getDatabase()->quote( $this->context ) .",
                    `type` = " . Claroline::getDatabase()->quote( $this->type ) . ",
                    `modifiable` = " . Claroline::getDatabase()->quote( $this->modifiable ) . ",
                    `visibility` = " . Claroline::getDatabase()->quote( $this->visibility ) . ",
                    `visibilityFrom` = " . $visibilityFrom . ",
                    `visibilityTo`= " . $visibilityTo . ",
                    `lock` = " . Claroline::getDatabase()->quote( $this->lock );
    
    // New subscription
    if( is_null( $this->id ) )
    {
        $query =    "INSERT INTO
                        `{$this->table['subscr_sessions']}`
                    SET
                        " . $query_fields;
        $result = Claroline::getDatabase()->exec( $query );
        if( $result )
        {
            $this->id = Claroline::getDatabase()->insertId();
        }
        
        return $result;
    }
    else
    {
        $query = "UPDATE
                    `{$this->table['subscr_sessions']}`
                SET
                    " . $query_fields . "
                WHERE
                    `id` = " . (int) $this->id . "
                LIMIT 1";
        
        $result = Claroline::getDatabase()->exec( $query );
        
        return $result;
    }
  }
  
  /**
   * Load a subscription
   *
   * @param int $id Id of the subscription
   * @return mixed False if subscription it unloadable or $this
   */
  public function load( $id )
  {
    $id = (int) $id;
    
    $query =    "SELECT
                    `id`, `title`, `description`, `context`, `type`, `modifiable`, `visibility`, `visibilityFrom`, `visibilityTo`, `lock`
                FROM
                    `{$this->table['subscr_sessions']}`
                WHERE
                    `id` = " . $id . "
                LIMIT 1";
    
    $result = Claroline::getDatabase()->query( $query );
    
    if( ! ( $result && $result->numRows() ) )
    {
        return false;
    }
    
    $subscription = $result->fetch();
    
    $this->id = $subscription['id'];
    $this->title = $subscription['title'];
    $this->description = $subscription['description'];
    $this->type = $subscription['type'];
    $this->modifiable = $subscription['modifiable'];
    $this->context = $subscription['context'];
    $this->visibility = $subscription['visibility'];
    $this->visibilityFrom = $subscription['visibilityFrom'];
    $this->visibilityTo = $subscription['visibilityTo'];
    $this->lock = $subscription['lock'];
    
    $slotsCollection = new slotsCollection();
    
    $this->slotsAvailable = $slotsCollection->getAvailableSlots( $this->id );
    $this->totalSlotsAvailable = $slotsCollection->getTotalAvailableSlots( $this->id );
    
    return $this;
  }
  
  /**
   * Validate a subscription before saving
   *
   * @return boolean True if it's a valide subscription, false in any other case
   */
  public function validate()
  {
    $acceptedContext = array( 'user', 'group' );
    $acceptedType = array( 'unique', 'multiple', 'preference' );
    $acceptedVisibility = array( 'visible', 'invisible' );
    $acceptedLock = array( 'lock', 'unlock' );
    
    if( empty( $this->title ) )
    {
        $this->setError( get_lang( 'The title cannot be empty.' ) );
    }
    
    if ( ! $this->context )
    {
        $this->setError( get_lang( 'You must choice a subscription type' ) );
    }
    elseif( ! in_array( $this->context, $acceptedContext ) )
    {
        $this->setError( get_lang( 'This context doesn\'t exist.' ) );
    }
    
    if ( ! $this->type )
    {
        $this->setError( get_lang( 'You must choice a subscription type' ) );
    }
    elseif( ! in_array( $this->type, $acceptedType ) )
    {
        $this->setError( get_lang( 'This subscription type is not allowed.' ) );
    }
    
    if ( ! $this->visibility )
    {
        $this->setError( get_lang( 'The visibility is not defined.' ) );
    }
    elseif( ! in_array( $this->visibility, $acceptedVisibility ) )
    {
        $this->setError( get_lang( 'This visibility is not allowed.' ) );
    }
    
    if( ! empty( $this->errors ) )
    {
        return false;
    }
    else
    {
        return true;
    }
  }
  
  /**
   * Delete a subscription
   *
   * @param boolean $deleteSlots Indicate if slots need to be deleted
   * @return int Affected rows
   */
  public function delete( $deleteSlots = true )
  {
    //Delete the subscription
    $result = Claroline::getDatabase()->exec( "DELETE FROM
                                        `{$this->table['subscr_sessions']}`
                                    WHERE
                                        `id` = " . (int) $this->id
                                    );
    
    if( $deleteSlots && $result )
    {
        
        //Delete the link between slots and subscribers for this subscription
        Claroline::getDatabase()->exec( "DELETE FROM
                                            `{$this->table['subscr_slots_subscribers']}`
                                        WHERE
                                            `subscriptionId` = " . (int) $this->id
                                        );
        
        //Delete every slots linked to this subescription
        Claroline::getDatabase()->exec( "DELETE FROM
                                            `{$this->table['subscr_slots']}`
                                        WHERE
                                            `subscriptionId` = " . (int) $this->id
                                        );
    }
    
    return $result;
  }
  
  /**
   * Return the subscription as an array
   *
   * @return array Subscription as an array instead of an object
   */
  public function flat()
  {
    $subscription['id'] = $this->id;
    $subscription['title'] = $this->title;
    $subscription['description'] = $this->description;
    $subscription['context'] = $this->context;
    $subscription['type'] = $this->type;
    $subscription['visibility'] = $this->visibility;
    $subscription['visibilityFrom'] = $this->visibilityFrom;
    $subscription['visibilityTo'] = $this->visibilityTo;
    $subscription['isVisible'] = isSubscriptionVisible( $this->visibility, $this->visibilityFrom, $this->visibilityTo );
    $subscription['lock'] = $this->lock;
    $subscription['slotsAvailable'] = $this->slotsAvailable;
    $subscription['totalSlotsAvailable'] = $this->totalSlotsAvailable;
    
    return $subscription;
  }
  /**
   * Setters & Getters
   */
  /**
   * Get id
   *
   * @return int Id
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Get title
   *
   * @return string Title
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Set title
   *
   * @param string $title Title of the Subscription
   * @return object $this
   */
  public function setTitle( $title )
  {
    $this->title = $title;
    
    return $this;
  }
  /**
   * Get description
   *
   * @return string Description
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Set description
   *
   * @param string $description Description of the Subscription
   * @return object $this
   */
  public function setDescription( $description )
  {
    $this->description = $description;
    
    return $this;
  }  
  /**
   * Get context
   *
   * @return string Context (user or group)
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Set context
   *
   * @param string $context Context of the Subscription (user or group)
   * @return object $this
   */
  public function setContext( $context )
  {
    $this->context = $context;
    
    return $this;
  }
  /**
   * Get type
   *
   * @return string Type (unique, multiple, preference)
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Set Type
   *
   * @param string $type Type of the Subscription (unique, multiple, preference)
   * @return object $this
   */
  public function setType( $type )
  {
    $this->type = $type;
    
    return $this;
  }
  /**
   * Get Modifiable
   *
   * @return string Modifiable (modifiable, not_modifiable)
   */
  public function getModifiable()
  {
    return $this->modifiable;
  }
  /**
   * Set Modifiable
   *
   * @param string $modifiable
   * @return $this
   */
  public function setModifiable( $modifiable = 'modifiable' )
  {
    if ( $modifiable != 'modifiable' && $modifiable !='not_modifiable')
    {
        throw new Exception( 'Wrong parameter' );
    }
    $this->modifiable = $modifiable;
    return $this;
  }
  /**
   * Control if the Subscription is modifiable
   * return boolean true if is modifiable
   */
  public function isModifiable()
  {
    return $this->modifiable == 'modifiable';
  }
  /**
   * Get Visibility
   *
   * @return string Visibility (visible, invisible)
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
  /**
   * Set visibility
   *
   * @param string $visibility Visibility of the Subscription (visibile, invisible)
   * @return object $this
   */
  public function setVisibility( $visibility )
  {
    $this->visibility = $visibility;
    
    return $this;
  }
  /**
   * Get starting date of visibility
   *
   * @return int Starting date (timestamp)
   */
  public function getVisibilityFrom()
  {
    return $this->visibilityFrom;
  }
  /**
   * Set starting date of visibility
   *
   * @param int $visibilityFrom Starting date (timestamp)
   * @return object $this
   */
  public function setVisibilityFrom( $visibilityFrom )
  {
    $this->visibilityFrom = (int) $visibilityFrom;
    
    if( $this->visibilityFrom == 0)
    {
        $this->visibilityFrom = null;
    }
    
    return $this;
  }
  /**
   * Get ending date of visibility
   *
   * @return int Ending date (timestamp)
   */
  public function getVisibilityTo()
  {
    return $this->visibilityTo;
  }
  /**
   * Set ending date of visibility
   *
   * @param string $visibilityTo Ending date (timestamp)
   * @return object $this
   */
  public function setVisibilityTo( $visibilityTo )
  {
    $this->visibilityTo = (int) $visibilityTo;
    
    if( $this->visibilityTo == 0)
    {
        $this->visibilityTo = null;
    }
    
    return $this;
  }
  /**
   * Check if the subscription is visible
   *
   * @return boolean False if is invisible or if starting/ending date doesn't match , true in other case
   */
  public function isVisible()
  {
    if( $this->visibility == 'invisible' )
    {
        return false;
    }
    else
    {
        $now = claro_time();
        
        if( ! is_null( $this->visibilityFrom ) && $this->visibilityFrom > $now )
        {
            return false;
        }
        if( ! is_null( $this->visibilityTo ) && $this->visibilityTo < $now )
        {
            return false;
        }
        return true;   
    }
  }
  /**
   * Check if the subscription is invisible
   *
   * @return boolean True if subscription is invisible, false in other case
   */
  public function isInvisible()
  {
    if( $this->visibility == 'invisible' )
    {
        return true;
    }
    
    return false;
  }
  /**
   * Verify if the user with specified id made a choice
   * @param $userId
   * @return boolean
   */
  public function choiceExists( $userId )
  {
    $slotCollection = new slotsCollection( $this->id );
    
    return (boolean)( $slotCollection->getAllFromUser( $userId , $this->getContext() ) );
  }
  /**
   * Get lock of the subscription
   *
   * @return string Open or close
   */
  public function getLock()
  {
    return $this->lock;
  }
  /**
   * Set lock of the subscription
   *
   * @param string $lock Open of close
   * @return object $this
   */
  public function setLock( $lock )
  {
    $this->lock = $lock;
    
    return $this;
  }
  /**
   * Check if the subscription is lockec
   *
   * @return boolean True if lock = close, false in other case
   */
  public function isLocked()
  {
    if( $this->lock == 'close' )
    {
        return true;
    }
    
    return false;
  }
  /**
   * Return errors set during validation
   *
   * @author Dimitri Rambout <dim@claroline.net>
   * 
   * @param boolean $asString Return errors as a string (default: false)
   * @return Array or String
   */
  public function getErrors( $asString = false)
  {
    if( $asString )
    {
        $errors = '';
        foreach( $this->errors as $error )
        {
            $errors .= $error . "\n";
        }
        
        return $errors;
    }
    
    return $this->errors;
  }
  /**
   * Add an error in $errors
   *
   * @param string $error The error
   * @return object $this
   */
  public function setError( $error )
  {
    $this->errors[] = $error;
    
    return $this;
  }
}

/**
 * Subscription Collection
 *
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */
class subscriptionsCollection
{
   /**
    * @var array $table Database tables
    */
   private $table;
   
   /**
    * Constructor
    */
   public function __construct()
   {
        $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
   }
   /**
    * Get the list of all subscriptions
    *
    * @param string $context Specify the context of the list (null will return the entire list)
    * @return ArrayIterator List of subscriptions
    */
   public function getAll( $context = null )
   {
        if( ! is_null( $context ) )
        {
            $acceptedContext = array( 'group', 'user' );
            
            if( ! in_array( $context, $acceptedContext ) )
            {
                $context = null;
            }
        }
        
        $query =    "SELECT
                        s.`id`, s.`title`, s.`description`, s.`context`, s.`type`, s.`modifiable`, s.`visibility`, s.`visibilityFrom`, s.`visibilityTo`, s.`lock`,
                        count( ss.`id` ) as `totalSlotsAvailable`                        
                    FROM
                        `{$this->table['subscr_sessions']}` s
                    LEFT JOIN
                        `{$this->table['subscr_slots']}` ss
                        ON s.`id` = ss.`subscriptionId`
                    "
                    .
                    ( ! is_null( $context ) && ! claro_is_allowed_to_edit() ? " WHERE s.`context` = '" . Claroline::getDatabase()->escape( $context ) . "'" : '' )
                    .
                    "GROUP BY
                        s.`id`
                    ORDER BY
                        s.`id`";
        
        $collection = Claroline::getDatabase()->query( $query );
        
        if( $collection )
        {
            $collection = iterator_to_array( $collection );
        }
        else
        {
            $collection = array();
        }
        
        $slotsCollection = new slotsCollection();
        
        foreach( $collection as $i => $c )
        {
            $collection[ $i ]['isVisible'] = isSubscriptionVisible( $c['visibility'], $c['visibilityFrom'], $c['visibilityTo'] );
            
            
            $slotsAvailable = $slotsCollection->getAvailableSlots( $c['id'] );
            
            $collection[ $i ]['slotsAvailable'] = $slotsAvailable;
        }        
        
        return  new ArrayIterator( $collection );
   }
}
/**
 * Slot
 * 
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */
class slot
{
    protected $id, $subscriptionId, $title, $description, $availableSpace, $visibility;
    
    protected $table;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = null;
        $this->subscriptionId = null;        
        $this->title = '';
        $this->description = '';
        $this->availableSpace = null;
        $this->visibility = 'visible';
        
        $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
    }
    /**
     * Used to manage dynamically Setters and Getters
     *
     * @param string $name function's name
     * @param array $arguments Array of arguments (used for the setter)
     * @return mixed $this if setter, value of the requested variable if getter
     */
    public function __call( $name, $arguments )
    {
        //setter
        if( strpos( $name, 'set' ) !== false && ( strpos( $name, 'set' ) ) === 0 )
        {
            $var = lcfirst( substr( $name, 3, strlen( $name ) ) );
            $this->$var = $arguments[0];
            
            return $this;
        }
        //getter
        elseif( strpos( $name, 'get' ) !== false && ( strpos( $name, 'get' ) ) == 0 )
        {
            $var = lcfirst( substr( $name, 3, strlen( $name ) ) );
            
            return $this->$var;
        }
        
        return false;
    }
    /**
     * Validate a slot before saving it in database
     *
     * @return boolean
     */
    public function validate()
    {
        if( empty( $this->title ) )
        {
            return false;
        }
        
        if( (int) $this->availableSpace <= 0 )
        {
            return false;
        }
        
        return true;
    }
    /**
     * Load a slot from database
     *
     * @param int $slotId Slot's id
     * @return mixed Boolen if the slot cannot be loaded, $this in other case
     */
    public function load( $slotId )
    {
        $slotId = (int) $slotId;
        
        $query =    "SELECT
                        `id`, `subscriptionId`, `title`, `description`, `availableSpace`, `visibility`
                    FROM
                        `{$this->table['subscr_slots']}`
                    WHERE
                        `id` = " . $slotId . "
                    LIMIT 1"
                    ;
        
        $result = Claroline::getDatabase()->query( $query );
    
        if( ! ( $result && $result->numRows() ) )
        {
            return false;
        }
        
        $slot = $result->fetch();
        
        $this->id = $slot['id'];
        $this->subscriptionId = $slot['subscriptionId'];
        $this->title = $slot['title'];
        $this->description = $slot['description'];
        $this->availableSpace = $slot['availableSpace'];
        $this->visibility = $slot['visibility'];
        
        return $this;
    }
    /**
     * Save a slot in database
     *
     * @return int Affected rows
     */
    public function save()
    {
        if( is_null( $this->id ) )
        {
            $query =    "INSERT INTO
                            `{$this->table['subscr_slots']}`
                        SET
                            `subscriptionId` = '" . Claroline::getDatabase()->escape( $this->subscriptionId ) . "',
                            `title` = '" . Claroline::getDatabase()->escape( $this->title ) . "',
                            `description` = '" . Claroline::getDatabase()->escape( $this->description ) . "',
                            `availableSpace` = " . (int) $this->availableSpace . ",
                            `visibility` = '" . Claroline::getDatabase()->escape( $this->visibility ) . "'"
                        ;
            
            $result = Claroline::getDatabase()->exec( $query );
            if( $result )
            {
                $this->id = Claroline::getDatabase()->insertId();
            }
            
            return $result;
        }
        else
        {
            $query =    "UPDATE
                            `{$this->table['subscr_slots']}`
                        SET
                            `subscriptionId` = '" . Claroline::getDatabase()->escape( $this->subscriptionId ) . "',
                            `title` = '" . Claroline::getDatabase()->escape( $this->title ) . "',
                            `description` = '" . Claroline::getDatabase()->escape( $this->description ) . "',
                            `availableSpace` = " . (int) $this->availableSpace . ",
                            `visibility` = '" . Claroline::getDatabase()->escape( $this->visibility ) . "'
                        WHERE
                            `id` = " . (int) $this->id ."
                        LIMIT 1"
                        ;
            
            $result = Claroline::getDatabase()->exec( $query );
            
            return $result;
        }
    }
    /**
     * Delete a slot in database
     *
     * @return boolean False if the slot cannot be deleted, true in other case
     */
    public function delete()
    {
        $result = Claroline::getDatabase()->exec(   "DELETE FROM
                                                        `{$this->table['subscr_slots_subscribers']}`
                                                    WHERE
                                                        `slotId` = " . (int) $this->id
                                                    );
        if( $result === false )
        {
            return false;
        }
        //Delete every slots linked to this subescription
        $result = Claroline::getDatabase()->exec(   "DELETE FROM
                                                        `{$this->table['subscr_slots']}`
                                                    WHERE
                                                        `id` = " . (int) $this->id
                                                    );
        
        if( $result === false )
        {
            return false;
        }
        
        return true;
    }
    /**
     * Get the space available in a slot
     *
     * @return int Available space in the slot
     */
    public function spaceAvailable()
    {
        $totalSubscribers = $this->totalSubscribers();
        return $this->availableSpace - $totalSubscribers;
    }
    /**
     * Get the total of subscribers in the slot
     *
     * return int total of subscribers
     */
    public function totalSubscribers()
    {
        $query =    "SELECT
                        count( `subscriberId` ) as `subscribersCount`
                    FROM `{$this->table['subscr_slots_subscribers']}`
                    WHERE
                        `slotId` = " . (int) $this->id;
        
        $result = Claroline::getDatabase()->query( $query );
        
        if( ! $result->numRows() )
        {
            return 0;
        }
        
        $data = $result->fetch();
        
        return $data['subscribersCount'];
    }
    /**
     * Check if the slot if visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        if( $this->visibility == 'visible' )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
}
/**
 * Slot collection
 * 
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */
class slotsCollection
{
    private $table;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
    }
    /**
     * Get all slots of a subscription
     *
     * @param int $subscriptionId Id of a subscription
     * @return ArrayIterator List of all slots of the subscription
     */
    public function getAll( $subscriptionId )
    {
        $subscriptionId = (int) $subscriptionId;
        
        $query =    "SELECT
                        s.`id`,
                        s.`subscriptionId`,
                        s.`title`,
                        s.`description`,
                        s.`availableSpace`,
                        s.`visibility`,
                        count( ss.`slotId` ) as `subscribersCount`                        
                    FROM
                        `{$this->table['subscr_slots']}` s
                    LEFT JOIN
                        `{$this->table['subscr_slots_subscribers']}` ss
                        ON s.`id` = ss.`slotId`
                    WHERE
                        s.`subscriptionId` = " . $subscriptionId . "
                    GROUP BY
                        ( s.`id` )
                    ORDER BY
                        s.`id`";
        
        $collection = Claroline::getDatabase()->query( $query );
        
        if ( ! $collection )
        {
            $collection = new ArrayIterator( array() );
        }
        
        return $collection;
    }
    /**
     * Get all slots from specific context
     *
     * @param int $subscriptionId Id of a subscription
     * @param string $context Subscription's context
     * @return array List of all slots
     */
    public function getAllFromUsers( $subscriptionId, $context )
    {
        $subscriptionId = (int) $subscriptionId;
        
        $query =    "SELECT
                        sl_sub.`subscriptionId`, sl_sub.`slotId`, sl_sub.`subscriberId`, slot.`title`, sub.`typeId`, sub.`type`
                    FROM
                        `{$this->table['subscr_subscribers']}` sub
                    JOIN
                        `{$this->table['subscr_slots_subscribers']}` sl_sub
                        ON
                            sub.`id` = sl_sub.`subscriberId`
                    JOIN
                        `{$this->table['subscr_slots']}` slot
                        ON
                            sl_sub.`slotId` = slot.`id`
                    WHERE
                        sub.`type` = '" . Claroline::getDatabase()->escape( $context ) . "' AND sl_sub.`subscriptionId` = " . $subscriptionId
                    ;
        
        $collection = Claroline::getDatabase()->query( $query );
        
        $slots = array();
        
        foreach( $collection as $c )
        {
            switch( $c['type'] )
            {
                case 'group' :
                    {
                        $groupData = claro_get_group_data( array( CLARO_CONTEXT_COURSE => claro_get_current_course_id(), CLARO_CONTEXT_GROUP => $c['typeId'] ) );
                        $c['subscriberData'] = $groupData;
                    }
                    break;
                case 'user' :
                    {
                        $userData = user_get_properties( $c['typeId'] );
                        $c['subscriberData'] = $userData;
                    }
                    break;
            }
            $slots[ $c['slotId'] ][ $c['subscriberId'] ] = $c;
            
        }
        
        return $slots;
    }
    /**
     * Get all slots from a specif user in a specif context
     *
     * @param int $userId Id of a user
     * @param string $collection Subscription's context
     * @return array List of all slots
     */
    public function getAllFromUser( $userId, $context )
    {
        $userId = (int) $userId;
        
        $query =    "SELECT
                       sl_sub.`subscriptionId`, sl_sub.`slotId`, sl_sub.`subscriberId`, slot.`title`
                    FROM
                        `{$this->table['subscr_subscribers']}` sub
                    JOIN
                        `{$this->table['subscr_slots_subscribers']}` sl_sub
                        ON
                            sub.`id` = sl_sub.`subscriberId`
                    JOIN
                        `{$this->table['subscr_slots']}` slot
                        ON
                            sl_sub.`slotId` = slot.`id`
                    WHERE
                        sub.`type` = '" . Claroline::getDatabase()->escape( $context ) ."'
                        AND
                        sub.`typeId` = " . $userId
                    ;
        
        $collection = Claroline::getDatabase()->query( $query );
        
        $slots = array();
        
        foreach( $collection as $c )
        {
            $slots[ $c['subscriptionId'] ][ $c['slotId'] ] = $c;            
        }        
        
        return $slots;
    }
    /**
     * Get number of slots available in a subscription
     *
     * @param int $subscriptionId Id of a subscription
     * @return int total of slots available
     */
    public function getAvailableSlots( $subscriptionId )
    {
        $slotsAvailable = 0;
        
        $query =    "SELECT                            
                        (ss.`availableSpace` - count( sl_sub.`subscriberId` ) ) as `slotsAvailable`
                    FROM
                        `{$this->table['subscr_slots']}` ss
                    LEFT JOIN
                        `{$this->table['subscr_slots_subscribers']}` sl_sub
                        ON ss.`id` = sl_sub.`slotId`
                    WHERE
                        ss.`subscriptionId` = " . (int) $subscriptionId ."
                    GROUP BY
                        ss.`id`
                    ";
        
        $collection = Claroline::getDatabase()->query( $query );
         
        if( $collection )
        {
            foreach( $collection as $c )
            {
               if( $c['slotsAvailable'] > 0 )
               {
                    $slotsAvailable++;
               }
            }
        }
        
        return $slotsAvailable;
    }
    
    public function getTotalAvailableSlots( $subscriptionId )
    {
        $result = Claroline::getDatabase()->query(
                    "SELECT
                        count( `id` ) as `totalSlotsAvailable`
                    FROM
                        `{$this->table['subscr_slots']}`
                    WHERE
                        `subscriptionId` = " . (int) $subscriptionId
                    );
        
        $data = $result->fetch();
        
        return $data['totalSlotsAvailable'];
    }
}


/***
 * Functions
 *
 */
/**
 * Function that check if a subscription if loadable ($_REQUEST['subscrId'], load(), ...) and return the dialobox render
 *
 * @param object $subscription Subscription object
 * @param object $dialogBox DialogBox object
 * @return string DialogBox render
 */
function checkRequestSubscription( &$subscription, & $dialogBox )
{
    $out = '';
    
    if( ! isset( $_REQUEST['subscrId'] ) )
    {
        $dialogBox->error( get_lang( 'Unable to load this subscription.') . ' ' . get_lang( 'The ID is missing.' ) );
        
        $out .= $dialogBox->render();
    }
    else
    {
        if( ! $subscription->load( $_REQUEST['subscrId'] ) )
        {
            $dialogBox->error( get_lang( 'Unable to load this subscription.' ) );
            
            $out .= $dialogBox->render();
        }
        elseif( ! claro_is_allowed_to_edit() && ! $subscription->isVisible() )
        {
            $dialogBox->error( get_lang( 'Not allowed' ) );
            
            $out .= $dialogBox->render();
        }
        elseif( $subscription->choiceExists( claro_get_current_user_id() ) && ! $subscription->isModifiable() && ! claro_is_allowed_to_edit() )
        {
            $dialogBox->error( get_lang( 'Not allowed' ) );
            
            $out .= $dialogBox->render();
        }
    }
    
    return $out;
}
/**
 * Check if a subscription if visibile
 *
 * @param string $visibility Visible or Invisible
 * @param int $from Starting date
 * @param int $to Ending date
 * @return boolean
 */
function isSubscriptionVisible( $visibility, $from, $to )
{
    if( $visibility == 'invisible' )
    {
        return false;
    }
    else
    {
        $now = claro_time();
        
        if( ! is_null( $from ) && $from > $now )
        {
            return false;
        }
        if( ! is_null( $to ) && $to < $now )
        {
            return false;
        }
        return true;   
    }
}

if(false === function_exists('lcfirst'))
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str ) {
        
        $str[0] = strtolower($str[0]);
        
        return (string) $str;
    }
}?>