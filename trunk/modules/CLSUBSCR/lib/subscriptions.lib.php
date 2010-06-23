<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class subscription
{
  
  private $id, $title, $description, $context, $type, $visibility, $lock, $slotsAvailable, $totalSlotsAvailable;
  private $table, $errors;
  
  public function __construct()
  {
    $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
    
    $this->errors = array();
    
    $this->id = null;
    $this->context = 'user';
    $this->type = 'unique';
    $this->visibility = 'visible';
    $this->lock = 'open';    
  }
  
  /**
   * Save a subscription
   *
   */
  public function save()
  {
    $query_fields = "`title` = '" . Claroline::getDatabase()->escape( $this->title ) . "',
                    `description` = '" . Claroline::getDatabase()->escape( $this->description ) . "',
                    `context` = '" . Claroline::getDatabase()->escape( $this->context ) ."',
                    `type` = '" . Claroline::getDatabase()->escape( $this->type ) . "',
                    `visibility` = '" . Claroline::getDatabase()->escape( $this->visibility ) . "',
                    `lock` = '" . Claroline::getDatabase()->escape( $this->lock ) . "'";
    
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
   * 
   * @return 
   */
  public function load( $id )
  {
    $id = (int) $id;
    
    $query =    "SELECT
                    `id`, `title`, `description`, `context`, `type`, `visibility`, `lock`
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
    $this->context = $subscription['context'];
    $this->visibility = $subscription['visibility'];
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
    
    if( ! in_array( $this->context, $acceptedContext ) )
    {
        $this->setError( get_lang( 'This subscription\'s type is not allowed.' ) );
    }
    
    if( ! in_array( $this->type, $acceptedType ) )
    {
        $this->setError( get_lang( 'This kind of subscription is not allowed.' ) );
    }
    
    if( ! in_array( $this->visibility, $acceptedVisibility ) )
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
  
  public function flat()
  {
    $subscription['id'] = $this->id;
    $subscription['title'] = $this->title;
    $subscription['description'] = $this->description;
    $subscription['context'] = $this->context;
    $subscription['type'] = $this->type;
    $subscription['visibility'] = $this->visibility;
    $subscription['lock'] = $this->lock;
    $subscription['slotsAvailable'] = $this->slotsAvailable;
    $subscription['totalSlotsAvailable'] = $this->totalSlotsAvailable;
    
    return $subscription;
  }
  /**
   * Setters & Getters
   */
  public function getId()
  {
    return $this->id;
  }
  public function getTitle()
  {
    return $this->title;
  }
  
  public function setTitle( $title )
  {
    $this->title = $title;
    
    return $this;
  }
  
  public function getDescription()
  {
    return $this->description;
  }
  
  public function setDescription( $description )
  {
    $this->description = $description;
    
    return $this;
  }
  
  public function getContext()
  {
    return $this->context;
  }
  
  public function setContext( $context )
  {
    $this->context = $context;
    
    return $this;
  }
  
  public function getType()
  {
    return $this->type;
  }
  
  public function setType( $type )
  {
    $this->type = $type;
    
    return $this;
  }
  
  public function getVisibility()
  {
    return $this->visibility;
  }
  
  public function setVisibility( $visibility )
  {
    $this->visibility = $visibility;
    
    return $this;
  }
  
  public function isVisible()
  {
    if( $this->visibility == 'visible' )
    {
        return true;
    }
    
    return false;
  }
  
  public function isInvisible()
  {
    if( $this->visibility == 'invisible' )
    {
        return true;
    }
    
    return false;
  }
  
  public function getLock()
  {
    return $this->lock;
  }
  
  public function setLock( $lock )
  {
    $this->lock = $lock;
    
    return $this;
  }
  
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

class subscriptionsCollection
{
   private $table;
   
   public function __construct()
   {
        $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
   }
   
   public function getAll()
   {
        $query =    "SELECT
                        s.`id`, s.`title`, s.`description`, s.`context`, s.`type`, s.`visibility`, s.`lock`,
                        count( ss.`id` ) as `totalSlotsAvailable`                        
                    FROM
                        `{$this->table['subscr_sessions']}` s
                    LEFT JOIN
                        `{$this->table['subscr_slots']}` ss
                        ON s.`id` = ss.`subscriptionId`
                    GROUP BY
                        s.`id`
                    ORDER BY
                        s.`id`";
        
        $collection = Claroline::getDatabase()->query( $query );
        
        if( $collection )
        {
            $collection = iterator_to_array( $collection, true );
        }
        else
        {
            $collection = array();
        }
        
        $slotsCollection = new slotsCollection();
        
        foreach( $collection as $i => $c )
        {
            $slotsAvailable = $slotsCollection->getAvailableSlots( $c['id'] );
            
            $collection[ $i ]['slotsAvailable'] = $slotsAvailable;
        }        
        
        return  new ArrayIterator( $collection );
   }
}

class slot
{
    protected $id, $subscriptionId, $title, $description, $availableSpace, $visibility;
    
    protected $table;
    
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
                            `availableSpace` = " . (int) $this->availableSpace
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
                            `availableSpace` = " . (int) $this->availableSpace . "
                        WHERE
                            `id` = " . (int) $this->id ."
                        LIMIT 1"
                        ;
            
            $result = Claroline::getDatabase()->exec( $query );
            
            return $result;
        }
    }
    
    public function spaceAvailable()
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
        
        return $this->availableSpace - $data['subscribersCount'];
    }
    
    
}

class slotsCollection
{
    private $table;
   
    public function __construct()
    {
        $this->table = get_module_course_tbl( array( 'subscr_sessions', 'subscr_slots', 'subscr_subscribers', 'subscr_slots_subscribers' ) );
    }
   
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
    
    public function getAllFromUser( $userId )
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
                        sub.`type` = 'user' AND sub.`typeId` = " . $userId
                    ;
        
        $collection = Claroline::getDatabase()->query( $query );
        
        $slots = array();
        
        foreach( $collection as $c )
        {
            $slots[ $c['subscriptionId'] ][ $c['slotId'] ] = $c;            
        }        
        
        return $slots;
    }
    
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
    }
    
    return $out;
}
?>