/**
* On page load functions
*/
$(document).ready(function(){
	
	// Add event listeners to all buttons
	$('.button').click(function(){veil()});
	
	// Add event listener to window close buttons
	$('.window-close-button').click(function(){
		window.opener.location.reload();
		window.close();
	});
	
	// Add event listener to notifications dismiss button
	$('#notifications-dismiss').click(function(){$('#notifications').fadeOut()});
	
	// Add event listener to back buttons
	$('.back-button').click(function(){window.location.href='.'});
	
	// Add event listeners to form submit buttons
	$('.form-submit-button').click(function(){
		$('#' + $(this).attr('id') + '-form').submit();
	});
	
	// Add event listeners to confirm buttons
	$('.confirm-button').click(function(e){
		if(!confirm('Are you sure?'))
		{
			e.preventDefault();
			unveil();
		}
	});
	
	$('.confirm-button-td').click(function(){
		
	});
	
	// Add event listeners to on-change submit buttons
	$('.submit-parent-change').change(function(){
		veil();
		$(this).parent().parent().submit();
	});
	
	// Add event listeners to search form additional fields buttons
	$('.search-additional-field-toggle').click(function(e){
		var additionalFields = $(this).parent().parent().find('.additional-fields');
		$.each(additionalFields, function(i, item){
			if($(item).is(':hidden'))
			{
				$(item).show();
				$(e.target).html("Show Less");
			}
			else
			{
				$(item).hide();
				$(e.target).html("Show More");
			}
		});
	});
	
	// Add listeners to region expand buttons
	$('.region-expand').click(function(e){
		if($(this).hasClass("region-expand-collapsed"))
		{
			// Change indicator
			$(this).addClass("region-expand-expanded");
			$(this).removeClass("region-expand-collapsed");
			
			// Show region
			$(this).next().show();
		}
		else
		{
			$(this).removeClass("region-expand-expanded");
			$(this).addClass("region-expand-collapsed");
			
			$(this).next().hide();
		}
	});
	
	// Initialize date pickers
	$('.date-input').datepicker({dateFormat:'yy-mm-dd'});
});

function veil()
{
	$('#veil').fadeIn();
}

function unveil()
{
	$('#veil').fadeOut();
}

