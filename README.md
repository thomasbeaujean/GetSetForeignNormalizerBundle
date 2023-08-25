GetSetForeignNormalizerBundle
=============================

DEPRECATED: This bundle is not usefull anymore, use Symfony serializer/normalizer instead

This bundle gives a service that permits to normalize a doctrine entity.

The entity is normalized and the ids of the foreign entities of the entity are also normalized.

# Install

    composer require "tbn/getsetforeignnormalizer-bundle"

# Usage

## Get the service in your controller

        $normalizer = $this->get('get_set_foreign_normalizer');

## If you do not want to normalize some attributes, use the setIgnoredAttributes parameter

        $normalizer->normalize($entities, true, false, array('allImages'));//avoid the getAllImages function

## The foreign entities are not fully normalized by default
You can normalize all entities using

        $normalizer->normalize($entities, true);
