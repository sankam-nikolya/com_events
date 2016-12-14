<?php

/**
 * @version	CVS: 1.0.0
 * @package	Com_Events
 * @author	 Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license	GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Events records.
 *
 * @since  1.6
 */
class EventModelEvent extends JModelList
{
	
	public function getEvent($id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select(
			$db->quoteName(
				array(
					'a.id', 'a.title', 'a.category', 'a.start_date', 'a.end_date', 'a.adress', 'a.description'
				)
			)
		);
		$query->select(
			$db->quoteName('c.title', 'category_title')
		);
		$query->from($db->quoteName('#__events_items', 'a'));
		$query->where($db->quoteName('a.id') . ' = '. $db->quote($id));
		$query->where($db->quoteName('a.state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('a.override_event') . ' = '. $db->quote(0));
		$query->join('LEFT', '`#__events_categories` AS `c` ON `c`.`id` = a.`category`');
		$query->setLimit(1);

		$db->setQuery($query);

		$result = $db->loadObject();

		if(empty($result)) {
			return false;
		}

		return $result;
	}

}
