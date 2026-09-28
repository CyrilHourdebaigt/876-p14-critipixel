<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture {

    public function load(ObjectManager $manager): void
    {
        //Je crée 25 tags
        for ($index = 0; $index < 25; $index++) {

            //Je crée un nouveau tag
            $tag = new Tag();

            //je donne un nom au tag
            $tag->setName(sprintf('Tag %d', $index));

            //Je demande à doctrine d'enregistrer le tag
            $manager->persist($tag);
        }

        //J'enregistre tous les tags en bdd
        $manager->flush();
    }
}