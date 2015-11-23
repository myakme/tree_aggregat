<?php
namespace Model\Entity;

use Model\CollectionBase;

/**
 * State as collection of counties
 *
 * Class State
 * @package Model\Entity
 */
class State extends CollectionBase
{

    protected $entitiesContainer = 'counties';
    protected $entityClass = 'Model\\Entity\\County';


}