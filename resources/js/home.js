import './bootstrap';

var selectedUserId

$('.delete-user-trigger').on('click', function() {
    let userName = $(this).attr('data-username')
    let userId = $(this).attr('data-id')
    
    selectedUserId = userId
    $('#model-delete-user-container .base-span').text(userName)
    $('#model-delete-user-container').addClass('show-model-delete-user-container')
})

$('#delete-user-link').on('click', function(e) {
    let deleteLink = $('#delete-user-link').attr('href')
    
    $('#model-delete-user-container .base-a').attr('href', `${deleteLink}/${selectedUserId}`)
})

$('#cancel-delete-user-btn').on('click', () => {
    $('#model-delete-user-container').removeClass('show-model-delete-user-container')
})