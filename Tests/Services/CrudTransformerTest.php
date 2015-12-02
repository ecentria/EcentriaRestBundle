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

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudTransformer;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * CRUD manager test
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudTransformerTest extends TestCase
{
    /**
     * Entity manager
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * Annotations reader
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $annotationsReader;

    /**
     * Crud transformer
     *
     * @var CrudTransformer
     */
    private $crudTransformer;

    /**
     * Serializer
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Serializer
     */
    private $serializer;

    /**
     * RecursiveValidator
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|RecursiveValidator
     */
    private $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->entityManager = $this->prepareEntityManager();
        $registry = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntityManager', 'getManagerForClass'))
            ->getMock();
        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($this->entityManager));
        $this->annotationsReader = $this->prepareAnnotationReader();
        $this->serializer = $this->prepareSerializer();
        $this->validator = $this->prepareValidator();
        $this->crudTransformer = new CrudTransformer(
            $registry,
            $this->annotationsReader,
            $this->serializer,
            $this->validator
        );
    }

    /**
     * Test for initializing class metadata
     *
     * @return void
     */
    public function testInitializeClassMetadata()
    {
        $classMetadata = $this->prepareClassMetadata();
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);
        $this->crudTransformer->initializeClassMetadata('className');
    }

    /**
     * Testing is property accessible exception
     *
     * @return void
     */
    public function testIsPropertyAccessibleException()
    {
        $this->setExpectedException('Exception', 'You forgot to call initializeClassMetadata method.');
        $this->crudTransformer->isPropertyAccessible('type', 'create');
    }

    /**
     * Testing is property accessible
     *
     * @return void
     */
    public function testIsPropertyAccessible()
    {
        $reflectionProperty1 = $this->prepareReflectionProperty();
        $reflectionProperty2 = $this->prepareReflectionProperty();
        $reflectionProperty3 = $this->prepareReflectionProperty();

        $classMetadata = $this->prepareClassMetadata();
        $classMetadata->expects($this->any())->method('hasField')->willReturn(true);
        $classMetadata->expects($this->exactly(6))
            ->method('getReflectionProperty')
            ->withConsecutive(
                array('first'),
                array('first'),
                array('second'),
                array('second'),
                array('third'),
                array('third')
            )
            ->willReturnOnConsecutiveCalls(
                $reflectionProperty1,
                $reflectionProperty1,
                $reflectionProperty2,
                $reflectionProperty2,
                $reflectionProperty3,
                $reflectionProperty3
            );

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->crudTransformer->initializeClassMetadata('className');

        $classMetadata->expects($this->exactly(6))
            ->method('hasAssociation')
            ->willReturn(false);

        $propertyRestriction1 = new PropertyRestriction(array('value' => 'update'));
        $propertyRestriction2 = new PropertyRestriction(array('value' => 'create'));
        $propertyRestriction3 = new PropertyRestriction(array('value' => array('create', 'update')));

        $this->annotationsReader->expects($this->exactly(6))
            ->method('getPropertyAnnotation')
            ->withConsecutive(
                array($reflectionProperty1, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty1, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty2, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty2, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty3, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty3, 'Ecentria\Libraries\EcentriaRestBundle\Annotation\PropertyRestriction')
            )
            ->willReturnOnConsecutiveCalls(
                $propertyRestriction1,
                $propertyRestriction1,
                $propertyRestriction2,
                $propertyRestriction2,
                $propertyRestriction3,
                $propertyRestriction3
            );


        $this->assertTrue($this->crudTransformer->isPropertyAccessible('first', 'create'));
        $this->assertFalse($this->crudTransformer->isPropertyAccessible('first', 'update'));

        $this->assertTrue($this->crudTransformer->isPropertyAccessible('second', 'update'));
        $this->assertFalse($this->crudTransformer->isPropertyAccessible('second', 'create'));

        $this->assertFalse($this->crudTransformer->isPropertyAccessible('third', 'update'));
        $this->assertFalse($this->crudTransformer->isPropertyAccessible('third', 'create'));
    }

    /**
     * Test Array To Object Validation
     *
     * @return void
     */
    public function testArrayToObjectValidation()
    {
        $classMetadata = $this->prepareClassMetadata();
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);
        $this->crudTransformer->initializeClassMetadata('className');

        $errors = $this->crudTransformer->arrayToObjectPropertyValidation(array('invalid' => 'test'), 'className');

        $this->assertEquals(1, $errors->count());
        $this->assertEquals('This is not a valid property of className', $errors->get(0)->getMessage());
    }

    /**
     * Testing transformation of a null value
     *
     * @return void
     */
    public function testTransformPropertyValueNull()
    {
        $this->assertNull($this->crudTransformer->transformPropertyValue('first', null));
    }

    /**
     * Testing transform of a value without association
     *
     * @return void
     */
    public function testTransformPropertyValueSimple()
    {
        $classMetadata = $this->prepareClassMetadata();
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->crudTransformer->initializeClassMetadata('className');

        $classMetadata->expects($this->once())
            ->method('hasAssociation')
            ->willReturn(false);

        $this->assertEquals('string', $this->crudTransformer->transformPropertyValue('first', 'string'));
    }

    /**
     * Testing transform of a value with association and null collection
     *
     * @return void
     */
    public function testTransformPropertyValueAssociationWithNullCollection()
    {
        $class = new \stdClass();
        $class->id = 'string';

        $this->entityManager->expects($this->once())
            ->method('getReference')
            ->willReturn($class);

        $classMetadata = $this->prepareClassMetadata();

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->crudTransformer->initializeClassMetadata('className');

        $classMetadata->expects($this->once())
            ->method('hasAssociation')
            ->willReturn(true);

        $classMetadata->expects($this->once())
            ->method('getAssociationTargetClass')
            ->willReturn(new \stdClass());

        $this->assertEquals($class, $this->crudTransformer->transformPropertyValue('first', 'string'));
    }

    /**
     * Testing transform of a value with association with collection
     *
     * @return void
     */
    public function testTransformPropertyValueAssociationWithCollection()
    {
        $class1 = $this->prepareClass();
        $class1->expects($this->exactly(2))->method('getId')->willReturn('class1');

        $class2 = $this->prepareClass();
        $class2->expects($this->exactly(2))->method('getId')->willReturn('class2');

        $collection = new ArrayCollection(array($class1, $class2));

        $classMetadata = $this->prepareClassMetadata();

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $this->crudTransformer->initializeClassMetadata('className');

        $classMetadata->expects($this->exactly(2))
            ->method('hasAssociation')
            ->willReturn(true);

        $classMetadata->expects($this->exactly(2))
            ->method('getAssociationTargetClass')
            ->willReturn('\stdClass');

        $classMetadata->expects($this->exactly(4))
            ->method('getSingleIdentifierFieldName')
            ->willReturn('id');

        $this->assertEquals($class1, $this->crudTransformer->transformPropertyValue('id', 'class1', $collection));
        $this->assertEquals($class2, $this->crudTransformer->transformPropertyValue('id', 'class2', $collection));
    }

    /**
     * Prepare
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function prepareClassMetadata()
    {
        return $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'hasAssociation',
                    'getReflectionProperty',
                    'getAssociationTargetClass',
                    'getSingleIdentifierFieldName',
                    'hasField'
                )
            )
            ->getMock();
    }

    /**
     * Preparing EntityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareEntityManager()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('persist', 'flush', 'getClassMetadata', 'getReference'))
            ->getMock();
    }

    /**
     * Preparing AnnotationReader
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareAnnotationReader()
    {
        return $this->getMockBuilder('\Doctrine\Common\Annotations\AnnotationReader')
            ->disableOriginalConstructor()
            ->setMethods(array('getPropertyAnnotation'))
            ->getMock();
    }

    /**
     * Preparing ReflectionProperty
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareReflectionProperty()
    {
        return $this->getMockBuilder('\ReflectionProperty')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Preparing stdClass
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareClass()
    {
        return $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();
    }

    /**
     * Preparing serializer
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareSerializer()
    {
        return $this->getMockBuilder('\JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * PrepareValidator
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|RecursiveValidator
     */
    private function prepareValidator()
    {
        return $this->getMockBuilder('\Symfony\Component\Validator\Validator\RecursiveValidator')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
