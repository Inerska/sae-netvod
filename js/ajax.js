$('input[name="search"]').focus();
var currentTimeout = null;

function htmlSpecialChars(str)
{
    return str.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>').replace(/"/g, '"').replace(/'/g, "'");
}

$(document).ready(function () {

    $('input[name="search"]').on('keydown', () => {
        if (currentTimeout) {
            clearTimeout(currentTimeout);
        }
    });

    $('input[name="search"]').on('keyup', function (e) {
        e.stopPropagation();
        let value = $('input[name="search"]').val();
        value = htmlSpecialChars(value);

        if (value.length > 0) {
            clearTimeout(currentTimeout);

            currentTimeout = setTimeout(() => {
                $.ajax({
                    url: '?action=search', type: 'POST', data: {
                        search: value
                    }, success: (html) => {
                        $('body').html(html);
                        $('input[name="search"]').val(value);
                    }
                }, 300);
            });
        }
    })
});