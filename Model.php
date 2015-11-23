<?php
/**
 * Created by PhpStorm.
 * User: Akme
 * Date: 12.10.15
 * Time: 23:30
 *
 *
 * Just in case
 */

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