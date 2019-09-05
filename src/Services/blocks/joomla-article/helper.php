<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
* Joomla article element class
* instead of using direct method use class
*/
class QuixJoomlSingleArticleElementClass
{   
    public static function getListJoomlaArticle() {

    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
                ->select('id, title')
                ->from('#__content');

    $db->setQuery($query);
    $items = $db->loadObjectList();
    return $items;
  } 
  
  public static function getJoomlaArticle($id) 
  {

    $hasArticle = QuixJoomlSingleArticleElementClass::articleExist($id);
    if(!$hasArticle){
        return false;
    }

    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

    // Get an instance of the generic articles model
    $model = JModelLegacy::getInstance( 'Article', 'ContentModel', [ 'ignore_request' => true ] );
    $model->setState( 'filter.published', 1 );

    // Access filter
    $params = JComponentHelper::getParams( 'com_content' );
    $access = ! $params->get( 'show_noauth' );
    $model->setState( 'filter.access', $access );
    
    // Load the parameters.
    $app = JFactory::getApplication('site');
    if(!$app->isAdmin()){
        $params = $app->getParams();
    }
    $model->setState('params', $params);

    // Retrieve Content
    $items = $model->getItem($id);

    return $items;
  }  

  public static function articleExist($id)
  {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true)
                  ->select('id, title')
                  ->from('#__content')
                  ->where('id = ' . $id)
                  ->where('state = 1');

      $db->setQuery($query);
      $item = $db->loadObject();
      
      if(!empty($item) and isset($item->id) ) return true;

      return false;
  }
}
