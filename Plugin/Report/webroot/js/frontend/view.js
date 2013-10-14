/**
 * This is the javascript file for the view action of 
 * the report section.
 * It handels possible form tweaking, ajax follow ups and other stuff.
 */

var activityFormSelector = '#ActivityViewForm';
var activityFormActivityIdInputSelector = activityFormSelector + '>#ActivityId';
var activityFormReportItemIdHolderSelector = activityFormSelector + '>#ReportItemId';
var activityFormLabelInputSelector = activityFormSelector + ' #ActivityLabel';
var activityFormDurationInputSelector = activityFormSelector + ' #ActivityDuration';

var reportItemsHolderSelector = '#reportItemsHolder';
var reportItemIdHolderSelector = 'input.reportItemIdHolder';
var curDayReportItemIdHolderSelector = 'li.curDay ' + reportItemIdHolderSelector;
var reportItemsListItemSelector = 'ul.reportItems>li';
var activityListItemSelector = 'div.activities ul>li';
var reportItemEditToggleSelector = '.reportItemEditToggle';
var activityEditToggleSelector = '.activityEditToggle';
var activityIdHolderSelector = 'input.activityIdHolder';
var labelSelector = 'div.label';
var durationSelector = 'div.duration';


/**
 * Different stuff is happenig after the dom nodes are available.
 * Tasks:
 *	- FORMAJAX: Define ajax actions for the activity form if present
 *	- REPORTITEMS: Get the report items for the current report
 *	- AUTOLABELCOMPLETION: Auto completion for the label field in the activity form
 *	- AUTODURATIONCOMPLETION: Auto completion for the duration field in the activity form
 *	- REPORTITEMCHANGES: Listen to report item id changes in activity form and highlight report item
 *
 */
jQuery(document).ready(function($){
	// FORMAJAX
 	var $form = $(activityFormSelector);

 	// This only works if we have a report generated
 	if($form.length){
 		$form.ajaxForm({
 			error: function(){
 				Twb.ajaxFormErrorCallback($form);
 			},
 			success: function(data){
				Twb.ajaxFormSuccessCallback($form, data);
				if(data.ajax.type == 'success'){
					$form.clearForm();
					setValue($(activityFormLabelInputSelector), '').trigger('change');
					getReportItems();
				}
 			}
 		});

 		// REPORTITEMS
 		getReportItems();

 		// AUTOLABELCOMPLETION
 		$(activityFormLabelInputSelector).select2({
			ajax: {
				data: function(term, page){
					return {
						data: {
							Activity: {
								label: term
							}
						}
					};
				},
				dataType: 'jsonp',
				quietMillis: 100,
				results: function(data, page){
					return {
						results: data,
						more: false
					}
				},
				type: 'post',
				url: getActivityLabelsAjaxUrl('json')
			},
			formatResult: function(result, container, query, escapeMarkup){
				return $.fn.select2.defaults.formatResult(
					{
						text: result.Activity.label
					},
					container,
					query,
					escapeMarkup
				);
			},
			formatSelection: function(object, container){
				return object.Activity.label;
			},
			id: function(e){
				return e.Activity.label;
			},
			initSelection: function(element, callback){
				var data = {
					Activity: {
						label: element.val()
					}
				};
				callback(data);				
			},
			minimumInputLength: 3,
			openOnEnter: false,
 		}).bind(
 			'open',
 			function(e){
 				/**
 				 * This callback is triggered everytime the select2 box is opened.
 				 * We need it because select2 is not able to put the already given input
 				 * in the search field if we reopen it.
 				 */
 				var $self = jQuery(this);
 				// Get select2 object
 				var select2Object = $self.data('select2');
 				// Put in the current value of the select2 in the search field of the select2
 				setValue(select2Object.search, $self.val());
 				// Focus search field (mobile fix)
 				select2Object.search.trigger('focus');
 			}
 		);

 		// AUTODURATIONCOMPLETION
 		$(activityFormDurationInputSelector).blur(function(event){
 			autoCompleteDuration(this);
 		});
		
		// REPORTITEMCHANGES
		$(activityFormReportItemIdHolderSelector).change(function(event){
			var self = $(this);
			
			var activeReportItem = jQuery(reportItemsHolderSelector + ' .reportItem.active');
			var reportItem = jQuery(reportItemsHolderSelector + ' .reportItem:has(' + reportItemIdHolderSelector + '[value=' + self.val() + '])');
			
			activeReportItem.removeClass('active');
			reportItem.addClass('active');
		});
 	}
});

/**
 * This method is creating the ajax request and puts
 * callbacks to it.
 *
 * @param string responseType The response type of the ajax request.
 * @param return void
 */
function getReportItems(responseType){
	responseType = responseType || 'html';

	jQuery.ajax({
		dataType: 'html',
		error: function(){
			alert('iwas stimmt nicht');
		},		
		success: function(data){
			refreshReportItemsFromHtml(data);
		},
		type: 'GET',
		url: getReportItemsAjaxUrl(responseType)
	});
}

/**
 * Report items holder is filled with new content. Also the report_item_id in
 * the activity form, if not already set, is changed to the newest report item id present.
 *
 * @param string data Html string
 * @return string
 */
