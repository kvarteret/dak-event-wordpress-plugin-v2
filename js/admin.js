function dak_event_import_poller() {
	var data = {
		action: 'dak_event_import',
		offset: 0
	};

	var poller = function(response) {
		data.offset = response.offset + response.count;
		console.log("import partial runtime: " + response.runtime);
		jQuery('#dak_event_import .msg').text(data.offset + '/' + response.totalCount);

		if (data.offset < response.totalCount) {
			jQuery.post(ajaxurl, data, poller, "json");
		}
	};

	jQuery.post(ajaxurl, data, poller, "json");
}

function dak_event_purge_poller(response) {
	var data = {
		action: 'dak_event_purge'
	};

	var count = 0;
	jQuery('#dak_event_purge .msg').text(count);

	var poller = function(response) {
		count += response.count;
		jQuery('#dak_event_purge .msg').text(count);

		if (response.count == response.limit) {
			// We will only delete a certain amount at a time
			// We'll continue until count is lesser than limit
			jQuery.post(ajaxurl, data, poller, "json");
		}
	};

	jQuery.post(ajaxurl, data, poller, "json");

}

jQuery(document).ready(function($) {
	$('#dak_event_import').on('click', dak_event_import_poller);
	$('#dak_event_purge').on('click', dak_event_purge_poller);
});
