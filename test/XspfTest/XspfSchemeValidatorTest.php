<?php

namespace XspfTest;

use PHPUnit\Framework\TestCase;
use Xspf\XspfSchemeValidator;

class XspfSchemeValidatorTest extends TestCase
{
    public function testIsValid_Success()
    {
        $this->markTestSkipped('Skipped due to external api call');

        $isValid = XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_ASC));
        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertTrue($isValid);
    }

    public function testIsValid_Failure()
    {
        $this->markTestSkipped('Skipped due to external api call');

        $isValid = XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_INVALID));
        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertFalse($isValid);
    }
}
