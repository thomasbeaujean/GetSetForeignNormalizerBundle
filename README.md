GetSetForeignNormalizerBundle
=============================

This bundle gives a service that permits to normalize a doctrine entity.

The entity is normalized and the ids of the foreign entities of the entity are also normalized.

# Usage 
## Get the service in your controller
$normalizer = $this->get('get_set_foreign_normalizer');
## If you do not want to normalize some attributes, use the setIgnoredAttributes
$normalizer->setIgnoredAttributes(array('allImages'));//avoid the getAllImages function
## The foreign entities are not fully normalized by default
You can normalize all entities using

$normalizer->setDeepNormalization(true);
## Normalize some object
$companyNormalized = $normalizer->normalize($company); //company is an doctrine entity


