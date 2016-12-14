<?php
/**
 * @version	CVS: 1.0.0
 * @package	Com_Events
 * @author	 Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license	GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');

$lang = JFactory::getLanguage();
$lang = $lang->getTag();
$lang = strtolower(substr($lang, 0, 2));

JHtml::_('jquery.framework');
JHtml::script('media/com_events/js/moment.min.js', false);
JHtml::script('media/com_events/js/fullcalendar.min.js', false);
JHtml::script('media/com_events/js/locale/' . $lang . '.js', false);
JHtml::stylesheet('media/com_events/css/fullcalendar.min.css');
JHtml::stylesheet('media/com_events/css/fullcalendar.print.min.css');

if($this->type == 'day') {
	$calendar_view = "\n				defaultView: 'listDay',";
} elseif($this->type == 'week') {
	$calendar_view = "\n				defaultView: 'listWeek',";
} else {
	$calendar_view = "";
}

$document = JFactory::getDocument();
$script = "
	(function($) {
		$(document).ready(function() {
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'listDay,listWeek,month'
				},

				views: {
					listDay: { buttonText: '" . JText::_('COM_EVENTS_DAY') . "' },
					listWeek: { buttonText: '" . JText::_('COM_EVENTS_WEEK') . "' },
					month: { buttonText: '" . JText::_('COM_EVENTS_MONTH') . "' }
				},

				locale: '" . $lang . "'," . $calendar_view . "
				defaultDate: '" . date('Y-m-d') . "',
				lazyFetching: true,
				navLinks: true,
				editable: true,
				eventLimit: true,
				events: {
						url: 'index.php?option=com_events&task=events.listing',
						type: 'POST',
						dataType: 'json',
						cache: true,
						data: {
							category: " . $this->item->id . ",
							lang: '" . $lang . "'
						},
						error: function(data) {
							alert('" . JText::_('COM_EVENTS_ERROR_LOADING_EVENTS') . "');
						},
						color: '" . $this->item->color . "',
						textColor: '" . $this->item->text_color . "'
				},
				eventClick: function(calEvent, jsEvent, view) {
					var start_date = calEvent.start;
					var end_date = calEvent.end;
					var data = {
						id: calEvent.id,
						s: start_date.format(),
						e: end_date.format()
					};
					$.ajax({
						type: 'POST',
						cache: false,
						dataType: 'html',
						data: data,
						url: 'index.php?option=com_events&task=event.data',
						success: function (data) {
							$('body').append(data);
							$('#eventModal').modal('show');
						},
						error: function(data) {
							alert('" . JText::_('COM_EVENTS_ERROR_LOADING_EVENT') . "');
						}
					});
				}
			});
			$(document).on('hidden.bs.modal', '#eventModal', function (event) {
				$('#eventModal').remove();
			});
		});
	})(jQuery);
";
$document->addScriptDeclaration($script);
?>
<div class="content-block">
	<div class="item-page">
		<div class="page-header">
			<h1><?php echo $this->item->title; ?></h1>
		</div>
	</div>
	<?php
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('modules');
		echo $renderer->render('breadcrumbs', array('style' => 'none'), null);
	?>
	<?php if(!empty($this->item->description)):?>
	<div class="item-description">
		<?php echo $this->item->description; ?>
	</div>
	<?php endif;?>
	<div id="calendar" class="calendar-block"></div>
</div>
