<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Services;

use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudManager;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudTransformer;
use Ecentria\Libraries\EcentriaRestBundle\Tests\Entity\CircularReferenceEntity;
use Ecentria\Libraries\EcentriaRestBundle\Tests\Entity\EntityConverterEntity;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ecentria\Libraries\EcentriaRestBundle\Converter\EntityConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Entity Converter test
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 */
class EntityConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Crud transformer
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|CrudTransformer
     */
    private $crudTransformer;

    /**
     * Doctrine Registry
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Registry
     */
    private $managerRegistry;

    /**
     * Entity Converter
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityConverter
     */
    private $entityConverter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->managerRegistry = $this->prepareManagerRegistry();
        $this->crudTransformer = $this->getMockBuilder('\Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudTransformer')
            ->disableOriginalConstructor()
            ->setMethods(array('arrayToObject', 'arrayToObjectPropertyValidation'))
            ->getMock();
        $this->entityConverter = $this->prepareEntityConverter();
    }

    /**
     * Test conversion of external references
     *
     * @return void
     */
    public function testConvertExternalReferences()
    {
        $objectContent = ['id' => 'one', 'second_id' => 'two'];
        $object = new EntityConverterEntity();
        $object->setIds($objectContent);

        $referenceObject = new CircularReferenceEntity();
        $this->entityConverter->expects($this->any())
            ->method('find')
            ->willReturn($referenceObject);

        $this->entityConverter->convertExternalReferences(
            new Request(array(), array(), array(), array(), array(), array(), json_encode($objectContent)),
            $object,
            array(
                'references' => array(
                    'class' => 'CircularReferenceEntity',
                    'name'  => 'CircularReferenceEntity'
                )
            )
        );

        //test validation and references conversion
        $this->assertEquals($referenceObject, $object->getCircularReferenceEntity());

    }

    /**
     * Prepare Entity Converter
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityConverter
     */
    private function prepareEntityConverter()
    {
        return $this->getMockBuilder('\Ecentria\Libraries\EcentriaRestBundle\Converter\EntityConverter')
            ->setMethods(array('find'))
            ->setConstructorArgs(array($this->crudTransformer, $this->managerRegistry))
            ->getMock();
    }

    /**
     * Prepare Manager Registry
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Registry
     */
    private function prepareManagerRegistry()
    {
        return $this->getMockBuilder('\Symfony\Bridge\Doctrine\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
