<?php

namespace spec\Indigo\Guardian\Identifier;

use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\EntityRepository;
use Indigo\Guardian\Caller\User\Simple as User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DoctrineSpec extends ObjectBehavior
{
    function let(EntityManager $entityManager)
    {
        $this->beConstructedWith($entityManager, 'Indigo\Guardian\Caller\User\Simple');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Indigo\Guardian\Identifier\Doctrine');
    }

    function it_is_an_identifier()
    {
        $this->shouldImplement('Indigo\Guardian\Identifier\LoginTokenIdentifier');
        $this->shouldImplement('Indigo\Guardian\Identifier');
    }

    function it_identifies_a_caller(EntityManager $entityManager, EntityRepository $entityRepository, User $user)
    {
        $criteria = ['username' => 'john_doe'];
        $subject = $criteria;
        $subject['name'] = 'John Doe';


        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy($criteria)->willReturn($user);

        $caller = $this->identify($subject);

        $caller->shouldHaveType('Indigo\Guardian\Caller\User\Simple');
        $caller->shouldImplement('Indigo\Guardian\Caller\HasLoginToken');
        $caller->shouldImplement('Indigo\Guardian\Caller\User');
    }

    function it_throws_an_exception_when_username_is_not_passed()
    {
        $this->shouldThrow('InvalidArgumentException')->duringIdentify([]);
    }

    function it_throws_an_exception_when_username_is_not_found(EntityManager $entityManager, EntityRepository $entityRepository)
    {
        $subject = ['username' => 'jane_doe'];

        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy($subject)->willReturn(null);

        $this->shouldThrow('Indigo\Guardian\Exception\IdentificationFailed')->duringIdentify($subject);
    }

    function it_identifies_a_caller_by_login_token(EntityManager $entityManager, EntityRepository $entityRepository, User $user)
    {
        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->find(1)->willReturn($user);

        $caller = $this->identifyByLoginToken(1);

        $caller->shouldHaveType('Indigo\Guardian\Caller\User\Simple');
        $caller->shouldImplement('Indigo\Guardian\Caller\HasLoginToken');
        $caller->shouldImplement('Indigo\Guardian\Caller\User');
    }

    function it_throws_an_exception_when_cannot_identify_by_login_token(EntityManager $entityManager, EntityRepository $entityRepository)
    {
        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->find(0)->willReturn(null);

        $this->shouldThrow('Indigo\Guardian\Exception\IdentificationFailed')->duringIdentifyByLoginToken(0);
    }
}
