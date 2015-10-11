<?php

/**
 * @file
 * Contains \Drupal\og\Plugin\Validation\Constraint\ValidOgMembershipReferenceConstraintValidator.
 */

namespace Drupal\og\Plugin\Validation\Constraint;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\og\Og;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks if referenced entities are valid.
 */
class ValidOgMembershipReferenceConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /* @var \Drupal\Core\Field\FieldItemInterface $value */
    if (!isset($value)) {
      return;
    }

    $entity = \Drupal::entityManager()
      ->getStorage($value->getFieldDefinition()->getTargetEntityTypeId())
      ->load($value->get('target_id')->getValue());

    if (!$entity) {
      // Entity with that entity ID does not exists. This could happens when the
      // user pass a bad value deliberately which can cause a fatal error.
      return;
    }

    $params['%label'] = $entity->label();

    if (!Og::isGroup($entity->getEntityTypeId(), $entity->bundle())) {
      $this->context->addViolation($constraint->NotValidGroup, $params);
      return;
    }
  }
}