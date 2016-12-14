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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		js('input:hidden.category').each(function(){
			var name = js(this).attr('name');
			if(name.indexOf('categoryhidden')) {
				js('#jform_category option[value="'+js(this).val()+'"]').attr('selected',true);
			}
		});
		js("#jform_category").trigger("liszt:updated");
		js('input:hidden.parent_id').each(function(){
			var name = js(this).attr('name');
			if(name.indexOf('parent_idhidden')){
				js('#jform_parent_id option[value="'+js(this).val()+'"]').attr('selected',true);
			}
		});
		js("#jform_parent_id").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'event.cancel') {
			Joomla.submitform(task, document.getElementById('event-form'));
		} else {
			if (task != 'event.cancel' && document.formvalidator.isValid(document.id('event-form'))) {
				Joomla.submitform(task, document.getElementById('event-form'));
			}
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_events&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="event-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('category'); ?>
					<?php foreach((array)$this->item->category as $value): ?>
					<?php if(!is_array($value)): ?>
					<input type="hidden" class="category" name="jform[categoryhidden][<?php echo $value;?>]" value="<?php echo $value;?>" />
					<?php endif; ?>
					<?php endforeach; ?>
					<?php echo $this->form->renderField('start_date'); ?>
					<?php echo $this->form->renderField('end_date'); ?>
					<?php echo $this->form->renderField('repeatable'); ?>
					<?php echo $this->form->renderField('recurrence_type'); ?>
					<?php echo $this->form->renderField('recurrence_until'); ?>
					<?php echo $this->form->renderField('state'); ?>
					<?php echo $this->form->renderField('adress'); ?>
					<?php echo $this->form->renderField('description'); ?>
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					<?php echo $this->form->renderField('created_by'); ?>
					<?php echo $this->form->renderField('modified_by'); ?>
					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
