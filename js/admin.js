function dak_event_import_poller() {
	var button = jQuery(this);

	var data = {
		action: 'dak_event_import',
		offset: 0,
		provider: button.data('provider')
	};

	var totalCount = 0;

	var poller = function(response) {
		data.offset = response.offset + response.limit;
		totalCount += response.count;

		console.log("import partial runtime: " + response.runtime);
		button.find('.msg').text(totalCount);

		if (data.offset < response.totalCount) {
			jQuery.post(ajaxurl, data, poller, "json");
		}
	};

	jQuery.post(ajaxurl, data, poller, "json");
}

function dak_event_purge_poller(response) {
	var button = jQuery(this);

	var data = {
		action: 'dak_event_purge',
		provider: button.data('provider')
	};

	var count = 0;
	button.find('.msg').text(count);

	var poller = function(response) {
		count += response.count;
		button.find('.msg').text(count);

		if (response.count == response.limit) {
			// We will only delete a certain amount at a time
			// We'll continue until count is lesser than limit
			jQuery.post(ajaxurl, data, poller, "json");
		}
	};

	jQuery.post(ajaxurl, data, poller, "json");

}

jQuery(document).ready(function($) {
	$('button.dak_event_import').on('click', dak_event_import_poller);
	$('button.dak_event_purge').on('click', dak_event_purge_poller);
});
