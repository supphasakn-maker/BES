fn.ui = {
	unique : 0,
	button : function(btnClass,iconClass,func,txtCaption){
		
		var settings = {
			class_name : "btn btn-primary",
			icon_type : "none", //none,font-awesome,material
			icon : "",
			onclick : "",
			caption : ""
		};
		
		var s = '';
		if(typeof btnClass == 'object'){
			$.extend(settings,btnClass);
		}else{
			settings.class_name = btnClass + ' btn-icon';
			settings.icon_type = "font-awesome",
			settings.icon = iconClass;
			settings.onclick = func;
			if(typeof txtCaption != "undefined")settings.caption = txtCaption;
		}
		
		s += '<button type="button" class="'+settings.class_name+'" onclick="'+settings.onclick+'">';
		switch(settings.icon_type){
			case "font-awesome":
				s += '<i class="'+settings.icon+'"></i>';
				break;
			case "material":
				s += '<i class="material-icons mr-1">'+settings.icon+'</i>';
				break;
		}
		if(settings.caption != "")s += ' ' + settings.caption;
		s += '</button>';
		return s;
	},
	checkbox : function(name,val,selected,multiple){
		if(typeof multiple == "undefined")multiple = true;
		
		var icon_selected = (multiple?"check-square":"dot-circle")
		var icon_notselected = (multiple?"square":"circle")
		
		var s = '';
		s += '<span name="' + name + '" type="checkbox" data="' + val + '" class="far fa-lg fa-'+(selected?icon_selected:icon_notselected) +'"></span>';
		return s;
	},
	switchbox : function(status,func){
		var newid = fn.ui.unique++;
		var s ='';
		s += '<div class="custom-control custom-switch">';
			s += '<input onchange="'+func+'" type="checkbox" class="custom-control-input" id="chk_'+newid+'" '+(status?'checked="checked"':'')+'>';
			s += '<label class="custom-control-label" for="chk_'+newid+'"></label>';
		s += '';
		s += '</div>';
		return s;
	},
	datatable : {
		dom : {
			default : "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		},
		load_period : function(table,dbtable,field,cookie){
			$.get('ajax/engine/action-load-period.php',{table:dbtable,field:field},function(period){
				var s = '';
				s += '<select class="form-control pull-right" id="cbbPeriod">';
					s += '<option value="">All Time</option>';
				for(i in period){
					var selected = (period[i][1]+','+period[i][2]) == Cookies.get(cookie)?" selected":"";
					s += '<option value="'+period[i][1]+','+period[i][2]+'"'+selected+'>'+period[i][0]+'</option>';
				}
				s += '</select>'
				$(table+"_filter").append(s);
				$("#cbbPeriod").change(function(){
					Cookies.set(cookie, $(this).val(), { expires: 7 });
					$(table).DataTable().ajax.reload();
				}).change();
			},'json');
		},
		selectable : function(tblName,chkName,multiple,func){
			var table = $(tblName);
			if(typeof multiple == "undefined"){
				multiple = true;
			}
			
			if(multiple){
				table.on('click', 'td:not(:last-child, .unselectable)', function () {
					var me = $(this).parent();
					var id = me[0].id;
					var index = $.inArray(id,table.data("selected"));
					if ( index === -1 ) {
						table.data( "selected").push( id );
						$(me).find('span[type=checkbox]').removeClass("fa-square").addClass("fa-check-square");
						
					} else {
						table.data( "selected").splice( index, 1 );
						$(me).find('span[type=checkbox]').removeClass("fa-check-square").addClass("fa-square");
					}
					
					$(me).toggleClass('selected').trigger('selecting');
					if(typeof func != "undefined"){
						func(me);
					}
					
				});
				table.find("span[type=checkall]").click(function(){
					var checkbox = $(this);
					if(checkbox.attr('control')==chkName){
						var allchecked = true;
						$('span[name='+chkName+']').each(function(){
							if($(this).hasClass("fa-square")){
								allchecked = false;
							}
						});
						
						if(allchecked){
							checkbox.removeClass("fa-check-square").addClass("fa-square");
							$('span[name='+chkName+']').removeClass("fa-check-square").addClass("fa-square");
							$('span[name='+chkName+']').each(function(){
								var tr = $(this).parent().parent();
								var id = tr[0].id;
								var index = $.inArray(id, table.data( "selected"));
								if ( index != -1 ) {
									table.data("selected").splice( index, 1 );
									tr.removeClass("selected").trigger('selecting');
								}
							});
						}else{
							checkbox.removeClass("fa-square").addClass("fa-check-square");
							$('span[name='+chkName+']').removeClass("fa-square").addClass("fa-check-square");
							$('span[name='+chkName+']').each(function(){
								if($(this).attr('type')!="checkall"){
									var tr = $(this).parent().parent();
									var id = tr[0].id;
									var index = $.inArray(id, table.data("selected"));
									if ( index === -1 ) {
										table.data( "selected").push( id );
										tr.addClass("selected").trigger('selecting');
									}
								}
							});
						}
					}
				});
			}else{ //For General Seelction
				table.on('click', 'td:not(:last-child, .unselectable)', function () {
					var me = $(this).parent();
					var id = me[0].id;
					table.find('span[type=checkbox]').removeClass("fa-dot-circle").addClass("fa-circle");
					$(me).find('span[type=checkbox]').removeClass("fa-circle").addClass("fa-dot-circle");
					table.data( "selected", id );
					table.find('tr').removeClass('selected');
					$(me).addClass('selected').trigger('selecting');
					if(typeof func != "undefined"){
						func(me);
					}
				});
			}
		}
	},
	numberic :{
		format : function(num,scale){
			if(typeof num != "number")num = parseFloat(num);
			if(typeof scale == "undefined")scale = 2;
			return num.toFixed(scale).toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
		},
		phone : function(text){
			if(isNaN(text)){
				if(text.length==10){
					return text.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
				}else if(text.length==9){
					return text.replace(/(\d{3})(\d{3})(\d{3})/, '$1-$2-$3');
				}else{
					return text;
				}
			}else{
				return text;
			}
			
		}
	},
	document : {
		label : function(status){
			var s = '';
			switch(parseInt(status)){
				case 0:
					s += '<label class="label label-default">Draft</label>';
					break;
				case 2:
					s += '<label class="label label-warning">Pending</label>';
					break;
				case 3:
					s += '<label class="label label-info">OnHold</label>';
					break;
				case 5:
					s += '<label class="label label-primary">Approved</label>';
					break;
				case 6:
					s += '<label class="label label-danger">Rejected</label>';
					break;
				case 9:
					s += '<label class="label label-success">Success</label>';
					break;
			}
			return s;
		}
		
		
	},
	modal : {
		setup : function(config){
			var cfg = {
				dialog_id : "",
				default_focus : 'input[name=txtName]',
				auto_show : true,
				remove : null
			}
			$.extend(cfg,config);
			$(cfg.dialog_id).on("hidden.bs.modal",function(){
				if(cfg.remove != null){
					cfg.remove();
				}else{
					$(this).remove();
				}
				
			});
			$(cfg.dialog_id).on('shown.bs.modal', function () {
				$(cfg.default_focus).focus();
			});
			
			if(cfg.auto_show){
				$(cfg.dialog_id).modal('show');
			}
			
		}
	}
};
