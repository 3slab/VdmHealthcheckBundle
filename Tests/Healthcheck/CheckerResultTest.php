<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerResult;

class CheckerResultTest extends TestCase
{
    public function testCheckerResultOk()
    {
        $okCheckerResult = new CheckerResult(true);
        $this->assertTrue($okCheckerResult->isUp());
        $this->assertNull($okCheckerResult->getErrorType());
    }

    public function testCheckerResultKo()
    {
        $okCheckerResult = new CheckerResult(false, 'UNKNOWN');
        $this->assertFalse($okCheckerResult->isUp());
        $this->assertEquals(
            'UNKNOWN',
            $okCheckerResult->getErrorType()
        );
    }
}
