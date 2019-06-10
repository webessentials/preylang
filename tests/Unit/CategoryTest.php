<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    /**
     * @return void
     */
    public function testFormatCategorySysValue()
    {
        $result = Category::formatSysValue('Don\'t know');
        $this->assertEquals(
            'dontKnow',
            $result,
            'Test format category sys value to omit space, apostrophe and apply lower camel case'
        );
    }
}
