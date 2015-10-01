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
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudManager;
use Ecentria\Libraries\EcentriaRestBundle\Tests\Entity\CircularReferenceEntity;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * CRUD manager test
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudManagerTest extends TestCase
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
     * @var CrudManager
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

        $this->recursiveValidator = $this->prepareRecursiveValidator();
        $this->dispatcher = $this->getMock(
            '\Symfony\Component\EventDispatcher\EventDispatcher',
            array('dispatch')
        );
        $this->crudTransformer = $this->prepareCRUDTransformer();
        $this->crudManager = new CrudManager(
            $registry,
            $this->recursiveValidator,
            $this->dispatcher,
            $this->crudTransformer
        );
    }

    /**
     * TestValidationItemSuccess
     *
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
     * Test Save
     *
     * @return void
     */
    public function testSave()
    {
        $entity = $this->prepareEntity();
        $violations = new ConstraintViolationList(
            array(
                new ConstraintViolation(
                    'Test',
                    'Test',
                    array(),
                    null,
                    null,
                    null
                )
            )
        );
        $this->recursiveValidator->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($entity))
            ->willReturn($violations);
        try {
            $this->crudManager->save($entity);
        } catch (ValidationFailedException $e) {
            $this->assertEquals('Test', $e->getConstraintViolationList()->get(0)->getMessage());
        }

    }

    /**
     * TestValidationCollectionSuccess
     *
     * @return void
     */
    public function testValidationCollectionSuccess()
    {
        $entity1 = $this->prepareEntity();
        $entity1->setIds(array('id' => 1));
        $entity2 = $this->prepareEntity();
        $entity2->setIds(array('id' => 2));
        $entities = new ArrayCollection(array($entity1, $entity2));
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

        $violations = $this->crudManager->validateCollection($entities);
        $this->assertEquals(0, $violations->count());
    }

    /**
     * TestCreateEntityPersist
     *
     * @return void
     */
    public function testCreateEntityPersist()
    {
        $entity1 = $this->prepareEntity();
        $entity1->setIds(array('id' => 1));
        $entity2 = $this->prepareEntity();
        $entity2->setIds(array('id' => 2));

        $entities = new ArrayCollection(
            array($entity1, $entity2)
        );

        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive(
                [$entity1],
                [$entity2]
            );

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->with(null);

        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->crudManager->createCollection($entities);

        $this->assertEquals(
            new ArrayCollection(array($entity1, $entity2)),
            $entities
        );
    }

    /**
     * TestSetData
     *
     * @return void
     */
    public function testSetData()
    {
        $entity = $this->prepareEntity();

        $ids = array('id' => 'new.email@opticsplanet.com');
        $type = 'email';
        $data = array(
            array(
                'id'   => $ids['id'],
                'type' => $type
            )
        );

        $entity->expects($this->never())
            ->method('setIds')
            ->with($ids);

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
     * Test create and not flush entity
     *
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
    private function prepareCRUDTransformer()
    {
        return $this->getMockBuilder('\Ecentria\Libraries\EcentriaRestBundle\Services\CRUD\CrudTransformer')
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
     * @return \PHPUnit_Framework_MockObject_MockObject|CircularReferenceEntity
     */
    private function prepareEntity()
    {
        return $this->getMockBuilder('\Ecentria\Libraries\EcentriaRestBundle\Tests\Entity\CircularReferenceEntity')
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
