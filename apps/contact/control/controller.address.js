
	fn.app.contact.address.load_country = function(combobox){
		$.ajax({
			url: "apps/contact/store/store-country.php",
			type: "POST",dataType: "json",
			success: function(json){
				$(combobox).html("");
				for(i in json.aaData){
					$(combobox).append('<option value="' + json.aaData[i][0] + '">' + json.aaData[i][1] + '</option>');
				}
				$(combobox).val(211); // Default Select Thailand
				$(combobox).change();
			}
		});
	}
	
	fn.app.contact.address.load_city = function(combobox,country){
		$.ajax({
			url: "apps/contact/store/store-city.php",
			type: "POST",
			data: {filter : "country = " + country},
			dataType: "json",
			success: function(json){
				$(combobox).html("");
				for(i in json.aaData){
					$(combobox).append('<option value="' + json.aaData[i][0] + '">' + json.aaData[i][1] + '</option>');
				}
				$(combobox).change();
			}
		});
	}
	
	fn.app.contact.address.load_district = function(combobox,city){
		$.ajax({
			url: "apps/contact/store/store-district.php",
			type: "POST",
			data: {filter : "city = " + city},
			dataType: "json",
			success: function(json){
				$(combobox).html("");
				for(i in json.aaData){
					$(combobox).append('<option value="' + json.aaData[i][0] + '">' + json.aaData[i][1] + '</option>');
				}
				$(combobox).change();
			}
		});
	}
	
	fn.app.contact.address.load_subdistrict = function(combobox,district){
		$.ajax({
			url: "apps/contact/store/store-subdistrict.php",
			type: "POST",
			data: {filter : "district = " + district},
			dataType: "json",
			success: function(json){
				$(combobox).html("");
				for(i in json.aaData){
					$(combobox).append('<option value="' + json.aaData[i][0] + '">' + json.aaData[i][1] + '</option>');
				}
				$(combobox).change();
			}
		});
	}
	
	fn.app.contact.address.initial = function(country,province,district,subsitrict){
		$(country).change(function(){
			fn.app.contact.address.load_city(province,$(this).val());
		});
		$(province).change(function(){
			fn.app.contact.address.load_district(district,$(this).val());
		});
		$(district).change(function(){
			fn.app.contact.address.load_subdistrict(subsitrict,$(this).val());
		});
		$(subsitrict).change(function(){
			$.ajax({
				url: "apps/contact/xhr/action-load-postal.php",
				type: "GET",
				data: {subdistrict : $(this).val()},
				dataType: "html",
				success: function(html){
					if(html != null){
						$("input[name=postal]").val(html);
					}
				}
			});
		});
	}
	
	