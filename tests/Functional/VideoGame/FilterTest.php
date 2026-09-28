<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');

        $this->client->submitForm(
            'Filtrer',
            ['filter[search]' => 'Jeu vidéo 49'],
            'GET'
        );

        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider provideTags
     */
    public function testShouldFilterVideoGamesByTags(
        array $tags,
        int $expectedCount
    ): void {
        // Je vais sur la liste des jeux vidéo
        $this->get('/');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je remplis et j'envoie le formulaire avec les tags choisis
        $this->client->submitForm(
            'Filtrer',
            $tags,
            'GET'
        );

        // Je vérifie que la page fonctionne après le filtrage
        self::assertResponseIsSuccessful();

        // Je vérifie le nombre de jeux affichés
        self::assertSelectorCount(
            $expectedCount,
            'article.game-card'
        );
    }

    public function testShouldHandleUnknownTag(): void
    {
        // Je vais sur la liste des jeux vidéo avec un tag qui n'existe pas
        $this->get('/', [
            'filter' => [
                'tags' => ['9999'],
            ],
        ]);

        // Je vérifie que l'application répond correctement
        self::assertResponseIsSuccessful();

        // Je vérifie que le tag inexistant est ignoré
        // et que la liste normale de 10 jeux est affichée
        self::assertSelectorCount(
            10,
            'article.game-card'
        );
    }

    public static function provideTags(): array
    {
        return [
            // Je teste sans sélectionner de tag
            'Aucun tag' => [
                [],
                10,
            ],

            // Je teste avec un seul tag
            'Un tag' => [
                [
                    'filter[tags][0]' => '1',
                ],
                10,
            ],

            // Je teste avec deux tags
            'Deux tags' => [
                [
                    'filter[tags][0]' => '1',
                    'filter[tags][1]' => '2',
                ],
                8,
            ],

            // Je teste avec trois tags
            'Trois tags' => [
                [
                    'filter[tags][0]' => '1',
                    'filter[tags][1]' => '2',
                    'filter[tags][2]' => '3',
                ],
                6,
            ],

            // Je teste avec cinq tags
            'Cinq tags' => [
                [
                    'filter[tags][0]' => '1',
                    'filter[tags][1]' => '2',
                    'filter[tags][2]' => '3',
                    'filter[tags][3]' => '4',
                    'filter[tags][4]' => '5',
                ],
                2,
            ],
        ];
    }
}
