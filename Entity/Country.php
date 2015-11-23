<?php
namespace Model\Entity;

use Model\CollectionBase;

/**
 * Country as collection of states
 *
 * Class Country
 * @package Model\Entity
 */
class Country extends CollectionBase
{

    protected $entitiesContainer = 'states';
    protected $entityClass = 'JL\\Model\\Entity\\State';


}