<?php

namespace Drupal\bookingsystem_autoecole\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\lesroidelareno\lesroidelareno;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Returns responses for bookingsystem_autoecole routes.
 */
class BookingsystemAutoecoleController extends ControllerBase {
  
  /**
   * Builds the response to showing the Vue-js app.
   * On definit les urls pour l'initialisation de l'application.
   */
  public function build($type_boite) {
    if (!$this->currentUser()->id()) {
      $this->messenger()->addWarning('Vous devez etre connecter afin de pouvoir effectuer une reservation');
      $build['content'] = [];
      return $build;
    }
    /**
     * L'id de la config est exactement l'id du domaine.
     *
     * @var string $booking_config_type_id
     */
    $booking_config_type_id = lesroidelareno::getCurrentDomainId();
    if ($type_boite == 'automatique')
      $booking_config_type_id = $booking_config_type_id . 'auto';
    $urlCalendar = Url::fromRoute("bookingsystem_autoecole.app_load_config_calendar");
    $urlCreneaux = Url::fromRoute("bookingsystem_autoecole.app_load_creneaux", [
      'booking_config_type_id' => $booking_config_type_id,
      'type_boite' => $type_boite,
      'date' => null
    ]);
    $urlSave = Url::fromRoute("bookingsystem_autoecole.save_reservation", [
      'booking_config_type_id' => $booking_config_type_id
    ]);
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'data-url-calendar' => '/' . $urlCalendar->getInternalPath(),
        'data-url-creneaux' => '/' . $urlCreneaux->getInternalPath(),
        'data-url-save' => '/' . $urlSave->getInternalPath(),
        'class' => [
          'p-md-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'booking_system/booking_system_app2';
    return $build;
  }
  
  /**
   * Permet de generer et de configurer RDV par domaine pour le type de
   * transmission manuel.
   */
  public function ConfigureDefault() {
    $entity_type_id = "booking_config_type";
    $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load(lesroidelareno::getCurrentDomainId());
    if (!$entityConfig) {
      $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create([
        'id' => lesroidelareno::getCurrentDomainId(),
        'label' => 'Configuration des creneaux boite manuelle',
        'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS
      ]);
      $entityConfig->save();
    }
    // dd($entityConfig->toArray());
    // $entityConfig->save();
    
    $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
      'redirect_route' => 'bookingsystem_autoecole.config_resume',
      'booking_config_type_id' => $entityConfig->id()
    ]);
    return $form;
  }
  
  /**
   * Permet de generer et de configurer RDV par domaine pour les boites
   * Automatique.
   */
  public function ConfigureDefaultBoiteAuto() {
    $entity_type_id = "booking_config_type";
    $key = lesroidelareno::getCurrentDomainId() . 'auto';
    $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($key);
    if (!$entityConfig) {
      $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create([
        'id' => $key,
        'label' => 'Configuration des creneaux boite automatique',
        'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS
      ]);
      $entityConfig->save();
    }
    // dd($entityConfig->toArray());
    // $entityConfig->save();
    
    $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
      'redirect_route' => 'bookingsystem_autoecole.config_resume',
      'booking_config_type_id' => $entityConfig->id()
    ]);
    return $form;
  }
  
}
