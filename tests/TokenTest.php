<?php

namespace App\Tests;

use App\Entity\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testTokenIsValid()
    {
        $token = new Token();
        $token->setToken('token.test');
        $token->setExpirationTime(new \DateTime('tomorrow'));

        $this->assertTrue($token->isValid('token.test'));
        $this->assertFalse($token->isValid('other.token'));

        $token->setExpirationTime(new \DateTime('yesterday'));

        $this->assertFalse($token->isValid('token.test'));

    }
}