<?php

namespace Adldap\Tests;

use Adldap\Classes\AdldapUtils;

class AdldapUtilityTest extends FunctionalTestCase
{
    protected function newUtilityMock()
    {
        return $this->mock('Adldap\Classes\AdldapUtils');
    }

    public function testUtilityValidateLdapIsBoundPasses()
    {
        $adldap = $this->mock('Adldap\Adldap');

        $adldap
            ->shouldReceive('getLdapConnection')->andReturn(true)
            ->shouldReceive('getLdapBind')->andReturn(true)
            ->shouldReceive('close')->andReturn(true);

        $utility = new AdldapUtils($adldap);

        $this->assertTrue($utility->validateLdapIsBound());
    }

    public function testUtilityValidateLdapIsBoundFailure()
    {
        $adldap = $this->mock('Adldap\Adldap');

        $adldap
            ->shouldReceive('getLdapConnection')->andReturn(true)
            ->shouldReceive('getLdapBind')->andReturn(false)
            ->shouldReceive('close')->andReturn(true);

        $utility = new AdldapUtils($adldap);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $utility->validateLdapIsBound();
    }

    public function testUtilityLdapSlashes()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $unescaped = '\ Testing **';

        $escaped = $utility->ldapSlashes($unescaped);

        $this->assertEquals('\5c Testing \2a\2a', $escaped);
    }

    public function testUtilityStrGuidToHex()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $str = '{25892e17-80f6-415f-9c65-7395632f0223}';

        $result = $utility->strGuidToHex($str);

        $this->assertEquals('\e1\92\58\{2\0f\78\15\64\f9\c6\57\39\56\32\f0\22\3}', $result);
    }

    public function testUtilityBoolToStr()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $boolTrue = true;
        $boolFalse = false;

        $true = $utility->boolToStr($boolTrue);
        $false = $utility->boolToStr($boolFalse);

        $this->assertEquals('TRUE', $true);
        $this->assertEquals('FALSE', $false);
    }

    public function testUtilityDnStrToArray()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $dn = 'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM';

        $includedExpected = array(
            'count' => 5,
            "CN=Karen Berge",
            "CN=admin",
            "DC=corp",
            "DC=Fabrikam",
            "DC=COM",
        );

        $includedAttributes = $utility->dnStrToArr($dn, true, true);

        $this->assertEquals($includedExpected, $includedAttributes);

        $notIncludedExpected = array(
            'count' => 5,
            "Karen Berge",
            "admin",
            "corp",
            "Fabrikam",
            "COM"
        );

        $notIncludedAttributes = $utility->dnStrToArr($dn, true, false);

        $this->assertEquals($notIncludedExpected, $notIncludedAttributes);

    }
}