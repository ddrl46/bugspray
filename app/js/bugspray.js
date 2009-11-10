/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

$(document).ready(function() {
	$("form.ajax input[type=submit]").after(' <img src="img/loading.gif" alt="please wait..." class="loadimg" style="display:none;" />');
	$("form.ajax").submit(function(e) {
		formelm = this;
		$(formelm).find(".loadimg").show();
		
		$.ajax({
			type: $(formelm).attr('method').toUpperCase(),
			url: $(formelm).attr('action'),
			data: $(formelm).serialize(),
			dataType: 'json',
			success: function(data) {
				window.location.hostname == '127.0.0.1' || window.location.hostname == 'localhost' ? delay = 250 : delay = 0;
				
				setTimeout(function() {
					$(formelm).find(".loadimg").hide();
					if (!data.success)
					{
						$.amwnd({
							title: 'Error!',
							content: data.message,
							buttons: ['ok'],
							closer: 'ok'
						});
					}
					else
					{
						history.go();
					}
				}, delay);
			}
		});
		
		e.preventDefault();
	});
});

function changestatus(id,status,assigns)
{
	// assigns list
	var a;
	for (var i in assigns)
	{
		a += '<option value="'+assigns[i][0]+'">'+assigns[i][1]+'</option>';
	}
	
	// show it	
	$.amwnd({
		title:'Change status',
		content:'Hello! You can change the status of this issue here; just select an option.<br /><br />\
		<form id="st" method="post" action="manage_issue.php?id='+id+'&status">\
			<input type="radio" name="st" value="0" id="st0" /> <label for="st0">Open</label> <br />\
			<input type="radio" name="st" value="1" id="st1" /> <label for="st1">Assigned to</label> <select id="st1a">'+a+'</select> (doesn\'t work right now)<br />\
			<input type="radio" name="st" value="2" id="st2" /> <label for="st2">Resolved</label> <br />\
			<input type="radio" name="st" value="3" id="st3" /> <label for="st3">Duplicate of</label> <input type="text" id="st3a" /> (doesn\'t work right now) <br />\
			<input type="radio" name="st" value="4" id="st4" /> <label for="st4">By design</label> <br />\
			<input type="radio" name="st" value="5" id="st5" /> <label for="st5">Declined</label> <br />\
			<input type="radio" name="st" value="6" id="st6" /> <label for="st6">Non-issue</label> <br />\
			<input type="radio" name="st" value="7" id="st7" /> <label for="st7">Spam</label> <br />\
			<br />\
			<div class="ibox_generic">\
				<b>You may enter some additional notes here. These will be posted in the comments. If you do not wish to write any, leave the box blank.</b> (doesn\'t work right now)\
				<br /><br />\
				<textarea cols="60" rows="2" class="mono" id="notes"></textarea>\
			</div>\
		</form>',
		buttons:['ok','cancel'],
		closer:'cancel'
	});
	
	// which status is current?
	$("#st"+status).attr({'checked':'checked'});
	
	// and when you click ok...
	$("#amwnd_ok").bind('click',function() {
		$("#st").submit();
	});
}

function confirmurl(title,url,permanent)
{
	var a = '';
	if (permanent)
	{
		a = '<br /><br /><div class="ibox_alert"><img src="img/alert/exclaim.png" alt="" /> <b>This cannot be undone!</b></div>';
	}
	
	$("#amwnd_yes").bind('click',function() {
		location.href = url;
	});
	
	$.amwnd({
		title:title,
		content:'Are you sure you want to do this?'+a,
		buttons:['yes','no'],
		closer:'no'
	});
}

function stringform(title,url)
{
	var a = '';
	if (url)
	{
		a = ' action="'+url+'"';
	}
	
	$.amwnd({
		title:title,
		content:'<form id="st" method="post"'+a+'>Enter the value: <input type="text" name="str" /><input type="hidden" name="sub" /></form>',
		buttons:['ok','cancel'],
		closer:'cancel'
	});
	$("#amwnd_ok").bind('click',function() {
		$("#st").submit();
	});
}