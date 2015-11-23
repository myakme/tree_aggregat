<?php
namespace Model;

/**
 * Interface ModelEntity
 * @package JL\Model
 */
interface ModelEntity
{

}

/**
 * Interface ModelCollection
 * @package Model
 */
interface ModelCollection
{

    public function addItem(ModelEntity $item);

}