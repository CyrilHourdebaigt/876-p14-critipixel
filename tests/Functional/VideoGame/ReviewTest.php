<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ReviewTest extends FunctionalTestCase
{
    public function testShouldPostReview(): void
    {
        // Je connecte un utilisateur
        $this->login();

        // Je vais sur la page d'un jeu
        $crawler = $this->get('/jeu-video-49');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je récupère le formulaire grâce au bouton "Poster"
        $form = $crawler->selectButton('Poster')->form([
            'review[rating]' => 4,
            'review[comment]' => 'Mon commentaire',
        ]);

        // J'envoie le formulaire
        $this->client->submit($form);

        // Je vérifie que je suis redirigé après l'envoi
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Je suis la redirection
        $this->client->followRedirect();

        // Je vérifie que le commentaire apparaît dans la page
        self::assertSelectorTextContains(
            'div.list-group-item:last-child p',
            'Mon commentaire'
        );
    }

    public function testShouldNotAllowSecondReview(): void
    {
        // Je connecte l'utilisateur
        $this->login();

        // Je vais sur un jeu que cet utilisateur a déjà noté dans les fixtures
        $this->get('/jeu-video-0');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je vérifie que le bouton "Poster" n'est pas affiché
        self::assertSelectorNotExists('button[type="submit"]');
    }

    public function testShouldNotPostReviewWithoutRating(): void
    {
        // Je connecte un utilisateur
        $this->login();

        // Je vais sur un jeu que l'utilisateur n'a pas encore noté
        $crawler = $this->get('/jeu-video-49');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je récupère le formulaire
        $form = $crawler->selectButton('Poster')->form();

        // Je récupère toutes les données du formulaire
        $formValues = $form->getPhpValues();

        // Je supprime volontairement la note pour simuler une note manquante
        unset($formValues['review']['rating']);

        // Je renseigne quand même un commentaire
        $formValues['review']['comment'] = 'Mon commentaire sans note';

        // J'envoie directement les données du formulaire
        $this->client->request(
            'POST',
            '/jeu-video-49',
            $formValues
        );

        // Je vérifie que je reste sur la page car le formulaire est invalide
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Je vérifie que le formulaire est toujours affiché
        self::assertSelectorExists('button[type="submit"]');

        // Je vérifie que le champ de la note est signalé comme invalide
        self::assertSelectorExists('#review_rating.is-invalid');
    }

    public function testShouldNotPostReviewWithInvalidRating(): void
    {
        // Je connecte un utilisateur
        $this->login();

        // Je vais sur un jeu que l'utilisateur n'a pas encore noté
        $crawler = $this->get('/jeu-video-49');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je récupère le formulaire
        $form = $crawler->selectButton('Poster')->form();

        // Je récupère toutes les valeurs du formulaire
        $formValues = $form->getPhpValues();

        // Je remplace la note par une valeur invalide
        $formValues['review']['rating'] = 6;

        // Je renseigne un commentaire
        $formValues['review']['comment'] = 'Commentaire avec une note invalide';

        // J'envoie directement le formulaire avec la note invalide
        $this->client->request(
            'POST',
            '/jeu-video-49',
            $formValues
        );

        // Je vérifie que Symfony refuse le formulaire
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Je vérifie que le formulaire est toujours affiché
        self::assertSelectorExists('button[type="submit"]');

        // Je vérifie que le champ de la note est signalé comme invalide
        self::assertSelectorExists('#review_rating.is-invalid');
    }

    public function testShouldNotShowReviewFormForGuest(): void
    {
        // Je vais sur la page d'un jeu sans me connecter
        $this->get('/jeu-video-49');

        // Je vérifie que la page fonctionne correctement
        self::assertResponseIsSuccessful();

        // Je vérifie que le bouton "Poster" n'est pas affiché
        self::assertSelectorNotExists('button[type="submit"]');
    }
}
