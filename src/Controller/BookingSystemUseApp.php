<?php

namespace Drupal\bookingsystem_autoecole\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stephane888\DrupalUtility\HttpResponse;
use Stephane888\Debug\ExceptionExtractMessage;
use Drupal\Component\Serialization\Json;
use Drupal\bookingsystem_autoecole\Services\ManagerCreneauxAuto;
use Drupal\bookingsystem_autoecole\Services\ManagerDateAuto;

/**
 * Returns responses for bookingsystem_autoecole routes.
 */
class BookingSystemUseApp extends ControllerBase {

  /**
   *
   * @var ManagerDateAuto
   */
  protected $BookingMangerDate;

  /**
   *
   * @var ManagerCreneauxAuto
   */
  protected $ManagerCreneaux;

  public function __construct(ManagerDateAuto $ManagerDate, ManagerCreneauxAuto $ManagerCreneaux) {
    $this->BookingMangerDate = $ManagerDate;
    $this->ManagerCreneaux = $ManagerCreneaux;
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('bookingsystem_autoecole.app_manager_date'), $container->get('bookingsystem_autoecole.app_manager_creneaux'));
  }

  /**
   * Permet de charger la configuration par defaut.
   */
  public function loadConfigCalandar(Request $Request) {
    try {
      $booking_config_type_id = '';
      /**
       *
       * @var string $booking_config_type_id
       */
      if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
        $booking_config_type_id = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain();
      } else {
        $configs = $this->config("wb_horizon_public.config_auto_ecole");
        $booking_config_type_id = $configs->get("conduite_auto");
      }
      $configs = $this->BookingMangerDate->loadBookingConfig($booking_config_type_id);
      return HttpResponse::response($configs);
    } catch (\Exception $e) {
      return HttpResponse::response(
        [
          "maintennace" => true,
          "ban_reason" => "<div>Erreur de configuration</div>"
        ]
      );      // return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    } catch (\Error $e) {
      return HttpResponse::response(
        [
          "maintennace" => true,
          "ban_reason" => "<div>Erreur de configuration</div>"
        ]
      );

      // return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    }
  }

  /**
   * Permet de recuperer les données de configurations pour la construction des
   * creneaux.
   *
   * @param string $booking_config_type_id
   * @param string $date
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function loadConfisCreneaux($booking_config_type_id, $date, $type_boite) {
    try {
      if (!(\Drupal::moduleHandler()->moduleExists('lesroidelareno'))) {
        $configEntry = "conduite_" . ($type_boite == "manuelle" ? "manuelle" : "auto");
        $configs = $this->config("wb_horizon_public.config_auto_ecole");
        $booking_config_type_id = $configs->get($configEntry);
      }
      $this->ManagerCreneaux->type_boite = $type_boite;
      $configs = $this->ManagerCreneaux->loadCreneaux($booking_config_type_id, $date);
      return HttpResponse::response($configs);
    } catch (\Exception $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    } catch (\Error $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    }
  }

  /**
   * Enregistrer un creneau.
   *
   * @param string $booking_config_type_id
   */
  public function SaveReservation(Request $Request, string $booking_config_type_id, $type_boite) {
    try {
      if (!(\Drupal::moduleHandler()->moduleExists('lesroidelareno'))) {
        $configEntry = "conduite_" . ($type_boite == "manuelle" ? "manuelle" : "auto");
        $configs = $this->config("wb_horizon_public.config_auto_ecole");
        $booking_config_type_id = $configs->get($configEntry);
      }
      $values = Json::decode($Request->getContent());
      $this->BookingMangerDate->type_boite = $type_boite;
      $configs = $this->BookingMangerDate->saveCreneaux($booking_config_type_id, $values);
      $this->BookingMangerDate->retrancheLesHeures($values, $type_boite);
      //
      return HttpResponse::response($configs);
    } catch (\Exception $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    } catch (\Error $e) {
      return HttpResponse::response(ExceptionExtractMessage::errorAll($e), 435);
    }
  }
}
