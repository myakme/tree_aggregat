<?php
/**
 * Created by PhpStorm.
 * User: Akme
 * Date: 12.10.15
 * Time: 23:28
 */

namespace Model;

/**
 * Abstract entity. Entity can be created only by fabric method
 *
 * Class EntityBase
 * @package Model
 *
 * @property string name
 */
abstract class EntityBase implements ModelEntity
{

    protected $name;
    protected $sharedProperties = [];

    /**
     * Fabric method - create empty entity with name
     *
     * @param $name
     * @return EntityBase
     */
    public static function create( $name )
    {
        $entityClass = get_called_class();
        /**
         * @var EntityBase $entity
         */
        $entity = new $entityClass( $name );
        return $entity;
    }

    /**
     * Fabric method - create entity from XML node
     *
     * @param \DOMElement $element
     * @return \Model\EntityBase
     */
    public static function createFromXML( \DOMElement $element )
    {
        $entityClass = get_called_class();

        $name = $element->getElementsByTagName('name')->item(0)->nodeValue;

        /**
         * @var EntityBase $entity
         */
        $entity = new $entityClass( $name );

        foreach( $entity->sharedProperties as $property ) {
            if( $property == 'name' ) {
                continue;
            }

            $nodes = $element->getElementsByTagName($property);
            if( $nodes->length > 0 ) {
                $entity->$property = $nodes->item(0)->nodeValue;
            }

        }

        return $entity;
    }

    /**
     * Return list of fields that can be aggregated
     * @return array
     */
    public static function getFieldsForAggregation()
    {
        return [];
    }

    /**
     * Protected constructor forbid to create entities directly
     * @param $name
     */
    protected function __construct( $name )
    {
        if( method_exists($this, 'getSharedProperties') ) {
            $this->sharedProperties = $this->getSharedProperties();
        }

        if( !in_array('name', $this->sharedProperties) ) {
            $this->sharedProperties[] = 'name';
        }
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if(in_array($name, $this->sharedProperties)) {
            return $this->$name;
        } else {
            throw new \Exception('Unknown field: '.$name);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $method = 'set'.ucfirst($name);
        if( method_exists( $this, $method ) ) {
            $this->$method($value);
        }elseif(in_array($name, $this->sharedProperties)) {
            $this->$name = $value;
        } else {
            throw new \Exception('Unknown field: '.$name);
        }
    }

}