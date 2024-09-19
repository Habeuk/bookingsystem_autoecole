<?php

namespace Drupal\bookingsystem_autoecole\Services;

use Drupal\booking_system\Services\BookingManager\ManagerCreneaux;

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
   * Permet de selectionner le type de boite de transmission.
   *
   * @var string
   */
  public $type_boite = 'manuelle';
  
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
  
  protected function buildCreneaux($date_string) {
    $results = parent::buildCreneaux($date_string);
    // On ajoute les paramettres specifique à ce sous module.
    $results['hours'] = $this->getHoursByUser();
    return $results;
  }
  
  /**
   * Recupere le nombre d'heure restant par utilisateur en fonction du type de
   * conduite.
   */
  public function getHoursByUser($type_boite = NULL, $cache = true) {
    if ($this->hours === NULL) {
      if ($type_boite === NULL)
        $type_boite = $this->type_boite;
      $hours = 0;
      $values = [
        'type_boite' => $type_boite
      ];
      if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
        $values['owner_heures_id'] = \Drupal\lesroidelareno\lesroidelareno::getCurrentUserId();
        $values[\Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD] = \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId();
      }
      $entities = $this->entityTypeManager->getStorage('bks_autoecole_heures')->loadByProperties($values);
      if ($entities) {
        foreach ($entities as $entity) {
          /**
           *
           * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $entity
           */
          $hours += $entity->getCreneauxLive();
        }
      }
      if (!$cache) {
        return $hours;
      }
      $this->hours = $hours;
    }
    return $this->hours;
  }
  
}