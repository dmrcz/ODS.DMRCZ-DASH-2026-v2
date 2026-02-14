function reloadDynamicTGs() {
    $.get($('.dynamic-tgs').data('update-url'), function(response) {
        const commonMarker = '<!--DYNTGTABLE-';
        const startMarker = commonMarker + 'BEGIN-->';
        const endMarker = commonMarker + 'END-->';

        const startIndex = response.indexOf(startMarker);
        const endIndex = response.indexOf(endMarker);

        if (startIndex !== -1 && endIndex != -1 && startIndex < endIndex) {
            const newHtml = response.substring(startIndex + startMarker.length, endIndex);
            const newTable = $('<div>').html(newHtml).find('table');
            if (newTable.length)
                $('.dynamic-tgs > table').replaceWith(newTable);
        }
    });

    setTimeout(reloadDynamicTGs, parseInt($('.dynamic-tgs').data('update-period')));
}

function updateRemainings() {
    const now = Math.floor(Date.now() / 1000);
    $('.auto-calculate-remaining').each(function() {
        const expTime = parseInt($(this).data('exptime'), 10);
        const remSec = expTime - now;

        let remText = "0:00";
        if (remSec > 0)
            remText = `${Math.floor(remSec / 60)}:${Math.floor(remSec % 60).toString().padStart(2, 0)}`;

        $(this).text(remText);
    });

    setTimeout(updateRemainings, 1000);
}

$(function() {
    setTimeout(reloadDynamicTGs, parseInt($('.dynamic-tgs').data('update-period')));
    updateRemainings();

    $(document).on('click', 'input.clickbtn', function() {
        const btn = $(this);
        url = btn.data('linkto');
        const loader = $('<span class="loader"></span>').insertAfter(btn);
        btn.prop('disabled', true);
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                loader.remove();
                btn.prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error('Error:', status, error);
                loader.remove();
                btn.prop('disabled', false);
            }
        });
        return false;
    });

    $(document).on('click', '.clickloader', function() {
        const loader = $('<span class="loader"></span>').insertAfter(this);
    });

    // auto-grow textarea
    $('#bmm-tg-static-form').on('input', '#add-bm-tg-list', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    $('#bmm-tg-static-form').on('change', '.bmm-tg-switch', function() {
        cmd = $(this).prop('checked')? 'link_static': 'drop_static';
        url = '/admin/system_api.php?action=bm_manager&cmd=' + cmd +
            '&tg=' + $(this).data('tg') + '&slot=' + $(this).data('slot');
        $.ajax({
            url: url,
            method: 'GET',
        });
    });
});
