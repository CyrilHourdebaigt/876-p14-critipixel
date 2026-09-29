<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Je crée 10 utilisateurs
        for ($index = 0; $index < 10; $index++) {

            // Je crée un utilisateur
            $user = new User();

            // Je renseigne son adresse mail
            $user->setEmail(sprintf('user+%d@email.com', $index));

            // Je renseigne son mot de passe
            $user->setPlainPassword('password');

            // Je renseigne son nom d'utilisateur
            $user->setUsername(sprintf('user+%d', $index));

            // Je demande à Doctrine d'enregistrer l'utilisateur
            $manager->persist($user);
        }

        // J'enregistre tous les utilisateurs en base de données
        $manager->flush();
    }
}
