jQuery(document).ready(function($) {
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
});
