<?php
/**
 * @version		CVS: 1.0.0
 * @package		Com_Events
 * @author		 Nikolya <k_m_i@i.ua>
 * @copyright	2016 Nikolya
 * @license		GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<?php if(!empty($displayData)):?>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo JText::_('JLIB_HTML_BEHAVIOR_CLOSE');?>"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="eventModalTitle"><?php echo $displayData->title;?></h4>
			</div>
			<div class="modal-body">
				<div class="event-date">
					<?php if($displayData->start_date == $displayData->end_date):?>
					<div class="start-date">
						<div class="event-modal-header">
							<?php echo JText::_('COM_EVENTS_EVENT_DATE');?>
						</div>
						<div>
							<span class="date"><?php echo $displayData->start_date;?></span> <span class="time"><?php echo $displayData->start_time . ' - ' . $displayData->end_time;?></span>
						</div>
					</div>
					<?php else:?>
					<div class="start-date">
						<div class="event-modal-header">
						<?php echo JText::_('COM_EVENTS_EVENT_DATE_START');?>
						</div>
						<div>
							<span class="date"><?php echo $displayData->start_date;?></span> <span class="time"><?php echo $displayData->start_time;?></span>
						</div>
					</div>
					<div class="end-date">
						<div class="event-modal-header">
							<?php echo JText::_('COM_EVENTS_EVENT_DATE_END');?>
						</div>
						<div>
							<span class="date"><?php echo $displayData->end_date;?></span> <span class="time"><?php echo $displayData->start_time;?></span>
						</div>
					</div>
					<?php endif;?>
				</div>
				<?php if(!empty($displayData->adress)):?>
				<div class="event-address">
					<div class="event-modal-header">
						<?php echo JText::_('COM_EVENTS_EVENT_ADDRESS');?>
					</div>
					<div>
						<?php echo $displayData->adress;?>
					</div>
				</div>
				<?php endif;?>
				<?php if(!empty($displayData->description)):?>
				<div class="event-description">
					<div class="event-modal-header">
						<?php echo JText::_('COM_EVENTS_EVENT_DESCRIPTION');?>
					</div>
					<div>
						<?php echo $displayData->description;?>
					</div>
				</div>
				<?php endif;?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('JLIB_HTML_BEHAVIOR_CLOSE');?></button>
			</div>
		</div>
	</div>
</div>
<?php endif;?>