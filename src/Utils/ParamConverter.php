<?php

namespace App\Utils;

use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotation
 */
class ParamConverter
{
    const ENTITY_NAMESPACE = "\\App\\Entity\\";
    const REQUEST_NAMESPACE = "\\App\\Types\\Requests\\";

    public function fillRequest(string $entityName, string $match, Request $request): mixed
    {
        $requestName = self::REQUEST_NAMESPACE . substr($match, 16);
        $requestTemplate = new $requestName;

        $entity = new ReflectionClass(self::ENTITY_NAMESPACE . $entityName);

        foreach ($entity->getProperties() as $property) {

            $propertyName = $property->getName();

            $parsedRequest = json_decode($request->getContent());

            $requestTemplate->$propertyName = $parsedRequest->$propertyName ?? null;
        }

        return $requestTemplate;
    }
}