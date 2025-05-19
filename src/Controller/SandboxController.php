<?php

namespace App\Controller;

use App\Entity\Sandbox;
use App\Repository\SandboxRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

final class SandboxController extends AbstractController
{
    /**
     * Enregistre un canevas envoyé depuis le front-end.
     * @uuid est un identifiant unique généré par le front-end, optionnel.
     */
    #[IsGranted('ROLE_TRAINER')]
    #[Route('/sandbox/save/{uuid?}', name: 'sandbox_save', methods: ['POST'])]
    public function saveSandbox(Request $request, SandboxRepository $sandboxRepository, EntityManagerInterface $em, ?string $uuid = null): JsonResponse
    {
        if (isset($uuid)) {
            $sandbox = $sandboxRepository->findOneBy(['uuid' => $uuid]);
        } else {
            $sandbox = new Sandbox();
        }

        // On attend un JSON du type : { "title": "Mon canevas", "canvas": [ ... ] }
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['canvas'])) {
            return new JsonResponse(['status' => 'error', 'message' => new TranslatableMessage('global.exceptions.400.message')], 400);
        }

        $sandbox->setTitle($data['title'] ?? 'Sans titre');
        // On enregistre le tableau d'éléments sous forme de JSON (on peut aussi appliquer json_encode si besoin)
        $sandbox->setData(json_encode($data['canvas']));
        $sandbox->setCreatedAt(new DateTimeImmutable());
        $sandbox->setSlide(0);
        $sandbox->setAuthor($this->getUser());

        $em->persist($sandbox);
        $em->flush();

        return new JsonResponse(['status' => 'success', 'id' => $sandbox->getId()]);
    }

    /**
     * Récupère un canevas par son identifiant pour modification.
     *
     */
    #[Route('/sandbox/{uuid}/edit', name: 'sandbox_edit', methods: ['GET'])]
    public function editSandbox(string $uuid, SandboxRepository $sandboxRepository): Response
    {
        $sandbox = $sandboxRepository->findOneBy(['uuid' => $uuid]);

        if (!$sandbox) {
            return new JsonResponse(['status' => 'error', 'message' => new TranslatableMessage('sandbox.not_found')], 404);
        }

        return $this->render('sandbox/builder.html.twig', [
            'title' => $sandbox->getTitle(),
            'data' => $sandbox->getData(),
            'uuid' => $sandbox->getUuid()
        ]);
    }

    /**
     * Récupère un canevas par son identifiant pour affichage.
     *
     */
    #[Route('/sandbox/{uuid}', name: 'sandbox_show', methods: ['GET'])]
    public function showSandbox(string $uuid, SandboxRepository $sandboxRepository): Response
    {
        $sandbox = $sandboxRepository->findOneBy(['uuid' => $uuid]);

        if (!$sandbox) {
            return new JsonResponse(['status' => 'error', 'message' => new TranslatableMessage('sandbox.not_found')], 404);
        }

        return $this->render('sandbox/builder.html.twig', [
            'title' => $sandbox->getTitle(),
            'data' => $sandbox->getData(),
            'uuid' => $sandbox->getUuid()
        ]);
    }

    #[IsGranted('ROLE_TRAINER')]
    #[Route('/{_locale}/sandbox/builder', name: 'sandbox_builder', methods: "GET")]
    public function builder(): Response
    {
        return $this->render('sandbox/builder.html.twig');
    }
}
