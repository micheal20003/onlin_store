document.addEventListener('DOMContentLoaded', () => {
            const cartIcon = document.querySelector('#cart-icon');
            const cart = document.querySelector('.cart');
            const closeCart = document.querySelector('#close-cart');

            cartIcon.addEventListener('click', () => cart.classList.add('active'));
            closeCart.addEventListener('click', () => cart.classList.remove('active'));

            const initCartFunctions = () => {
                document.querySelectorAll('.cart-remove').forEach((button) => {
                    button.addEventListener('click', removeCartItem);
                });

                document.querySelectorAll('.cart-quantity').forEach((input) => {
                    input.addEventListener('change', quantityChanged);
                });

                document.querySelectorAll('.add-cart').forEach((button) => {
                    button.addEventListener('click', addCartClicked);
                });

                document.querySelector('.btn-buy').addEventListener('click', buyButtonClicked);
            };

            initCartFunctions();

            function buyButtonClicked() {
                const cartContent = document.querySelector('.cart-content');
                const cartItems = cartContent.querySelectorAll('.cart-box');
                if (cartItems.length === 0) {
                    alert('Your cart is empty');
                    return;
                }

                const cartData = [];

                cartItems.forEach((cartBox) => {
                    const title = cartBox.querySelector('.cart-product-title').innerText;
                    const price = cartBox.querySelector('.cart-price').innerText.replace('€', '').trim();
                    const quantity = cartBox.querySelector('.cart-quantity').value;
                    const image = cartBox.querySelector('.cart-img').src;
                    cartData.push({
                        title,
                        price,
                        quantity,
                        image
                    });
                });

                localStorage.setItem('cartData', JSON.stringify(cartData));
                window.location.href = "#payment-form"; // Redirect to payment form
            }

            function removeCartItem(event) {
                event.target.parentElement.remove();
                updateTotalPrice();
            }

            function quantityChanged(event) {
                const input = event.target;
                if (isNaN(input.value) || input.value <= 0) {
                    input.value = 1;
                }
                updateTotalPrice();
            }

            let isAddingToCart = false;

            function addCartClicked(event) {
                isAddingToCart = true;
                const button = event.target;
                const shopProducts = button.parentElement;
                const title = shopProducts.querySelector('.product-title').innerText;
                const price = shopProducts.querySelector('.price').innerText;
                const productImg = shopProducts.querySelector('.product-img').src;
                addProductToCart(title, price, productImg);
                updateTotalPrice();
                setTimeout(() => (isAddingToCart = false), 100);
            }

            function addProductToCart(title, price, productImg) {
                const cartItems = document.querySelector('.cart-content');
                const cartItemNames = cartItems.querySelectorAll('.cart-product-title');

                if (Array.from(cartItemNames).some((itemName) => itemName.innerText === title)) {
                    alert('This item is already in your cart');
                    return;
                }

                const cartShopBox = document.createElement('div');
                cartShopBox.classList.add('cart-box');
                cartShopBox.innerHTML = `
          <img src="${productImg}" alt="${title}" class="cart-img">
          <div class="detail-box">
            <div class="cart-product-title">${title}</div>
            <div class="cart-price">${price}</div>
            <input type="number" value="1" class="cart-quantity">
          </div>
          <i class="bx bxs-trash-alt cart-remove"></i>`;

                cartItems.append(cartShopBox);

                cartShopBox.querySelector('.cart-remove').addEventListener('click', removeCartItem);
                cartShopBox.querySelector('.cart-quantity').addEventListener('change', quantityChanged);

                cart.classList.add('active');
            }

            function updateTotalPrice() {
                const cartContent = document.querySelector('.cart-content');
                const cartBoxes = cartContent.querySelectorAll('.cart-box');
                let total = 0;
                cartBoxes.forEach((cartBox) => {
                    const priceElement = cartBox.querySelector('.cart-price');
                    const quantityElement = cartBox.querySelector('.cart-quantity');
                    let price = parseFloat(priceElement.innerText.replace('€', '').replace(',', '.'));
                    let quantity = parseInt(quantityElement.value);
                    total += price * quantity;
                });

                document.querySelector('.total-price').innerText = `${total.toFixed(2).replace('.', ',')} €`;
            }

            document.addEventListener('click', (event) => {
                if (!cart.contains(event.target) && !cartIcon.contains(event.target) && !isAddingToCart && cart.classList.contains('active')) {
                    cart.classList.remove('active');
                }
            });
        });

        // Payment Form JS
        const form = document.querySelector("form");
        const inputs = document.querySelectorAll("input");
        const submitButton = document.querySelector("button[type='submit']");
        const cardNumberInput = document.querySelector("#card-number");
        const expirationDateInput = document.querySelector("#expiration");
        const CVVInput = document.querySelector("#cvv");
        const cardImgElement = document.querySelector("#card-icon");

        const visaIcon = "./icons/visa.svg";
        const mastercardIcon = "./icons/mastercard.svg";
        const defaultCardIcon = "./icons/credit-card.png";

        const cleaveCC = new Cleave(cardNumberInput, {
            creditCard: true,
            delimiter: " ",
            onCreditCardTypeChanged: function(type) {
                switch (type) {
                    case "visa":
                        cardImgElement.src = visaIcon;
                        break;
                    case "mastercard":
                        cardImgElement.src = mastercardIcon;
                        break;
                    default:
                        cardImgElement.src = defaultCardIcon;
                        break;
                }
            },
        });

        const cleaveExpiration = new Cleave(expirationDateInput, {
            date: true,
            datePattern: ["m", "y"],
        });

        const cleaveCVV = new Cleave(CVVInput, {
            numeralPositiveOnly: true,
            blocks: [3],
        });

        inputs.forEach((input) => {
            input.addEventListener("focus", () => handleInputFocus(input));
            input.addEventListener("input", () => handleInputFocus(input));
            input.addEventListener("focusout", () => handleInputFocusout(input));
        });

        function handleInputFocus(input) {
            input.classList.remove("empty");
            if (input.parentElement.classList.contains("icon-group")) {
                input.parentElement.classList.remove("empty");
                input.parentElement.classList.add("filling");
            }
            input.classList.add("filling");
            checkInputsFilled();
        }

        function handleInputFocusout(input) {
            if (!input.value) {
                input.classList.remove("filling");
                if (input.parentElement.classList.contains("icon-group")) {
                    input.parentElement.classList.remove("filling");
                    input.parentElement.classList.add("empty");
                }
                input.classList.add("empty");
            }
            checkInputsFilled();
        }

        function checkInputsFilled() {
            const allFilled = Array.from(inputs).every(input => input.value.trim() !== "");
            if (allFilled) {
                submitButton.classList.remove("disabled");
                submitButton.removeAttribute("disabled");
            } else {
                submitButton.classList.add("disabled");
                submitButton.setAttribute("disabled", "disabled");
            }
        }

        submitButton.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent default form submission
            // Add your payment submission logic here
            alert("Payment submitted successfully!");
            // Optionally, you can redirect or clear the form here
        });