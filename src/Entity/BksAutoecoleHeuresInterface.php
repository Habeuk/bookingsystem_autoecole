<?php

namespace Drupal\bookingsystem_autoecole\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Bks autoecole heures entities.
 *
 * @ingroup bookingsystem_autoecole
 */
interface BksAutoecoleHeuresInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Bks autoecole heures name.
   *
   * @return string
   *   Name of the Bks autoecole heures.
   */
  public function getName();

  /**
   * Sets the Bks autoecole heures name.
   *
   * @param string $name
   *   The Bks autoecole heures name.
   *
   * @return \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface
   *   The called Bks autoecole heures entity.
   */
  public function setName($name);

  /**
   * Gets the Bks autoecole heures creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Bks autoecole heures.
   */
  public function getCreatedTime();

  /**
   * Sets the Bks autoecole heures creation timestamp.
   *
   * @param int $timestamp
   *   The Bks autoecole heures creation timestamp.
   *
   * @return \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface
   *   The called Bks autoecole heures entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Bks autoecole heures revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Bks autoecole heures revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface
   *   The called Bks autoecole heures entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Bks autoecole heures revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Bks autoecole heures revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface
   *   The called Bks autoecole heures entity.
   */
  public function setRevisionUserId($uid);

}
