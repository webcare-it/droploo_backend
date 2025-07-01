@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <!-- Product List -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Product List</div>
                        <div class="card-body">
                            <input type="text" class="form-control mb-3" id="product-search"
                                placeholder="Search product...">
                            <div class="row" id="product-list">
                                @foreach ($products as $index => $product)
                                    <div class="col-md-4 mb-3 product-item" data-name="{{ strtolower($product->name) }}"
                                        style="display: {{ $index < 3 ? 'block' : 'none' }};">
                                        <div class="card">
                                            <img src="{{ $product->imageUrl }}" class="card-img-top"
                                                alt="{{ $product->name }}">
                                            <div class="card-body text-center">
                                                <h6>{{ $product->name }}</h6>
                                                <p class="text-success">{{ $product->discount_price ?? $product->regular_price }}
                                                </p>
                                                <input type="text" class="form-control mb-2 product-color"
                                                    placeholder="Color" data-id="{{ $product->id }}">
                                                <input type="text" class="form-control mb-2 product-size"
                                                    placeholder="Size" data-id="{{ $product->id }}">
                                                <textarea class="form-control mb-2 product-notes" placeholder="Notes" data-id="{{ $product->id }}"></textarea>
                                                <button class="btn btn-sm btn-primary add-to-cart"
                                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    data-price="{{ $product->discount_price ?? $product->regular_price }}">Add
                                                    to Cart</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-secondary mt-3" id="load-more">Load More</button>
                        </div>
                    </div>
                </div>

                <!-- Manual Product Entry -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Add Manual Product</div>
                        <div class="card-body">
                            <input type="text" class="form-control mb-2" id="manual-name" placeholder="Product Name">
                            <input type="number" class="form-control mb-2" id="manual-price" placeholder="Price">
                            <input type="text" class="form-control mb-2" id="manual-color" placeholder="Color">
                            <input type="text" class="form-control mb-2" id="manual-size" placeholder="Size">
                            <textarea class="form-control mb-2" id="manual-notes" placeholder="Notes"></textarea>
                            <textarea class="form-control mb-2" id="manual-description" placeholder="Description" style="display: none">Test Description</textarea>
                            <input type="file" class="form-control mb-2" id="manual-image">
                            <small id="image-placeholder" class="text-muted">Max image size: 2MB</small><br>
                            <button class="btn btn-sm btn-secondary" id="add-manual">Add to Cart</button>
                        </div>
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header bg-success text-white">Cart</div>
                        <div class="card-body">
                            <ul class="list-group" id="cart-items"></ul>
                            <hr>
                            <h5>Total: <span id="total-price">0.00</span></h5>

                            <!-- Customer Information -->
                            <input type="text" class="form-control mb-2" id="customer-name" placeholder="Customer Name">
                            <input type="text" class="form-control mb-2" id="customer-phone" placeholder="Phone Number">
                            <textarea class="form-control mb-2" id="customer-address" placeholder="Delivery Address"></textarea>

                            <!-- Select Delivery Area -->
                            <select class="form-control mb-2" id="customer-area">
                                <option value="120" selected>Outside Dhaka (120)</option>
                                <option value="60">Inside Dhaka (60)</option>
                                <option value="0">Free Delivery (0)</option>
                            </select>

                            <button class="btn btn-success btn-block mt-3" id="checkout-btn">Checkout</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('product-search').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                item.style.display = item.getAttribute('data-name').includes(searchValue) ? 'block' :
                    'none';
            });
        });

        document.getElementById('load-more').addEventListener('click', function() {
            let hiddenItems = document.querySelectorAll('.product-item[style*="display: none"]');
            let count = 0;
            hiddenItems.forEach(item => {
                if (count < 6) {
                    item.style.display = 'block';
                    count++;
                }
            });
            if (hiddenItems.length <= 6) {
                this.style.display = 'none';
            }
        });

        // Cart data array
        let cart = [];

        // Function to update the cart UI
        function updateCartUI() {
            let cartItemsList = document.getElementById('cart-items');
            let totalPrice = 0;
            cartItemsList.innerHTML = ''; // Clear the cart list before updating it

            cart.forEach(item => {
                let listItem = document.createElement('li');
                listItem.classList.add('list-group-item');
                listItem.innerHTML = `
                <strong>${item.name}</strong><br>
                Color: ${item.color || 'N/A'}<br>
                Size: ${item.size || 'N/A'}<br>
                Notes: ${item.notes || 'N/A'}<br>
                Price: ${item.price.toFixed(2)}<br>
                <!--<img src="${item.image}" alt="${item.name}" style="width: 50px;">-->
                <button class="btn btn-sm btn-danger float-right remove-from-cart" data-id="${item.id}">Remove</button>
            `;
                cartItemsList.appendChild(listItem);
                totalPrice += item.price;
            });

            // Update the total price
            document.getElementById('total-price').innerText = totalPrice.toFixed(2);

            // Attach remove event listeners to remove buttons
            document.querySelectorAll('.remove-from-cart').forEach(button => {
                button.addEventListener('click', function() {
                    let productId = this.getAttribute('data-id');
                    removeFromCart(productId);
                });
            });
        }

        // Function to remove an item from the cart
        function removeFromCart(productId) {
            // Remove the item from the cart array
            cart = cart.filter(item => item.id !== productId);

            // Update the cart UI
            updateCartUI();
        }

        // Attach the "Add to Cart" button event listeners
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-id');
                let productName = this.getAttribute('data-name');
                let productPrice = parseFloat(this.getAttribute('data-price'));
                let productColor = document.querySelector(`.product-color[data-id='${productId}']`).value;
                let productSize = document.querySelector(`.product-size[data-id='${productId}']`).value;
                let productNotes = document.querySelector(`.product-notes[data-id='${productId}']`).value;

                addToCart(productId, productName, productPrice, productColor, productSize, productNotes);

                // Show Toastr notification
                toastr.success('Product added successfully!');
            });
        });

        // Define the addToCart function
        function addToCart(productId, productName, productPrice, productColor, productSize, productNotes,
            productDescription = '', productImage = '') {
            // Add the product to the cart array
            cart.push({
                id: productId, // Temporary ID for manual products
                name: productName,
                price: productPrice,
                color: productColor,
                size: productSize,
                notes: productNotes,
                description: productDescription,
                image: productImage
            });

            // Update the cart UI
            updateCartUI();
        }

        // Handle manual product entry
        document.getElementById('add-manual').addEventListener('click', function() {
            let productName = document.getElementById('manual-name').value.trim();
            let productPrice = parseFloat(document.getElementById('manual-price').value) || 0; // Fix NaN issue
            let productColor = document.getElementById('manual-color').value.trim();
            let productSize = document.getElementById('manual-size').value.trim();
            let productNotes = document.getElementById('manual-notes').value.trim();
            let productDescription = document.getElementById('manual-description').value.trim();
            let productImage = document.getElementById('manual-image').files[0]?.name || 'No Image';

            if (!productName || productPrice <= 0) {
                toastr.error('Product Name and Price are required!');
                return;
            }

            // Generate a temporary ID for the manual product
            let manualProductId = Date.now();

            // Add to cart only after successful insertion in the database
            let formData = new FormData();
            formData.append('temp_id', manualProductId);
            formData.append('name', productName);
            formData.append('price', productPrice);
            formData.append('color', productColor);
            formData.append('size', productSize);
            formData.append('notes', productNotes);
            formData.append('description', productDescription);
            formData.append('image', document.getElementById('manual-image').files[0]);

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/add-manual-product', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add to cart after database insertion
                        addToCart(manualProductId, productName, productPrice, productColor, productSize,
                            productNotes, productDescription, productImage);
                        toastr.success('Product added successfully!');

                        // Clear input fields
                        document.getElementById('manual-name').value = '';
                        document.getElementById('manual-price').value = '';
                        document.getElementById('manual-color').value = '';
                        document.getElementById('manual-size').value = '';
                        document.getElementById('manual-notes').value = '';
                        // document.getElementById('manual-description').value = '';
                        document.getElementById('manual-image').value = '';
                    } else {
                        toastr.error('Failed to add product. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Something went wrong! Please try again.');
                });
        });
    </script>

    {{-- Image Validation...- --}}
    <script>
        document.getElementById('manual-image').addEventListener('change', function() {
            let placeholderText = document.getElementById('image-placeholder');
            if (this.files.length > 0) {
                placeholderText.textContent = this.files[0].name; // Show file name
            } else {
                placeholderText.textContent = "Max image size: 2MB"; // Reset placeholder
            }
        });
    </script>

    {{-- Checkout- --}}
    <script>
        document.getElementById('checkout-btn').addEventListener('click', function() {
            let customerName = document.getElementById('customer-name').value.trim();
            let customerPhone = document.getElementById('customer-phone').value.trim();
            let customerAddress = document.getElementById('customer-address').value.trim();
            let customerArea = document.getElementById('customer-area').value;

            if (!customerName || !customerPhone || !customerAddress) {
                toastr.error("Please fill in all customer details before checkout!");
                return;
            }

            let orderData = {
                customer_name: customerName,
                customer_phone: customerPhone,
                customer_address: customerAddress,
                customer_area: customerArea,
                cart: cart
            };

            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/checkout-custom', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success("Order placed successfully!");
                        cart = []; // Clear cart after successful checkout
                        updateCartUI();

                        // Clear customer details
                        document.getElementById('customer-name').value = '';
                        document.getElementById('customer-phone').value = '';
                        document.getElementById('customer-address').value = '';
                    } else {
                        toastr.error("Failed to place order. Try again!");
                        console.log(data);
                    }
                })
                .catch(error => {
                    console.error("Checkout Error:", error);
                    toastr.error("Something went wrong. Please try again.");
                });
        });
    </script>

    {{-- CK editor... --}}
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
@endsection