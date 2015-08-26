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
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Entity Converter test
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 */
class EntityConverterTest extends TestCase
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
     * Test creation of new object
     *
     * @return void
     */
    public function testCreateNewObject()
    {
        $objectContent = ['id' => 'one', 'second_id' => 'two'];
        $object = new EntityConverterEntity();
        $object->setIds($objectContent);
        $badProperties = new ConstraintViolationList();
        $badProperties->add(
            new ConstraintViolation(
                'This is not a valid property of CLASS',
                'This is not a valid property of CLASS',
                array(),
                null,
                null,
                null
            )
        );

        $this->crudTransformer->expects($this->any())
            ->method('arrayToObject')
            ->willReturn($object);
        $this->crudTransformer->expects($this->any())
            ->method('arrayToObjectPropertyValidation')
            ->willReturn($badProperties);

        $referenceObject = new CircularReferenceEntity();
        $this->entityConverter->expects($this->any())
            ->method('find')
            ->willReturn($referenceObject);


        /** @var EntityConverterEntity $newObject */
        $newObject = $this->entityConverter->createNewObject(
            'EntityConverterEntity',
            new Request(array(), array(), array(), array(), array(), array(), json_encode($objectContent)),
            true,
            array(
                'references' => array(
                    'class' => 'CircularReferenceEntity',
                    'name'  => 'CircularReferenceEntity'
                )
            )
        );

        //test validation and references conversion
        $this->assertEquals($referenceObject, $newObject->getCircularReferenceEntity());
        $this->assertEquals($badProperties, $newObject->getViolations());
        $this->assertFalse($newObject->isValid());

        $secondIds = array(
            'id'        => 'test ONE',
            'second_id' => 'test TWO'
        );
        /** @var EntityConverterEntity $secondObject */
        $secondObject = $this->entityConverter->createNewObject(
            'EntityConverterEntity',
            new Request(
                array(),
                array(),
                $secondIds,
                array(),
                array(),
                array(),
                json_encode($objectContent)
            ),
            false,
            array()
        );

        //test set ids
        $this->assertEquals($secondIds, $secondObject->getIds());
    }

    /**
     * Test new object validation
     *
     * @return void
     */
    public function testValidateNewObject()
    {
        $reflectionClass = new \ReflectionClass('\Ecentria\Libraries\EcentriaRestBundle\Converter\EntityConverter');
        $reflectionMethod = $reflectionClass->getMethod('validateNewObject');
        $reflectionMethod->setAccessible(true);
        $badProperties = new ConstraintViolationList();

        //removing reference properties
        $this->crudTransformer->expects($this->any())
            ->method('arrayToObjectPropertyValidation')
            ->with($this->equalTo(['do_not_remove' => 'value']), $this->equalTo('Object'))
            ->willReturn($badProperties);
        $object = new EntityConverterEntity();
        $options = ['references' => [['property' => 'remove_me']]];
        $data = ['do_not_remove' => 'value', 'remove_me' => 'value'];

        $reflectionMethod->invoke($this->entityConverter, $object, 'Object', $data, $options);

        //returning mocked violations, setting, and setting valid state is tested in above method
    }

    /**
     * Test id getter
     *
     * @return void
     */
    public function testGetIdentifier()
    {
        $reflectionClass = new \ReflectionClass('\Ecentria\Libraries\EcentriaRestBundle\Converter\EntityConverter');
        $reflectionMethod = $reflectionClass->getMethod('getIdentifier');
        $reflectionMethod->setAccessible(true);

        //Test options[id] set
        //test not array options[id]
        $attributes = ['testName' => '123', 'testName2' => '345'];
        $request = new Request([], [], $attributes);
        $name = 'name';
        $options = ['id' => 'testName'];
        $out = $reflectionMethod->invoke($this->entityConverter, $request, $options, $name);
        $this->assertEquals('123', $out);

        //test array options[id]
        $options = ['id' => ['testName', 'testName2']];
        $out = $reflectionMethod->invoke($this->entityConverter, $request, $options, $name);
        $this->assertEquals($attributes, $out);

        //test $options['property']
        $options = ['property' => 'test_property', 'data' => ['test_property' => 'value']];
        $out = $reflectionMethod->invoke($this->entityConverter, $request, $options, $name);
        $this->assertEquals('value', $out);

        //test no id in options but in attributes
        $options = [];
        $request = new Request([], [], ['id' => 'here']);
        $out = $reflectionMethod->invoke($this->entityConverter, $request, $options, $name);
        $this->assertEquals('here', $out);
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
        return $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
