<?php

declare(strict_types=1);

namespace App\Tests\Modules\Customer\Service;

use App\Modules\Customer\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerServiceTest extends KernelTestCase
{
    public function testList(): void
    {
        // $kernel = self::bootKernel();
        // $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');

        $container = static::getContainer();
        $customService = $container->get(CustomerService::class);

        $list = $customService->list();

        // $this->assertEquals('...', $list->getData());
    }
}
