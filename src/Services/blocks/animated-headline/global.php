<?php

if( !defined('QX_ELEMENT_ANIMATED-HEADLINE') ) {
  define('QX_ELEMENT_ANIMATED-HEADLINE', true); 
  // JFactory::getDocument()->addStylesheet(QUIX_URL."/app/frontend/elements/animated-headline/animated-headline.css");
  // JFactory::getDocument()->addScript(QUIX_URL."/app/frontend/elements/animated-headline/script.js");

  Assets::Css('animated-headline', QUIX_URL."/app/frontend/elements/animated-headline/animated-headline.css");
  Assets::Js('animated-headline', QUIX_URL."/app/frontend/elements/animated-headline/script.js");
}