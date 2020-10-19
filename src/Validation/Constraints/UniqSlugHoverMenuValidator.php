<?php

namespace App\Validation\Constraints;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqSlugHoverMenuValidator extends ConstraintValidator
{
    /**
     * @var CategoryRepository
     */
    private $cr;

    /**
     * UniqSlugHoverMenuValidator constructor.
     * @param CategoryRepository $cr
     */
    public function __construct(CategoryRepository $cr)
    {
        $this->cr = $cr;
    }

    /**
     * @param mixed $entity
     * @param Constraint $constraint
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validate($entity, Constraint $constraint)
    {
        /** @var $entity Category */
        if (!$entity->getCustomeCategory()) {
            return;
        }

        $category = $this->cr->matchExistSlug($entity);

        if ($category instanceof Category) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ uniq_value }}', $entity->getSlug())
                ->setParameter('{{ exist_category_id }}', $category->getId())
                ->atPath('user')
                ->addViolation();
        }
    }
}
