<?php

namespace XspfTest;


use Xspf\XspfSchemeValidator;

class XspfSchemeValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid_Success()
    {
        $this->assertTrue(XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_ASC)));
    }

    public function testIsValid_Failure()
    {
        $this->assertFalse(XspfSchemeValidator::isValid(file_get_contents(XSPF_FIXTURE_INVALID)));
    }
}
