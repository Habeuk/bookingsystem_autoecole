<?php

namespace Drupal\bookingsystem_autoecole\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\commerce_order\Event\OrderEvents;
use Drupal\commerce_order\Event\OrderEvent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\lesroidelareno\lesroidelareno;

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
      
      if ($entityPurchase && $entityPurchase->bundle() == 'forfait_heure') {
        if ($entityPurchase->hasField('field_hours')) {
          $qty = (int) $item->getQuantity();
          $hours += $qty * $entityPurchase->get('field_hours')->value;
        }
      }
    }
    if ($hours > 0) {
      $uid = \Drupal::currentUser()->id();
      $values = [
        'booking_config_type' => lesroidelareno::getCurrentDomainId(),
        'name' => $order->label(),
        'source' => 'order',
        'user_id' => $uid,
        'owner_heures_id' => $uid,
        'heures' => $hours,
        'commerce_order' => $order->id()
      ];
      /**
       *
       * @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $bks_autoecole_heures
       */
      $bks_autoecole_heures = $this->entityTypeManger->getStorage('bks_autoecole_heures')->create($values);
      $bks_autoecole_heures->save();
      $this->messenger->addMessage("Vous bénéficiez de : " . $hours . " heure(s)");
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

