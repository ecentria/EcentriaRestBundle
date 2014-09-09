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
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->entityManager = $this->prepareEntityManager();
        $this->recursiveValidator = $this->prepareRecursiveValidator();
        $this->dispatcher = $eventDispatcherMock = $this->getMock(
            '\Symfony\Component\EventDispatcher\EventDispatcher',
            array('dispatch')
        );
        $this->crudManager = new CRUDManager(
            $this->entityManager,
            $this->recursiveValidator,
            $this->dispatcher
        );
    }

    /**
     * @return void
     */
    public function testValidationCollectionFailed()
    {
        $contact1 = $this->prepareEntity();
        $contact2 = $this->prepareEntity();
        $contacts = new ArrayCollection(
            array($contact1, $contact2)
        );
        $violationList = $this->prepareViolationList();
        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive(
                array($this->equalTo($contact1)),
                array($this->equalTo($contact2))
            )
            ->willReturnOnConsecutiveCalls(
                new ConstraintViolationList(),
                $violationList
            );
        $this->setExpectedException('\JMS\Serializer\Exception\ValidationFailedException');
        $this->crudManager->validateCollection($contacts);
    }

    /**
     * @return void
     */
    public function testValidationItemSuccess()
    {
        $contact = $this->prepareEntity();
        $this->recursiveValidator->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($contact))
            ->willReturn(new ConstraintViolationList());
        $this->assertEquals(true, $this->crudManager->validate($contact));
    }

    /**
     * @return void
     */
    public function testValidationCollectionSuccess()
    {
        $contact1 = $this->prepareEntity();
        $contact2 = $this->prepareEntity();
        $contacts = new ArrayCollection(array($contact1, $contact2));
        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive(
                array($this->equalTo($contact1)),
                array($this->equalTo($contact2))
            )
            ->willReturnOnConsecutiveCalls(
                new ConstraintViolationList(),
                new ConstraintViolationList()
            );
        $this->assertEquals(true, $this->crudManager->validateCollection($contacts));
    }

    /**
     * @return void
     */
    public function testCreateEntityPersist()
    {
        $contact = $this->prepareEntity();
        $contacts = new ArrayCollection(
            array($contact, $contact)
        );

        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->with($contact);

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->with(null);

        $this->recursiveValidator->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->crudManager->createCollection($contacts);

        $this->assertEquals(
            new ArrayCollection(array($contact, $contact)),
            $contacts
        );
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $contact = $this->prepareEntity();
        $classMetadata = $this->prepareClassMetadata();

        $id = 'new.email@opticsplanet.com';
        $type = 'email';
        $data = array(array(
            'id' => $id,
            'type' => $type
        ));

        $classMetadata->expects($this->once())
            ->method('hasAssociation')
            ->willReturn(false);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $contact->expects($this->never())
            ->method('setId')
            ->with($id);

        $contact->expects($this->once())
            ->method('setType')
            ->with($type);

        $this->crudManager->setData($contact, $data);
    }


    /**
     * @return void
     */
    public function testCreateAndNotFlushEntity()
    {
        $contact = $this->prepareEntity();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($contact)
            ->will($this->returnValue(null));

        $this->entityManager->expects($this->never())
            ->method('flush')
            ->with($contact)
            ->will($this->returnValue(null));

        $this->crudManager->create($contact, false);
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
            ->setMethods(array('persist', 'flush', 'getClassMetadata'))
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
        return $this->getMockBuilder('\stdClass')
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function prepareClassMetadata()
    {
        return $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(array('hasAssociation'))
            ->getMock();
    }
}
