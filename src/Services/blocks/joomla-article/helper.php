<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\Registry\Registry;
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
* Joomla article element class
* instead of using direct method use class
*/
class QuixJoomlaArticleElement
{   
    public static function getListJoomlaArticle() {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select('id, title')
                    ->from('#__content')
                    ->where('state = 1');

        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
  

    public static function getAjax($data = array())
    {
        $app       = JFactory::getApplication();
        if(!$data){
          $data = $app->input->get('data', '', 'BASE64', 'raw');
          $data = base64_decode($data);      
        }

        $params = new Registry($data);
        $id = $params->get('id');
        $show_date_field  = $params->get('show_date_field', 'publish_up');
        $show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');

        // add precheck to avoid error when article trashed or removed
        $hasArticle = QuixJoomlaArticleElement::articleExist($id);
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
        $item = $model->getItem($id);
        if($item)
        {
            $item->slug    = $item->id . ':' . $item->alias;
            $item->images = json_decode($item->images);
            $item->displayDate = JHtml::_('date', $item->$show_date_field, $show_date_format);
            $item->link = ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language);

            $item->link = JRoute::_($item->link);
            
            $item->text = $item->introtext . ' ' . $item->fulltext;
            $item->text = JHtml::_('content.prepare', $item->text, '', 'com_content.article');
            
            $item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'com_content.article');
            $item->displayIntrotext = self::_cleanIntrotext($item->introtext);

            $item->fulltext = JHtml::_('content.prepare', $item->fulltext, '', 'com_content.article');
        }

        return $item;
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

    /**
   * Strips unnecessary tags from the introtext
   *
   * @param   string  $introtext  introtext to sanitize
   *
   * @return mixed|string
   *
   * @since  1.6
   */
    public static function _cleanIntrotext($introtext)
    {
        $introtext = str_replace('<p>', ' ', $introtext);
        $introtext = str_replace('</p>', ' ', $introtext);
        $introtext = strip_tags($introtext, '<a><em><strong>');
        $introtext = trim($introtext);

        return $introtext;
    }
}
