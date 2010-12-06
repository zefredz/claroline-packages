<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

FromKernel::uses( 'csv.class' );

class Poll2Csv extends csv
{
    public function loadDataList( $poll )
    {
        try
        {
            $header = array();
            $displayNames = $poll->getOption( '_privacy' ) != '_anonymous';
            
            if ( $displayNames )
            {
                $header[] = get_lang( 'Name' );
            }
            
            $choiceList = $poll->getChoiceList();
            ksort( $choiceList );
            $result = array_fill_keys( array_keys( $choiceList ) , 0 );
            
            foreach( $choiceList as $choiceId => $choice )
            {
                $header[] = str_replace( ',' , ' ' , $choice );
            }
            
            $this->recordList[] = $header;
            
            foreach( $poll->getAllVoteList() as $vote )
            {
                $line = array();
                $line[] = $vote[ 'lastName' ] . ' ' . $vote[ 'firstName' ];
                ksort( $vote );
                
                foreach( $vote as $key => $item )
                {
                    if ( ! is_string( $key ) )
                    {
                        if ( $item == 'checked' )
                        {
                            $line[] = 1;
                            $result[ $key ]++;
                        }
                        else
                        {
                            $line[] = 0;
                        }
                    }
                }
                $this->recordList[] = $line;
            }
            
            $this->recordList[] = $displayNames
                                ? array_merge( array( 'Result' ) , $result )
                                : $result;
        }
        catch ( Exception $e ) // exceptions handling
        {
            if ( claro_debug_mode() )
            {
                $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
            }
            else
            {
                $dialogBox->error( $e->getMessage() );
            }
        }
    }
}