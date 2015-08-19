<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\EventListener;

use Ecentria\Libraries\EcentriaRestBundle\Services\Embedded\EmbeddedManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Embedded manager test
 *
 * @property \PHPUnit_Framework_MockObject_MockObject|Request request
 * @property EmbeddedManager                                  manager
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EmbeddedManagerTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->manager = new EmbeddedManager();
    }

    /**
     * Test embedded data
     *
     * @param mixed $embed
     * @param mixed $embedded
     * @param array $groups
     *
     * @dataProvider embeddedDataProvider
     *
     * @return void
     */
    public function testEmbeddedData($embed, $embedded, array $groups)
    {
        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with('_embed')
            ->willReturn($embed);

        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('_embedded')
            ->willReturn($embedded);

        $this->assertSame(
            $groups,
            $this->manager->generateGroups($this->request)
        );
    }

    /**
     * Embedded data provider
     *
     * @return array
     */
    public function embeddedDataProvider()
    {
        return [
            [
                null,
                false,
                ['Default']
            ],
            [
                null,
                true,
                ['Default', 'embedded.all']
            ],
            [
                'property1',
                false,
                ['Default', 'embedded.property1']
            ],
            [
                'property1,property2',
                false,
                ['Default', 'embedded.property1', 'embedded.property2']
            ],
            [
                'property1,property2,property3.property4',
                false,
                ['Default', 'embedded.property1', 'embedded.property2', 'embedded.property3.property4']
            ],
            [
                'property1,property2,property3.property4,property5.property6.property7',
                false,
                ['Default', 'embedded.property1', 'embedded.property2', 'embedded.property3.property4', 'embedded.property5.property6.property7']
            ],
            [
                'property1,property2,property3.property4,property5.property6.property7',
                true,
                ['Default', 'embedded.property1', 'embedded.property2', 'embedded.property3.property4', 'embedded.property5.property6.property7', 'embedded.all']
            ]
        ];
    }
}
