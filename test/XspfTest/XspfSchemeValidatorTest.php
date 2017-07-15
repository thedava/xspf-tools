<?php

namespace XspfTest;

use Xspf\XspfSchemeValidator;

class XspfSchemeValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid_Success()
    {
        $isValid = XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_ASC));
        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertTrue($isValid);
    }

    public function testIsValid_Failure()
    {
        $isValid = XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_INVALID));
        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertFalse($isValid);
    }
}
