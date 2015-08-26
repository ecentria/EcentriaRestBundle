<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalTrait,
    Ecentria\Libraries\EcentriaRestBundle\Validator\Constraints as EcentriaAssert;
use Ecentria\Libraries\EcentriaRestBundle\Model\Validatable\ValidatableInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\Validatable\ValidatableTrait;

/**
 * EntityConverterEntity test
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 */
class EntityConverterEntity implements CrudEntityInterface, ValidatableInterface
{
    use TransactionalTrait;
    use ValidatableTrait;

    /**
     * Identifier
     *
     * @var string
     * @ORM\Id
     */
    private $id;

    /**
     * Second Identifier
     *
     * @var string
     */
    private $secondId;

    /**
     * Circular Reference Entity
     *
     * @var CircularReferenceEntity
     */
    private $circularReferenceEntity;

    /**
     * Get primary key
     *
     * @return mixed|string
     */
    public function getPrimaryKey()
    {
        return $this->id;
    }

    /**
     * Get Second Id
     *
     * @return string
     */
    public function getSecondId()
    {
        return $this->secondId;
    }

    /**
     * Set second id
     *
     * @param string $secondId Second id
     * @return void
     */
    public function setSecondId($secondId)
    {
        $this->secondId = $secondId;
    }

    /**
     * Id getter
     *
     * @return mixed
     */
    public function getIds()
    {
        return [
            'id'        => $this->getPrimaryKey(),
            'second_id' => $this->getSecondId()
        ];
    }

    /**
     * Id setter
     *
     * @param mixed $ids ids
     *
     * @return CircularReferenceEntity
     */
    public function setIds($ids)
    {
        if (isset($ids['id'])) {
            $this->id = $ids['id'];
        }
        if (isset($ids['second_id'])) {
            $this->secondId = $ids['second_id'];
        }
        return $this;
    }

    /**
     * Set Circular Reference Entity
     *
     * @param CircularReferenceEntity $entity
     * @return void
     */
    public function setCircularReferenceEntity($entity)
    {
        $this->circularReferenceEntity = $entity;
    }

    /**
     * Get Circular Reference Entity
     *
     * @return CircularReferenceEntity
     */
    public function getCircularReferenceEntity()
    {
        return $this->circularReferenceEntity;
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'        => $this->getPrimaryKey(),
            'second_id' => $this->getSecondId()
        );
    }
}
