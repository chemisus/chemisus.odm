<?php

namespace Snuggie;

use Mockery;
use PHPUnit_Framework_TestCase;
use Exception;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    public function shouldThrow($exception_class, $callback)
    {
        try {
            call_user_func($callback);

            $this->assertInstanceOf($exception_class, null);
        } catch (Exception $e) {
            $this->assertInstanceOf($exception_class, $e);
        }
    }
}