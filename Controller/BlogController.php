<?php

namespace BlogBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class BlogController implements ContainerAwareInterface
{
    private $container;

    public function index()
    {

    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}