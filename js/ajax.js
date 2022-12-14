$('input[name="search"]').focus();
var currentTimeout = null;

$(document).ready(function () {

    $('input[name="search"]').on('keydown', () => {
        if (currentTimeout) {
            clearTimeout(currentTimeout);
        }
    });

    $('input[name="search"]').on('keyup', function (e) {
        e.stopPropagation();
        let value = $('input[name="search"]').val();

        if (value.length > 0) {
            clearTimeout(currentTimeout);

            currentTimeout = setTimeout(() => {
                $.ajax({
                    url: '?action=search-ajax', type: 'POST', data: {
                        search: value
                    }, success: (html) => {
                        $('#articles').html(html);
                    }
                }, 300);
            });
        }
    })
});