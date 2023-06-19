import './bootstrap';

var cart = [], selectedProductId, selectedProductName, selectedProductPrice, selectedProductQty,
    qtyToAdd = 1;

// Selected product
$('.product-container').on('click', function() {
    if ($('#model-select-product').hasClass('show-model-select-product')) $('#model-select-product').removeClass('show-model-select-product')
    else {
        // Update product info in model
        let id = $(this).data('id')
        let productName = $(`.product-name[data-id='${id}']`).text()
        let productPrice = $(`.product-price[data-id='${id}']`).data('price')
        let productQty = $(this).data('qty')
        selectedProductId = id
        selectedProductName = productName
        selectedProductPrice = productPrice
        selectedProductQty = productQty
        qtyToAdd = 1 // Reset
        
        $('#model-select-product #select-product-container .base-heading').text(productName)
        $('#model-select-product #select-product-top-left .base-span').text(`RM${productPrice}`)
        $('#model-select-product #select-product-top-right .base-span').text(`${productQty} Remaining`)
        $('#model-select-product #prod-ttl-price').text(`Subtotal: RM${productPrice}`)

        // Check cart qty
        enableAddToBasketBtn()
        for (let i = 0; i < cart.length; i++) {
            if (cart[i].id == id && cart[i].qty >= productQty) {
                disableAddToBasketBtn()
                break
            } 
        }

        $('#count-to-add').val(1) // Reset count-to-add input
        $('#model-select-product').addClass('show-model-select-product')
    }
})
// Hide Model if clicked outside of white panel
$('#model-select-product').on('click', (e) => {
    if (e.target.id === 'model-select-product') $('#model-select-product').removeClass('show-model-select-product')
})
// Minus quantity btn
$('#minus-qty').on('click', () => {
    let currentCount = Number($('#count-to-add').val())
    currentCount--
    
    if (currentCount < 1) return

    qtyToAdd = currentCount
    let totalPrice = (Math.round(selectedProductPrice * currentCount * 100) / 100).toFixed(2) // Format to 2 decimals
    $('#model-select-product #prod-ttl-price').text(`Subtotal: RM${totalPrice}`)
    $('#count-to-add').val(currentCount)
})
// Add quantity btn
$('#add-qty').on('click', () => {
    let countInCart = 0
    let currentCount = Number($('#count-to-add').val())
    currentCount++
    
    for (let i = 0; i < cart.length; i++) {
        if (cart[i].id == selectedProductId) {
            countInCart = cart[i].qty
            break
        }
    }

    if ((currentCount + countInCart) > selectedProductQty) return

    qtyToAdd = currentCount
    let totalPrice = (Math.round(selectedProductPrice * currentCount * 100) / 100).toFixed(2) // Format to 2 decimals
    $('#model-select-product #prod-ttl-price').text(`Subtotal: RM${totalPrice}`)
    $('#count-to-add').val(currentCount)
})
// Add to basket btn
$('#add-to-basket').on('click', () => {
    // Add selected product into cart
    let prodExists = false
    for (let i = 0; i < cart.length; i++) {
        if (cart[i].id == selectedProductId) { // Add qty if product already in cart
            cart[i].qty += qtyToAdd
            let totalPrice = (Math.round(selectedProductPrice * cart[i].qty * 100) / 100).toFixed(2) // Format to 2 decimals
            setTimeout(() => {
                $(`.prod-container[data-id='${selectedProductId}'] .prod-left #prod-qty`).text(`Qty x${cart[i].qty}`)
                $(`.prod-container[data-id='${selectedProductId}'] .prod-left #prod-ttl`).text(`Total Price: RM${totalPrice}`)
            }, 300);
            prodExists = true
            break
        }
    }
    if (!prodExists) { // Add new product in cart
        let prod = {
            id: selectedProductId,
            qty: qtyToAdd 
        }
        cart.push(prod)
        // Append HTML element
        let productList = document.getElementById('prod-list')
        let sampleContainer = $('#prod-container-sample').html()
        let totalPrice = (Math.round(selectedProductPrice * qtyToAdd * 100) / 100).toFixed(2) // Format to 2 decimals
        // Replace values to selected product
        sampleContainer = sampleContainer.replaceAll('{prod-id}', selectedProductId)
        sampleContainer = sampleContainer.replace('{prod-name}', selectedProductName)
        sampleContainer = sampleContainer.replace('{prod-qty}', `Qty x${qtyToAdd}`)
        sampleContainer = sampleContainer.replace('{prod-ttl}', `Total Price: RM${totalPrice}`)
        setTimeout(() => {
            productList.innerHTML += sampleContainer
        }, 300);
    }
    
    $('#model-select-product').removeClass('show-model-select-product')
})
// Remove product in cart
$(document).on('click', '.prod-container .base-button', function() {
    let id = $(this).data('id')

    for (let i = 0; i < cart.length; i++) {
        if (cart[i].id == id) {
            cart.splice(i, 1)
            break
        }
    }

    if (selectedProductId == id) enableAddToBasketBtn()
    
    $(`.prod-container[data-id='${id}']`).remove()
})
// Checkout
$('#driver-action-button-container .base-button').on('click', function() {
    let encodedCart = JSON.stringify(cart)
    $('#cart-input').val(encodedCart)
    $('#form-submit').submit()
})

function enableAddToBasketBtn() {
    $('#model-select-product #add-to-basket').attr('disabled', false)
    $('#model-select-product #add-to-basket').text('Add To Basket')
    $('#model-select-product #add-to-basket').removeClass('qty-not-enough')
}

function disableAddToBasketBtn() {
    $('#model-select-product #add-to-basket').attr('disabled', true)
    $('#model-select-product #add-to-basket').text('Not Enough Quantity')
    $('#model-select-product #add-to-basket').addClass('qty-not-enough')
}