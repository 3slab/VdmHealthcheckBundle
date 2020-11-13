<?php

namespace Vdm\Bundle\HealthcheckBundle\Tests\Healthcheck;

use PHPUnit\Framework\TestCase;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckerResult;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckResult;

class CheckResultTest extends TestCase
{
    public function testAddAndGetCheckerResults()
    {
        $check1 = new CheckerResult(true);
        $check2 = new CheckerResult(true);

        $checkResult = new CheckResult();
        $checkResult->addCheckerResult('check1', $check1);
        $checkResult->addCheckerResult('check2', $check2);

        $checkerResults = $checkResult->getResults();

        $this->assertEquals(
            ['check1' => $check1, 'check2' => $check2],
            $checkerResults
        );
        $this->assertSame($check1, $checkerResults['check1']);
        $this->assertSame($check2, $checkerResults['check2']);
    }

    public function testAddCheckerResultsOverrideIfSameName()
    {
        $check1 = new CheckerResult(true);
        $check2 = new CheckerResult(true);
        $check3 = new CheckerResult(true);

        $checkResult = new CheckResult();
        $checkResult->addCheckerResult('check1', $check1);
        $checkResult->addCheckerResult('check2', $check2);
        $checkResult->addCheckerResult('check2', $check3);

        $checkerResults = $checkResult->getResults();

        $this->assertEquals(
            ['check1' => $check1, 'check2' => $check3],
            $checkerResults
        );
        $this->assertSame($check1, $checkerResults['check1']);
        $this->assertSame($check3, $checkerResults['check2']);
    }

    public function testIsUpWithoutCheckerResult()
    {
        $checkResult = new CheckResult();

        $this->assertTrue($checkResult->isUp());
    }

    public function testCheckResultOkWithAllCheckerResultOk()
    {
        $checkResult = new CheckResult();
        $checkResult->addCheckerResult('check1', new CheckerResult(true));

        $this->assertTrue($checkResult->isUp());

        $checkResult->addCheckerResult('check2', new CheckerResult(true));

        $this->assertTrue($checkResult->isUp());
    }

    public function testCheckResultKoIfOneCheckerResultKo()
    {
        $checkResult = new CheckResult();
        $checkResult->addCheckerResult('check1', new CheckerResult(true));

        $this->assertTrue($checkResult->isUp());

        $checkResult->addCheckerResult('check2', new CheckerResult(false));

        $this->assertFalse($checkResult->isUp());

        $checkResult->addCheckerResult('check3', new CheckerResult(false));

        $this->assertFalse($checkResult->isUp());

        $checkResult->addCheckerResult('check4', new CheckerResult(true));

        $this->assertFalse($checkResult->isUp());
    }
}
