<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Events
 * @author     Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Events', JPATH_COMPONENT);
JLoader::register('EventsController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Events');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
