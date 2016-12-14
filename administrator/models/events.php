<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Events
 * @author     Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Events records.
 *
 * @since  1.6
 */
class EventsModelEvents extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'title', 'a.`title`',
				'category', 'a.`category`',
				'start_date', 'a.`start_date`',
				'end_date', 'a.`end_date`',
				'repeatable', 'a.`repeatable`',
				'occurrence_date', 'a.`occurrence_date`',
				'recurrence_type', 'a.`recurrence_type`',
				'recurrence_until', 'a.`recurrence_until`',
				'adress', 'a.`adress`',
				'description', 'a.`description`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering category
		$this->setState('filter.category', $app->getUserStateFromRequest($this->context.'.filter.category', 'filter_category', '', 'string'));

		// Filtering repeatable
		$this->setState('filter.repeatable', $app->getUserStateFromRequest($this->context.'.filter.repeatable', 'filter_repeatable', '', 'string'));

		// Filtering recurrence_type
		$this->setState('filter.recurrence_type', $app->getUserStateFromRequest($this->context.'.filter.recurrence_type', 'filter_recurrence_type', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_events');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__events_items` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		// Join over the foreign key 'category'
		$query->select('c.`title` AS category_title');
		$query->join('LEFT', '#__events_categories AS c ON c.`id` = a.`category`');

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.title LIKE ' . $search . '  OR  a.adress LIKE ' . $search . ' )');
			}
		}


		//Filtering category
		$filter_category = $this->state->get("filter.category");
		if ($filter_category)
		{
			$query->where("a.`category` = '".$db->escape($filter_category)."'");
		}

		//Filtering repeatable
		$filter_repeatable = $this->state->get("filter.repeatable");
		if ($filter_repeatable)
		{
			$query->where("a.`repeatable` = '".$db->escape($filter_repeatable)."'");
		}

		//Filtering recurrence_type
		$filter_recurrence_type = $this->state->get("filter.recurrence_type");
		if ($filter_recurrence_type)
		{
			$query->where("a.`recurrence_type` = '".$db->escape($filter_recurrence_type)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $oneItem) {
			$oneItem->repeatable = (bool) $oneItem->repeatable;
			$oneItem->recurrence_type = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_OPTION_' . strtoupper($oneItem->recurrence_type));
			$oneItem->override_event = (bool) $oneItem->override_event;
		}

		return $items;
	}
}
