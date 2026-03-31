<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ContactController.php',
        ]);
    }

    #[Route('/api/contact', name: 'create_contact', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        $requiredFields = ['name', 'email', 'text'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => 'The following fields are required: ' . implode(', ', $missingFields)
            ], 400);
        }

        $contact = new Contact();
        $contact->setName(trim($data['name']));
        $contact->setEmail(trim($data['email']));
        $contact->setText(trim($data['text']));

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => (string) $errors
            ], 400);
        }
    
        $em->persist($contact);
        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Contact saved successfully'
        ], 201);
    }

    #[Route('/api/contacts', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $contacts = $em->getRepository(Contact::class)->findAll();

        $data = [];

        foreach($contacts as $contact) {
            $data[] = [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'email' => $contact->getEmail(),
                'text' => $contact->getText()
            ];
        }

        $data = [
            'status' => 'success',
            'data' => $data
        ]; 

        return new JsonResponse($data, 200);
    }

    #[Route('/api/contacts/{id}', methods: ['GET'])]
    public function show(EntityManagerInterface $em, int $id): JsonResponse
    {
        $contact = $em->getRepository(Contact::class)->find($id);
        if (!$contact) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }
        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'email' => $contact->getEmail(),
                'text' => $contact->getText()
            ]
        ], 200);
    }

    #[Route('/api/contacts/{id}', methods: ['PUT'])]
    public function update(EntityManagerInterface $em, Request $request, ValidatorInterface $validator, int $id): JsonResponse
    {
        $contact = $em->getRepository(Contact::class)->find($id);
        if (!$contact) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if(!isset($data['name']) || empty(trim($data['name'])) || !isset($data['email']) || empty(trim($data['email'])) || !isset($data['text']) || empty(trim($data['text']))) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => 'The following fields are required: name, email, text and cannot be empty for the put method'
            ], 400);
        }

        $contact->setName(trim($data['name']));
        $contact->setEmail(trim($data['email']));
        $contact->setText(trim($data['text']));

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => (string) $errors
            ], 400);
        }

        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Contact updated successfully'
        ], 200);
    }

    #[Route('/api/contacts/{id}', methods: ['PATCH'])]
    public function partialUpdate(EntityManagerInterface $em, Request $request, ValidatorInterface $validator, int $id): JsonResponse
    {
        $contact = $em->getRepository(Contact::class)->find($id);
        if (!$contact) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name']) && !empty(trim($data['name']))) {
            $contact->setName(trim($data['name']));
        }
        if (isset($data['email']) && !empty(trim($data['email']))) {
            $contact->setEmail(trim($data['email']));
        }
        if (isset($data['text']) && !empty(trim($data['text']))) {
            $contact->setText(trim($data['text']));
        }

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => (string) $errors
            ], 400);
        }

        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Contact updated successfully'
        ], 200);
    }

    #[Route('/api/contacts/{id}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $contact = $em->getRepository(Contact::class)->find($id);
        if (!$contact) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404); 
        }

        $em->remove($contact);
        $em->flush();
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Contact deleted successfully'
        ], 204);
    }
}
