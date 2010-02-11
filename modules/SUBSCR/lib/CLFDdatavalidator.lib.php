<?php
/**
 * CLAROLINE
 *
 * This is an extention of the DataValidator class including new validation functions and allowing parameters to be arrays
 *
 * @author Pierre Raynaud <pierre.raynaud@u-clermont1.fr>
 *
 * @package SUBSCRIBE
 *
 */
 
class CLFDdataValidator extends DataValidator
{
    /**
     * Check if date is correct
     * @param array $startDate
     * @return boolean
     */

    function rl_checkdate($startDate)
    {
        return (bool) checkdate($startDate[3],$startDate[4],$startDate[5]);
    }


    /**
     * Check if the second date is prior to the other
     * @param array $dates ($startDate,$endDate)
     * @return boolean
     */

    function rl_checkDateRange($dates)
    {

             $startDate = mktime($dates[0][0],$dates[0][1],$dates[0][2],$dates[0][3],$dates[0][4],$dates[0][5]);
            $endDate   = mktime($dates[1][0],$dates[1][1],$dates[1][2],$dates[1][3],$dates[1][4],$dates[1][5]);

            if ($startDate < $endDate)
            return true;
            else 
            return false;
    }
    
    
    /**
     * This function was modified to accept arrays
     * @param bool $strict 'true' apply every rule on every dataKey, even for
     *                      not required data
     *                     'false' (default) leave rule for emtpy data not required
     * @return boolean
     */

    function validate( $strict = false )
    {
        $this->wrongDataList    = array();
        $this->errorMessageList = array();

        // First, validate required keys

        foreach( $this->requiredDataList as $refKey => $dataKey )
        {
            if ( ! array_key_exists($dataKey, $this->dataList) )
            {
                $this->wrongDataList[]    = $dataKey;
                $this->errorMessageList[] = 'UNDEFINED INDEX';
                continue;
            }

            if ( $this->rl_required($this->dataList[$dataKey]) == false )
            {
                $this->wrongDataList[]    = $dataKey;
                $this->errorMessageList[] = $this->requiredErrorList[$refKey];
            }
        }

        // Then, validate other key rules ...

        foreach( $this->ruleNameList as $ruleKey => $ruleName )
        {
            $dataKey   = $this->ruleRelDataKeyList[$ruleKey];

                if (is_array($dataKey))
                {
                    unset($dataValue);
                    foreach ($dataKey as $key)
                    {
                        if ( ! array_key_exists($key, $this->dataList) )
                    {
                        $this->wrongDataList[]    = $key;
                        $this->errorMessageList[] = 'UNDEFINED INDEX';
                        continue;
                    }

                    if (    ! $strict
                           && ! in_array($key, $this->requiredDataList )
                             && ! $this->rl_required($this->dataList[$dataKey[$key]]) )
                        {
                            // when strict mode is not activated, if element is empty and
                            // not required we shouldn't validate it with other rules
                            continue;
                        }
               
                         $dataValue[] = $this->dataList[$key];                        
                    }
                }
                else
                {
                if ( ! array_key_exists($dataKey, $this->dataList) )
                {
                    $this->wrongDataList[]    = $dataKey;
                    $this->errorMessageList[] = 'UNDEFINED INDEX';
                    continue;
                }

                if (    ! $strict
                     && ! in_array($dataKey, $this->requiredDataList )
                     && ! $this->rl_required($this->dataList[$dataKey]) )
                {
                // when strict mode is not activated, if element is empty and
                // not required we shouldn't validate it with other rules

                continue;
               }
               
                $dataValue = $this->dataList[$dataKey];
            }
                      
             
            $completeParamList = array_merge( array($dataValue), array_values($this->ruleParamList[$ruleKey]) );

            if ( method_exists( $this, 'rl_' . $ruleName) )
            {
                $callback = array( &$this, 'rl_' . $ruleName);
            }
            elseif ( function_exists($ruleName) )
            {
                $callback = $ruleName;
            }
            else
            {
                trigger_error('CALL TO UNDEFINED FUNCTION : ' . $ruleName, E_USER_WARNING);
                return false;
            }

            if ( call_user_func_array( $callback, $completeParamList) == false)
            {
                $this->wrongDataList[]    = $dataKey;
                $this->errorMessageList[] = $this->ruleRelErrorList[$ruleKey];
            }
        } // end foreach $this->ruleNameList as $ruleKey => $ruleName

        if ( count($this->wrongDataList) > 0 ) return false;
        else                                   return true;
    }
}
?>