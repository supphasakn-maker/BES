var fn = {
	app: {},
	iface: {},
	ui: {},
	system: {
		get_record: function (data, func) {
			$.post("ajax/system/action-get-record.php", data, function (json) {
				if (typeof func != "undefined") {
					func(json);
				}
			}, "json");
		},
		request_reload: function (func) {
			$.post("ajax/system/action-request-reload.php", function (html) {
				if (typeof func != "undefined") {
					func();
				}
			});
		},
		request_restart: function (func) {
			$.post("ajax/system/action-request-restart.php", function (html) {
				if (typeof func != "undefined") {
					func();
				}
			});
		},
		restart: function () {
			fn.dialog.confirmbox("This action need to confirm?", "Are you sure to restart the system?", function () {
				$.post("ajax/system/action-restart.php", function (html) {
					window.location.reload();
				});
			});
		},
		reload: function () {
			fn.dialog.confirmbox("This action need to confirm?", "Are you sure to reload the system?", function () {
				$.post("ajax/system/action-reload.php", function (html) {
					window.location.reload();
				});
			});

		},
		update: function (func) {
			$.post("ajax/system/action-update.php", function (html) {
				if (typeof func != "undefined") {
					func();
				}
			});
		}
	},
	noEnterSubmit(e) {
		if (e.which == 13) e.preventDefault();
	},
	blockUI: function (element) {
		$(element).block({
			message: '<div class=\'sk-three-bounce\'><div class=\'sk-child sk-bounce1\'></div><div class=\'sk-child sk-bounce2\'></div><div class=\'sk-child sk-bounce3\'></div></div>',
			css: {
				border: 'none',
				backgroundColor: 'transparent'
			},
			overlayCSS: {
				backgroundColor: '#FAFEFF',
				opacity: 0.5,
				cursor: 'wait'
			}
		});
	},
	unblockUI: function (element) {
		$(element).unblock();
	},
	service: {
		runtime: [],
		register: function (service, data) {
			var found = false;
			for (i in fn.service.runtime) {
				if (fn.service.runtime[i] == service) {
					found = true;
					fn.service.runtime[i].data = data;
					fn.service.runtime[i].updated = new Date();
				}
			}
			if (!found) {
				fn.service.runtime.push({
					service: service,
					data: data,
					updated: new Date()
				});
				return 1;
			} else {
				return 2;
			}
		},
		read: function (service) {
			var found = false;
			var data = null;
			for (i in fn.service.runtime) {
				if (fn.service.runtime[i] == service) {
					data = fn.service.runtime[i].data;
				}
			}
			if (!found) {
				return false;
			} else {
				return data;
			}
		}
	},
	notify: {
		infobox: function (msg) {
			bootbox.alert(msg);
		},
		warnbox: function (msg, title) {
			bootbox.alert({
				message: '<h5 class="d-flex align-items-center"><i class="material-icons text-danger mr-2 mb-0">warning</i>' + (typeof title == "undefined" ? 'Warning !' : title) + '</h5>' + msg
			});
		},
		successbox: function (msg, title) {
			bootbox.alert({
				message: '<h5 class="d-flex align-items-center"><i class="material-icons text-success mr-2 mb-0">check_circle_outline</i>' + (typeof title == "undefined" ? 'Warning !' : title) + '</h5>' + msg
			});
		},
		dialog_view: function (id, li) {
			$.ajax({
				url: "ajax/notify/dialog.notify.view.php",
				data: { id: id },
				type: "POST",
				dataType: "html",
				success: function (html) {
					$("body").append(html);
					$("#dialog_view_message").on("hidden.bs.modal", function () {
						$(this).remove();
						$("#activity").click();
						$(li).children("span").removeClass("unread");
					});
					$("#dialog_view_message").modal('show');
				}
			});
		},
		dialog_message: function (id, li) {
			$.ajax({
				url: "ajax/notify/dialog.message.view.php",
				data: { id: id },
				type: "POST",
				dataType: "html",
				success: function (html) {
					$("body").append(html);
					$("#dialog_view_message").on("hidden.bs.modal", function () {
						$(this).remove();
						$("#activity").click();
						$(li).children("span").removeClass("unread");
					});
					$("#dialog_view_message").modal('show');
				}
			});
		},
		confirmbox: function () {

		}
	},
	dialog: {
		confirmbox: function (title, msg, func) {
			bootbox.confirm({
				title: '<h3 class="d-flex align-items-center"><i class="material-icons mr-1">report_problem</i>' + title + '</h3>',
				message: msg,
				centerVertical: true,
				swapButtonOrder: true,
				buttons: {
					confirm: {
						label: 'Confirm',
						className: 'btn-primary shadow-0'
					},
					cancel: {
						label: 'Cancel',
						className: 'btn-secondary'
					}
				},
				className: "modal-alert",
				closeButton: false,
				callback: function (result) {
					if (result == true) {
						func();
					}
				}
			});
		},
		change_language: function () {
			bootbox.prompt({
				title: "Please Select Language",
				inputType: 'select',
				value: $("html").attr("lang"),
				inputOptions: [{
					text: 'English',
					value: 'en',
				}, {
					text: 'Thai',
					value: 'th',
				}],
				callback: function (result) {
					if (result != null)
						$.post("ajax/action-change-language.php", { lang: result }, function (html) {
							window.location.reload();
						});

				}
			});
		},
		open: function (url, selector, data) {
			$.ajax({
				url: url,
				data: typeof data != "undefined" ? data : null,
				type: "POST",
				dataType: "html",
				success: function (html) {
					$("body").append(html);
					fn.ui.modal.setup({ dialog_id: selector });
				}
			});
		}
	},
	oceanos: {
		data: {
			session_id: null,
			user_id: null,
			user_name: null,
			server_time: null,
			actions: [],
			notificaiton: null,
			widget: null
		},
		timer: null,
		process: function (ps) {
			switch (ps.action) {
				case "instance_message":
					var s = '';
					s += '<li class="message">';
					s += '<span class="thumb-sm">';
					s += '<img class="img-circle" src="demo/img/people/a2.jpg" alt="' + ps.sender + '">';
					s += '</span>';
					s += '<div class="message-body">' + ps.msg + '</div>';
					s += '</li>';
					$(".message-list[data-to=" + ps.source + "]").append(s);
					$(".message-list[data-to=" + ps.source + "]").slimscroll({ scrollBy: '400px' });
					break;
			}
		},
		login: function () {
			$.post("ajax/abox/action-login.php", $("form[name=form_login]").serialize(), function (response) {
				if (response.success) {
					window.location = "#apps/dashboard/index.php";
					fn.navigate("dashboard");
					//window.location.reload();
				} else {
					fn.alertbox("Access Denied", response.msg);
				}
			}, "json");
			return false;
		},
		logout: function () {
			GoogleAuth.signOut();
			$.post("ajax/abox/action-logout.php", function (html) {
				window.location.reload();
			});
		},
		init: function (auto_start) {
			new Switchery(document.getElementById('checkbox-ios1'));
			var changeCheckbox = document.querySelector('#checkbox-ios1');

			$("#checkbox-ios1").click(function () {
				var status = changeCheckbox.checked;
				if (status) {
					fn.abox.run_timer();
				} else {
					clearInterval(fn.abox.timer);
				}

				$.post("ajax/abox/action-update-switch.php", { status: status }, function (html) {

				});
			});

			if (auto_start) {
				fn.abox.run_timer();
			}

			/*
			$("#notifications-toggle").on("change",function(){
				var btnActive = $(this).find('.active').load("btnActive").
				console.log(this);
			});
			*/
			fn.abox.notification.update();
			fn.navigate('dashboard');
		},
		run_timer: function () {
			fn.abox.timer = setInterval(function () {
				var now = new moment();
				$.post("ajax/abox/action-update.php", { local_time: now.unix(), actions: fn.abox.data.actions }, function (json) {
					fn.abox.data.server_time = json.server_time;
					fn.abox.data.actions = [];

					if (json.process.length > 0) {
						for (i in json.process) {
							fn.abox.process(json.process[i]);
						}
					}
				}, "json");
			}, 4000);
		},
		send_message: function (me, to, msg) {
			$.post("ajax/abox/action-send-message.php", { to: to, msg: msg }, function (response) {

				var $currentMessageList = $('.chat-sidebar-chat.open .message-list'),
					$message = $('<li class="message from-me">' +
						'<span class="thumb-sm"><img class="img-circle" src="img/avatar.png" alt="..."></span>' +
						'<div class="message-body"></div>' +
						'</li>');
				$message.appendTo($currentMessageList).find('.message-body').text(msg);
				$(me).val('');
				$currentMessageList.slimscroll({ scrollBy: '400px' });
				/*
				if(response.success){
					window.location.reload();
				}else{
					fn.alertbox("Access Denied",response.msg);
				}
				*/
			}, "json");
			return false;
		},
		message: {
			view: function (id, li) {
				$.ajax({
					url: "ajax/notify/dialog.message.view.php",
					data: { id: id },
					type: "POST",
					dataType: "html",
					success: function (html) {
						$("body").append(html);
						$("#dialog_view_message").on("hidden.bs.modal", function () {
							$(this).remove();
							$("#activity").click();
							$(li).children("span").removeClass("unread");
						});
						$("#dialog_view_message").modal('show');
					}
				});
			}
		},
		notification: {
			view: function (id, li) {
				$.ajax({
					url: "ajax/notify/dialog.notify.view.php",
					data: { id: id },
					type: "POST",
					dataType: "html",
					success: function (html) {
						$("body").append(html);
						$("#dialog_view_notify").on("hidden.bs.modal", function () {
							$(this).remove();
							$("#activity").click();
							$(li).children("span").removeClass("unread");
						});
						$("#dialog_view_notify").modal('show');
					}
				});
			},
			update: function () {
				$.post("ajax/live/store-notification.php", function (html) {
					var now = new Date();
					$("#notifications-list").html(html);
					$("#notifications-status").html("Synced at: " + now.format("d F Y H:i"));
				}, "html");
			},
			initial: function () {

			}
		},
		load_widget_each: function (current) {
			var widget = $("#" + fn.abox.data.widget[current]);
			var widget_name = widget.attr("widget");
			var widget_param = widget.attr("param");
			$.get("widget/" + widget_name + "/widget.json", function (json) {
				$.post("widget/" + widget_name + "/index.php", {
					id: widget.attr("id"),
					widget: widget_name,
					param: widget_param
				}, function (html) {
					widget.html(html);
					widget.addClass(json.class);
					if (json.controller != "") {
						$.post("widget/" + widget_name + "/" + json.controller, {
							id: widget.attr("id"),
							widget: widget_name,
							param: widget_param
						}, function (html) {
							$("body").append(html);
						}, "html");
					}
					current++;
					if (fn.abox.data.widget.length > current) {
						fn.abox.load_widget_each(current);
					}

				}, "html");

			}, "json");

		},
		initial_widget: function () {
			var widgets = [];
			$(".widget").each(function () {
				widgets.push($(this).attr("id"));
			});
			fn.abox.data.widget = widgets;
			fn.abox.load_widget_each(0);
		}
	},
	navigate: function (app, param) {
		var path = '#apps/' + app + '/index.php';
		if (typeof param != "undefined") {
			path += '?' + param;
		}

		window.location = path;
	},
	reload: function () {
		window.location.reload();
	},
	change_language: function (lang) {
		$.post("ajax/action-change-language.php", { lang: lang }, function (html) {
			window.location.reload();
		});
		return false;
	},
	noaccess: function (me) {
		fn.notify.warnbox("Sorry! You have no permission to access this action!", "Access Denied!");
		if (typeof me != "undefined") {
			if ($(me).is("input[type=checkbox]")) {
				$(me).prop('checked', !$(me).prop('checked'));

			}
		}
		return false;
	},
	login: function () {
		$.post("ajax/auth/action-login.php", $("#login-form").serialize(), function (response) {
			if (response.success) {
				//console.log(response.token);
				//window.location = "#apps/dashboard/index.php";
				//fn.navigate("dashboard");
				window.location.reload();
			} else {
				//alert(response.msg);
				fn.notify.warnbox(response.msg);
				//bootbox.alert(response.msg);
				//Swal.fire("Access Denied",response.msg,"warning");
			}
		}, "json");
		return false;
	},
	logout: function () {
		bootbox.confirm({
			title: "<i class='fal fa-exclamation-triangle text-warning mr-2'></i>Are you sure to logout?",
			message: "<span><strong>Warning:</strong> You can improve your security further after logging out by closing this opened browser.</span>",
			centerVertical: true,
			swapButtonOrder: true,
			buttons: {
				confirm: {
					label: 'Logout',
					className: 'btn-danger shadow-0'
				},
				cancel: {
					label: 'Cancel',
					className: 'btn-success'
				}
			},
			className: "modal-alert",
			closeButton: false,
			callback: function (result) {
				console.log(result);
				if (result == true) {
					setTimeout(function () {
						$.post("ajax/auth/action-logout.php", function (html) {
							window.location.reload();
						});
					}, 1000);
				}
			}

		});
	}
};
