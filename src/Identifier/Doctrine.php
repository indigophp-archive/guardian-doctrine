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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
     * A set of fields which MUST be included in the subject
     *
     * @var string[]
     */
    protected $identificationFields = ['username'];

    /**
     * A field where login token is stored
     *
     * @var string
     */
    protected $loginTokenField = 'loginToken';

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
        $criteria = $this->buildIdentificationCriteria($subject);

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
        $caller = $repository->findOneBy([$this->loginTokenField => $loginToken]);

        if (!$caller) {
            throw new IdentificationFailed('User not found');
        }

        return $caller;
    }

    /**
     * Sets the identification fields
     *
     * @param array $fields
     */
    public function setIdentificationFields(array $fields)
    {
        $this->identificationFields = $fields;
    }

    /**
     * Sets the login token field
     *
     * @param string $field
     */
    public function setLoginTokenField($field)
    {
        $this->loginTokenField = (string) $field;
    }

    /**
     * Checks if all identification field is included in the subject and builds a criteria
     *
     * @param array $subject
     *
     * @return array
     *
     * @throws \InvalidArgumentException If one of the fields is missing
     */
    protected function buildIdentificationCriteria(array $subject)
    {
        $criteria = [];

        foreach ($this->identificationFields as $dbField => $subjectField) {
            if (empty($subject[$subjectField])) {
                throw new \InvalidArgumentException(sprintf('Subject must contain a(n) "%s" field', $subjectField));
            }

            if (is_numeric($dbField)) {
                $dbField = $subjectField;
            }

            $criteria[$dbField] = $subject[$subjectField];
        }

        return $criteria;
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
