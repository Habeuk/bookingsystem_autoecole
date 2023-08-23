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
   * Prepare le contenu du mail à envoyer à l'utilisateur.
   *
   * @param BookingReservation $reservation
   */
  protected function prepareMailToUser(BookingReservation $reservation) {
    $email = \Drupal::currentUser()->getEmail();
    $creneaux = $reservation->getCreneauxReatable();
    if ($email && $creneaux) {
      $subject = "Reservation of a slot";
      $messages['titre'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => 'You have booked a slot'
      ];
      if (count($creneaux) > 1) {
        $subject = "Reservation of slots";
        $messages['titre'] = [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => 'You have booked slots'
        ];
      }
      $messages['creneaux'] = $creneaux;
      $this->sendMails($email, $subject, [
        '#theme' => 'wbh_php_mailer_plugin_mail',
        '#description' => $messages,
        '#footer' => "Copyright © Wb-Horizon - 2022"
      ]);
    }
  }
  
}