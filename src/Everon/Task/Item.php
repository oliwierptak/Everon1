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
     * @inheritdoc
     */
    public abstract function execute();

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
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    
}