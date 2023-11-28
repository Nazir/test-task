<?php

namespace App\EventListener;

use App\Exception\BaseException;
use App\Exception\ValidationException;
use App\Model\Api\ApiResponse;
use App\Model\Error;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class KernelEventsExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -1]];
    }

    /**
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        $error = new Error();
        $error->setMessage($throwable->getMessage());

        if ($throwable instanceof BaseException) {
            $error->setCode($throwable->getCode());
            $error->setStatus($throwable->getStatusCode());
            $error->setDetails($throwable->getDetails());
        } elseif ($throwable instanceof ValidationException) {
            $error->setStatus($throwable->getStatusCode());
            $error->setDetails($throwable->getDetails());
        } elseif ($throwable instanceof HttpExceptionInterface) {
            $error->setStatus($throwable->getStatusCode());
        }

        if ($error->getStatus() === ApiResponse::HTTP_INTERNAL_SERVER_ERROR && $this->kernel->isDebug()) {
            $error->setDetails($throwable->getTrace());
        }

        $response = new ApiResponse(data: $error->jsonSerialize(), status: $error->getStatus());

        // HttpExceptionInterface is a special type of exception that holds status code and header details
        if ($throwable instanceof HttpExceptionInterface) {
            $response->headers->add($throwable->getHeaders());
        }

        $event->setResponse($response);
    }
}
