<?php

namespace Drupal\bookingsystem_autoecole\Services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\booking_system\Entity\BookingConfigType;
use Drupal\booking_system\Exception\BookingSystemException;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\booking_system\Services\BookingManager\ManagerDate;

/**
 * Permet de gerer l'affichage du calendrier ou des jours à afficher pour les
 * cas auto-ecoles, donc, la particularite est d'avoir une "limit_reservation"
 * dynamique.
 */
class ManagerDateAuto extends ManagerDate {
  
  /**
   *
   * @var ManagerCreneauxAuto
   */
  protected $ManagerCreneaux;
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\booking_system\Services\BookingManager\ManagerBase::checkAccess()
   */
  public function checkAccess(array &$results, bool $status = true) {
    $hours = $this->ManagerCreneaux->getHoursByUser();
    if ($hours > 0) {
      $results['access'] = true;
      $results['ban_reason'] = '';
    }
    else {
      $results['access'] = false;
      $results['ban_reason'] = " Vous devez acheter des heures afin de pouvoir selectionner les creneaux ";
    }
  }
  
}