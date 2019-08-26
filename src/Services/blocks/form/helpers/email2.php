<?php

/**
 * @version    1.0.0
 * @package    Contact Form Quix element
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;


/**
* QuixSimpleContactElement helper class
*/
class QuixFormElementHelperEmail2
{
    /**
     * Basic method
     * params data = form data, config = action hooks settings, info = elements settings
     */
    public static function action($data, $config, $info)
    {
        $sysconfig = JFactory::getConfig();
        
        $configNew = array();
        foreach ($config as $key => $conf) {
            $keyName = key($conf);
            $configNew[$keyName] = new stdClass;
            $configNew[$keyName]->name = $keyName;
            $configNew[$keyName]->value = $conf->$keyName;
        }

        /*
        * validation successful, do your job here
        */
        // Now send email
        $mail = JFactory::getMailer();
        
        // add recipient        
        $recipient = $configNew['email2_to']->value;
        if( empty($recipient))
        {
            $recipient = $sysconfig->get('mailfrom');
        }
        $mail->addRecipient($recipient);

        // sender is system email
        $name = $sysconfig->get('fromname');
        $email = JStringPunycode::emailToPunycode($sysconfig->get('mailfrom'));
        $mail->setSender(array($email, $name));

        // set subject
        $subject = $configNew['email2_subject']->value;
        $mail->setSubject($subject);

        // set reply_to
        $reply_to = isset($configNew['reply_to']->value) ? $configNew['reply_to']->value : 'none';
        if ( $reply_to == 'system' ) {
            $mail->addReplyTo($email);
        }elseif( $reply_to == 'emailfield' && isset($data['email']) && !empty($data['email'])){ // from users input
            $mail->addReplyTo($data['email']);
        }
        
        
        // add cc and bcc
        if (!empty($configNew['email_cc']->value)) {
            $emailcc = explode(',', $configNew['email_cc']->value);
            $mail->addCc($emailcc);
        }
        if (!empty($configNew['email_bcc']->value)) {
            $email_bcc = explode(',', $configNew['email_bcc']->value);
            $mail->addBcc($email_bcc);
        }
    
        // get shortcodes
        $prepareShortcode = self::getAllData($data, $config, $info);
        
        // set subject
        $subjectText = $configNew['email_subject']->value;
        $subject = strtr($subjectText, $prepareShortcode);        
        $mail->setSubject($subject);

        // prepare body
        $content = $configNew['email2_content']->value;
        if(is_array($content)) $content = implode("", $content);
        
        
        $body = strtr($content, $prepareShortcode);

        // add meta
        $email_metas = $configNew['email2_meta']->value;
        $credit = false;
        if(count($email_metas))
        {
            $bodyTag = '<p style="text-align: center;font-family: monospace;padding: 10px 0;margin: 0;"><small>';
            $bodyTagFooter = '';
            foreach ($email_metas as $key => $meta) {
                switch ($meta) {
                    case 'date':
                        $bodyTag .= Date("Y/m/d") . ' | ';
                        break;

                    case 'time':
                        $bodyTag .= Date("h:i:sa") . ' | ';
                        break;

                    case 'page_url':
                        $bodyTag .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' | ';
                        break;
                        
                    case 'user_agent':
                        $bodyTag .= $_SERVER['HTTP_USER_AGENT'] . ' | ';
                        break;

                    case 'remote_ip':
                        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $ip = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        }
                        $bodyTag .= $ip . ' | ';
                        break;                  
                    case 'credit':
                        $credit = true;
                        break;

                }
            }
            $bodyTag .= '</small></p>';

            $body .= $bodyTag;
        }
        if($credit){
            $body .= '<p><small><center>Powered by Quix - Joomla page builder</center></small></p>';
        }
        
        $mail->setBody($body);

        // email_sendas
        if($configNew['email2_sendas']->value == 'html')
        {
            $mail->isHTML(true);
            $mail->Encoding = 'base64';
        }
        else
        {
            $mail->isHTML(false);
        }
                
        return $mail->Send();
    }
    
    /*
    * data is form data
    * config is after email hook, is this event hook
    * info is element config
    */
    public static function getAllData($data, $config, $info)
    {
        $codes = [];
        $form_fiels = $info->general->form_fields;

        foreach ($form_fiels as $key => $fields) {
            
            $name = strtolower($fields[0]->value);
            $value = (isset($data[$name]) ? $data[$name] : '');

            foreach ($fields as $key2 => $field) {
                if($field->name == 'shortcode'){
                    if(!empty($field->value)){
                        $codes[$field->value] = $value;
                    }
                }
            }
        }

        // now add all-fields
        // now add all-fields
        $body = '<table cellpadding="5" cellspacing="1" border="0" bgcolor="#FFFFFF"><tbody>';
        foreach ($data as $key => $value) {
            if ($key == 'info') {
                continue;
            }
            $body .= "<tr><th align='left' valign='top'>" . ucfirst($key) . '</th>';
            $body .= '<td>' . $value . "</td></tr>\n";
        }
        $body .= '<tbody></table>';
        $codes['[all-fields]'] = $body;

        return $codes;
    }

}