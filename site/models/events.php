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

require_once JPATH_ROOT . '/components/com_events/lib/autoload.php';
require_once JPATH_ROOT . '/components/com_events/helpers/Event.php';
require_once JPATH_ROOT . '/components/com_events/helpers/RecurrentEvent.php';

use \Plummer\Calendarful\Event\EventRegistryInterface;
use \Plummer\Calendarful\Recurrence\RecurrenceFactory;
use \Calendarful\Elements\Event AS Event;
use \Calendarful\Elements\RecurrentEvent AS RecurrentEvent;

/**
 * Methods supporting a list of Events records.
 *
 * @since  1.6
 */
class EventsModelEvents extends JModelList implements EventRegistryInterface
{
	public function getEvents(array $filters = array())
	{

		$events = [];

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
		$query->select(
			$db->quoteName('c.color', 'color')
		);
		$query->select(
			$db->quoteName('c.text_color', 'text_color')
		);
		$query->from($db->quoteName('#__events_items', 'a'));
		$query->where($db->quoteName('a.start_date') . ' BETWEEN STR_TO_DATE('. $db->quote($filters['fromDate']->format('Y-m-d 00:00:00')) .', '.$db->quote('%Y-%m-%d %H:%i:%s').') AND STR_TO_DATE('. $db->quote($filters['toDate']->format('Y-m-d 23:59:59')) .', '.$db->quote('%Y-%m-%d %H:%i:%s').')');
		if(isset($this->category) && !empty($this->category)) {
			$query->where($db->quoteName('a.category') . ' = '. $db->quote($this->category));
		}
		$query->where($db->quoteName('a.state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('a.repeatable') . ' = '. $db->quote(0));
		$query->join('LEFT', '`#__events_categories` AS `c` ON `c`.`id` = a.`category`');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		if(!empty($results)) {
			foreach($results as $event) {
				$events[$event->id] = $this->toEvent($event);
			}
		}

		return $events;
	}

	public function getRecurrentEvents(array $filters = array())
	{

		$recurrentEvents = [];

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select(
			$db->quoteName(
				array(
					'a.id', 'a.title', 'a.category', 'a.start_date', 'a.end_date', 'a.adress', 'a.description', 'a.recurrence_type'
				)
			)
		);
		$query->select(
			$db->quoteName('c.title', 'category_title')
		);
		$query->select(
			$db->quoteName('c.color', 'color')
		);
		$query->select(
			$db->quoteName('c.text_color', 'text_color')
		);
		$query->from($db->quoteName('#__events_items', 'a'));

		if(isset($this->category) && !empty($this->category)) {
			$query->where($db->quoteName('a.category') . ' = '. $db->quote($this->category));
		}
		$query->where($db->quoteName('a.state') . ' = '. $db->quote(1));
		$query->where($db->quoteName('a.repeatable') . ' = '. $db->quote(1));
		$query->join('LEFT', '`#__events_categories` AS `c` ON `c`.`id` = a.`category`');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		if(!empty($results)) {
			foreach($results as $event) {
				$recurrentEvents[$event->id] = $this->toRecurrenceEvent($event);
			}
		}

		return $recurrentEvents;
	}

	private function toRecurrenceEvent($item)
	{
		if(!is_object($item) || empty($item)) {
			return false;
		}

		$data = array(
				'title' => $item->title,
				'color' => $item->color,
				'textColor' => $item->text_color
			);

		$event = new RecurrentEvent(
					$item->id,
					$item->start_date,
					$item->end_date,
					$item->parent_id,
					$item->occurrence_date,
					$item->recurrence_type,
					$item->recurrence_until,
					$data
				);
		
		return $event;
	}

	private function toEvent($item)
	{
		if(!is_object($item) || empty($item)) {
			return false;
		}

		$data = array(
				'title' => $item->title,
				'color' => $item->color,
				'textColor' => $item->text_color
			);

		$event = new Event(
					$item->id,
					$item->start_date,
					$item->end_date,
					$item->parent_id,
					$item->occurrence_date,
					$data
				);
		
		return $event;
	}

}
