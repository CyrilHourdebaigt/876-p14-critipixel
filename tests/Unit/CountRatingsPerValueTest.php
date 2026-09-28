<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class CountRatingsPerValueTest extends TestCase
{
    /**
     * @dataProvider provideVideoGame
     */
    public function testShouldCountRatingsPerValue(
        VideoGame $videoGame,
        int $expectedOne,
        int $expectedTwo,
        int $expectedThree,
        int $expectedFour,
        int $expectedFive
    ): void {
        // Je crée le service qui compte les notes
        $ratingHandler = new RatingHandler();

        // Je demande au service de compter combien il y a de notes 1, 2, 3, 4 et 5
        $ratingHandler->countRatingsPerValue($videoGame);

        // Je récupère le résultat du comptage
        $ratingsCount = $videoGame->getNumberOfRatingsPerValue();

        // Je vérifie le nombre de notes de valeur 1
        self::assertSame(
            $expectedOne,
            $ratingsCount->getNumberOfOne()
        );

        // Je vérifie le nombre de notes de valeur 2
        self::assertSame(
            $expectedTwo,
            $ratingsCount->getNumberOfTwo()
        );

        // Je vérifie le nombre de notes de valeur 3
        self::assertSame(
            $expectedThree,
            $ratingsCount->getNumberOfThree()
        );

        // Je vérifie le nombre de notes de valeur 4
        self::assertSame(
            $expectedFour,
            $ratingsCount->getNumberOfFour()
        );

        // Je vérifie le nombre de notes de valeur 5
        self::assertSame(
            $expectedFive,
            $ratingsCount->getNumberOfFive()
        );
    }

    public static function provideVideoGame(): array
    {
        // Je retourne plusieurs jeux de données pour tester plusieurs cas
        return [
            // Je teste un jeu vidéo sans aucune review
            'Aucune review' => [
                new VideoGame(),
                0,
                0,
                0,
                0,
                0,
            ],

            // Je teste un jeu vidéo avec une seule review notée 5
            'Une seule review' => [
                self::createVideoGame(5),
                0,
                0,
                0,
                0,
                1,
            ],

            // Je teste un jeu vidéo avec plusieurs reviews
            'Plusieurs reviews' => [
                self::createVideoGame(
                    1,
                    2,
                    2,
                    3,
                    3,
                    3,
                    4,
                    4,
                    4,
                    4,
                    5,
                    5,
                    5,
                    5,
                    5
                ),
                1,
                2,
                3,
                4,
                5,
            ],
        ];
    }

    private static function createVideoGame(int ...$ratings): VideoGame
    {
        // Je crée un nouveau jeu vidéo
        $videoGame = new VideoGame();

        // Je parcours toutes les notes reçues
        foreach ($ratings as $rating) {

            // Je crée une nouvelle review
            $review = new Review();

            // Je donne une note à la review
            $review->setRating($rating);

            // J'ajoute la review au jeu vidéo
            $videoGame->getReviews()->add($review);
        }

        // Je retourne le jeu vidéo avec toutes ses reviews
        return $videoGame;
    }
}
