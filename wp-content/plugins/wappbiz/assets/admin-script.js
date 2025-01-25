jQuery(document).ready(function ($) {
    $(document).on('click', '.view-response', function (e) {
        e.preventDefault();

        const responseText = $(this).data('response') || '';

        let formattedResponse = '';

        try {
            // First, parse the response
            const parsedResponse = JSON.parse(responseText);

            // If it's an object, use a more robust stringify method
            if (typeof parsedResponse === 'object') {
                formattedResponse = JSON.stringify(parsedResponse, null, 2);
            } else {
                formattedResponse = String(parsedResponse);
            }
        } catch (error) {
            // If parsing fails, convert to string directly
            formattedResponse = String(responseText);
        }

        // Ensure something is displayed
        if (!formattedResponse || formattedResponse.trim() === '') {
            formattedResponse = 'No response data available';
        }

        $('#response-text').text(formattedResponse);
        $('#response-modal').css('display', 'flex');
    });

    // Modal close handlers
    $('#response-modal .close').on('click', function () {
        $('#response-modal').hide();
    });

    $(document).on('click', function (event) {
        if ($(event.target).is('#response-modal')) {
            $('#response-modal').hide();
        }
    });
});