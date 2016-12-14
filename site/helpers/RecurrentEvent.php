<?php

namespace Calendarful\Elements;

require_once JPATH_ROOT . '/components/com_events/lib/autoload.php';
require_once __DIR__ . '/Event.php';

use Plummer\Calendarful\Event\RecurrentEventInterface;

class RecurrentEvent extends Event implements RecurrentEventInterface
{
	protected $recurrenceType;

	protected $recurrenceUntil;

	public function __construct($id, $startDate, $endDate, $parentId = null, $occurrenceDate = null, $recurrenceType = null, $recurrenceUntil = null, $data = array())
	{
		$this->id = $id;
		$this->startDate = new \DateTime($startDate);
		$this->endDate = new \DateTime($endDate);
		$this->parentId = $parentId;
		$this->occurrenceDate = $occurrenceDate ? new \DateTime($occurrenceDate) : null;
		$this->recurrenceType = $recurrenceType;
		$this->recurrenceUntil = $recurrenceUntil ? new \DateTime($recurrenceUntil) : null;
		$this->data = $data;
	}

	public function getRecurrenceType()
	{
		return $this->recurrenceType;
	}

	public function setRecurrenceType($type = null)
	{
		if ($type === null) {
			$this->recurrenceUntil = null;
		}

		$this->recurrenceType = $type;
	}

	public function getRecurrenceUntil()
	{
		return $this->recurrenceUntil;
	}
}
