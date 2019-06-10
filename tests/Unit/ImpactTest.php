<?php

namespace Tests\Unit;

use App\Models\Impact;
use Tests\TestCase;

class ImpactTest extends TestCase
{

    /**
     * @return void
     */
    public function testFormatImpactNumber()
    {
        $result = Impact::formatImpactNumber('1');
        $this->assertEquals(
            'PLI-0001',
            $result,
            'Test format impact id by raw impact id that is less than thousand.'
        );

        $result = Impact::formatImpactNumber('100000');
        $this->assertEquals(
            'PLI-100000',
            $result,
            'Test format impact id by raw impact id that is more than thousand.'
        );
    }

    /**
     * @return void
     */
    public function testGetImpactNumberFromFormat()
    {
        $result = Impact::getImpactNumberFromFormat('PLI-0001');
        $this->assertEquals(
            1,
            $result,
            'Test extract number from formatted impact that is less than thousand.'
        );

        $result = Impact::getImpactNumberFromFormat('PLI-100000');
        $this->assertEquals(
            100000,
            $result,
            'Test extract number from formatted impact that is more than thousand.'
        );
    }
}
