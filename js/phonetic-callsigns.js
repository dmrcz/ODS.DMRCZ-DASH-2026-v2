$(document).ready(function() {
    'use strict';

    const phoneticAlphabet = {
        'A': 'Alpha', 'B': 'Bravo', 'C': 'Charlie', 'D': 'Delta', 'E': 'Echo', 'F': 'Foxtrot',
        'G': 'Golf', 'H': 'Hotel', 'I': 'India', 'J': 'Juliet', 'K': 'Kilo', 'L': 'Lima',
        'M': 'Mike', 'N': 'November', 'O': 'Oscar', 'P': 'Papa', 'Q': 'Quebec', 'R': 'Romeo',
        'S': 'Sierra', 'T': 'Tango', 'U': 'Uniform', 'V': 'Victor', 'W': 'Whiskey', 'X': 'X-ray',
        'Y': 'Yankee', 'Z': 'Zulu',
        '0': 'Zero', '1': 'One', '2': 'Two', '3': 'Three', '4': 'Four', '5': 'Five',
        '6': 'Six', '7': 'Seven', '8': 'Eight', '9': 'Nine'
    };

    let pinnedElement = null;

    function getPhonetic(callsign) {
        return callsign.split('').map(char => phoneticAlphabet[char.toUpperCase()] || char).join(' ');
    }

    function addPhoneticLabels() {
        $('.divTableCellMono a').each(function() {
            const callsignLink = $(this);
            const callsign = callsignLink.text().trim();
            if (callsign && callsignLink.next('.phonetic-label').length === 0) {
                const phonetic = getPhonetic(callsign);
                const phoneticElement = $('<div></div>')
                    .addClass('phonetic-label')
                    .css({
                        'font-size': '1em',
                        'color': '#0000FF',
                        'text-align': 'center'
                    })
                    .html(phonetic);

                phoneticElement.on('click', function() {
                    // Unpin if already pinned
                    const existingPinSection = $('#pin-section');
                    if (existingPinSection.length) {
                        existingPinSection.remove();
                        pinnedElement = null;
                    }
                    // Pin the clicked element
                    pinnedElement = phoneticElement;

                    const callsignElement = $('<div></div>')
                        .css({
                            'font-size': '4em',
                            'font-weight': 'bold',
                            'color': '#ffffff',
                            'background-color': '#008000',
                            'padding': '10px',
                            'text-align': 'center'
                        })
                        .text(callsign);

                    const timestampElement = $('<div></div>')
                        .css({
                            'font-size': '2em',
                            'color': '#ffffff',
                            'text-align': 'center'
                        })
                        .text(`Pinned at: ${new Date().toLocaleString()}`);

                    const unpinLabel = $('<div></div>')
                        .css({
                            'font-size': '1em',
                            'color': '#ffffff',
                            'text-align': 'center',
                            'margin-top': '10px'
                        })
                        .text('Click anywhere on this section to unpin');

                    const pinSection = $('<div></div>')
                        .attr('id', 'pin-section')
                        .css({
                            'background-color': '#008000',
                            'padding': '20px',
                            'margin-top': '10px',
                            'text-align': 'center',
                            'cursor': 'pointer'
                        })
                        .append(callsignElement, phoneticElement.clone().css({'font-size': '2em', 'border': '2px solid #008000', 'margin-top': '10px'}), timestampElement, unpinLabel)
                        .on('click', function() {
                            $(this).remove();
                            pinnedElement = null;
                        });

                    $('h1').after(pinSection);
                });

                callsignLink.attr('title', 'Click here to pin');
                callsignLink.parent().append(phoneticElement);
            }
        });
    }

    addPhoneticLabels();

    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' || mutation.type === 'subtree') {
                addPhoneticLabels();
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
