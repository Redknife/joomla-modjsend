<?php
defined('_JEXEC') or die('Restricted access');
define('MOD_JSEND_FIELD_REQUIRED', 2);
define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.filesystem.file');

sendHelper::send();

class sendHelper{

    public static $name;
    public static $email;
    public static $desc;
    public static $phone;
    public static $firm;
    public static $attachment;
    public static $header;

    public static $client_subject;
    public static $client_body;
    public static $client_sign;

    public static $manager_subject;
    public static $manager_body;
    public static $manager_email;

    public static $list_val;

    public static $page_redirect;

    public static function fmtFilesArray($name, $type, $tmp_name, $error, $size)
    {
            $name = ru_RULocalise::transliterate(str_replace(' ', '_',$name));
            $name = JFile::makeSafe($name);
            return array(
                    'name'		=> $name,
                    'type'		=> $type,
                    'tmp_name'	        => $tmp_name,
                    'error'		=> $error,
                    'size'		=> $size,
                    'filepath'	=> JPATH_BASE.DS.'tmp'.DS.$name
            );
    }

    public static function canUpload($file, &$err)
    {
            $params = JComponentHelper::getParams('com_media');

            if (empty($file['name'])) {
                    $err = 'COM_MEDIA_ERROR_UPLOAD_INPUT';
                    return false;
            }

            if ($file['name'] !== JFile::makesafe($file['name'])) {
                    $err = 'COM_MEDIA_ERROR_WARNFILENAME';
                    return false;
            }

            $format = strtolower(JFile::getExt($file['name']));

            $allowable = explode(',', $params->get('upload_extensions'));
            $ignored = explode(',', $params->get('ignore_extensions'));
            if (!in_array($format, $allowable) && !in_array($format, $ignored))
            {
                    $err = 'COM_MEDIA_ERROR_WARNFILETYPE';
                    return false;
            }

            $maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
            if ($maxSize > 0 && (int) $file['size'] > $maxSize)
            {
                    $err = 'COM_MEDIA_ERROR_WARNFILETOOLARGE';
                    return false;
            }

            $xss_check =  JFile::read($file['tmp_name'], false, 256);
            $html_tags = array('abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink', 'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext', 'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object', 'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar', 'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--');
            foreach($html_tags as $tag) {
                    // A tag is '<tagname ', so we need to add < and a space or '<tagname>'
                    if (stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
                            $err = 'COM_MEDIA_ERROR_WARNIEXSS';
                            return false;
                    }
            }
            return true;
    }

