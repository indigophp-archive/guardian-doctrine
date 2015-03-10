<?php

/*
 * This file is part of the Indigo Guardian Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Guardian\Identifier;

use Assert\Assertion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Indigo\Guardian\Caller\User\Simple as SimpleUser;
use Indigo\Guardian\Exception\IdentificationFailed;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Doctrine implements LoginTokenIdentifier
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entity
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function identify(array $subject)
    {
        Assertion::notEmptyKey($subject, 'username');

        $criteria = ['username' => $subject['username']];

        $repository = $this->getRepository();
        $caller = $repository->findOneBy($criteria);

        if (!$caller) {
            throw new IdentificationFailed('User not found');
        }

        return $caller;
    }

    /**
     * {@inheritdoc}
     */
    public function identifyByLoginToken($loginToken)
    {
        $repository = $this->getRepository();
        $caller = $repository->find($loginToken);

        if (!$caller) {
            throw new IdentificationFailed('User not found');
        }

        return $caller;
    }

    /**
     * Returns an entity repository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository($this->entity);
    }
}
