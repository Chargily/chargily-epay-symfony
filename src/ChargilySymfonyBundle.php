<?php
namespace Chargily\SymfonyBundle;

use Chargily\SymfonyBundle\Controller\ChargilyEpaySymfonyController;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChargilySymfonyBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}
