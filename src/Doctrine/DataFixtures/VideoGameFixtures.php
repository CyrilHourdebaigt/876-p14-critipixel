<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

use function array_fill_callback;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        //Je récupère les tags présents en bdd
        $tags = $manager->getRepository(Tag::class)->findAll();

        $videoGames = array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame)
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        // TODO : Ajouter les tags aux vidéos
        //Je parcours le sjeux vidéos
        foreach ($videoGames as $index => $videoGame) {

            /** @var VideoGame $videoGame */

            //Je parcours 5 fois pour ajouter 5 tags au jeu
            for ($tagIndex = 0; $tagIndex< 5; $tagIndex++) {

                //Je choisis un tag en fonction de l'index du jeu et de la boucle
                $tag = $tags[($index + $tagIndex) % count($tags)];

                //J'ajoute le tag au jeu
                $videoGame->getTags()->add($tag);
            }
        }
        

        array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();

        // TODO : Ajouter des reviews aux vidéos
        // Je récupère tous les utilisateurs présents en base de données
        $allUsers = $manager->getRepository(User::class)->findAll();

        //Je divise les utilisateurs en groupe de 5
        $userGroups = array_chunk($allUsers, 5);

        //Je parcours tous les jeux vidéos
        foreach ($videoGames as $index => $videoGame) {

            /** @var VideoGame $videoGame */

            // Je choisis un groupe d'utilisateurs pour ce jeu
            // count($userGroups) me permet de m'adapter au nombre de groupes disponibles
            $usersForThisGame = $userGroups[$index % count($userGroups)];

            //Je parcours les utilisateurs de ce groupe
            foreach ($usersForThisGame as $user) {

                //Je génère un commentaire aléatoire
                /** @var string $comment */
                $comment = $this->faker->paragraphs(1, true);

                //Je crée une nouvelle review
                $review = new Review();

                //Je définis l'utilisateur qui écrit la review
                $review->setUser($user);

                //Je définis le jeu concerné
                $review->setVideoGame($videoGame);

                //Je génère une note aléatoire entre 1 et 5
                $review->setRating($this->faker->numberBetween(1, 5));

                //J'ajoute le commentaire à la review
                $review->setComment($comment);

                //J'ajoute la review dans la liste des review du jeu
                $videoGame->getReviews()->add($review);

                //J'enregistre
                $manager->persist($review);

                //Je recalcule la moyenne des notes du jeu
                $this->calculateAverageRating->calculateAverage($videoGame);

                //Je recompte le nombre de note 1, 2, 3, 4 et 5
                $this->countRatingsPerValue->countRatingsPerValue($videoGame);
            }
        }

        //J'enregistre en bdd
        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [TagFixtures::class, UserFixtures::class];
    }
}
