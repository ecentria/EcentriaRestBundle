<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Tests\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction;
use Ecentria\Libraries\CoreRestBundle\Services\CRUD\CRUDTransformer;
use JMS\Serializer\Metadata\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * CRUD manager test
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDTransformerTest extends TestCase
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
     * @var CRUDTransformer
     */
    private $crudTransformer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->entityManager = $this->prepareEntityManager();
        $this->annotationsReader = $this->prepareAnnotationReader();
        $this->crudTransformer = new CRUDTransformer(
            $this->entityManager,
            $this->annotationsReader
        );
    }

    /**
     * Test for initializing class metadata
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
     */
    public function testIsPropertyAccessibleException()
    {
        $this->setExpectedException('Exception', 'You forgot to call initializeClassMetadata method.');
        $this->crudTransformer->isPropertyAccessible('type', 'create');
    }

    /**
     * Testing is property accessible
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
                array($reflectionProperty1, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty1, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty2, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty2, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty3, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction'),
                array($reflectionProperty3, 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction')
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
     * Testing transformation of a null value
     */
    public function testTransformPropertyValueNull()
    {
        $this->assertNull($this->crudTransformer->transformPropertyValue('first', null));
    }

    /**
     * Testing transform of a value without association
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
     */
    public function testTransformPropertyValueAssociationWithNullCollection()
    {
        $class = new \stdClass;
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
            ->willReturn(new \stdClass);

        $this->assertEquals($class, $this->crudTransformer->transformPropertyValue('first', 'string'));
    }

    /**
     * Testing transform of a value with association with collection
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
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function prepareClassMetadata()
    {
        return $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(array('hasAssociation', 'getReflectionProperty', 'getAssociationTargetClass', 'getSingleIdentifierFieldName', 'hasField'))
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
}
