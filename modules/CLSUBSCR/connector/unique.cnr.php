<?php

class slotUnique extends slot
{
    public function saveSubscriberChoice( $userId, $subscriptionId, $context = 'user' )
    {
        $userId = (int) $userId;
        
        if( ! ( in_array( $context, array( 'user', 'group' ) ) && $userId ) )
        {
            return get_lang( 'Unable to save your choice.' );
        }
        
        //check if subscriber exists in database
        $query = "SELECT `id` FROM `{$this->table['subscr_subscribers']}` WHERE `type` = '" . $context . "' AND `typeId` = " . $userId;
        
        $subscriber = Claroline::getDatabase()->query( $query )->fetch();
        if( ! $subscriber )
        {
            $query = "INSERT INTO `{$this->table['subscr_subscribers']}` SET `type` = '" . $context . "', `typeId` = " . $userId;
            $result = Claroline::getDatabase()->exec( $query );
            if( $result )
            {
                $subscriberId = Claroline::getDatabase()->insertId();
            }
            else
            {
                return get_lang( 'Unable to save your choice.' );
            }
        }
        else
        {
            $subscriberId = (int) $subscriber['id'];
        }
        
        //check if we need to update or save a new choice
        $query =    "SELECT
                        `slotId`
                    FROM
                        `{$this->table['subscr_slots_subscribers']}`
                    WHERE
                        `subscriberId` = " . $subscriberId . " AND `subscriptionId` = " . $subscriptionId . "
                    LIMIT 1"
                    ;
        
        $result = Claroline::getDatabase()->query( $query );
        
        $slot_subscrib = $result->fetch();
        
        if( $result->numRows() )
        {
            //Update only if the slotId is not the same
            if( !( $slot_subscrib && $slot_subscrib['slotId'] == $this->id ) )
            {
                //check if there is enough space in the slot
                if( $this->spaceAvailable() == 0 )
                {
                    return get_lang( 'No enough space in the selected slot.' );
                }
                else
                {
                    $result = Claroline::getDatabase()->exec(
                        "UPDATE
                            `{$this->table['subscr_slots_subscribers']}`
                        SET
                            `slotId` = " . (int) $this->id . "
                        WHERE
                            `subscriberId` = " . $subscriberId . " AND `subscriptionId` = " . $subscriptionId . "
                        LIMIT 1 "
                    );                    
                }
            }
            else
            {
                return true;
            }
        }
        else
        {
            //check if there is enough space in the slot
            if( $this->spaceAvailable() == 0 )
            {
                return get_lang( 'No enough space in the selected slot.' );
            }
            else
            {
                $result = Claroline::getDatabase()->exec(
                    "INSERT INTO
                            `{$this->table['subscr_slots_subscribers']}`
                    SET
                        `slotId` = " . (int) $this->id . ",
                        `subscriberId` = " . $subscriberId . ",
                        `subscriptionId` = " . $subscriptionId
                );                
            }
        }
        
        if( ! $result )
        {
            return get_lang( 'Unable to save your choice.' );
        }
        
        return $result;        
    }
}

?>