<?php

namespace App\Serializer;

use App\Entity\JobOffer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JobOfferNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    public function __construct(readonly  private Security $security)
    {
    }

    private const ALREADY_CALLED = true;

    /**
     * @param JobOffer $object
     * @throws ExceptionInterface
     */
    public function normalize(mixed $object, string $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        if (isset($context['operation_name']) && ($context['operation_name'] === '_api_/job_offers/{slug}/metadata_get')){
            $user = $this->security->getUser();

            if($user === $object->getUser()){

                $context['groups'][] = 'jobOffer:management';
            }


        }
        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return $data instanceof JobOffer;
    }
}