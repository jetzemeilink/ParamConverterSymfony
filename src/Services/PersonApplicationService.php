<?php

namespace App\Services;

use App\Entity\Person;
use App\Repository\PersonRepository;
use App\Types\Requests\PersonRequest;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class PersonApplicationService
{
    public function __construct(private PersonRepository $personRepository, private EntityManagerInterface $em)
    {
    }

    public function getPerson(int $personId): Person
    {
        $person = $this->personRepository->find(['id' => $personId]);

        if (!$person instanceof Person) {
            throw new EntityNotFoundException('No such entity exists');
        }

        return $person;
    }

    public function updatePerson(PersonRequest $personRequest): Person
    {
        try {
            $this->em->beginTransaction();

            $person = $this->personRepository->find($personRequest->id);

            if (!$person instanceof Person) {
                throw new EntityNotFoundException('No person found with this id');
            }

            $person->setName($personRequest->name)
                ->setAge($personRequest->age)
                ->setCity($personRequest->city)
                ->setIsMarried($personRequest->isMarried);

            $this->em->persist($person);

            $this->em->flush();

            $this->em->commit();
        } catch(Exception $e ) {
            $this->em->rollback();

            throw $e;
        }

        return $person;
    }
}