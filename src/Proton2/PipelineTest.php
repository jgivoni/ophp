<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

use PHPUnit\Framework\TestCase;

class PipelineTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testPipeline($input, $callables, $expectedResult)
    {
        $runner = pipeline(...$callables);
        $actualResult = $runner($input);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function dataProvider()
    {
        return [
            [
                'getalife',
                ['str_rot13', 'base64_encode'],
                'dHJnbnl2c3I=',
            ],
        ];
    }
}
