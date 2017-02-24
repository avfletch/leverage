<?php

namespace PrestigeDigital\Leverage\Lang;

trait DelegatorTrait
{
    private $delegate;

    protected function setDelegate($delegate)
    {
        $this->delegate = $delegate;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->delegate, $method], $args);
    }
}
