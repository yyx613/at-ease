import './bootstrap';

var specialPriceInput = []

// Initialize edit user mode
const MODE = $('#create-edit-user-form').data('mode')
if (MODE === 'edit') initializeEditUserMode()

function initializeEditUserMode() {
    var userSpecialPriceInput = $('#product-list-container').data('specialpriceinput')
    specialPriceInput = userSpecialPriceInput
    
    for (let i = 0; i < userSpecialPriceInput.length; i++) {
        $(`#product-name option[value='${userSpecialPriceInput[i].product_id}']`).hide()
    }

    if (specialPriceInput.length <= 0) $('#no-special-price-msg').show();
    else $('#no-special-price-msg').hide()
}

// Role selection
$('#role').on('change', function() {
    let val = $(this).find(':selected').text().toLowerCase()
    if (val === 'driver') {
        $('#form-right').hide()
        $('.input-container[data-name="password"]').show()
        $('.input-container[data-name="driver"]').hide()
        $('.input-container[data-name="foc"]').hide()
    } else {
        $('#form-right').show()
        $('.input-container[data-name="password"]').hide()
        $('.input-container[data-name="driver"]').show()
        $('.input-container[data-name="foc"]').show()
    }
})

// Product name selection
$('#product-name').on('change', function() {
    let val = $(this).find(':selected').val()
    let price = $(this).find(':selected').data('price')
    
    if (val) {
        // Change default price
        $('#default-price').text(`Default Price: RM${price}`)
        $('#default-price').show()
    } else {
        $('#default-price').hide()
    }
})

// Only price input is allowed
$('#product-price').on('keydown', function(e) {
    let inputKey = e.originalEvent.key
    let price = $(this).val()
    
    if (!(/[0-9]/g.test(inputKey)) && inputKey.toLowerCase() !== 'backspace' && inputKey.toLowerCase() !== '.') e.preventDefault()
    if (/^[0-9]*\.[0-9]{2}$/g.test(price) && inputKey.toLowerCase() !== 'backspace') e.preventDefault() // Only 2 decimals
    if (price + inputKey > 100000) e.preventDefault()
})

$('#foc-2-n, #foc-3-n, #foc-3-m').on('keydown', function(e) {
    if (!(/[0-9]/g.test(e.originalEvent.key)) && e.originalEvent.key.toLowerCase() !== 'backspace') e.preventDefault()
})

// Assign product
$('#assign-product-btn').on('click', function() {
    let selectedProduct = $('#product-name').find(':selected')
    let selectedProductId = selectedProduct.val()
    let specialPrice = $('#product-price').val()
    
    if (!selectedProductId || !specialPrice) return // ignore event if product is not selected or special price is not entered
    
    specialPrice = (Math.round(specialPrice * 100) / 100).toFixed(2) // Format to 2 decimals
    let selectedProductName = selectedProduct.text()
    let selectedProductImg = selectedProduct.data('img') 

    // Append new product in HTML
    let productList = document.getElementById('product-list-container')
    let sampleContainer = $('#product-container-sample').html()
    // Replace values to selected product
    sampleContainer = sampleContainer.replaceAll('{product-id}', selectedProductId)
    sampleContainer = sampleContainer.replace('{product-img}', `/images/${selectedProductImg}`)
    sampleContainer = sampleContainer.replace('{product-name}', selectedProductName)
    sampleContainer = sampleContainer.replace('{product-price}', `RM${specialPrice}`)
    
    productList.innerHTML += sampleContainer
    
    // Reset option to default
    selectedProduct.hide() // Hide selected product option
    $('#product-name').val($('#product-name option:first').val())
    $('#product-price').val('')
    $('#default-price').hide()

    $('#no-special-price-msg').hide()

    let inputObj = {
        product_id: selectedProductId,
        special_price: specialPrice
    }
    specialPriceInput.push(inputObj)
})

// Remove product
$(document).on('click', '.product-delete-btn', function() {
    let productId = $(this).data('id')
    
    $(`#product-name option[value='${productId}']`).show()
    $(`.product-container[data-id='${productId}']`).remove()

    for (let i = 0; i < specialPriceInput.length; i++) {
        if (specialPriceInput[i].product_id == productId) {
            specialPriceInput.splice(i, 1)
            break
        }
    }

    if (specialPriceInput.length <= 0) $('#no-special-price-msg').show();
})

// Form submit
$('#create-edit-user-form').on('submit', function(e) {
    if (specialPriceInput.length > 0) {
        let encodeSpecialPriceInput = JSON.stringify(specialPriceInput)
        let inputElem = document.createElement('input')
        inputElem.name = 'special-price'
        inputElem.type = 'text'
        inputElem.value = encodeSpecialPriceInput
        inputElem.setAttribute('hidden', true)
        $(this).append(inputElem)
    }
})