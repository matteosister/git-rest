<?php

namespace spec\GitRest;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\RouteCollection;

class ApplicationSpec extends ObjectBehavior
{
    function let(RouteCollection $routes)
    {
        $this->beConstructedWith($routes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('GitRest\Application');
    }

    function it_is_callable()
    {
        $this()->shouldBeAnInstanceOf('closure');
    }

    function it_has_a_repositoryRoot_setter()
    {
        $this->setRepositoryRoot('root')->shouldReturn(null);
    }

    function it_has_a_projectRoot_setter()
    {
        $this->setProjectRoot('root')->shouldReturn(null);
    }
}
