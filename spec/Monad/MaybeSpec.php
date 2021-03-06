<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Maybe
 */
class MaybeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Maybe');
        $this->shouldHaveType('Monad\MonadInterface');
    }

    public function it_should_bind_value_from_constructor_to_given_function_if_value_is_not_null()
    {
        $this->beConstructedWith(2);
        $this->bind(function ($value) {
            return $value * $value;
        })->shouldReturn(4);
    }

    public function it_should_not_bind_value_from_constructor_to_given_function_if_value_is_null()
    {
        $this->beConstructedWith(null);
        $result = $this->bind(function ($value) {
            return $value * $value;
        });

        $result->shouldReturn(null);
    }

    public function it_should_bind_value_from_constructor_to_given_function()
    {
        $this->beConstructedWith(2);
        $this->bind(function ($value) {
            return $value * $value;
        })->shouldReturn(4);
    }

    public function it_should_obey_first_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Maybe::create($value + 1);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne);
        $left = $mAddOne(3);

        $right->valueOf()->shouldReturn($left->valueOf());
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(\Monad\Maybe::create);
        $left = \Monad\Identity::create(3);

        $right->valueOf()->shouldReturn($left->valueOf());
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Maybe::create($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Maybe::create($value + 2);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne)->bind($mAddTwo);
        $left = $this->bind(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->bind($mAddTwo);
        });

        $right->valueOf()->shouldReturn($left->valueOf());
    }
}