function refreshReportItemsFromHtml(data){
	var $reportItemsHolder = jQuery(reportItemsHolderSelector);

	$reportItemsHolder.html(data);

	var $activityReportItemIdHolder = jQuery(activityFormReportItemIdHolderSelector);
	if(!$activityReportItemIdHolder.val()){
		var curDayReportItemId = jQuery(curDayReportItemIdHolderSelector).val();
		setValue($activityReportItemIdHolder, curDayReportItemId);
	}

	addEditLinkCallbacks();
}

/**
 * Add javascript callbacks to edit links in the report item activities list.
 *
 * @return void
 */
function addEditLinkCallbacks(){
	var $reportItemsHolder = jQuery(reportItemsHolderSelector);

	// Define the inputs to use them later on
	var $activityReportItemIdInput = jQuery(activityFormReportItemIdHolderSelector);
	var $activityIdInput = jQuery(activityFormActivityIdInputSelector);
	var $activityLabelInput = jQuery(activityFormLabelInputSelector);
	var $activityDurationInput = jQuery(activityFormDurationInputSelector);

	// Circle through all report items
	$reportItemsHolder.find(reportItemsListItemSelector).each(function(index, val){
		// Current report item
		var $curListItem = jQuery(this);
		// Holder of the current item's id 
		var $idHolder = $curListItem.find(reportItemIdHolderSelector);
		// Edit toggler
		var $reportItemToggler = $curListItem.find(reportItemEditToggleSelector)

		// Add functionality to the edit toggler
		$reportItemToggler.click(function(event){
			// Set the report item id input in the activity form
			setValue($activityReportItemIdInput, $idHolder.val(), true);
			// Set the activity id input in the activity form
			setValue($activityIdInput, '');

			// Return false to prevent hash tag in url
			return false;
		});


		// Circle through all activity items of the current report item
		$curListItem.find(activityListItemSelector).each(function(index, val){
			var $curActivityListItem = jQuery(this);
			// Current activity item
			var $activityToggler = $curActivityListItem.find(activityEditToggleSelector);
			// Holder of the current activity's id
			var $activityIdHolder = $curActivityListItem.find(activityIdHolderSelector);
			// Element container the label of the current activity item
			var $label = $curActivityListItem.find(labelSelector);
			// Element container the duration of the current activity item
			var $duration = $curActivityListItem.find(durationSelector);


			$activityToggler.click(function(event){
				// Set the activity id input in the activity form
				setValue($activityIdInput, $activityIdHolder.val());
				// Set the report item id input in the activity form
				setValue($activityReportItemIdInput, $idHolder.val(), true);
				// Set the label input in the activity form
				// Trigger change here to make select2 work
				setValue($activityLabelInput, jQuery.trim($label.html())).trigger('change');
				// Set the duration input in the activity form
				setValue($activityDurationInput, jQuery.trim($duration.html()));

				// Return false to prevent hash tag in url
				return false;
			});
		});
	});
}

/**
 * Simple wrapper method for getting ajax url from the response type
 *
 * @param object target The object to extract from
 * @param string responseType
 * @return string
 */
function getAjaxUrl(target, responseType){
	responseType = responseType || 'html';

	return (
		(
			typeof target.ajaxUrls !== 'undefined' && target.ajaxUrls[responseType]
		) || 
		''
	);
}

/**
 * Wrapper to get report item ajax urls
 *
 * @param string responseType
 * @return string
 */
function getReportItemsAjaxUrl(responseType){

	var reportData = getReportData();

	return getAjaxUrl(reportData, responseType)
}

/**
 * Wrapper to get activity labels ajax urls
 *
 * @param string responseType
 * @return string
 */
function getActivityLabelsAjaxUrl(responseType){

	var activityLableData = getActivityLabelData();

	return getAjaxUrl(activityLableData, responseType)
}

/**
 * This method auto completes the duration field.
 *
 * Examples:
 *		- 0:3		=> 0:30
 *		- :55		=> 0:55
 *		- 3: 		=> 3:00
 *
 * @param DomNode target The target input to process.
 * @return void
 */
function autoCompleteDuration(target){
	var $el = jQuery(target);
	var val = $el.val();

	if(val.indexOf(':') !== -1){
		var splittedVal = val.split(':');

		// If one part of the time is not given, set it to 0
		jQuery(splittedVal).each(function(index, val){
			if(!splittedVal[index]){
				splittedVal[index] = '0';
			}
		});

		if(splittedVal[1].length < 2){
			splittedVal[1] = splittedVal[1] + '0';
		}

		val = splittedVal.join(':');

		setValue($el, val);
	}
}

/**
 * Wrapper method for setting a value. There were once issues with
 * manipulating those in forms with some browsers.
 *
 * @param object target	The jQuery object containing the form
 * 						element to change to value of.
 * @param mixed value	The value
 * @return object
 */
 function setValue(target, value, triggerChange){
	triggerChange = (triggerChange || false);
	
 	target.val(value).attr('value', value);
	
	if(target.attr('type') == 'hidden' && triggerChange){
		target.trigger('change');
	}

 	return target;
 }