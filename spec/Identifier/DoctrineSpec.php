<?php

namespace spec\Indigo\Guardian\Identifier;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Indigo\Guardian\Caller\User\Simple as User;
use PhpSpec\ObjectBehavior;

class DoctrineSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager)
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

    function it_identifies_a_caller(EntityManagerInterface $entityManager, EntityRepository $entityRepository, User $user)
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

    function it_accepts_identification_fields(EntityManagerInterface $entityManager, EntityRepository $entityRepository, User $user)
    {
        $this->setIdentificationFields([
            'field1',
            'field2',
        ]);

        $criteria = ['field1' => 'john_doe', 'field2' => 'secret_id'];
        $subject = $criteria;

        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy($criteria)->willReturn($user);

        $this->identify($subject);
    }

    function it_throws_an_exception_when_an_identification_field_is_not_passed()
    {
        $this->shouldThrow('InvalidArgumentException')->duringIdentify([]);
    }

    function it_throws_an_exception_when_a_user_is_not_found(EntityManagerInterface $entityManager, EntityRepository $entityRepository)
    {
        $subject = ['username' => 'jane_doe'];

        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy($subject)->willReturn(null);

        $this->shouldThrow('Indigo\Guardian\Exception\IdentificationFailed')->duringIdentify($subject);
    }

    function it_identifies_a_caller_by_login_token(EntityManagerInterface $entityManager, EntityRepository $entityRepository, User $user)
    {
        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy(['loginToken' => 1])->willReturn($user);

        $caller = $this->identifyByLoginToken(1);

        $caller->shouldHaveType('Indigo\Guardian\Caller\User\Simple');
        $caller->shouldImplement('Indigo\Guardian\Caller\HasLoginToken');
        $caller->shouldImplement('Indigo\Guardian\Caller\User');
    }

    function it_accepts_a_login_token_field(EntityManagerInterface $entityManager, EntityRepository $entityRepository, User $user)
    {
        $this->setLoginTokenField('id');

        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy(['id' => 1])->willReturn($user);

        $this->identifyByLoginToken(1);
    }

    function it_throws_an_exception_when_cannot_identify_by_login_token(EntityManagerInterface $entityManager, EntityRepository $entityRepository)
    {
        $entityManager->getRepository('Indigo\Guardian\Caller\User\Simple')->willReturn($entityRepository);
        $entityRepository->findOneBy(['loginToken' => 0])->willReturn(null);

        $this->shouldThrow('Indigo\Guardian\Exception\IdentificationFailed')->duringIdentifyByLoginToken(0);
    }
}
