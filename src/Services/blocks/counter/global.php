<?php
if( !defined('QX_ELEMENT_COUNTER') ) {
  define('QX_ELEMENT_COUNTER', true); 
  // JFactory::getDocument()->addScript(QUIX_URL."/app/frontend/elements/counter/counter.js");
  Assets::Js('counter-element', QUIX_URL."/app/frontend/elements/counter/counter.js");
}