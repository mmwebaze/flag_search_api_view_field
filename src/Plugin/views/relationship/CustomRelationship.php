<?php

namespace Drupal\flag_search_api_view_field\Plugin\views\relationship;

use Drupal\flag\Plugin\views\relationship\FlagViewsRelationship;
use Drupal\views\Plugin\views\relationship\RelationshipPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Views;

/**
 * Add custom view relationship
 *
 * @ingroup views_relationship_handlers
 *
 * @ViewsRelationship("flag_search_api_view_relationship_plugin")
 */
class CustomRelationship extends FlagViewsRelationship  {

}