<?php

namespace App\EventListener;
use App\Entity\Post;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreateSlug
{

    public function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!property_exists($entity, 'slug')) {
            return;
        }
        if (null !== $entity->getSlug()) {
            return;
        }
        if ($entity instanceof Post) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getTitle())));
        }else{
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }

    }
}
