<?php

namespace App\EventListener;


use App\Controller\PersonController;
use App\Entity;
use App\Utils\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

class ControllerArgumentListener
{

    const CONTROLLER_NAMESPACE = "\\App\\Controller\\";

    private ControllerArgumentsEvent $event;
    private ReflectionClass $reflectorController;
    private string $entityName;

    public function __construct(private ParamConverter $paramConverter)
    {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $argumentsEvent): void
    {
        $this->event = $argumentsEvent;
        $this->setReflectorController();

        $matches = $this->getParamConverterRequest();
        if ($matches) {
            $filledRequest = $this->paramConverter->fillRequest($this->entityName, $matches[0], $argumentsEvent->getRequest());

            $argumentsEvent->setArguments([$argumentsEvent->getArguments()[0], $filledRequest]);
        }
    }

    private function getMethod(Request $request): string|bool
    {
        $controller = $request->attributes->get('_controller');

        return explode('::', $controller)[1] ?? false;
    }

    private function setReflectorController(): void
    {
        $url = $this->event->getRequest()->getRequestUri();

        $explodedUrl = explode("/", $url);
        $this->entityName  = ucfirst($explodedUrl[3]);

        $this->reflectorController = new ReflectionClass(self::CONTROLLER_NAMESPACE . $this->entityName . 'Controller');
    }

    private function getParamConverterRequest(): array|bool
    {
        $methodName = $this->getMethod($this->event->getRequest());

        if (!$methodName) {
            return false;
        }

        $method = $this->reflectorController->getMethod($methodName);

        $docComments = $method->getDocComment();

        $pattern = "/@ParamConverter.*/";

        preg_match($pattern, $docComments, $matches);

        return $matches;
    }
}