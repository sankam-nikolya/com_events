<?php

namespace Calendarful\Elements;

require_once JPATH_ROOT . '/components/com_events/lib/autoload.php';

use Plummer\Calendarful\Event\EventInterface;

class Event implements EventInterface
{
	protected $id;

	protected $startDate;

	protected $endDate;
	
	protected $parentId;

	protected $occurrenceDate;

	protected $data;

	public function __construct($id, $startDate, $endDate, $parentId = null, $occurrenceDate = null, $data = array())
	{
		$this->id = $id;
		$this->startDate = new \DateTime($startDate);
		$this->endDate = new \DateTime($endDate);
		$this->parentId = $parentId;
		$this->occurrenceDate = $occurrenceDate ? new \DateTime($occurrenceDate) : null;
		$this->data = $data;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getStartDate()
	{
		return $this->startDate;
	}

	public function setStartDate(\DateTime $startDate)
	{
		$this->startDate = $startDate;
	}

	public function getEndDate()
	{
		return $this->endDate;
	}

	public function setEndDate(\DateTime $endDate)
	{
		$this->endDate = $endDate;
	}

	public function getDuration()
	{
		return $this->startDate->diff($this->endDate);
	}

	public function getParentId()
	{
		return $this->parentId;
	}

	public function getOccurrenceDate()
	{
		return $this->occurrenceDate;
	}

	public function getEvent($date_format = 'Y-m-d\TH:i:s\Z')
	{
		$event = new \StdClass();

		$event->id    = (int) $this->id;
		$event->start = $this->startDate->format($date_format);
		$event->end   = $this->endDate->format($date_format);

		if(!empty($this->data)) {
			foreach ($this->data as $key => $value) {
				$event->{$key} = $value;
			}
		}

		return $event;
	}
}
