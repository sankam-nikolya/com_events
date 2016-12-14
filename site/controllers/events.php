<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Events
 * @author     Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_events/lib/autoload.php';
require_once JPATH_ROOT . '/components/com_events/helpers/Yearly.php';

use \Plummer\Calendarful\Recurrence\RecurrenceFactory AS RecurrenceFactory;
use \Plummer\Calendarful\Calendar\Calendar AS Calendar;

/**
 * Events list controller class.
 *
 * @since  1.6
 */ 
class EventsControllerEvents extends EventsController
{
	public $category;

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Events', $prefix = 'EventsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	public function listing()
	{
		$app = JFactory::getApplication();

		header('Content-Type: application/json');

		try
		{
			$this->category = (int) $app->input->get('category', 0, 'INT');
			$start_date = $app->input->get('start');
			$end_date = $app->input->get('end');

			$result = $this->getEvents($start_date, $end_date);

			echo json_encode($result);
		}
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}

		$app->close();
	}

	private function getEvents($start, $end) {
		$events = array();
		$eventsRegistry = $this->getModel();

		$recurrenceFactory = new RecurrenceFactory();
		$recurrenceFactory->addRecurrenceType('daily', 'Plummer\Calendarful\Recurrence\Type\Daily');
		$recurrenceFactory->addRecurrenceType('weekly', 'Plummer\Calendarful\Recurrence\Type\Weekly');
		$recurrenceFactory->addRecurrenceType('monthly', 'Plummer\Calendarful\Recurrence\Type\MonthlyDate');
		$recurrenceFactory->addRecurrenceType('yearly', 'Events\Recurrence\Yearly');

		$calendar = Calendar::create($recurrenceFactory)->populate($eventsRegistry, new \DateTime($start), new \DateTime($end));

		foreach ($calendar as $event) {
			$events[] = $event->getEvent();
		}
		
		return $events;
	}
}
