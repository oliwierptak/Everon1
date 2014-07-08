<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Task;


abstract class Item implements Interfaces\Item
{
    const STATUS_EXECUTED = 'EXECUTED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';

    /**
     * @var string
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var boolean
     */
    protected $result = null;

    /**
     * @var int
     */
    protected $priority = null;

    /**
     * @var string
     */
    protected $error = null;

    /**
     * @var \DateTime
     */
    protected $executed_at = null;

    /**
     * @var mixed
     */
    protected $Data = null;

    /**
     * @inheritdoc
     */
    public abstract function execute();


    /**
     * @param mixed $Data
     */
    function __construct($Data)
    {
        $this->Data = $Data;
    }

    /**
     * @inheritdoc
     */
    public function markAsExecuted()
    {
        $this->status = static::STATUS_EXECUTED;
    }

    /**
     * @inheritdoc
     */
    public function markAsFailed()
    {
        $this->status = static::STATUS_FAILED;
    }

    /**
     * @inheritdoc
     */
    public function markAsPending()
    {
        $this->status = static::STATUS_PENDING;
    }

    /**
     * @inheritdoc
     */
    public function markAsProcessing()
    {
        $this->status = static::STATUS_PROCESSING;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @inheritdoc
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param \DateTime $executed_at
     */
    public function setExecutedAt(\DateTime $executed_at)
    {
        $this->executed_at = $executed_at;
    }

    /**
     * @return \DateTime
     */
    public function getExecutedAt()
    {
        return $this->executed_at;
    }
    
    /**
     * @inheritdoc
     */
    public function setData($Item)
    {
        $this->Data = $Item;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->Data;
    }
    
}