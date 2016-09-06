<?php

namespace Exfriend\Rotator;

use Illuminate\Support\Collection;

class ProxyRotator
{

    /**
     * All proxy collection
     * @var Collection
     */
    private $proxies;
    /**
     * Working proxy collection
     * @var Collection
     */
    private $workingProxies;

    /**
     * Rotator configuration
     * @var array
     */
    private $config = [
        'forgive_proxies' => false, // Reset stats when no working proxies left
        'max_consecutive_fails' => 5,
        'max_total_fails' => 9,
    ];

    public function __construct( array $proxies = null, $config = null )
    {
        if ( $proxies === null )
        {
            $proxies = new Collection();
        }
        $this->setProxies( $proxies );

        if ( $config !== null )
        {
            $this->setConfig( $config );
        }
    }

    public function getProxies()
    {
        return $this->proxies;
    }

    public function setProxies( array $proxies )
    {
        foreach ( $proxies as $proxy )
        {

            if ( !$proxy instanceof RotatingProxy )
            {
                $proxy = new RotatingProxy( $proxy );
            }

            $this->proxies[ $proxy->getProxyString() ] = $proxy;
            $this->workingProxies[ $proxy->getProxyString() ] = $proxy;
        }
    }

    public function getWorkingProxies()
    {
        return $this->workingProxies;
    }

    public function getWorkingProxy()
    {
        while ( $this->hasEnoughWorkingProxies() )
        {
            $key = array_rand( $this->workingProxies );
            $proxy = $this->workingProxies[ $key ];
            if ( !$proxy->isUsable() )
            {
                unset( $this->workingProxies[ $key ] );
                continue;
            }
            return $proxy;
        }

        if ( $this->config[ 'forgive_proxies' ] )
        {
            foreach ( $this->proxies as $proxy )
            {
                $proxy->currentTotalFails = 0;
                $proxy->currentConsecutiveFails = 0;
                $proxy->blocked = 0;
                $proxy->totalRequests = 0;
            }
            return $this->getWorkingProxy();
        }

        $msg = "No proxies left";
        throw new \Exception( $msg );
    }

    /**
     * @return bool
     */
    private function hasEnoughWorkingProxies()
    {
        return count( $this->workingProxies ) > 0;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig( $config )
    {
        $this->config = $config;
    }
}
