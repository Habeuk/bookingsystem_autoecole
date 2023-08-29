<?php

namespace Drupal\bookingsystem_autoecole\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\booking_system\Entity\BookingConfigType;
use Drupal\booking_system\Exception\BookingSystemException;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\booking_system\Services\BookingManager\ManagerCreneaux;
use Drupal\lesroidelareno\lesroidelareno;

/**
 * Permet de gerer l'affichage du calendrier ou des jours à afficher pour les
 * cas auto-ecoles, donc, la particularite est d'avoir une "limit_reservation"
 * dynamique.
 */
class ManagerCreneauxAuto extends ManagerCreneaux {
  
  /**
   * Contient le nombre d'heure de l'utilisateur.
   *
   * @var int
   */
  private $hours = null;
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\booking_system\Services\BookingManager\ManagerCreneaux::getLimitReservation()
   */
  public function getLimitReservation() {
    $limit = parent::getLimitReservation();
    $hours = $this->getHoursByUser();
    if ($limit > 0 && $hours > 0) {
      if ($hours > $limit) {
        return $limit;
      }
      else {
        return $hours;
      }
    }
    return 0;
  }
  
  public function buildCreneaux($date_string) {
    $results = parent::buildCreneaux($date_string);
    // on ajoute les paramettres specifique à ce sous module.
    $results['hours'] = $this->getHoursByUser();
    return $results;
  }
  
  /**
   * Recupere le nombre d'heure restant par utilisateur.
   */
  public function getHoursByUser() {
    if ($this->hours === NULL) {
      $hours = 0;
      $entities = $this->entityTypeManager->getStorage('bks_autoecole_heures')->loadByProperties([
        'owner_heures_id' => lesroidelareno::getCurrentUserId(),
        \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => lesroidelareno::getCurrentDomainId()
      ]);
      if ($entities) {
        foreach ($entities as $entity) {
          /**
           *
           * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $entity
           */
          $hours += $entity->getCreneauxLive();
        }
      }
      $this->hours = $hours;
    }
    return $this->hours;
  }
  
}