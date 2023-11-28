<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * CORS
 *
 * Manual:
 *  ".env.{dev|prod}.local"
 *       ACCESS_CONTROL_ALLOW_ORIGIN={FRONT_DOMAIN_FULL}
 *  "config/services.yaml" - parameters:
 *       Access-Control-Allow-Origin: "%env(ACCESS_CONTROL_ALLOW_ORIGIN)%"
 */
final class CorsListener implements EventSubscriberInterface
{
    /** @var string PARAMETER_NAME Parameter name */
    public const PARAMETER_NAME = 'Access-Control-Allow-Origin';

    public function __construct(
        private KernelInterface $kernel,
        private ContainerBagInterface $params,
    ) {
    }

    /**
     * @see EventSubscriberInterface
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Don't do anything if it's not the main request.
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method = $request->getRealMethod();

        if (Request::METHOD_OPTIONS === $method) {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        // Don't do anything if it's not the main request.
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->params->has(self::PARAMETER_NAME)) {
            return;
        }

        $response = $event->getResponse();
        if ($this->kernel->isDebug()) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } else {
            $response->headers->set(
                'Access-Control-Allow-Origin',
                (string) $this->params->get(self::PARAMETER_NAME)
            );
        }

        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE');
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->params->has(self::PARAMETER_NAME)) {
            return;
        }

        $response = $event->getResponse();

        if ($response) {
            if ($this->kernel->isDebug()) {
                $response->headers->set('Access-Control-Allow-Origin', '*');
            } else {
                $response->headers->set(
                    'Access-Control-Allow-Origin',
                    (string) $this->params->get(self::PARAMETER_NAME)
                );
            }

            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE');
        }
    }
}
