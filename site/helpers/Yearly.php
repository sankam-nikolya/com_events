<?php

namespace Events\Recurrence;

require_once JPATH_ROOT . '/components/com_events/lib/autoload.php';

use Plummer\Calendarful\Recurrence\RecurrenceInterface;


class Yearly implements RecurrenceInterface
{
	/**
	 * @var string
	 */
	protected $label = 'yearly';

	/**
	 * @var string
	 */
	protected $limit = '+200 years';

	/**
	 * Get the label of the recurrence type.
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Get the limit of the recurrence type.
	 *
	 * @return string
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Generate the occurrences for each daily recurring event.
	 *
	 * @param  EventInterface[]		$events
	 * @param  \DateTime			$fromDate
	 * @param  \DateTime			$toDate
	 * @param  int|null				$limit
	 * @return EventInterface[]
	 */
	public function generateOccurrences(Array $events, \DateTime $fromDate, \DateTime $toDate, $limit = null)
	{
		$return = array();
		$object = $this;

		$yearlyEvents = array_filter($events, function ($event) use ($object) {
			return $event->getRecurrenceType() === $object->getLabel();
		});

		foreach ($yearlyEvents as $yearlyEvent) {
			
			$yearlyEventTime = $yearlyEvent->getStartDate()->format('H:i:s');

			$startMarker = $fromDate > $yearlyEvent->getStartDate()
				? clone($fromDate)
				: clone($yearlyEvent->getStartDate());

			$maxEndMarker = clone($startMarker);
			$maxEndMarker->modify($this->limit);

			$endMarker = $yearlyEvent->getRecurrenceUntil()
				? min($yearlyEvent->getRecurrenceUntil(), clone($toDate), $maxEndMarker)
				: min(clone($toDate), $maxEndMarker);

			$actualEndMarker = clone($endMarker);

			// The DatePeriod class does not actually include the end date so you have to increment it first
			$endMarker->modify('+1 day');

			$dateInterval = new \DateInterval('P1Y');
			$datePeriod = new \DatePeriod($startMarker, $dateInterval, $endMarker);

			$limitMarker = 0;

			foreach ($datePeriod as $date) {

				if (($limit and ($limit === $limitMarker)) or ($date > $actualEndMarker)) {
					break;
				}

				if($yearlyEvent->getStartDate()->format('n') != $date->format('n')){
					continue;
				}

				$date->setDate($date->format('Y'), $yearlyEvent->getStartDate()->format('m'), $yearlyEvent->getStartDate()->format('d'));

				$newYearlyEvent = clone($yearlyEvent);
				$newStartDate = new \DateTime($date->format('Y-m-d') . $yearlyEventTime);

				if ($newStartDate < $startMarker) {
					continue;
				}

				$duration = $newYearlyEvent->getDuration();

				$newYearlyEvent->setStartDate($newStartDate);

				$newEndDate = clone($newStartDate);
				$newEndDate->add($duration);
				
				$newYearlyEvent->setEndDate($newEndDate);
				$newYearlyEvent->setRecurrenceType();

				$return[] = $newYearlyEvent;

				$limit and $limitMarker++;
			}
		}

		return $return;
	}
}
