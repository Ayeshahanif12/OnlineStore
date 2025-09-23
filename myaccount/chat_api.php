<?php
header("Content-Type: application/json");

// Sample FAQ database
$faq = [
    // Greetings
    "hello" => "Hi there! 👋 How can I help you?",
    "hi" => "Hello! How are you doing today?",
    "hey" => "Hey! Welcome to our store 😊",

    // Orders
    "how to order" => "You can place an order by adding items to your cart and proceeding to checkout.",
    "order status" => "You can check your order status in the 'My Orders' section of your account.",
    "cancel order" => "Orders can only be canceled within 2 hours of placing them.",
    "change order" => "If you want to change your order, please contact support before shipping.",
   

    // Returns & Refunds
    "return policy" => "You can return within 7 days if unused and in original packaging.",
    "refund" => "Refunds are issued within 5-7 business days after product return.",
    "damage product" => "If you received a damaged product, share photos with support for a replacement/refund.",

    // Shipping
    "shipping" => "We provide fast shipping all over Pakistan 🚚.",
    "delivery time" => "Delivery takes 3-5 working days in cities and up to 7 days in remote areas.",
    "shipping charges" => "We charge PKR 200. Free delivery on orders above PKR 3,000!",

    // Account
    "login" => "Go to the 'Login' page and enter your email and password.",
    "logout" => "Click the 'Logout' button in your account menu.",
    "sign in" => "Click 'Sign In' and enter your registered details.",
    "sign up" => "Click 'Sign Up' and create your account.",

    // Stock
    "restock" => "If a product is out of stock, click 'Notify Me' to get updates.",
    "available sizes" => "Sizes are mentioned on the product page.",
    "colors available" => "Colors are shown on the product page.",

    // Payments
    "payment methods" => "We accept Cash on Delivery, Bank Transfer, EasyPaisa & JazzCash.",
    "cod available" => "Yes, COD is available across Pakistan 💵.",

    // Support
    "contact" => "You can contact us via our Contact page or WhatsApp support 📞.",
    "support hours" => "Support is available 9 AM - 9 PM, Mon-Sat.",
    "track order" => "Track your order in 'My Orders' with your tracking ID.",

    // General
    "discount" => "Check homepage for ongoing discounts 🎉.",
    "sale" => "Our seasonal sale is live now 🔥.",
    "help" => "I can help with orders, returns, shipping, payments, or accounts."
];

// Get user message
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = strtolower(trim($input["message"] ?? ""));

$reply = "Sorry, I couldn’t understand that. Please try asking about orders, shipping, returns, or accounts.";

// Step 1: Exact match
if (isset($faq[$userMessage])) {
    $reply = $faq[$userMessage];
} else {
    // Step 2: Partial match (keywords)
    foreach ($faq as $key => $answer) {
        if (strpos($userMessage, $key) !== false) {
            $reply = $answer;
            break;
        }
    }
}

// Return JSON response
echo json_encode(["reply" => $reply]);
