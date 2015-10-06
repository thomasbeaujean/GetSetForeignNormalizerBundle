<?php

namespace tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\Traits;

/**
 *
 * @author Thomas BEAUJEAN
 */
trait IsDoctrineEntityTrait
{
    /**
     * Is the data a doctrine entity
     *
     * @param unknown $data
     *
     * @return boolean
     */
    protected function isDoctrineEntity($data)
    {
        $isDoctrineEntity = false;

        if (is_object($data)) {
            $className = get_class($data);
            $doctrine = $this->doctrine;
            $metadataFactory = $doctrine->getManager()->getMetadataFactory();

            try {
                $metadataFactory->getMetadataFor($className);
                $isDoctrineEntity = true;
            } catch (\Doctrine\ORM\Mapping\MappingException $ex) {
                $isDoctrineEntity = false;
            }
        }

        return $isDoctrineEntity;
    }
}
