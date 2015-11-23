<?php
namespace Model\Entity;

use Model\EntityBase;

/**
 * County contains tax rate and collected tax amount
 *
 * Class County
 * @property float rate
 * @property float amount

 * @package Model\Entity
 *
 */
class County extends EntityBase
{

    /**
     * @var array
     */
    protected $sharedProperties = [
        'rate',
        'amount'
    ];

    /**
     * @var float
     */
    protected $rate;

    /**
     * @var float
     */
    protected $amount;

    /**
     * Fields to be aggregated
     * @return array
     */
    public static function getFieldsForAggregation()
    {
        return [
            'rate',
            'amount'
        ];
    }

    /**
     * Set method for value validation
     * @param $value
     * @throws \Exception
     */
    protected function setRate( $value )
    {
        if( is_numeric($value) ) {
            $this->rate = (float)$value;
        } else {
            throw new \Exception('Invalid value for rate');
        }
    }

    /**
     * Set method for value validation
     * @param $value
     * @throws \Exception
     */
    protected function setAmount( $value )
    {
        if( is_numeric($value) ) {
            $this->amount = (float)$value;
        } else {
            throw new \Exception('Invalid value for amount');
        }
    }


}