<?php

declare(strict_types=1);

namespace Skeletton\DataFixtures;

use Skeletton\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin@gmail.com';

    /** @var UserPasswordEncoderInterface $passEncoder */
    private $passEncoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $users = Yaml::parseFile(__DIR__ . '/fixtures/users.yaml');

        foreach ($users['users'] as $nickName => $user) {

            $user = $this->instantiate($user['email'], $user['password'], $user['roles'], $nickName);
            $manager->persist($user);
        }

        $manager->flush();
        $this->addReference(self::ADMIN_USER_REFERENCE, $user);
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $roles
     * @param string|null $nickName
     *
     * @return User
     */
    public function instantiate(
        string $email,
        string $password,
        string $roles,
        string $nickName = null
    ): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passEncoder->encodePassword($user, $password));
        $user->setRoles([$roles]);
        return $user;
    }
}
