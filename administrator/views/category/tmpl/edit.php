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

	Joomla.submitbutton = function (task) {
		if (task == 'category.cancel') {
			Joomla.submitform(task, document.getElementById('category-form'));
		} else {
			if (task != 'category.cancel' && document.formvalidator.isValid(document.id('category-form'))) {
				Joomla.submitform(task, document.getElementById('category-form'));
			}
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_events&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="category-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12 form-horizontal">
				<fieldset class="adminform">
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('color'); ?>
					<?php echo $this->form->renderField('text_color'); ?>
					<?php echo $this->form->renderField('state'); ?>
					<?php echo $this->form->renderField('description'); ?>
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
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
