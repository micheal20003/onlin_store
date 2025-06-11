<?php
// Connection
$con = mysqli_connect('localhost', 'root', '', 'fitness_app');
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['full_name'];
    $address = $_POST['address'];
    $cart_data = json_decode($_POST['cart_data'], true);
    $total_price = 0;

    foreach ($cart_data as $item) {
        $price = $item['price'];
        $quantity = $item['quantity'];
        $item_total_price = $price * $quantity;
        $total_price += $item_total_price;

        $product_name = strtolower($item['product_name']);

        $sql = "INSERT INTO orders (customer_name, product_name, price, quantity, address, order_status, total_price)
                VALUES ('$customer_name', '$product_name', '$price', '$quantity', '$address', 'ongoing', '$item_total_price')";

        if (!mysqli_query($con, $sql)) {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }

    echo "<script>alert('Order placed successfully! Total: €" . number_format($total_price, 2) . "'); window.location.href='store.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Simple Online Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        .cart {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
        }

        .cart.active {
            display: block;
        }

        .cart-content .cart-box {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .cart-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
        }

        .detail-box {
            flex-grow: 1;
        }

        .cart-remove {
            cursor: pointer;
            color: red;
            margin-left: 10px;
        }

        #payment-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            border-radius: 10px;
        }

        body.no-scroll {
            overflow: hidden;
        }

        #main-content.hidden {
            display: none;
        }

        .btn-close-payment {
            float: right;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: red;
        }
    </style>
</head>

