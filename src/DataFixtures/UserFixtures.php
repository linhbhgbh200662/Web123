<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    //  mã hóa mật khẩu
    public function UserFixtures (UserPasswordHasherInterface $hasher) {
        $this -> hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        // tạo role User
        $user = new User;
        $user -> setUsername("User");
        $user -> serPassword($this -> hasher -> hashPassword("123456"));
        $user -> setRoles(["ROLE_USER"]);
        $manager -> persist($user);

        // tạo role Admin
        $user = new User;
        $user -> setUsername("Admin");
        $user -> serPassword($this -> hasher -> hashPassword("123456"));
        $user -> setRoles(["ROLE_Admin"]);
        $manager -> persist($user);

        // tạo role Manager
        $user = new User;
        $user -> setUsername("Manager");
        $user -> serPassword($this -> hasher -> hashPassword("123456"));
        $user -> setRoles(["ROLE_MANAGER"]);
        $manager -> persist($user);

        $manager->flush();
    }
}
