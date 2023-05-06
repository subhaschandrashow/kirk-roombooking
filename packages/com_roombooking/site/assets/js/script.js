// JavaScript Document
jQuery( document ).ready(function() {
		jQuery("#term").autocomplete({
			minLength: 3,
	 	    delay : 4,
			source: function(request, response) { 
				jQuery.ajax({
				   url: "index.php",
				   data:  {
				   			mode : "ajax",
				   			option : "com_vmapi",
				   			keyword : jQuery('#term').val(),
				   			task : "vmapi.suggest",
				   			limit : 10
				   	},
				   dataType: "json",
				   success: function(data) 	{ 
					 response(data);
				  }	

				})
	 	   },
		   select:  function(event, ui) { 
			   jQuery('#product_id').val(ui.item.value);
			   jQuery.ajax({
				   url: "index.php",
				   data:  {
				   			mode : "ajax",
				   			option : "com_vmapi",
				   			product_id : jQuery('#product_id').val(),
					   		index : jQuery('#index').val(),
				   			task : "vmapi.fetchproductdata",
				   			limit : 10
				   	},
				   dataType: "html",
				   success: function(data) 	{ 
					 	jQuery('#fetchdatatable').append('<tr><td>'+data+'</td></tr>');
					   	jQuery('#index').val(parseInt(jQuery('#index').val())+1);
					   	jQuery('#actionbuttons').show();
				  }	

				})
			   	
			},
			focus: function(event, ui) {
				jQuery(this).val('');
			},
			close: function(event, ui) {
				event.preventDefault();
				jQuery(this).val('');
			}
		  
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
		  return jQuery( "<li>" )
			.append( "<div><b>" + item.label + "</b><br><span class='greycolored'>" + item.domains + "</span></div>" )
			.appendTo( ul );
		};
	
	
		jQuery("#barcode").autocomplete({
			minLength: 3,
	 	    delay : 4,
			source: function(request, response) { 
				jQuery.ajax({
				   url: "index.php",
				   data:  {
				   			mode : "ajax",
				   			option : "com_vmapi",
				   			keyword : jQuery('#barcode').val(),
				   			task : "selling.suggest",
				   			limit : 10
				   	},
				   dataType: "json",
				   success: function(data) 	{ 
					 response(data);
				  }	

				})
	 	   },
		   select:  function(event, ui) { 
			   window.location.href = "index.php?option=com_vmapi&task=selling.fetchproductdata&product_id="+ui.item.value;
			},
			focus: function(event, ui) {
				jQuery(this).val('');
			},
			close: function(event, ui) {
				event.preventDefault();
				jQuery(this).val('');
			}
		  
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
		  return jQuery( "<li>" )
			.append( "<div><b>" + item.label + "</b><br><span class='greycolored'>" + item.domains + "</span></div>" )
			.appendTo( ul );
		};
		
	});

	
	function submitsearch() {
		if(jQuery('#search_terms').val() != '') {
			document.forms['searchform'].submit();
		}
	}

	
	function checkdetails(index) {
		var form = '';
		jQuery('#tr_edit_'+index).show();
		jQuery('#edit_form').appendTo('#tr_edit_'+index);
		jQuery('#edit_form').show();
		jQuery('#edit_product_name').val(jQuery('#product_name_'+index).val());
		jQuery('#edit_category_name').val(jQuery('#category_name_'+index).val());
		jQuery('#edit_qty').val(jQuery('#quantity_'+index).val());
		jQuery('#edit_default').val(jQuery('#default_'+index).val());
		jQuery('#edit_loyal').val(jQuery('#loyal_'+index).val());
		jQuery('#edit_dealer').val(jQuery('#dealer_'+index).val());
		jQuery('#edit_gold').val(jQuery('#gold_'+index).val());
		jQuery('#edit_platinum').val(jQuery('#platinum_'+index).val());
		jQuery('#edit_partner').val(jQuery('#partner_'+index).val());
		jQuery('#edit_warranty').val(jQuery('#warranty_'+index).val());
		jQuery('#edit_barcode').val(jQuery('#barcode_'+index).val());
		jQuery('#currindex').val(index);
	}
	
	function cleareditform() {
		var index = jQuery('#currindex').val(index);
		jQuery('#tr_edit_'+index).hide();
		jQuery('#edit_product_name').val('');
		jQuery('#edit_category_name').val('');
		jQuery('#edit_qty').val('');
		jQuery('#edit_default').val('');
		jQuery('#edit_loyal').val('');
		jQuery('#edit_dealer').val('');
		jQuery('#edit_gold').val('');
		jQuery('#edit_platinum').val('');
		jQuery('#edit_partner').val('');
		jQuery('#edit_warranty').val('');
		jQuery('#edit_barcode').val('');
		jQuery('#edit_form').hide();
		jQuery('#currindex').val('0');
	}

	function updateproduct() { 
		var currindex = jQuery('#currindex').val();
		var f = document.searchform;
        if (!document.formvalidator.isValid(f)) {
			return false;
		}
		
		var productnamehtml = '<a onclick="checkdetails('+currindex+');">'+jQuery('#edit_product_name').val()+'</a>'+'<input type="hidden" name="product_name[]" id="product_name_'+currindex+'" value="'+jQuery('#edit_product_name').val()+'">';
		var categorynamehtml = jQuery('#edit_category_name').val()+'<input type="hidden" name="category_name[]" id="category_name_'+currindex+'" value="'+jQuery('#edit_category_name').val()+'">';
		var qtyhtml = jQuery('#edit_qty').val()+'<input type="hidden" name="quantity[]" id="quantity_'+currindex+'" value="'+jQuery('#edit_qty').val()+'">';
		var defaulthtml = jQuery('#edit_default').val()+'<input type="hidden" name="default[]" id="default_'+currindex+'" value="'+jQuery('#edit_default').val()+'">';
		var loyalhtml = jQuery('#edit_loyal').val()+'<input type="hidden" name="loyal[]" id="loyal_'+currindex+'" value="'+jQuery('#edit_loyal').val()+'">';
		var dealerhtml = jQuery('#edit_dealer').val()+'<input type="hidden" name="dealer[]" id="dealer_'+currindex+'" value="'+jQuery('#edit_dealer').val()+'">';
		var goldhtml = jQuery('#edit_gold').val()+'<input type="hidden" name="gold[]" id="gold_'+currindex+'" value="'+jQuery('#edit_gold').val()+'">';
		var platinumhtml = jQuery('#edit_platinum').val()+'<input type="hidden" name="platinum[]" id="platinum_'+currindex+'" value="'+jQuery('#edit_platinum').val()+'">';
		var partnerhtml = jQuery('#edit_partner').val()+'<input type="hidden" name="partner[]" id="partner_'+currindex+'" value="'+jQuery('#edit_partner').val()+'">';
		var warrantyhtml = jQuery('#edit_warranty').val()+'<input type="hidden" name="warranty[]" id="warranty_'+currindex+'" value="'+jQuery('#edit_warranty').val()+'">';
		var barcodehtml = jQuery('#edit_barcode').val()+'<input type="hidden" name="barcode[]" id="barcode_'+currindex+'" value="'+jQuery('#edit_barcode').val()+'">';
		var have_serialhtml = '<input type="hidden" name="have_serial[]" id="have_serial_'+currindex+'" value="'+jQuery('#have_serial').val()+'">';
		var deletebuttonhtml = '<input type="button" value="DELETE" onClick="deleterow('+currindex+');">';
		var finalhtml = '<td width="20" align="center">'+currindex+have_serialhtml+'</td>'+'<td width="150px">'+productnamehtml+'</td>'+'<td width="120px">'+categorynamehtml+'</td>'+'<td align="center">'+qtyhtml+'</td>'+'<td align="center">'+defaulthtml+'</td>'+'<td align="center">'+loyalhtml+'</td>'+'<td align="center">'+dealerhtml+'</td>'+'<td align="center">'+goldhtml+'</td>'+'<td align="center">'+platinumhtml+'</td>'+'<td align="center">'+partnerhtml+'</td>'+'<td align="center">'+warrantyhtml+'</td>'+'<td align="center">'+barcodehtml+'</td><td>'+deletebuttonhtml+'</td>';
		jQuery('#tr_'+currindex).html(finalhtml);
		jQuery('#edit_form').hide();
		jQuery('#currindex').val('0');
	}

	
	function saveasdraft()
	{
		jQuery('#statusdraft').val('-1');
		document.forms['searchform'].submit();
	}
	
	function openproductform()
	{
		jQuery('#addproduct').show();
	}

	function closeproductform()
	{
		jQuery('#addproduct').hide();
	}

	function deleterow(a)
	{
		jQuery('#tr_'+a).remove();
	}
	
	
