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
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Services\CRUDManager;
use JMS\Serializer\Metadata\ClassMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * CRUD manager test
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity manager
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * Recursive validator
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $recursiveValidator;

    /**
     * CRUD manager
     *
     * @var CRUDManager
     */
    private $crudManager;

    /**
     * Dispatcher
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * Crud transformer
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $crudTransformer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->entityManager = $this->prepareEntityManager();
        $this->recursiveValidator = $this->prepareRecursiveValidator();
        $this->dispatcher = $this->getMock(
            '\Symfony\Component\EventDispatcher\EventDispatcher',
            array('dispatch')
        );
        $this->crudTransformer = $this->prepareCRUDTransformet();
        $this->crudManager = new CRUDManager(
            $this->entityManager,
            $this->recursiveValidator,
            $this->dispatcher,
            $this->crudTransformer
        );
    }

    /**
     * @return void
     */
    public function testValidationCollectionFailed()
    {
        $entity1 = $this->prepareEntity();
        $entity2 = $this->prepareEntity();
        $entities = new ArrayCollection(
            array($entity1, $entity2)
        );
        $violationList = $this->prepareViolationList();
        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive(
                array($this->equalTo($entity1)),
                array($this->equalTo($entity2))
            )
            ->willReturnOnConsecutiveCalls(
                new ConstraintViolationList(),
                $violationList
            );
        $this->setExpectedException('\JMS\Serializer\Exception\ValidationFailedException');
        $this->crudManager->validateCollection($entities);
    }

    /**
     * @return void
     */
    public function testValidationItemSuccess()
    {
        $entity = $this->prepareEntity();
        $this->recursiveValidator->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($entity))
            ->willReturn(new ConstraintViolationList());
        $this->assertEquals(true, $this->crudManager->validate($entity));
    }

    /**
     * @return void
     */
    public function testValidationCollectionSuccess()
    {
        $entity1 = $this->prepareEntity();
        $entity2 = $this->prepareEntity();
        $entitys = new ArrayCollection(array($entity1, $entity2));
        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive(
                array($this->equalTo($entity1)),
                array($this->equalTo($entity2))
            )
            ->willReturnOnConsecutiveCalls(
                new ConstraintViolationList(),
                new ConstraintViolationList()
            );
        $this->assertEquals(true, $this->crudManager->validateCollection($entitys));
    }

    /**
     * @return void
     */
    public function testCreateEntityPersist()
    {
        $entity = $this->prepareEntity();
        $entities = new ArrayCollection(
            array($entity, $entity)
        );

        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->with($entity);

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->with(null);

        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->crudManager->createCollection($entities);

        $this->assertEquals(
            new ArrayCollection(array($entity, $entity)),
            $entities
        );
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $entity = $this->prepareEntity();

        $id = 'new.email@opticsplanet.com';
        $type = 'email';
        $data = array(array(
            'id' => $id,
            'type' => $type
        ));

        $entity->expects($this->never())
            ->method('setId')
            ->with($id);

        $unitOfWorkMock = $this->prepareUnitOfWork();

        $unitOfWorkMock
            ->expects($this->once())
            ->method('getEntityState')
            ->willReturn(UnitOfWork::STATE_MANAGED);

        $this->entityManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWorkMock);

        $this->crudTransformer
            ->expects($this->once())
            ->method('initializeClassMetadata');

        $this->crudTransformer
            ->expects($this->exactly(2))
            ->method('processPropertyValue')
            ->withConsecutive(
                $this->equalTo('id'),
                $this->equalTo('type')
            );

        $this->crudManager->setData($entity, $data);
    }


    /**
     * @return void
     */
    public function testCreateAndNotFlushEntity()
    {
        $entity = $this->prepareEntity();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($entity)
            ->will($this->returnValue(null));

        $this->entityManager->expects($this->never())
            ->method('flush')
            ->with($entity)
            ->will($this->returnValue(null));

        $this->crudManager->create($entity, false);
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
            ->setMethods(array('persist', 'flush', 'getClassMetadata', 'getUnitOfWork'))
            ->getMock();
    }

    /**
     * Preparing EntityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareUnitOfWork()
    {
        return $this->getMockBuilder('\Doctrine\ORM\UnitOfWork')
            ->disableOriginalConstructor()
            ->setMethods(array('getEntityState'))
            ->getMock();
    }

    /**
     * Preparing EntityRepository
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareCRUDTransformet()
    {
        return $this->getMockBuilder('\Ecentria\Libraries\CoreRestBundle\Services\CRUDTransformer')
            ->disableOriginalConstructor()
            ->setMethods(array('initializeClassMetadata', 'processPropertyValue'))
            ->getMock();
    }

    /**
     * Preparing EntityRepository
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareRecursiveValidator()
    {
        return $this->getMockBuilder('\Symfony\Component\Validator\Validator\RecursiveValidator')
            ->disableOriginalConstructor()
            ->setMethods(array('validate'))
            ->getMock();
    }

    /**
     * Preparing EntityRepository
     *
     * @return \stdClass|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareEntity()
    {
        return $this->getMockBuilder('\Ecentria\Libraries\CoreRestBundle\Tests\Entity\CircularReferenceEntity')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getType', 'setType'))
            ->getMock();
    }

    /**
     * Preparing Violation
     *
     * @return ConstraintViolation|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareViolation()
    {
        return $this->getMockBuilder('\Symfony\Component\Validator\ConstraintViolation')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Preparing Violation List
     *
     * @return ConstraintViolationList
     */
    private function prepareViolationList()
    {
        $violationList = new ConstraintViolationList();
        $violation = $this->prepareViolation();
        $violationList->add($violation);
        return $violationList;
    }
}
