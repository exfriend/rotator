<?php

namespace Exfriend\Rotator;

class RotatingProxy
{
    /**
     * @var string
     */
    private $proxyString;

    /**
     * @var int
     */
    private $currentTotalFails;

    /**
     * @var int
     */
    private $maxTotalFails;

    /**
     * @var int
     */
    private $currentConsecutiveFails;

    /**
     * @var int
     */
    private $maxConsecutiveFails;

    /**
     * @var bool
     */
    private $blocked;

    /**
     * @var int
     */
    private $totalRequests;

    public function __construct( $proxyString, $maxConsecutiveFails = null, $maxTotalFails = null )
    {
        $this->proxyString = $proxyString;

        if ( $maxConsecutiveFails === null )
        {
            $maxConsecutiveFails = 5;
        }
        $this->maxConsecutiveFails = $maxConsecutiveFails;
        $this->currentConsecutiveFails = 0;
        if ( $maxTotalFails === null )
        {
            $maxTotalFails = -1;
        }
        $this->maxTotalFails = $maxTotalFails;
        $this->currentTotalFails = 0;

        $this->totalRequests = 0;
        $this->blocked = false;
    }

    /**
     * @return bool
     */
    public function isUsable()
    {
        return ( !$this->isBlocked() && !$this->hasTooManyFails() );
    }

    /**
     * Call after any request
     * @return void
     */
    public function requested()
    {
        $this->totalRequests++;
    }

    /**
     * Call after a request failed
     * @return void
     */
    public function failed()
    {
        $this->currentTotalFails++;
        $this->currentConsecutiveFails++;
    }

    /**
     * Call afer a request was successful
     * @return void
     */
    public function succeeded()
    {
        $this->currentConsecutiveFails = 0;
    }

    /**
     * @return bool
     */
    public function hasTooManyFails()
    {
        return ( $this->hasTooManyConsecutiveFails() || $this->hasTooManyTotalFails() );
    }

    /**
     * @return bool
     */
    public function hasTooManyConsecutiveFails()
    {
        return $this->maxConsecutiveFails > -1 && $this->currentConsecutiveFails >= $this->maxConsecutiveFails;
    }

    /**
     * @return bool
     */
    public function hasTooManyTotalFails()
    {
        return $this->maxTotalFails > -1 && $this->currentTotalFails >= $this->maxTotalFails;
    }

    /**
     * @return boolean
     */
    public function isBlocked()
    {
        return $this->blocked;
    }

    /**
     */
    public function block()
    {
        $this->blocked = true;
    }

    /**
     */
    public function unblock()
    {
        $this->blocked = false;
    }

    /**
     * @return mixed
     */
    public function getCurrentConsecutiveFails()
    {
        return $this->currentConsecutiveFails;
    }

    /**
     * @param mixed $currentConsecutiveFails
     */
    public function setCurrentConsecutiveFails( $currentConsecutiveFails )
    {
        $this->currentConsecutiveFails = $currentConsecutiveFails;
    }

    /**
     * @return mixed
     */
    public function getCurrentTotalFails()
    {
        return $this->currentTotalFails;
    }

    /**
     * @param mixed $currentTotalFails
     */
    public function setCurrentTotalFails( $currentTotalFails )
    {
        $this->currentTotalFails = $currentTotalFails;
    }

    /**
     * @return int|null
     */
    public function getMaxConsecutiveFails()
    {
        return $this->maxConsecutiveFails;
    }

    /**
     * @param int|null $maxConsecutiveFails
     */
    public function setMaxConsecutiveFails( $maxConsecutiveFails )
    {
        $this->maxConsecutiveFails = $maxConsecutiveFails;
    }

    /**
     * @return int|null
     */
    public function getMaxTotalFails()
    {
        return $this->maxTotalFails;
    }

    /**
     * @param int|null $maxTotalFails
     */
    public function setMaxTotalFails( $maxTotalFails )
    {
        $this->maxTotalFails = $maxTotalFails;
    }

    /**
     * @return string
     */
    public function getProxyString()
    {
        return $this->proxyString;
    }

    /**
     * @param string $proxyString
     */
    public function setProxyString( $proxyString )
    {
        $this->proxyString = $proxyString;
    }

    /**
     * @return int
     */
    public function getTotalRequests()
    {
        return $this->totalRequests;
    }

    /**
     * @param int $totalRequests
     */
    public function setTotalRequests( $totalRequests )
    {
        $this->totalRequests = $totalRequests;
    }

}