/**
* Displays results to a DOM node
* @param id The id of the object to post the results to
* @param data Data as a JSON strong
* @param perPage Number of results to show per page
*/
function showResults(id, data, perPage, start = 0)
{
	var resultDisplay = document.getElementById(id);
	
	// Empty the result item
	$(resultDisplay).empty();
	
	// Variables for this table
	var total = 0;
	var totalHidden = 0;
	
	if(data.type == "table") // Display data as a table
	{
		// Create table
		var table = document.createElement("table");
		$(table).addClass("results");
		
		// Create header
		var header = document.createElement("tr");
		
		for(var i = 0; i < data.head.length; i++)
		{
			var cell = document.createElement("th");
			
			if((data.selectColumn !== undefined) && (data.selectColumn == i))
			{
				var checkAll = document.createElement("input");
				checkAll.setAttribute("type", "checkbox");
				$(checkAll).addClass("table-select-all");
				
				// Add listener to table-select-all
				$(checkAll).click(function(){
					
					var table = $(this).parent().parent().parent(); // Select the containing table
					
					if($(this).is(':checked')) // Select all
					{
						$.each($(table).find('.table-row-select'), function(i, item){
							$(item).prop('checked', true);
						});
					}
					else // De-select all
					{
						$.each($(table).find('.table-row-select'), function(i, item){
							$(item).prop('checked', false);
						});			
					}
				});
				
				cell.appendChild(checkAll);
			}
			else
			{
				var value = document.createTextNode(data.head[i]);
				cell.appendChild(value);
			}
			
			header.appendChild(cell);
		}
		
		table.appendChild(header);
		
		total = data.data.length;
		
		// Create data rows
		for(var i = start; i < total; i++)
		{
			var row = document.createElement("tr");
			$(row).addClass("result-item");
			
			if(data.rowClasses !== undefined)
			{
				$(row).addClass(data.rowClasses[i]);
			}
			
			if(i >= start + perPage)
			{
				$(row).hide();
				totalHidden++;
			}
			
			for(var j = 0; j < data.data[i].length; j++)
			{
				var cell = document.createElement("td");
				if(data.classes !== undefined)
				{
					$(cell).addClass(data.classes[j]);
				}
				
				if(data.widths !== undefined)
				{
					$(cell).width(data.widths[j]);
				}
				
				if(data.align !== undefined)
				{
					cell.setAttribute("align", data.align[j]);
				}
				
				// Check if this column is the link column, if link column is only a single value
				if((data.linkColumn !== undefined) && !(data.linkColumn instanceof Array) && (data.linkColumn == j))
				{
					var a = document.createElement("a");
					a.setAttribute("href", data.href + data.refs[i]);
					var value = document.createTextNode(data.data[i][j]);
					a.appendChild(value);
					cell.appendChild(a);
				}
				// Check if this column is one of the link columns, in case multiple link columns are defined
				else if((data.linkColumn !== undefined) && (data.linkColumn instanceof Array) && (data.linkColumn.indexOf(j)) != -1)
				{
					var linkColumnIndex = data.linkColumn.indexOf(j); // Get the index of this link's data in the 'refs' array
					var a = document.createElement("a");
					a.setAttribute("href", data.href[linkColumnIndex] + data.refs[i][linkColumnIndex]);
					var value = document.createTextNode(data.data[i][j]);
					a.appendChild(value)
					cell.appendChild(a);
					
				}
				else if((data.selectColumn !== undefined) && (data.selectColumn == j))
				{
					var check = document.createElement("input");
					check.setAttribute("type", "checkbox");
					check.setAttribute("name", "select");
					check.setAttribute("value", data.data[i][j]);
					$(check).addClass("table-row-select");
					cell.appendChild(check);
				}
				else
				{
					var value = document.createTextNode(data.data[i][j]);
					cell.appendChild(value);
				}
				
				row.appendChild(cell);
			}
			
			table.appendChild(row);
		}
		
		// Add table to specified id
		document.getElementById(id).appendChild(table);
	}
	else if(data.type == "description")
	{
		var resultsDiv = document.createElement("div");
		$(resultsDiv).addClass("results results-description");
		
		total = data.data.length;
		
		// Create result divs
		for(var i = start; i < total; i++)
		{
			var item = document.createElement("div");
			$(item).addClass("result-item");
			
			if(i >= start + perPage)
			{
				$(item).hide();
				totalHidden++;
			}
			
			var head = document.createElement("p");
			head.appendChild(document.createTextNode(data.data[i].header));
			
			var notes = document.createElement("p");
			
			$.each(data.data[i].description.split('\n'), function(index, value){
				notes.appendChild(document.createTextNode(value));
				notes.appendChild(document.createElement("br"));
			});
			
			item.appendChild(head);
			item.appendChild(notes);
			
			resultsDiv.appendChild(item);
		}
		
		document.getElementById(id).appendChild(resultsDiv);
	}
	
	// Create results counter
	var counter = document.createElement("div");
	$(counter).addClass("result-count");
	counter.appendChild(document.createTextNode("Displaying " + (start + 1) + " - " + (total - totalHidden) + " of " + total));
	
	// Add results counter
	resultDisplay.insertBefore(counter, resultDisplay.firstChild);
	
	// Create display navigation buttons
	var navButtons = document.createElement("div");
	$(navButtons).addClass("button-bar results-button-bar");
	
	// Add the previous and next buttons
	var prevButton = document.createElement("span");
	var nextButton = document.createElement("span");
	
	// Add classes to buttons
	$(prevButton).addClass("button-noveil results-button");
	$(nextButton).addClass("button-noveil results-button");
	
	// Add event listeners
	$(prevButton).click(function(){showResults(id, data, perPage, Math.max(0, start - perPage))});
	$(nextButton).click(function(){showResults(id, data, perPage, Math.min(total, start + perPage))});
	
	// Hide buttons and button bar
	$(prevButton).hide();
	$(nextButton).hide();
	$(navButtons).hide();
	
	// Add text
	prevButton.appendChild(document.createTextNode("< Previous Page"));
	nextButton.appendChild(document.createTextNode("Next Page >"));
	
	navButtons.appendChild(prevButton);
	navButtons.appendChild(nextButton);
	
	// Determine buttons to show
	if(start != 0)
	{
		$(prevButton).show();
		$(navButtons).show();
	}
	
	if(totalHidden != 0)
	{
		$(nextButton).show();
		$(navButtons).show();
	}
	
	resultDisplay.appendChild(navButtons);
	
	// Unveil screen if it was veiled
}