<body>
    <header>
        <div class="nav container">
            <a href="#" class="logo">Online Store</a>
            <i class="bx bx-shopping-bag" id="cart-icon"></i>
            <div class="cart">
                <h2 class="cart-title">Your Cart</h2>
                <div class="cart-content"></div>
                <div class="total">
                    <div class="total-title">Total</div>
                    <div class="total-price">0,00 €</div>
                </div>
                <button type="button" class="btn-buy">Buy Now</button>
                <i class="bx bx-x" id="close-cart"></i>
            </div>
        </div>
    </header>

    <main id="main-content">
        <section class="shop container">
            <h2 class="section-title">Products</h2>
            <div class="shop-content">
                <?php
                $result = mysqli_query($con, "SELECT * FROM products");
                while ($row = mysqli_fetch_array($result)) {
                    $image_path = "http://localhost:8080/onlin%20store/a/img/" . basename($row['image']);
                    echo "
                    <div class='product-box'>
                        <img src='$image_path' alt='$row[name]' class='product-img' onerror=\"this.onerror=null; this.src='img/default.jpg';\">
                        <h2 class='product-title'>$row[name]</h2>
                        <span class='price'>$row[price] €</span>
                        <i class='bx bx-shopping-bag add-cart' data-id='$row[id]' data-name='$row[name]'></i>
                    </div>";
                }
                mysqli_close($con);
                ?>
            </div>
        </section>
    </main>

    <div id="payment-form" class="card">
        <button class="btn-close-payment" onclick="closePayment()">✖</button>
        <div class="card__header">
            <img src="./icons/logo.png" class="logo" alt="Logo" />
            <h1>Payment Information</h1>
        </div>

        <form id="checkout-form" method="POST" action="">
            <div class="form-group">
                <label for="full-name">Full Name</label>
                <input name="full_name" type="text" id="full-name" placeholder="Alex Montoya" required />
            </div>

            <div class="icon-group-container">
                <label for="card-number">Card Number</label>
                <div class="icon-group">
                    <input type="text" id="card-number" placeholder="1234 1234 1234 1234" pattern="\d{15,16}" minlength="15" maxlength="16" inputmode="numeric" required />
                    <img id="card-icon" src="./icons/credit-card.png" height="14" width="14" alt="Card Type" />
                </div>
            </div>

            <div class="row-group">
                <div class="form-group">
                    <label for="expiration">Expiration Date</label>
                    <input type="text" id="expiration" name="expiration" placeholder="MM/YYYY" maxlength="7" required />
                </div>

                <div class="icon-group-container">
                    <label for="cvv">CVV</label>
                    <div class="icon-group">
                        <input type="tel" id="cvv" name="cvv" minlength="3" maxlength="4" pattern="\d{3,4}" placeholder="···" inputmode="numeric" required />
                        <img id="info-icon" src="./icons/info.png" height="14" width="14" alt="CVV Info" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Av. Morelos 123" required />
            </div>

            <input type="hidden" name="cart_data" id="cart-data-input">
            <input type="hidden" name="total_price" id="total-price-input">

            <button type="submit">Confirm Payment</button>
        </form>

        <div class="verify-info">
            <small>Verify the information is correct</small>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.querySelector('#cart-icon');
            const cart = document.querySelector('.cart');
            const closeCart = document.querySelector('#close-cart');
            const paymentForm = document.getElementById('payment-form');
            const buyNowButton = document.querySelector('.btn-buy');

            cartIcon.addEventListener('click', () => cart.classList.add('active'));
            closeCart.addEventListener('click', () => cart.classList.remove('active'));

            buyNowButton.addEventListener('click', () => {
                const cartItems = document.querySelectorAll('.cart-content .cart-box');
                if (cartItems.length === 0) {
                    alert("Your cart is empty. Please select something before proceeding.");
                } else {
                    paymentForm.style.display = 'block';
                    document.getElementById('main-content').classList.add('hidden');
                    document.body.classList.add('no-scroll');
                    cart.classList.remove('active');
                }
            });

            document.body.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-cart')) {
                    const productBox = e.target.closest('.product-box');
                    addProductToCart(productBox);
                    updateTotal();
                }
                if (e.target.classList.contains('cart-remove')) {
                    e.stopPropagation();
                    e.preventDefault();
                    const cartBox = e.target.closest('.cart-box');
                    if (cartBox) {
                        cartBox.remove();
                        updateTotal();
                    }
                }
            });

            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-quantity')) {
                    if (isNaN(e.target.value) || e.target.value <= 0) {
                        e.target.value = 1;
                    }
                    updateTotal();
                }
            });

            function addProductToCart(productBox) {
                const addCartButton = productBox.querySelector('.add-cart');
                const productId = addCartButton.getAttribute('data-id');
                const productName = addCartButton.getAttribute('data-name');
                const price = productBox.querySelector('.price').innerText;
                const imgSrc = productBox.querySelector('.product-img').src;
                const cartContent = document.querySelector('.cart-content');
                const existingItems = cartContent.querySelectorAll('.cart-product-title');

                for (let item of existingItems) {
                    if (item.dataset.id === productId) {
                        const quantityInput = item.closest('.cart-box').querySelector('.cart-quantity');
                        quantityInput.value = parseInt(quantityInput.value) + 1;
                        updateTotal();
                        return;
                    }
                }

                const cartBox = document.createElement('div');
                cartBox.classList.add('cart-box');
                cartBox.innerHTML = `
                    <img src="${imgSrc}" class="cart-img">
                    <div class="detail-box">
                        <div class="cart-product-title" data-id="${productId}">${productName}</div>
                        <div class="cart-price">${price}</div>
                        <input type="number" value="1" class="cart-quantity">
                    </div>
                    <i class='bx bx-trash-alt cart-remove'></i>
                `;
                cartContent.appendChild(cartBox);
            }

            function updateTotal() {
                const cartItems = document.querySelectorAll('.cart-content .cart-box');
                let total = 0;
                cartItems.forEach(item => {
                    const price = parseFloat(item.querySelector('.cart-price').innerText.replace('€', '').replace(',', '.').trim());
                    const quantity = parseInt(item.querySelector('.cart-quantity').value);
                    if (!isNaN(price) && !isNaN(quantity)) {
                        total += price * quantity;
                    }
                });
                document.querySelector('.total-price').innerText = total.toFixed(2) + ' €';
            }

            document.getElementById('checkout-form').addEventListener('submit', function(e) {
                const expiration = document.getElementById('expiration').value.trim();
                const expRegex = /^(0[1-9]|1[0-2])\/\d{4}$/;

                if (!expRegex.test(expiration)) {
                    e.preventDefault();
                    alert("Expiration date must be in MM/YYYY format.");
                    return;
                }

                const [inputMonth, inputYear] = expiration.split('/').map(Number);
                const today = new Date();
                const thisMonth = today.getMonth() + 1;
                const thisYear = today.getFullYear();

                if (inputYear < thisYear || (inputYear === thisYear && inputMonth < thisMonth)) {
                    e.preventDefault();
                    alert("Expiration date must be in the future.");
                    return;
                }

                let cartItems = document.querySelectorAll('.cart-content .cart-box');
                let cartData = [];

                cartItems.forEach(function(item) {
                    let productTitleElement = item.querySelector('.cart-product-title');
                    let productId = productTitleElement.dataset.id;
                    let productName = productTitleElement.innerText;
                    let price = item.querySelector('.cart-price').innerText.replace('€', '').replace(',', '.').trim();
                    let quantity = item.querySelector('.cart-quantity').value;

                    cartData.push({
                        product_id: parseInt(productId),
                        product_name: productName,
                        price: parseFloat(price),
                        quantity: parseInt(quantity)
                    });
                });

                document.getElementById('cart-data-input').value = JSON.stringify(cartData);
            });

            document.getElementById('expiration').addEventListener('input', function(e) {
                let input = e.target.value.replace(/\D/g, '');
                if (input.length >= 1) {
                    if (parseInt(input.slice(0, 2)) > 12) input = '12' + input.slice(2);
                    if (parseInt(input.slice(0, 2)) < 1) input = '01' + input.slice(2);
                }
                if (input.length > 2) {
                    e.target.value = input.slice(0, 2) + '/' + input.slice(2, 6);
                } else {
                    e.target.value = input;
                }
            });
        });

        function closePayment() {
            document.getElementById('payment-form').style.display = 'none';
            document.getElementById('main-content').classList.remove('hidden');
            document.body.classList.remove('no-scroll');
        }
    </script>
</body>

</html>