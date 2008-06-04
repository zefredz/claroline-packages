<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * CRUD (Create-Read-Update-Delete) Resource Interfaces Definition
 *
 * FIXME : move to inc/lib/database/crud.lib.php
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     database
 */

/**
 * CRUD Resource Standard Interface
 */
interface CrudResource
{
    /**
     * Create the current resource
     * @throws Exception on failure
     */
    public function create();
    
    /**
     * Delete the current resource
     * @throws Exception on failure
     */
    public function delete();
    
    /**
     * Update the current resource
     * @throws Exception on failure
     */
    public function update();
    
    /**
     * Convert the current resource to an associative array
     * @return  array
     * @throws Exception on failure
     */
    public function toArray();
    
    /**
     * Load a resource given its id
     * @param   mixed $id
     * @return  CrudResource
     * @throws Exception on failure
     */
    public static function load( $id );
    
    /**
     * Create a resource from an associative array of properties
     * @param   array $data
     * @return  CrudResource
     * @throws Exception on failure
     */
    public static function fromArray( $data );
    
    /**
     * Load all resources
     * @return  array or countable iterator of CrudResource instances
     * @throws Exception on failure
     */
    public static function loadAll();
}

/**
 * User-related CRUD Resource Standard Interface
 */
interface UserCrudResource extends CrudResource
{
    /**
     * Load all resources for a given user
     * @param   int $userId id of the user
     * @return  array or countable iterator of CrudResource instances
     * @throws Exception on failure
     */
    public static function loadAllForUSer( $userId );
}
