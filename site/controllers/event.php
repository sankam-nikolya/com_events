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

/**
 * Events list controller class.
 *
 * @since  1.6
 */
class EventsControllerEvent extends EventsController
{
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
	public function &getModel($name = 'Event', $prefix = 'EventModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	public function data()
	{
		$app = JFactory::getApplication();

		header('Content-Type: application/json');

		try
		{
			$event_id = (int) $app->input->get('id', false, 'INT');

			if(empty($event_id)) {
				$result = array();
			} else {
				$result = $this->getModel()->getEvent($event_id);

				if(!empty($result)) {
					$start = $app->input->get('s');
					if(!empty($start)) {
						$start = strtotime($start);
						$result->start_date = date('d.m.Y', $start);
						$result->start_time = date('H:i', $start);
					} else {
						$start = strtotime($result->start_date);
						$result->start_date = date('d.m.Y', $start);
						$result->start_time = date('H:i', $start);
					}
					
					$end   = $app->input->get('e');
					if(!empty($end)) {
						$end   = strtotime($end);
						$result->end_date = date('d.m.Y', $end);
						$result->end_time = date('H:i', $end);
					} else {
						$end   = strtotime($result->end_date);
						$result->end_date = date('d.m.Y', $end);
						$result->end_time = date('H:i', $end);
					}
				} else {
					$result = new StdClass();
				}
			}

			echo json_encode($result);
		}
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}

		$app->close();
	}
}
