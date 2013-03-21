<?php

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package kernel
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

define ( 'ITERATOR_MODE_INDEX',     'ITERATOR_MODE_INDEX' );
define ( 'ITERATOR_MODE_PROPERTY',  'ITERATOR_MODE_PROPERTY' );
define ( 'ITERATOR_MODE_METHOD',    'ITERATOR_MODE_METHOD' );
define ( 'ITERATOR_MODE_HASH',      'ITERATOR_MODE_HASH' );
define ( 'ITERATOR_MODE_CALLBACK',  'ITERATOR_MODE_CALLBACK' );

function iterator_to_set( $iterator, $column, $mode = ITERATOR_MODE_INDEX )
{
    $set = array();
    
    foreach ( $iterator as $row )
    {
        if ( ITERATOR_MODE_INDEX == $mode )
        {
            $set[$row[$column]] = $row;
        }
        elseif ( ITERATOR_MODE_PROPERTY == $mode )
        {
            $set[$row->$column] = $row;
        }
        elseif ( ITERATOR_MODE_METHOD == $mode )
        {
            $key = call_user_func( array( $row, $column ) );
            $set[$key] = $row;
        }
        elseif ( ITERATOR_MODE_HASH == $mode )
        {
            $set[spl_object_hash($row)] = $row;
        }
        elseif ( ITERATOR_MODE_CALLBACK == $mode )
        {
            $key = $column($row);
            $set[$key] = $row;
        }
        else
        {
            throw new Exception('UNSUPORTED_ITERATOR_CONVERSION_TO_SET_MODE');
        }
    }
    
    return $set;
}

function array_to_set ( $array, $keyName, $setValue = null )
{
    $set = array();
    
    foreach ( $array as $item )
    {
        if ( !isset( $item[$keyName] ) )
        {
            throw new BadFunctionCallException( "Missing key {$keyName} in given array" );
        }
        
        $set[$item[$keyName]] = $setValue ? $setValue : $item;
    }
    
    return $set;
}
