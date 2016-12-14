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

jimport('joomla.application.component.view');

/**
 * View class for a list of Events.
 *
 * @since  1.6
 */
class EventsViewEvents extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		EventsHelpersEvents::addSubmenu('events');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = EventsHelpersEvents::getActions();

		JToolBarHelper::title(JText::_('COM_EVENTS_TITLE_EVENTS'), 'calendar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/event';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('event.add', 'JTOOLBAR_NEW');
				JToolbarHelper::custom('events.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('event.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('events.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('events.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'events.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('events.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('events.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'events.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('events.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_events');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_events&view=events');

		$this->extra_sidebar = '';                                                
        //Filter for the field category;
        jimport('joomla.form.form');
        $options = array();
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        $form = JForm::getInstance('com_events.event', 'event');

        $field = $form->getField('category');

        $query = $form->getFieldAttribute('filter_category','query');
        $translate = $form->getFieldAttribute('filter_category','translate');
        $key = $form->getFieldAttribute('filter_category','key_field');
        $value = $form->getFieldAttribute('filter_category','value_field');

        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items))
        {
            foreach ($items as $item)
            {
                if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        JHtmlSidebar::addFilter(
            '$Category',
            'filter_category',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.category')),
            true
        );
		//Filter for the field repeatable
		$select_label = JText::_('COM_EVENTS_EVENTS_REPEATABLE_FILTER');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = JText::_('JYES');
		$options[1] = new stdClass();
		$options[1]->value = "2";
		$options[1]->text = JText::_('JNO');
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_repeatable',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.repeatable'), true)
		);

		//Filter for the field recurrence_type
		$select_label = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_FILTER');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "daily";
		$options[0]->text = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_OPTION_DAILY');
		$options[1] = new stdClass();
		$options[1]->value = "weekly";
		$options[1]->text = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_OPTION_WEEKLY');
		$options[2] = new stdClass();
		$options[2]->value = "monthly";
		$options[2]->text = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_OPTION_MONTHLY');
		$options[3] = new stdClass();
		$options[3]->value = "yearly";
		$options[3]->text = JText::_('COM_EVENTS_EVENTS_RECURRENCE_TYPE_OPTION_YEARLY');
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_recurrence_type',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.recurrence_type'), true)
		);

		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`title`' => JText::_('COM_EVENTS_EVENTS_TITLE'),
			'a.`category`' => JText::_('COM_EVENTS_EVENTS_CATEGORY'),
			'a.`start_date`' => JText::_('COM_EVENTS_EVENTS_START_DATE'),
			'a.`end_date`' => JText::_('COM_EVENTS_EVENTS_END_DATE'),
			'a.`repeatable`' => JText::_('COM_EVENTS_EVENTS_REPEATABLE'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
		);
	}
}
