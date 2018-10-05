<?php

namespace App\Tests;

use App\Entity\Safebox;
use PHPUnit\Framework\TestCase;

class SafeboxTest extends TestCase
{
    public function testHasValidPlainPassword()
    {
        $safebox = new Safebox();

        $safebox->setPlainPassword('123456');
        $this->assertFalse($safebox->hasValidPlainPassword());

        $safebox->setPlainPassword('abcdefg');
        $this->assertFalse($safebox->hasValidPlainPassword());

        $safebox->setPlainPassword('qwerty');
        $this->assertFalse($safebox->hasValidPlainPassword());

        $safebox->setPlainPassword('samantha');
        $this->assertFalse($safebox->hasValidPlainPassword());

        $safebox->setPlainPassword('this.is.a.password');
        $this->assertTrue($safebox->hasValidPlainPassword());

    }
}