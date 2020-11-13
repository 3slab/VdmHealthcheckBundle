<?php

namespace Vdm\Bundle\HealthcheckBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Vdm\Bundle\HealthcheckBundle\Healthcheck\CheckManager;

/**
 * HealthController
 */
class HealthController
{
    public const HEADER_SECRET = 'VDM-HEALTHCHECK-SECRET';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $secret;

    /**
     * HealthController constructor.
     *
     * @param string|null $secret
     * @param SerializerInterface $serializer
     */
    public function __construct(?string $secret, SerializerInterface $serializer)
    {
        $this->secret = $secret;
        $this->serializer = $serializer;
    }

    /**
     * Readiness check route
     *
     * @param Request $request
     * @param CheckManager $readinessManager
     * @return JsonResponse
     */
    public function readiness(Request $request, CheckManager $readinessManager): JsonResponse
    {
        return $this->buildCheckResponse($request, $readinessManager);
    }

    /**
     * Liveness check route
     *
     * @param Request $request
     * @param CheckManager $livenessManager
     * @return JsonResponse
     */
    public function liveness(Request $request, CheckManager $livenessManager): JsonResponse
    {
        return $this->buildCheckResponse($request, $livenessManager);
    }

    /**
     * @param Request $request
     * @param CheckManager $manager
     * @return JsonResponse
     */
    protected function buildCheckResponse(Request $request, CheckManager $manager): JsonResponse
    {
        $secret = $request->get('secret', null) ?? $request->headers->get(static::HEADER_SECRET, null) ?? null;

        $result = $manager->check();

        $successResponseCode = 204;
        $content = '';
        if (($this->secret === null) || ($secret === $this->secret)) {
            $content = $this->serializer->normalize($result->getResults());
            $successResponseCode = 200;
        }

        return new JsonResponse(
            $content,
            $result->isUp() ? $successResponseCode : 503
        );
    }
}
