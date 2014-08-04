<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jsend
 *
 * @copyright   Copyright (C) 2012 Saity 74 LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$layout = $params->get('layout', 'default');

require JModuleHelper::getLayoutPath('mod_jsend', $layout);
