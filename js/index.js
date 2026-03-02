$(document).ready(function() {
    $('.footer').css({
        minWidth: $('#content table').first().width() + 20 + 'px'
    });

    // js determine if url has import
    if (window.location.href.includes('import')) {
        $("body").addClass('import');
    }
    
    if (window.location.href.includes('&select')) {
        $("body").addClass('select-data-page');
    }

    function autoGrowTextarea(element) {
        var maxHeight = parseInt(window.getComputedStyle(element).maxHeight, 10);
        element.style.height = 'auto';

        if (!isNaN(maxHeight) && maxHeight > 0) {
            element.style.height = Math.min(element.scrollHeight, maxHeight) + 'px';
            element.style.overflowY = element.scrollHeight > maxHeight ? 'auto' : 'hidden';
            return;
        }

        element.style.height = element.scrollHeight + 'px';
        element.style.overflowY = 'hidden';
    }

    var editTextareas = document.querySelectorAll('#form table.layout textarea');
    editTextareas.forEach(function(textarea) {
        autoGrowTextarea(textarea);
        textarea.addEventListener('input', function() {
            autoGrowTextarea(textarea);
        });
    });
});
