function set_description(){
	var des = jQuery('input[name=izc_event_description]').attr('value',jQuery('#editor').html());
}

function get_course_dates(E_name,ajax_url){

    E_name.toString();

    var data = {
				action: 'ui_get_js_calender',
				e_name: E_name
               };
		jQuery.post(ajax_url, data, function(response)
			{
				 lastChar = response.length;
				 jQuery('div.p-content').html(response.substr(0,lastChar-1));
				 adjustHeight(1);
			});
}

function count_events(ajax_url,month,interface){
		
		var count_events = {
					action: 'ui_count_events',
					month: month,
					year: document.date_values.oldyearvalue.value
				   };	
			
		var obj = document.getElementById('count-months-'+month);
		
		jQuery.post(ajax_url, count_events, function(response)
			{
				lastChar = response.length;
				obj.innerHTML = response.substr(0,lastChar-1);
			});	
}

function format_date(day,year,month,time){
		var mydate = {
					action: 'my_date_format',
					day: day,
					month: month,
					year: year,
					time: time
				   };
		var obj = document.getElementById('date-format');
		jQuery.post(ajaxurl, mydate, function(response)
			{
			lastChar = response.length;
			obj.innerHTML = response.substr(0,lastChar-1);
			}); 
}
function getEvent(month,p_id){
		if(p_id!='')
			{
			var data = {
					action: 'get_fields',
					month: month,
					year: document.date_values.oldyearvalue.value,
					edit:1,
					p_id: p_id
				   };
			}
		else
			{
		var data = {
					action: 'get_fields',
					year: document.date_values.oldyearvalue.value,
					month: month
				   };
			}
		var obj = document.getElementById('calender-days');
		jQuery.post(ajaxurl, data, function(response)
			{
			obj.innerHTML = response;
			}); 				
}
function getEventDescription(p_id,ajax_url){
		var data = {
					action: 'ui_event_description',
					p_id: p_id
				   };
				   
		var offset = jQuery(".ui-iz-calender-events").offset();
		jQuery('#ui-iz-calender-event-description').css("position","absolute");
		jQuery('#ui-iz-calender-event-description').css("top",offset.top);
		jQuery('#ui-iz-calender-event-description').css("left",offset.left-210);
			
		var obj = document.getElementById('list-event-description');
		jQuery('#ui-iz-calender-event-description').slideDown(300);
		jQuery.post(ajax_url, data, function(response)
			{
			lastChar = response.length;
			obj.innerHTML = response.substr(0,lastChar-1);
			}); 			
}
function getEventUI(month,ajax_url){
	
			var data = {
					action: 'ui_list_events',
					month: month,
					year: document.date_values.oldyearvalue.value
				   };
		var obj = document.getElementById('list-events');
		
		jQuery.post(ajax_url, data, function(response)
			{
					lastChar = response.length;
					obj.innerHTML = response.substr(0,lastChar-1);
			}); 				
}
function showEvents(element,month,p_id,interface,ajax_url){	

	jQuery('.'+document.date_values.oldmonthvalue.value, '.wrap').removeClass('active');
	jQuery('.' + element, '.wrap').addClass('active');		
	
	document.date_values.oldmonthvalue.value = element;	
	
	if(interface=='admin')
		{
		getEvent(month,p_id);	
		jQuery('.calender-days', '.wrap').slideUp(200);
		jQuery('.calender-days', '.wrap').slideDown(300);	
		}
	if(interface=='ui')
		{
		var offset = jQuery('li.' + element + ' a').offset();

		getEventUI(month,ajax_url);
		jQuery('.ui-iz-calender-events').css("position","absolute");
		jQuery('.ui-iz-calender-events').css("top",offset.top);
		jQuery('.ui-iz-calender-events').css("left",offset.left-210);
		
		jQuery('#ui-iz-calender-event-description').slideUp(300);
		jQuery('.ui-iz-calender-events').hide();
		jQuery('.ui-iz-calender-events').slideDown(300);
		var e_des = document.getElementById('list-event-description');e_des.innerHTML = '';
		}									
}
function changYear(check,interface,ajax_url){
	var setyear = '';
	if(interface!='admin'){
		interface = 'ui';
	jQuery('#ui-iz-calender-event-description').slideUp(300);
	jQuery('#ui-iz-calender-events').slideUp(300);
	}
	if(check == 'next')
		{
		setyear = parseInt(document.events.izc_event_year.value) + 1;
		var obj = document.getElementById('calender-year');
		obj.innerHTML = setyear;
		document.date_values.oldyearvalue.value = setyear;
		document.events.izc_event_year.value = setyear;
		}
	if(check == 'prev')
		{
		setyear = parseInt(document.events.izc_event_year.value) - 1;
		var obj = document.getElementById('calender-year');
		obj.innerHTML = setyear;
		document.date_values.oldyearvalue.value = setyear;
		document.events.izc_event_year.value = setyear;
		}
	if(interface=='admin')
		{
		format_date(document.events.izc_event_day.value,setyear,document.events.izc_event_month.value,document.events.izc_event_time.value);
		}
	else
		{
		for(i=1; i<=12;i++){						
						count_events(ajax_url,i,interface);
						}
		var e_des = document.getElementById('list-event-description');e_des.innerHTML = '';
		var e_month = document.getElementById('ui-iz-calender-month');e_month.innerHTML = '';
		var e_events = document.getElementById('list-events');e_events.innerHTML = '';
		jQuery('.'+document.date_values.oldmonthvalue.value, '.wrap').removeClass('active');
		}
}
function closeBox(element){
	jQuery(element).slideUp(300);
}
function izc_event_mail(event_title){
  mail_str = "mailto:?subject=" + event_title;
  mail_str += "&body=" + location.href; 
  location.href = mail_str;
}
function izc_event_print(){
	window.print();
}