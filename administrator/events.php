<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Events
 * @author     Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_events'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Events', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Events');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
