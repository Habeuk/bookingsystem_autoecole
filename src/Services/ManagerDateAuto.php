<?php

namespace Drupal\bookingsystem_autoecole\Services;

use Drupal\booking_system\Entity\BookingReservation;
use Drupal\booking_system\Services\BookingManager\ManagerDate;

/**
 * Permet de gerer l'affichage du calendrier ou des jours à afficher pour les
 * cas auto-ecoles, donc, la particularite est d'avoir une "limit_reservation"
 * dynamique.
 */
class ManagerDateAuto extends ManagerDate {
  
  /**
   * Permet de selectionner le type de boite de transmission.
   *
   * @var string
   */
  public $type_boite = 'manuelle';
  
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
  
  /**
   *
   * @param array $values
   */
  public function retrancheLesHeures(array $values, $type_boite) {
    if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
      $filters = [
        'owner_heures_id' => \Drupal\lesroidelareno\lesroidelareno::getCurrentUserId(),
        \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId()
      ];
    }
    if ($type_boite == 'automatique' || $type_boite == 'manuelle') {
      $filters['type_boite'] = $type_boite;
    }
    $bks_autoecole_heures = $this->entityTypeManager->getStorage('bks_autoecole_heures')->loadByProperties($filters);
    $hours = count($values['creneaux']);
    foreach ($bks_autoecole_heures as $bks_autoecole_heure) {
      /**
       *
       * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $bks_autoecole_heure
       */
      $timeLive = $bks_autoecole_heure->getCreneauxLive();
      if ($timeLive >= $hours) {
        $temp = $timeLive - $hours;
        $bks_autoecole_heure->setCreneauxLive($temp);
        $bks_autoecole_heure->save();
        break;
      }
      else {
        $hours = $hours - $timeLive;
        $bks_autoecole_heure->setCreneauxLive(0);
        $bks_autoecole_heure->save();
      }
    }
  }
  
  protected function getHoursRemainds() {
    $hours = 0;
    $filters = [
      'type_boite' => $this->type_boite
    ];
    if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
      $filters['owner_heures_id'] = \Drupal\lesroidelareno\lesroidelareno::getCurrentUserId();
      $filters[\Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD] = \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId();
    }
    $entities = $this->entityTypeManager->getStorage('bks_autoecole_heures')->loadByProperties($filters);
    if ($entities) {
      foreach ($entities as $entity) {
        /**
         *
         * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $entity
         */
        $hours += $entity->getCreneauxLive();
      }
    }
    return $hours;
  }
  
  /**
   * Prepare le contenu du mail à envoyer à l'utilisateur.
   *
   * @param BookingReservation $reservation
   */
  protected function prepareMailToUser(BookingReservation $reservation) {
    $email = \Drupal::currentUser()->getEmail();
    $creneaux = $reservation->getCreneauxReatable();
    if ($email && $creneaux) {
      $subject = t("Reservation of a slot");
      $messages['titre'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => t('You have booked a slot')
      ];
      if (count($creneaux) > 1) {
        $subject = t("Reservation of slots");
        $messages['titre'] = [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => t('You have booked slots')
        ];
      }
      //
      $messages['type_boite'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => " Boite " . $this->type_boite
      ];
      $messages['hours'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => " Heures restantes :  " . $this->getHoursRemainds()
      ];
      //
      $messages['creneaux'] = $creneaux;
      $this->sendMails($email, $subject, [
        '#theme' => 'wbh_php_mailer_plugin_mail',
        '#description' => $messages,
        '#footer' => "Copyright © Wb-Horizon - 2022"
      ]);
    }
  }
  
}
