<?php
namespace Model;

/**
 * Abstract collection class. Allow to keep a collection of entities,
 * make an aggregate calculations and have array access
 *
 * Class CollectionBase
 * @package Model
 */
abstract class CollectionBase extends EntityBase implements \Iterator, \ArrayAccess, \Countable
{

    protected $pointerCurrent = 0;
    protected $items = [];
    protected $entitiesContainer = '';
    protected $entityClass = '';
    protected $sumData = [];
    protected $quantityData = [];

    /**
     * Fabric method - Create a collection from XML
     * @param \DOMElement $element
     * @return CollectionBase
     */
    public static function createFromXML( \DOMElement $element )
    {
        /**
         * @var CollectionBase $collection
         */
        $collection = parent::createFromXML($element);

        /**
         * Preparing aggregation of entities
         */
        $fieldsForAggregation = call_user_func($collection->entityClass.'::getFieldsForAggregation');
        foreach($fieldsForAggregation as $field) {
            $collection->sumData[$field] = 0;
            $collection->quantityData[$field] = 0;
        }

        $containerNode = $element->getElementsByTagName($collection->entitiesContainer);

        /**
         * Scan XML-node for needful entities
         */
        if( $containerNode->length > 0 ) {
            $entityNodes = $containerNode->item(0)->childNodes;

            foreach($entityNodes as $node) {
                if($node->nodeType == XML_TEXT_NODE) {
                    continue;
                }
                /**
                 * Call recursive generation
                 */
                $collection->addItem(call_user_func($collection->entityClass.'::createFromXML', $node));
            }
        }

        return $collection;
    }

    /**
     * Add an item to collection. It's supposed to be the only method for adding and contain aggregation
     *
     * @param EntityBase $item
     * @throws \Exception
     */
    public function addItem(EntityBase $item)
    {
        /**
         * Allow to add only items of correct class
         */
        $itemClass = get_class($item);
        if($itemClass==$this->entityClass) {

            /**
             * Duplication is forbidden
             */
            if( array_key_exists($item->name, $this->items) ) {
                throw new \Exception('Trying to add a repetitive entity');
            }

            /**
             * Aggregation
             */
            $fieldsForAggregation = call_user_func($this->entityClass.'::getFieldsForAggregation');
            foreach($fieldsForAggregation as $field) {
                $this->sumData[$field] += $item->$field;
                $this->quantityData[$field]++;
            }

            /**
             * if items are collections, aggregating prepared values
             */
            if( is_subclass_of($item, __CLASS__) ) {
                /**
                 * @var CollectionBase $item
                 */
                foreach( $item->sumData as $field=>$value ) {
                    if(array_key_exists($field, $this->sumData)) {
                        $this->sumData[$field] += $value;
                        $this->quantityData[$field] += $item->quantityData[$field];
                    } else {
                        $this->sumData[$field] = $value;
                        $this->quantityData[$field] = $item->quantityData[$field];
                    }
                }
            }

            $this->items[$item->name] = $item;
        } else {
            throw new \Exception('Expected '.$this->entityClass.', got '.$itemClass);
        }
    }

    /**
     * Return sum by field name
     * @param $field
     * @return float
     */
    public function sumAll($field)
    {
        return $this->sumData[$field];
    }

    /**
     * Return average by field name
     * @param $field
     * @return float
     */
    public function avgAll($field)
    {
        return $this->sumAll($field) / $this->countAll($field);
    }

    /**
     * Return count of entities that contains field with presented name
     * @param $field
     * @return mixed
     */
    public function countAll($field)
    {
        return $this->quantityData[$field];
    }

    /**
     * Get list of items names
     * @return array
     */
    public function getNamesList()
    {
        return array_keys($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return EntityBase Can return any type.
     */
    public function current()
    {
        return $this->items[ $this->pointerCurrent ];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->pointerCurrent++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->pointerCurrent;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->offsetExists($this->pointerCurrent);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->pointerCurrent = 0;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $offset>0 && $offset < count($this->items);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws \Exception
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        /**
         * Prevent direct write-access
         */
        throw new \Exception('Direct entity set via [] is forbidden');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @throws \Exception
     * @return void
     */
    public function offsetUnset($offset)
    {
        /**
         * Prevent direct write-access
         */
        throw new \Exception('Direct entity unset via [] is forbidden');
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
}