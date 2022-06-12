<?php

namespace App\Controller;

use App\Entity\Person;
use App\Services\PersonApplicationService;
use App\Types\Requests\PersonRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\ParamConverter;


class PersonController extends AbstractController
{
    private PersonApplicationService $personApplicationService;

    public function __construct(PersonApplicationService $personApplicationService)
    {
        $this->personApplicationService = $personApplicationService;
    }

    public function getPerson(Request $request): JsonResponse
    {
        return $this->json($this->personApplicationService->getPerson($request->query->get('personId')));
    }

    /**
     * @ParamConverter PersonRequest
     * @return JsonResponse
     */
    public function updatePerson(Request $request, PersonRequest $personRequest): JsonResponse
    {

        $updatedPerson = $this->personApplicationService->updatePerson($personRequest);

        return $this->json($updatedPerson);
    }

}
