<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class CalculateAverageRatingTest extends TestCase
{
    /**
     * @dataProvider provideVideoGame
     */
    public function testShouldCalculateAverageRating(
        VideoGame $videoGame,
        ?int $expectedAverageRating
    ): void {
        // Je crée le service qui calcule la moyenne des notes
        $ratingHandler = new RatingHandler();

        // Je demande au service de calculer la moyenne du jeu vidéo
        $ratingHandler->calculateAverage($videoGame);

        // Je vérifie que la moyenne obtenue correspond à la moyenne attendue
        self::assertSame(
            $expectedAverageRating,
            $videoGame->getAverageRating()
        );
    }

    public static function provideVideoGame(): array
    {
        // Je retourne plusieurs jeux de données pour tester plusieurs cas
        return [
            // Je teste un jeu vidéo sans aucune review
            'Aucune review' => [
                new VideoGame(),
                null,
            ],

            // Je teste un jeu vidéo avec une seule review notée 5
            'Une seule review' => [
                self::createVideoGame(5),
                5,
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
                4,
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
