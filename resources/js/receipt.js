import './bootstrap'

$('#print-container .base-button').on('click', function() {
    let objFra = document.getElementById('pdf-frame');
    objFra.contentWindow.print();
})