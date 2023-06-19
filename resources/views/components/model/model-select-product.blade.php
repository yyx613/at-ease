<div id="model-select-product">
    <div id="cart-select-product-container">
        <div id="cart-container">
            <div class="title-container">
                <h6 class="base-heading">My Cart</h6>
            </div>
            <div id="prod-list"></div>
            <div id="prod-container-sample">
                <div class="prod-container" data-id="{prod-id}">
                    <div class="prod-left">
                        <h3 class="base-heading">{prod-name}</h3>
                        <span class="base-span" id="prod-qty">{prod-qty}</span>
                        <span class="base-span" id="prod-ttl">{prod-ttl}</span>
                    </div>
                    <div class="prod-right">
                        <button class="base-button" type="button" data-id="{prod-id}">
                            @include('components.icons.icon-delete')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="select-product-container">
            <div id="select-product-top">
                <div id="select-product-top-left">
                    <h2 class="base-heading">Product Name</h2>
                    <span class="base-span"></span>
                </div>
                <div id="select-product-top-right">
                    <span class="base-span">60 Remaining</span>
                </div>
            </div>
            <div id="select-product-mid">
                <button class="base-button" id="minus-qty">
                    @include('components.icons.icon-minus')
                </button>
                <input type="number" id="count-to-add" value="1">
                <button class="base-button" id="add-qty">
                    @include('components.icons.icon-plus')
                </button>
            </div>
            <div id="select-product-bottom">
                <span class="base-span" id="prod-ttl-price"></span>
                <button class="base-button" id="add-to-basket">Add To Basket</button>
            </div>
        </div>
    </div>
</div>