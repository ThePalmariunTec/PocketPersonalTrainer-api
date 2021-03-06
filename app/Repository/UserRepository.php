<?php


namespace App\Repository;


use App\Model\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;

class UserRepository implements UserRepositoryInterface
{

    private $entityManager;
    private $entityName;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityName = User::class;

    }

    public function insert($entity)
    {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (QueryException $exception) {
            $this->entityManager->rollback();
            throw new QueryException('Falha ao inserir');
        }
    }


    public function update($entity)
    {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->merge($entity);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (QueryException $exception) {
            $this->entityManager->rollback();
            throw new QueryException('Falha ao atualizar');
        }
    }

    public function delete($id)
    {
        $this->entityManager->beginTransaction();
        try {
            $entity = $this->entityManager->find($this->entityName, $id);
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (QueryException $exception) {
            $this->entityManager->rollback();
            throw new QueryException('Falha ao excluir');
        }
    }

    public function findById($id)
    {
        return $this->entityManager->find($this->entityName, $id);
    }

    public function findAll()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('u')
            ->from($this->entityName, 'u');

        return $qb->getQuery()->getResult();
    }

    public function findBy($entity)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('c')
            ->from($this->entityName, 'c')
            ->where($entity);

        return $qb->getQuery()->getResult();
    }

    public function login(string $username, string $password)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.username = :username')
            ->andWhere('u.password = :password')
            ->setParameters(['username' => $username, 'password' => $password]);
        return $qb->getQuery()->getOneOrNullResult();
    }


}