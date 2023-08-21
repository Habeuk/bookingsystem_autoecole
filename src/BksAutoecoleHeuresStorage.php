<?php

namespace Drupal\bookingsystem_autoecole;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface;

/**
 * Defines the storage handler class for Bks autoecole heures entities.
 *
 * This extends the base storage class, adding required special handling for
 * Bks autoecole heures entities.
 *
 * @ingroup bookingsystem_autoecole
 */
class BksAutoecoleHeuresStorage extends SqlContentEntityStorage implements BksAutoecoleHeuresStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BksAutoecoleHeuresInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {bks_autoecole_heures_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {bks_autoecole_heures_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BksAutoecoleHeuresInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {bks_autoecole_heures_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('bks_autoecole_heures_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
