<?php

namespace Drupal\bookingsystem_autoecole;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Bks autoecole heures entity.
 *
 * @see \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures.
 */
class BksAutoecoleHeuresAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished bks autoecole heures entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published bks autoecole heures entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit bks autoecole heures entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete bks autoecole heures entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add bks autoecole heures entities');
  }


}
