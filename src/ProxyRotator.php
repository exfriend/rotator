<?php

namespace Exfriend\Rotator;

class ProxyRotator
{

    private $proxies;
    private $workingProxies;


    public function __construct( array $proxies )
    {
        if ( $proxies === null )
        {
            $proxies = [ ];
        }
        $this->setProxies( $proxies );
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

    protected function getWorkingProxy()
    {
        $waitingProxies = [ ];
        while ( $this->hasEnoughWorkingProxies() )
        {
            $randKey = $this->randKey( $this->workingProxies );
            $proxy = $this->workingProxies[ $randKey ];
            if ( !$proxy->isUsable() )
            {
                unset( $this->workingProxies[ $randKey ] );
                continue;
            }
            return $proxy;
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

    private function randNum( $from, $to )
    {
        return rand( $from, $to );
    }

    private function randKey( array &$arr )
    {
        if ( count( $arr ) == 0 )
        {
            return false;
        }
        return array_rand( $arr );
    }
}
