window.addEvent('domready', function () {
	var a = $('jxplugin-enable');
	if (a) {
		var href = a.getProperty('href');
		a.addEvent('hide', function () {
			this.getParent().setProperty('hidden', 'hidden');
			var mySlider = new Fx.Slide(this.getParent(), {
				duration: 300
			});
			mySlider.slideOut();
		});
		a.addEvent('click', function () {
			var action = href + '&tmpl=component&protocol=json';
			new Json.Remote(action, {
				linkId: this.getProperty('id'),
				onComplete: function (response) {
					if (response.error == false) {
						$(this.options.linkId).fireEvent('hide');
						$('system-message').fireEvent('check');
					} else {
						alert(response.message);
					}
				}
			}).send();
			return false;
		}, a);
		a.setProperty('href', 'javascript: void(0);');
	}
	sm = $('system-message');
	if (sm) {
		sm.addEvent('check', function () {
			open = 0;
			messages = this.getElements('li');
			for (i = 0, n = messages.length; i < n; i++) {
				if (messages[i].getProperty('hidden') != 'hidden') {
					open++;
				}
			}
			if (open < 1) {
				this.remove();
			}
		});
	}

	function hideWarning(e) {
		new Json.Remote(this.getProperty('link') + '&protocol=json', {
			linkId: this.getProperty('id'),
			onComplete: function (response) {
				if (response.error == false) {
					$(this.options.linkId).fireEvent('hide');
					$('system-message').fireEvent('check');
				} else {
					alert(response.message);
				}
			}
		}).send();
	}
	$$('a.hide-warning').each(function (a) {
		a.setProperty('link', a.getProperty('href'));
		a.setProperty('href', 'javascript: void(0);');
		a.addEvent('hide', function () {
			this.getParent().setProperty('hidden', 'hidden');
			var mySlider = new Fx.Slide(this.getParent(), {
				duration: 300
			});
			mySlider.slideOut();
		});
		// TODO: bindWithEvent deprecated in MT 1.3
		a.addEvent('click', hideWarning.bindWithEvent(a));
	});
});
