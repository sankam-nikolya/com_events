<?php
/**
 * @version    CVS: 1.0.0
 * @author     Nikolya <k_m_i@i.ua>
 * @copyright  2016 Nikolya
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/*
 * DOCUMENTATION for jQuery plugin
 * http://www.malot.fr/bootstrap-datetimepicker/
 */

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldDatetimepicker extends JFormField
{
	protected $options = array(
			'format' => 'dd.mm.yyyy hh:ii',
			/**
			 * String. Default: 'mm/dd/yyyy'
			 * The date format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy.
			 * p : meridian in lower case ('am' or 'pm') - according to locale file
			 * P : meridian in upper case ('AM' or 'PM') - according to locale file
			 * s : seconds without leading zeros
			 * ss : seconds, 2 digits with leading zeros
			 * i : minutes without leading zeros
			 * ii : minutes, 2 digits with leading zeros
			 * h : hour without leading zeros - 24-hour format
			 * hh : hour, 2 digits with leading zeros - 24-hour format
			 * H : hour without leading zeros - 12-hour format
			 * HH : hour, 2 digits with leading zeros - 12-hour format
			 * d : day of the month without leading zeros
			 * dd : day of the month, 2 digits with leading zeros
			 * m : numeric representation of month without leading zeros
			 * mm : numeric representation of the month, 2 digits with leading zeros
			 * M : short textual representation of a month, three letters
			 * MM : full textual representation of a month, such as January or March
			 * yy : two digit representation of a year
			 * yyyy : full numeric representation of a year, 4 digits
			 */
			'weekStart' => 0,
			/**
			 * Day of the week start. 0 (Sunday) to 6 (Saturday)
			 */
			'startDate' => null,
			/*
			 * Date. Default: Beginning of time
			 * The earliest date that may be selected; all earlier dates will be disabled.
			 */
			'endDate' => null,
			/**
			 * Date. Default: End of time
			 * The latest date that may be selected; all later dates will be disabled.
			 */
			'daysOfWeekDisabled' => '',
			/**
			 * String, Array. Default: '', []
			 * Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday). Multiple values should be comma-separated. Example: disable weekends: '0,6' or [0,6].
			 */
			'autoclose' => true,
			/**
			 * Boolean. Default: false
			 * Whether or not to close the datetimepicker immediately when a date is selected.
			 */
			'startView' => 'month',
			/**
			 * Number, String. Default: 2, 'month'
			 * The view that the datetimepicker should show when it is opened. Accepts values of :
			 * 0 or 'hour' for the hour view
			 * 1 or 'day' for the day view
			 * 2 or 'month' for month view (the default)
			 * 3 or 'year' for the 12-month overview
			 * 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
			 */
			'minView' => 'hour',
			/*
			 * Number, String. Default: 0, 'hour'
			 * The lowest view that the datetimepicker should show.
			 */
			'maxView' => 'decade',
			/**
			 * Number, String. Default: 4, 'decade'
			 * The highest view that the datetimepicker should show.
			 */
			'todayBtn' => true,
			/**
			 * Boolean, "linked". Default: false
			 * If true or "linked", displays a "Today" button at the bottom of the datetimepicker to select the current date. If true, the "Today" button will only move the current date into view; if "linked", the current date will also be selected.
			 */
			'todayHighlight' => true,
			/**
			 * Boolean. Default: false
			 * If true, highlights the current date.
			 */
			'keyboardNavigation' => true,
			/**
			 * Boolean. Default: true
			 * Whether or not to allow date navigation by arrow keys.
			 */
			'language' => 'en',
			/**
			 * String. Default: 'en'
			 * The two-letter code of the language to use for month and day names. These will also be used as the input's value (and subsequently sent to the server in the case of form submissions). Currently ships with English ('en'), German ('de'), Brazilian ('br'), and Spanish ('es') translations, but others can be added (see I18N below). If an unknown language code is given, English will be used.
			 */
			'forceParse' => true,
			/**
			 * Boolean. Default: true
			 * Whether or not to force parsing of the input value when the picker is closed. That is, when an invalid date is left in the input field by the user, the picker will forcibly parse that value, and set the input's value to the new, valid date, conforming to the given format.
			 */
			'minuteStep' => 10,
			/*
			 * Number. Default: 5
			 * The increment used to build the hour view. A preset is created for each minuteStep minutes.
			 */
			'pickerPosition' => 'bottom-right',
			/**
			 * String. Default: 'bottom-right' (other value supported : 'bottom-left')
			 * This option is currently only available in the component implementation. With it you can place the picker just under the input field.
			 */
			'showMeridian' => false,
			/**
			 * Boolean. Default: false
			 * This option will enable meridian views for day and hour views.
			 */
			'initialDate' => false,
			/**
			 * Date or String. Default: new Date()
			 * You can initialize the viewer with a date. By default it's now, so you can specify yesterday or today at midnight ...
			 */
			'linkField' => false,
			/**
			 * On each update event, a secondary field is updated with a specific date format. Both id and format can be specified.
			 * The reset method will clear too this field.
			 */
			'linkFormat' => 'yyyy-mm-dd hh:ii',
			'formatType' => 'standard'
		);

	protected $input_type = 'input';
	/* 
	 * alaible options 'input', 'component' and 'inline'
	 */
	protected $icon = 'icon-calendar';
	protected $clearicon = 'icon-remove';
	protected $clearBtn = true;
	protected $readonly = false;
	

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput()
	{
		$this->getOptions();

		$assets = $this->getAttribute('assets', false);

		if(empty($assets)) {
			return 'Assets path NOT set!';
		}

		$assets = rtrim($assets, '/');

		$language = $this->options['language'];

		JHtml::_('jquery.framework');
		JHtml::script($assets.'/js/bootstrap-datetimepicker.min.js', false);	

		JHtml::stylesheet($assets.'/css/bootstrap-datetimepicker.min.css');

		if(!empty($language)) {
			JHtml::script($assets.'/js/locales/bootstrap-datetimepicker.' . $language . '.js', false);
		}

		$this->input_type = $this->getAttribute('input_type', 'input');
		$this->icon = $this->getAttribute('icon', $this->icon);
		$this->clearicon = $this->getAttribute('clearicon', $this->clearicon);
		$this->clearBtn = ($this->getAttribute('clearBtn', $this->clearBtn) == 'true') ? true : false ;
		$this->readonly = ($this->getAttribute('readonly', $this->readonly) == 'true') ? true : false ;

		$readonly = '';
		if($this->readonly) {
			$readonly = ' readonly';
		}

		$linked_field = $this->id . '_val';
		$this->options['linkField'] = $linked_field;

		$document = JFactory::getDocument();
		
		if($this->input_type == 'input') {
			$id = $this->id;
		} else {
			$id = 'dp_' . $this->id;
		}

		$script = "
			jQuery(document).ready(function() {
				jQuery(\"#" . $id . "\").datetimepicker(" . json_encode($this->options) . ");
			});
		";
		
		$document->addScriptDeclaration($script);

		if($this->options['formatType'] == 'php') {
			$date_format = $this->options['format'];
		} else {
			$date_format = $this->convertFormatToPhp($this->options['format']);
		}

		$this->options['linkField'] = $this->id . '_val';
		
		if($this->value != '0000-00-00 00:00:00' && !empty($this->value)) {
			$value = strtotime($this->value);
			$value = date($date_format, $value);
		} else {
			$value = '';
		}

		switch ($this->input_type) {
			case 'inline':
				$html .= '<div id="' . $id . '">';
				$html .= '</div>';
				$html .= '<input type="hidden" name="' . $this->name . '" id="' . $linked_field . '" value="' . $this->value . '"/>';
				break;
			case 'component':
				$html .= '<div id="' . $id . '" class="input-append date">';
				$html .= '<input type="text" id="' . $this->id . '" value="' . $value . '"' . $readonly . '>';
				if($this->clearBtn) {
					$html .= '<span class="add-on"><i class="' . $this->clearicon . '"></i></span>';
				}
				$html .= '<span class="add-on"><i class="' . $this->icon . '"></i></span>';
				$html .= '</div>';
				$html .= '<input type="hidden" name="' . $this->name . '" id="' . $linked_field . '" value="' . $this->value . '"/>';
				break;
			case 'input':
			default:
				$html .= '<input id="' . $this->id . '" type="text" value="' . $value . '"' . $readonly . '>';
				$html .= '<input type="hidden" name="' . $this->name . '" id="' . $linked_field . '" value="' . $this->value . '"/>';
				break;
		}

		return $html;
	}

	/**
	 * Wrapper method for getting attributes from the form element
	 *
	 * @param   string  $attr_name  Attribute name
	 * @param   mixed   $default    Optional value to return if attribute not found
	 *
	 * @return mixed The value of the attribute if it exists, null otherwise
	 */
	public function getAttribute($attr_name, $default = null)
	{
		if (!empty($this->element[$attr_name])) {
			return (string) $this->element[$attr_name];
		} else {
			return $default;
		}
	}

	public function getOptions() {
		$options = $this->options;
		unset($options['formatType']);
		unset($options['linkField']);
		unset($options['linkFormat']);
		$numbers = array(
				'weekStart',
				'minuteStep'
			);
		$booleans = array(
				'autoclose',
				'todayBtn',
				'todayHighlight',
				'keyboardNavigation',
				'forceParse',
				'showMeridian',
				'initialDate'
			);

		foreach ($options as $key => $option) {
			if (!empty($this->element['option_' . $key])) {
				$this->options[$key] = (string) $this->element['option_' . $key];
				if(in_array($key, $numbers)) {
					$this->options[$key] = (int) $option;
				}
				if(in_array($key, $booleans)) {
					$this->options[$key] = (bool) $option;
				}
			} else {
				switch ($key) {
					case 'language':
						$lang = JFactory::getLanguage();
						$lang = $lang->getTag();
						$lang = strtolower(substr($lang, 0, 2));
						$this->options[$key] = $lang;
						break;
					case 'initialDate':
						if($this->options['formatType'] == 'php') {
							$date_format = $this->options['format'];
						} else {
							$date_format = $this->convertFormatToPhp($this->options['format']);
						}
						$this->options[$key] = date($date_format);
						break;
					case 'startDate':
					case 'endDate':
					case 'daysOfWeekDisabled':
					case 'startView':
					case 'minView':
					case 'maxView':
						unset($this->options[$key]);
						break;
					default:
						break;
				}
			}
		}
	}

	public function convertFormatToPhp($format) {
		$formatCharacters = array(
				'p'    => 'a',
				'P'    => 'A',
				'hh'   => '-H-',
				'h'    => 'G',
				'HH'   => 'h',
				'H'    => 'g',
				'-g-'  => 'H',
				'ii'   => '-i-',
				'i'    => 'i',
				'-i-'  => 'i',
				'ss'   => '-s-',
				's'    => 's',
				'-s-'  => 's',
				'dd'   => '-d-',
				'd'    => 'j',
				'-j-'  => 'd',
				'mm'   => '-m-',
				'm'    => 'n',
				'-n-'  => 'm',
				'MM'   => 'F',
				'M'    => 'M',
				'yyyy' => 'Y',
			);

		$format = str_replace(array_keys($formatCharacters), array_values($formatCharacters), $format);

		return $format;
	}
}