    public static function check()
    {
        //load language constants
        $lang = JFactory::getLanguage();
        $lang->load('mod_jsend');
        //application
        $app = JFactory::getApplication();
        //load com_media params
        $params = JComponentHelper::getParams('com_media');


        $jinput = $app->input;

        //check token
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $position = base64_decode($jinput->get('hash', '', 'BASE64'));
        if ($position)
        {
            $modules = JModuleHelper::getModules($position);

            foreach($modules as $module)
            {
                if ($module->module == 'mod_jsend')
                {
                    $jsend_params = new JRegistry($module->params);
                    //load mod_jsend params
                    // $jsend = JModuleHelper::getModules($position);
                    // $jsend_params = new JRegistry($jsend[0]->params);

                    $menu = $app->getMenu();
                    self::$page_redirect = $menu->getItem($jsend_params->get('form_redirect'))->route;

                    self::$header = $jsend_params->get('header');
                    self::$client_subject = $jsend_params->get('client_subject');
                    self::$client_body = $jsend_params->get('client_body');
                    self::$client_sign = $jsend_params->get('client_sign');

                    self::$manager_subject = $jsend_params->get('manager_subject');
                    self::$manager_body = $jsend_params->get('manager_body');
                    self::$manager_email = $jsend_params->get('manager_email');
                }
            }
        }

        if ($showlist = $jsend_params->get('showlist', 0) > 0)
        {
            self::$list_val = $jinput->getVar('list', '');
        }

        if ($showemail = $jsend_params->get('showemail', 0) > 0)
        {
            self::$email = $jinput->getVar('email', '');

            if ($showemail === MOD_JSEND_FIELD_REQUIRED && !preg_match("~^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+$~i", self::$email))
            {
                    $app->redirect('/', JText::_('Некоректный Email'));
                    return;
            }
        }

        if ($showfirm = $jsend_params->get('showfirm', 0) > 0)
        {
            self::$firm = $jinput->getVar('firm', '');
            if ($showfirm === MOD_JSEND_FIELD_REQUIRED && !self::$firm)
            {
                    $app->redirect('/', JText::_('Некоректное значение поля "Название организации"'));
                    return;
            }
        }

        if ($showname = $jsend_params->get('showname', 0) > 0)
        {
            self::$name = $jinput->getVar('name', '');

            if ($showname === MOD_JSEND_FIELD_REQUIRED && (!self::$name || strlen(self::$name) < 3))
            {
                    $app->redirect('/', JText::_('Некоректное имя'));
                    return;
            }
        }

        if ($showphone = $jsend_params->get('showphone', 0) > 0)
        {
            self::$phone = $jinput->getVar('phone', '');

            if ($showphone === MOD_JSEND_FIELD_REQUIRED && !self::$phone)
            {
                    $app->redirect('/', JText::_('Некоректный телефон'));
                    return;
            }

        }

        if ($showdesc = $jsend_params->get('showdesc', 0) > 0)
        {
            self::$desc = $jinput->getVar('desc', '');

            if ($showdesc === MOD_JSEND_FIELD_REQUIRED && !self::$desc)
            {
                    $app->redirect('/', JText::_('Некоректное сообщение'));
                    return;
            }
        }

        if ($showfileupload = $jsend_params->get('showfileupload', 0) > 0)
        {
            $files = $jinput->files->get('Filedata', '', 'files', 'array');

            $files = array_map(array(self, 'fmtFilesArray'), (array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']);

            if ($files && !empty($files)) foreach ($files as $file)
            {
                    if (!$file['error'])
                    {

                        if (!self::canUpload($file, $err))
                            {
                                    // The file can't be upload
                                    JError::raiseNotice(100, JText::_($err));
                                    return false;
                            }

                        if (!JFile::upload($file['tmp_name'], $file['filepath']))
                        {
                                // Error in upload
                                JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
                                return false;
                        }

                        self::$attachment[] = $file['filepath'];
                    }
            }
        }

    }

    public static function send()
    {
        sendHelper::check();

        $app = JFactory::getApplication();
        $mail = JFactory::getMailer();

        $mailfrom	= $app->getCfg('mailfrom');
        $fromname	= $app->getCfg('fromname');
        $sitename	= $app->getCfg('sitename');

        $url_refer = $_SERVER['HTTP_REFERER'];

        $mail->addRecipient(self::$manager_email);
        $mail->addReplyTo(array(self::$manager_email, self::$name));
        $mail->setSender(array($mailfrom, $fromname));
        $mail->setSubject(self::$manager_subject);

        $body = '';

        if (self::$name)
            $body .= 'ФИО отправителя: '.self::$name."\n";

        if (self::$phone)
            $body .= 'Телефон: '.self::$phone."\n";

        if (self::$email)
            $body .= 'E-mail: '.self::$email."\n";

        if (self::$firm)
            $body .= 'Название организации: '.self::$firm."\n";

        if (self::$list_val)
        {
            $body .= 'Список: '.self::$list_val."\n";
        }
        if (self::$desc)
            $body .= 'Описание вопроса: '.self::$desc."\n";

        $body .= 'Страница отправки: '.$url_refer."\n";

        $body .= strip_tags(str_replace(array('<br />','<br/>'), "\n", self::$manager_body));

        $mail->setBody($body);

        if (self::$attachment && !empty(self::$attachment))
            $mail->addAttachment(self::$attachment);

        $sent = $mail->Send();

        if (self::$email)
        {
            $mail = JFactory::getMailer();
            $mail->addRecipient(self::$email);
            $mail->addReplyTo(array(self::$email, self::$name));
            $mail->setSender(array($mailfrom, $fromname));
            $mail->setSubject(self::$client_subject);

            $body = strip_tags(str_replace(array('<br />','<br/>'), "\n", self::$client_body)."\n");

            $body .= strip_tags(str_replace(array('<br />','<br/>'), "\n", self::$client_sign));

            $mail->setBody($body);

            $sent = $mail->Send();
        }

        if ($sent) $app->redirect(self::$page_redirect, '');
    }
}





?>