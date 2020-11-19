<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testValidator($input, $callables, $expectedResult)
    {
        $runner = validator(...$callables);
        $actualResult = $runner($input);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function dataProvider()
    {
        return [
            [
                'getalife',
                [
                    function ($value) {
                        return $value === 'getalife';
                    },
                    function ($value) {
                        return strlen($value) === 8;
                    },
                ],
                true,
            ],
        ];
    }
}
