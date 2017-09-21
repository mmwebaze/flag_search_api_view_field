<?php

namespace Drupal\flag_search_api_view_field\Plugin\views\field;
use Drupal\Core\Entity\EntityInterface;
use Drupal\flag\FlagServiceInterface;
use Drupal\flag\Plugin\ActionLink\FormEntryInterface;
use Drupal\flag\Plugin\views\field\FlagViewsLinkField;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("flag_search_api_view_field_plugin")
 */
class FlagViewsFlaggerField extends FlagViewsLinkField  {
  protected $flag_service;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, FlagServiceInterface $flag_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->flag_service = $flag_service;
  }


  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['flag_type'] = array('default' => 'flag');
    if (isset($options['relationship'])){
      unset($options['relationship']);
    }

    return $options;
  }
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $flagTypes = \Drupal::entityTypeManager()->getStorage('flag')->loadMultiple();
    $options = [];
    foreach ($flagTypes as $key  => $flagType){
      $options[$key] = $flagType->id();
    }
    $form['flag_type'] = array(
      '#title' => $this->t('Which flag type should be flagged?'),
      '#type' => 'select',
      '#default_value' => $this->options['flag_type'],
      '#options' => $options,
    );
    parent::buildOptionsForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $ids = $storage->getQuery()->condition('nid', explode("/", $values->search_api_url[0])[2], '=')->execute();
    $node = $storage->loadMultiple($ids);
    $entity = current($node);

    return $this->renderLink($entity, $values);
  }
  /**
   * {@inheritdoc}
   */
  protected function renderLink(EntityInterface $entity, ResultRow $values) {
    // Output nothing as there is no flag.
    // For an 'empty text' option use the default 'No results behavior'
    // option provided by Views.
    if (empty($entity)) {
      return '';
    }

    $flag = $this->flag_service->getFlagById($this->options['flag_type']);

    $link_type_plugin = $flag->getLinkTypePlugin();

    return $link_type_plugin->getAsFlagLink($flag, $entity);

  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('flag')
    );
  }
}