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
  public function build() {
    /**
     *
     * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
     */
    $domain_source = \Drupal::service('domain_source.path_processor');
    $domain = $domain_source->getActiveDomain();
    if (!$domain || !$this->currentUser()->id()) {
      $this->messenger()->addWarning('Vous devez etre connecter afin de pouvoir effectuer une reservation');
      $build['content'] = [];
      return $build;
    }
    /**
     * L'id de la config est exactement l'id du domaine.
     *
     * @var string $booking_config_type_id
     */
    
    $booking_config_type_id = $domain->id(); // on definit cet id juste pour les
                                             // tests.
    $urlCalendar = Url::fromRoute("bookingsystem_autoecole.app_load_config_calendar");
    $urlCreneaux = Url::fromRoute("bookingsystem_autoecole.app_load_creneaux", [
      'booking_config_type_id' => $booking_config_type_id,
      'date' => null
    ]);
    $urlSave = Url::fromRoute("booking_system.save_reservation", [
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
          'm-5',
          'p-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'booking_system/booking_system_app2';
    return $build;
  }
  
  /**
   * Permet de generer et de configurer RDV par domaine.
   */
  public function ConfigureDefault() {
    $entity_type_id = "booking_config_type";
    /**
     *
     * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
     */
    $domain_source = \Drupal::service('domain_source.path_processor');
    $domain = $domain_source->getActiveDomain();
    $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($domain->id());
    if (!$entityConfig) {
      $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create([
        'id' => $domain->id(),
        'label' => $domain->label(),
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
