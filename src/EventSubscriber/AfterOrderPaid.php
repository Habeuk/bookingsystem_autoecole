<?php

namespace Drupal\bookingsystem_autoecole\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\commerce_order\Event\OrderEvents;
use Drupal\commerce_order\Event\OrderEvent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\commerce_order\Entity\OrderInterface;

/**
 * bookingsystem_autoecole event subscriber.
 */
class AfterOrderPaid implements EventSubscriberInterface {
  /**
   *
   * @var EntityTypeManager
   */
  protected $entityTypeManger;
  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *        The messenger.
   */
  public function __construct(EntityTypeManager $entityTypeManger, MessengerInterface $messenger) {
    $this->messenger = $messenger;
    $this->entityTypeManger = $entityTypeManger;
  }

  public function onOrderPaid(OrderEvent $event) {
    $order = $event->getOrder();
    $this->AddHoursIfIscorrectProduct($order);
  }

  /*
   * Permet d'ajouter/creer les heures si les données sont valides.
   */
  protected function AddHoursIfIscorrectProduct(OrderInterface $order) {
    $items = $order->getItems();
    $hours_manuel = 0;
    $hours_auto = 0;
    $hours = 0;
    foreach ($items as $item) {
      /**
       *
       * @var \Drupal\commerce_order\Entity\OrderItem $item
       */
      /**
       *
       * @var \Drupal\commerce_product\Entity\ProductVariation $entityPurchase
       */
      $entityPurchase = $item->getPurchasedEntity();
      //
      if ($entityPurchase && ($entityPurchase->bundle() == 'forfait_heure' || $entityPurchase->bundle() == 'service_auto_ecole')) {
        if ($entityPurchase->hasField('field_hours')) {
          $qty = (int) $item->getQuantity();
          $type_de_transmission = $entityPurchase->get('field_type_de_transmission')->value;
          if ($type_de_transmission == 'automatique') {
            $hours_auto += $qty * $entityPurchase->get('field_hours')->value;
          } elseif ($type_de_transmission == 'manuelle') {
            $hours_manuel += $qty * $entityPurchase->get('field_hours')->value;
          } else {
            $hours += $qty * $entityPurchase->get('field_hours')->value;
          }
        }
      }
    }
    if ($hours_auto > 0) {
      $uid = \Drupal::currentUser()->id();
      $values = [
        'name' => $order->label(),
        'source' => 'order',
        'user_id' => $uid,
        'owner_heures_id' => $uid,
        'creneaux_live' => $hours_auto,
        'commerce_order' => $order->id(),
        'type_boite' => 'automatique'
      ];
      if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
        $values['booking_config_type'] = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain();
      } else {
        $configs = \Drupal::config("wb_horizon_public.config_auto_ecole");
        $values['booking_config_type'] = $configs->get("conduite_auto");
      }
      /**
       *
       * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $bks_autoecole_heures
       */
      $bks_autoecole_heures = $this->entityTypeManger->getStorage('bks_autoecole_heures')->create($values);
      $bks_autoecole_heures->save();
      $this->messenger->addMessage("Vous bénéficiez de : " . $hours_auto . " heure(s) pour la coduite automatique");
    }
    if ($hours_manuel > 0) {
      $uid = \Drupal::currentUser()->id();
      $values = [
        'name' => $order->label(),
        'source' => 'order',
        'user_id' => $uid,
        'owner_heures_id' => $uid,
        'creneaux_live' => $hours_manuel,
        'commerce_order' => $order->id(),
        'type_boite' => 'manuelle'
      ];
      if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
        $values['booking_config_type'] = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain();
      } else {
        $configs = \Drupal::config("wb_horizon_public.config_auto_ecole");
        $values['booking_config_type'] = $configs->get("conduite_manuelle");
      }
      /**
       *
       * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $bks_autoecole_heures
       */
      $bks_autoecole_heures = $this->entityTypeManger->getStorage('bks_autoecole_heures')->create($values);
      $bks_autoecole_heures->save();
      $this->messenger->addMessage(" Vous bénéficiez de : " . $hours_manuel . " heure(s) pour la coduite manuellle ");
    }
    if ($hours > 0) {
      $uid = \Drupal::currentUser()->id();
      $values = [
        'name' => $order->label(),
        'source' => 'order',
        'user_id' => $uid,
        'owner_heures_id' => $uid,
        'creneaux_live' => $hours,
        'commerce_order' => $order->id()
      ];
      if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
        $values['booking_config_type'] = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain();
      } else {
        $configs = \Drupal::config("wb_horizon_public.config_auto_ecole");
        $values['booking_config_type'] = $configs->get("conduite_manuelle");
      }
      /**
       *
       * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $bks_autoecole_heures
       */
      $bks_autoecole_heures = $this->entityTypeManger->getStorage('bks_autoecole_heures')->create($values);
      $bks_autoecole_heures->save();
      $this->messenger->addMessage(" Vous bénéficiez de : " . $hours . " heure(s) ");
    }
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      OrderEvents::ORDER_PAID => [
        'onOrderPaid'
      ]
    ];
  }
}
