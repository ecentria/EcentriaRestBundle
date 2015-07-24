<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Validator;

use Doctrine\ORM\EntityManager;
use Ecentria\Libraries\EcentriaRestBundle\Tests\Entity\CircularReferenceEntity;
use Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints\CircularReference;
use Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints\CircularReferenceValidator;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Circular Reference Validator Test
 *
 * @author Sergey Chernecov <sergey.chenrnecov@intexsys.lv>
 */
class CircularReferenceValidatorTest extends TestCase
{
    /**
     * Context Mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ExecutionContext
     */
    protected $context;

    /**
     * Entity Manager Mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    protected $entityManager;

    /**
     * Circular Reference Validator
     *
     * @var CircularReferenceValidator
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->getMock('\Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->context = $this->getMock('\Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new CircularReferenceValidator(false);
        $this->validator->setEntityManager($this->entityManager);
        $this->validator->initialize($this->context);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->context = null;
        $this->validator = null;
    }

    /**
     * Testing setting itself as a parent
     *
     * @return void
     */
    public function testItselfAsParent()
    {
        $this->context->expects($this->exactly(2))
            ->method('addViolationAt')
            ->with(
                'Parent',
                sprintf(
                    'You cannot set object #%s as parent for object #%s because of circular reference',
                    'test',
                    'test'
                )
            );

        $channel = new CircularReferenceEntity();
        $channel->setIds(array('id' => 'test'));
        $channel->setParent($channel);
        $this->validator->validate($channel, new CircularReference());
    }

    /**
     * Testing circular reference
     *
     * @return void
     */
    public function testCircularReference()
    {
        $this->context->expects($this->once())
            ->method('addViolationAt')
            ->with(
                'Parent',
                sprintf(
                    'You cannot set object #%s as parent for object #%s because of circular reference',
                    'test1',
                    'test3'
                )
            );

        $channel1 = new CircularReferenceEntity();
        $channel1->setIds(array('id' => 'test1'));
        $channel2 = new CircularReferenceEntity();
        $channel2->setIds(array('id' => 'test2'));
        $channel3 = new CircularReferenceEntity();
        $channel3->setIds(array('id' => 'test3'));

        $constraint = new CircularReference();

        /*
         * Validation should go after each item
         * because we don't have batch update
         * operations for now
         */

        $channel1->setParent($channel2);
        $this->validator->validate($channel1, $constraint);

        $channel2->setParent($channel3);
        $this->validator->validate($channel2, $constraint);

        $channel3->setParent($channel1);
        $this->validator->validate($channel3, $constraint);
    }

    /**
     * Testing circular reference
     *
     * @return void
     */
    public function testCircularReferenceAllEntitiesAreWrong()
    {
        $this->context->expects($this->exactly(3))
            ->method('addViolationAt')
            ->withConsecutive(
                array(
                    'Parent',
                    sprintf(
                        'You cannot set object #%s as parent for object #%s because of circular reference',
                        'test2',
                        'test1'
                    )
                ),
                array(
                    'Parent',
                    sprintf(
                        'You cannot set object #%s as parent for object #%s because of circular reference',
                        'test3',
                        'test2'
                    )
                ),
                array(
                    'Parent',
                    sprintf(
                        'You cannot set object #%s as parent for object #%s because of circular reference',
                        'test1',
                        'test3'
                    )
                )
            );

        $channel1 = new CircularReferenceEntity();
        $channel1->setIds(array('id' => 'test1'));
        $channel2 = new CircularReferenceEntity();
        $channel2->setIds(array('id' => 'test2'));
        $channel3 = new CircularReferenceEntity();
        $channel3->setIds(array('id' => 'test3'));

        $constraint = new CircularReference();

        /*
         * Validation should go after each item
         * because we don't have batch update
         * operations for now
         */

        $channel1->setParent($channel2);
        $channel2->setParent($channel3);
        $channel3->setParent($channel1);

        $this->validator->validate($channel1, $constraint);
        $this->validator->validate($channel2, $constraint);
        $this->validator->validate($channel3, $constraint);

    }

    /**
     * Testing circular reference
     *
     * @return void
     */
    public function testCircularReference1()
    {
        $this->context->expects($this->once())
            ->method('addViolationAt')
            ->with(
                'Parent',
                sprintf(
                    'You cannot set object #%s as parent for object #%s because of circular reference',
                    'test2',
                    'test1'
                )
            );

        $channel1 = new CircularReferenceEntity();
        $channel1->setIds(array('id' => 'test1'));
        $channel2 = new CircularReferenceEntity();
        $channel2->setIds(array('id' => 'test2'));

        $constraint = new CircularReference();

        /*
         * Validation should go after each item
         * because we don't have batch update
         * operations for now
         */

        $channel2->setParent($channel1);
        $this->validator->validate($channel2, $constraint);

        $channel1->setParent($channel2);
        $this->validator->validate($channel1, $constraint);
    }

    /**
     * Testing circular reference
     *
     * @return void
     */
    public function testCircularReferenceFatalErrorOnGetParents()
    {
        $this->context->expects($this->once())
            ->method('addViolationAt')->with(
                'Parent',
                sprintf(
                    'You cannot set object #%s as parent for object #%s because of circular reference',
                    'test2',
                    'test1'
                )
            );

        $channel1 = new CircularReferenceEntity();
        $channel1->setIds(array('id' => 'test1'));
        $channel2 = new CircularReferenceEntity();
        $channel2->setIds(array('id' => 'test2'));
        $channel3 = new CircularReferenceEntity();
        $channel3->setIds(array('id' => 'test3'));

        $constraint = new CircularReference();

        /*
         * Validation should go after each item
         * because we don't have batch update
         * operations for now
         */

        $channel2->setParent($channel1);
        $this->validator->validate($channel2, $constraint);

        $channel1->setParent($channel2);
        $this->validator->validate($channel1, $constraint);

        $channel3->setParent($channel2);
        $this->validator->validate($channel3, $constraint);
    }
}
