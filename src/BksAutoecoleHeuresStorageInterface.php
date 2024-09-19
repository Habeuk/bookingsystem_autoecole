<?php

namespace Drupal\bookingsystem_autoecole;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface BksAutoecoleHeuresStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Bks autoecole heures revision IDs for a specific Bks autoecole heures.
   *
   * @param \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface $entity
   *   The Bks autoecole heures entity.
   *
   * @return int[]
   *   Bks autoecole heures revision IDs (in ascending order).
   */
  public function revisionIds(BksAutoecoleHeuresInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Bks autoecole heures author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Bks autoecole heures revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface $entity
   *   The Bks autoecole heures entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BksAutoecoleHeuresInterface $entity);

  /**
   * Unsets the language for all Bks autoecole heures with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
