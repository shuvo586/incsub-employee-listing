jQuery(document).ready(function($) {
    /**
     * Employee Search form
     */
    $('#employee-search-form').on('submit', function(e) {
        e.preventDefault();

        let searchQuery = $('#employee-search').val();

        let data = {
            action: 'incsub_employee_search',
            query: searchQuery
        }

        console.log( data );

        $.ajax({
            url: incsub_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'incsub_employee_search',
                query: searchQuery
            },
            success: function(response) {
                $('.employee-lists__wrap').html(response);
            }
        });
    });

    /**
     * Employee insert form
     */
    $('#employee-submit-form').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize(); // Serialize form data

        $.ajax({
            url: incsub_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'incsub_submit_employee', // Action name to trigger the PHP function
                formData: formData,
                security: incsub_ajax_object.ajax_nonce // Nonce for security
            },
            success: function(response) {
                $('.form-message').html('<div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><strong class="font-bold">Success!</strong><span class="block sm:inline"> ' + response + '</span></div>');
                $('#employee-submit-form')[0].reset();
            },
            error: function(err) {
                $('.form-message').html('<div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' + err.responseText + '</span></div>');
            }
        });
    });
});
