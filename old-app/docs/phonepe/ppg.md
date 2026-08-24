# PhonePe PHP SDK Integration – Agent Instructions

## Objective
Integrate **PhonePe Payment Gateway** using the **official PhonePe PHP SDK**.  
This integration must support:

- Payment initiation
- Order status checking
- Refund initiation
- Refund status tracking
- Webhook verification
- Robust exception handling

All implementation must follow **PhonePe SDK best practices** and use **Composer-based installation only**.

---

## 1. SDK Installation (Mandatory)

### Steps
1. Update the project’s `composer.json` file with the **PhonePe SDK repository details** as provided in the official documentation.
2. Install dependencies using Composer:
   ```bash
   composer install


2. SDK Client Initialization
Initialization Rules

Initialize StandardCheckoutClient only once per application runtime.

Required parameters:

clientId

clientSecret

clientVersion

environment = PRODUCTION

Re-initializing the client will throw a PhonePeException.

Best Practices

Initialize the client inside a service class or singleton

Do NOT initialize inside controllers or repeatedly inside functions

3. Initiate Payment
Payment Flow

Build a payment request using:

StandardCheckoutPayRequestBuilder


Mandatory fields:

merchantOrderId (must be unique)

amount (in paise)

Optional but recommended:

redirectUrl

Initiate payment using:

$client->pay($request)

Payment Response Contains

redirectUrl → Redirect customer to PhonePe hosted payment page

Merchant order details

Payment status

Session expiry time

4. Check Order Status
API Usage

Use the following method:

getOrderStatus(merchantOrderId, detailsFlag)

Parameters

merchantOrderId → Order reference ID

detailsFlag

true → Fetch all payment attempts

false → Fetch only the latest attempt

Order Status Response Includes

Current order state

Amount

Transaction details

Error codes (if the payment failed)

5. Refund & Refund Status
Initiate Refund

Build a refund request with:

merchantRefundId (unique)

originalMerchantOrderId

amount

Call:

refund($refundRequest)

Refund Response Includes

Refund ID

Refund amount

Refund state

Check Refund Status

Use:

getRefundStatus(merchantRefundId)

Refund Status Response Includes

Refund state

Original order details

Amount refunded

List of refund attempts

Payment method used

6. Webhook Handling (Critical)
Callback Processing Rules

PhonePe sends payment and refund updates to your configured callbackUrl.

Every callback must be verified.

Always respond with HTTP 200 after successful processing.

Callback Verification

Use the SDK method:

verifyCallbackResponse(
  username,
  password,
  authorizationHeader,
  responseBody
)

On Successful Verification

Returns CallbackResponse containing:

Event type

Transaction status

Payment attempts

On Verification Failure

Throws PhonePeException

Log the error

Ignore the callback

Do NOT process invalid payloads

7. Exception Handling
Error Handling Strategy

Wrap all SDK interactions inside try-catch blocks

Catch:

PhonePeException

Exception Details Available

HTTP status code

Error message

Additional error metadata (key-value format)

Best Practices

Log errors using file logs or monitoring tools

Do not expose raw error details to end users

8. Security & Best Practices

Store credentials only in environment variables

Never hardcode secrets in code

Use unique merchantOrderId and merchantRefundId

Validate and verify all webhook callbacks

Handle idempotency where applicable

Ensure server time and SSL configuration are correct

9. Deliverables Expected from Coding Agent

The coding agent must deliver:

Composer-based PhonePe SDK installation

Centralized PhonePe client service class

Payment initiation module

Order status handler

Refund initiation and status handlers

Webhook controller with signature verification

Centralized exception handling and logging

## PHP SDK Installation

### Mandatory Setup

Before proceeding with the installation, you **must** add the following repository configuration to the **root `composer.json` file** of the project.  
This step is mandatory to allow Composer to fetch the **PhonePe PHP SDK** package.

### Composer Repository Configuration

Add the following configuration inside `composer.json`:

```json
{
  "repositories": [
    {
      "type": "package",
      "url": "./vendor/phonepe/pg-sdk-php/",
      "package": {
        "name": "phonepe/pg-php-sdk-v2",
        "version": "2.0.0",
        "dist": {
          "url": "https://phonepe.mycloudrepo.io/public/repositories/phonepe-pg-php-sdk/v2-sdk.zip",
          "type": "zip"
        },
        "autoload": {
          "classmap": ["/"]
        }
      }
    }
  ],
  "require": {
    "phonepe/pg-php-sdk-v2": "^2.0",
    "vlucas/phpdotenv": "^5.6",
    "netresearch/jsonmapper": "^4.4"
  }
}

Option 1: Using Composer (Recommended)

After updating composer.json, install the SDK and dependencies using Composer:

composer install


✅ This method automatically resolves and installs all required dependencies.
✅ This is the recommended and supported approach.

## PHP SDK Initialization

The `StandardCheckoutClient` class is used to communicate with the PhonePe APIs.  
This client **must be initialized only once per runtime**, as it follows the **singleton pattern**.

Ensure that the correct and final credentials are used during initialization.

---

### Initialization Parameters

| Parameter Name   | Data Type | Mandatory | Description |
|------------------|-----------|-----------|-------------|
| `client_id`      | String    | Yes       | Unique Client ID provided by PhonePe for secure communication |
| `client_version` | Integer   | Yes       | Client version provided by PhonePe |
| `client_secret`  | String    | Yes       | Secret key provided by PhonePe (must be stored securely) |
| `env`            | String    | Yes       | Environment for SDK execution (`PRODUCTION` only) |

---

### Sample Initialization Code

```php
<?php

require_once 'vendor/autoload.php'; // Composer autoloader

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";             // Replace with actual Client ID
$clientVersion = YOUR_CLIENT_VERSION;     // Replace with actual Client Version
$clientSecret = "YOUR_CLIENT_SECRET";     // Replace with actual Client Secret
$env = Env::PRODUCTION;                   // Only Production is supported

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

// $client can now be used to interact with PhonePe APIs

?>
Singleton Pattern Notice

The StandardCheckoutClient follows the singleton pattern.

Calling getInstance() multiple times will always return the same instance

Re-initialization with different credentials in the same runtime is not allowed

Incorrect or repeated initialization may result in a PhonePeException

Configuration Instructions

Replace:

YOUR_CLIENT_ID

YOUR_CLIENT_VERSION

YOUR_CLIENT_SECRET

Use credentials obtained from the PhonePe Business Dashboard (Production)

PHP SDK Environment & Event Publishing

The PHP SDK currently supports only the Production environment

The fifth parameter $shouldPublishEvents is optional

Set to true to enable event publishing

Helps PhonePe with analytics and monitoring

Default value is false

## Initiate Payment with PHP SDK

The `pay()` method is used to initiate a payment via the **PhonePe Payment Gateway**.  
A payment request must be created using the  
`StandardCheckoutPayRequestBuilder::builder()` method by providing the required attributes.

---

### Payment Request Parameters

Use the **Standard Checkout Pay Request Builder** to create a payment request.

| Parameter Name        | Data Type | Mandatory | Description | Constraints |
|----------------------|-----------|-----------|-------------|-------------|
| `merchantOrderId`    | String    | Yes       | Unique order ID assigned by the merchant | Max length: 63 characters, only `_` and `-` allowed |
| `amount`             | Long      | Yes       | Order amount in paisa | Minimum value: 100 (₹1.00) |
| `metaInfo`           | Object    | No        | Custom metadata stored by merchant and returned in status & callback | — |
| `metaInfo.udf1-5`    | String    | No        | Optional additional information | Max length: 256 characters |
| `redirectUrl`        | String    | No        | URL to redirect the user after payment (success/failure) | Valid URL |
| `message`            | String    | No        | Optional message related to the order | — |

---

### Sample Payment Request

```php
<?php

use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;

$merchantOrderId = "ORDER_101"; // Unique order ID
$amount = 1000; // Amount in paisa (₹10.00)
$redirectUrl = "https://your-website.com/redirect"; // Redirect URL after payment
$message = "Your order details";

$payRequest = StandardCheckoutPayRequestBuilder::builder()
    ->merchantOrderId($merchantOrderId)
    ->amount($amount)
    ->redirectUrl($redirectUrl)
    ->message($message) // Optional
    ->build();

?>

Call the pay() Method
<?php

try {
    $payResponse = $client->pay($payRequest);

    if ($payResponse->getState() === "PENDING") {
        // Redirect user to PhonePe hosted payment page
        header("Location: " . $payResponse->getRedirectUrl());
        exit();
    } else {
        // Handle payment initiation failure
        echo "Payment initiation failed. State: " . $payResponse->getState();
    }

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    // Exception handling
    echo "Error initiating payment: " . $e->getMessage();
}

?>

Payment Response

The pay() method returns a StandardCheckoutPayResponse object with the following properties:

Property Name	Data Type	Description
state	String	Current order status (e.g., PENDING)
redirectUrl	String	PhonePe Standard Checkout payment URL
orderId	String	Internal order ID generated by PhonePe
expireAt	Long	Order expiry timestamp (epoch time)

Important Notes

Always redirect the user using redirectUrl when the state is PENDING

Do not assume payment success at this stage

Final payment confirmation must be done using:

Order Status API

Webhook callbacks

## Check Order Status with PHP SDK

The **Order Status API** is used to retrieve the real-time status of an order using the  
`getOrderStatus()` function.

This API helps you:
- Verify payment success or failure
- Fetch payment attempt details
- Handle post-payment business logic reliably

---

### Order Status Request Parameters

| Parameter Name      | Data Type | Mandatory | Description |
|---------------------|-----------|-----------|-------------|
| `merchantOrderId`   | String    | Yes       | Merchant order ID for which status is fetched |
| `details`           | Boolean   | No        | `true` → Returns all payment attempts<br>`false` → Returns only latest attempt |

---

### Sample Order Status Request

```php
<?php

require_once "vendor/autoload.php"; // Composer autoloader

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";              // Replace with Client ID
$clientVersion = CLIENT_VERSION;           // Replace with Client Version
$clientSecret = "YOUR_CLIENT_SECRET";      // Replace with Client Secret
$env = Env::PRODUCTION;

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

$merchantOrderId = "YOUR_MERCHANT_ORDER_ID"; // Order ID to check

try {
    $statusCheckResponse = $client->getOrderStatus($merchantOrderId, true);

    echo "Order ID: " . $statusCheckResponse->getOrderId() . PHP_EOL;
    echo "Transaction ID: " . $statusCheckResponse->getTransactionId() . PHP_EOL;
    echo "State: " . $statusCheckResponse->getState() . PHP_EOL;
    echo "Amount: " . $statusCheckResponse->getAmount() . PHP_EOL;

    // Additional details can be accessed using:
    // $statusCheckResponse->getPaymentDetails()
    // $statusCheckResponse->getMetaInfo()
    // $statusCheckResponse->getErrorCode()

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    echo "Error checking order status: " . $e->getMessage();
}

?>

Order Status Response

The getOrderStatus() function returns a StatusCheckResponse object with the following properties:

Property Name	Data Type	Description
orderId	String	Order ID generated by PhonePe PG
state	String	Order state (PENDING, FAILED, COMPLETED)
expireAt	Number	Expiry time in epoch
amount	Long	Order amount in paisa
metaInfo	Object	Merchant-defined metadata
errorCode	String	Error code (only if state is FAILED)
detailedErrorCode	String	Detailed error code (only if state is FAILED)
paymentDetails	List	List of payment attempts
Payment Details Object

The paymentDetails property contains a list of payment attempts made for the order.

Property Name	Data Type	Description
transactionId	String	Transaction ID generated by PhonePe
paymentMode	String	Payment method used:
UPI_INTENT, UPI_COLLECT, UPI_QR, CARD, TOKEN, NET_BANKING
timestamp	Long	Epoch timestamp of transaction attempt
state	String	Attempt state (PENDING, COMPLETED, FAILED)
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code (if failed)
splitInstruments	List	Split instrument details
Split Instruments Details

Each entry in splitInstruments is an InstrumentCombo object.

Property Name	Data Type	Description
instrument	Object	Payment instrument used
rails	Object	Payment rail used
amount	Integer	Amount processed using this instrument
Important Notes

Do NOT rely only on redirect response for payment success

Always confirm final payment status using:

Order Status API

Webhook callbacks

COMPLETED state confirms successful payment

Order Status Response

The getOrderStatus() function returns a StatusCheckResponse object with the following properties:

Property Name	Data Type	Description
orderId	String	Order ID generated by PhonePe PG
state	String	Order state (PENDING, FAILED, COMPLETED)
expireAt	Number	Expiry time in epoch
amount	Long	Order amount in paisa
metaInfo	Object	Merchant-defined metadata
errorCode	String	Error code (only if state is FAILED)
detailedErrorCode	String	Detailed error code (only if state is FAILED)
paymentDetails	List	List of payment attempts
Payment Details Object

The paymentDetails property contains a list of payment attempts made for the order.

Property Name	Data Type	Description
transactionId	String	Transaction ID generated by PhonePe
paymentMode	String	Payment method used:
UPI_INTENT, UPI_COLLECT, UPI_QR, CARD, TOKEN, NET_BANKING
timestamp	Long	Epoch timestamp of transaction attempt
state	String	Attempt state (PENDING, COMPLETED, FAILED)
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code (if failed)
splitInstruments	List	Split instrument details
Split Instruments Details

Each entry in splitInstruments is an InstrumentCombo object.

Property Name	Data Type	Description
instrument	Object	Payment instrument used
rails	Object	Payment rail used
amount	Integer	Amount processed using this instrument
Important Notes

Do NOT rely only on redirect response for payment success

Always confirm final payment status using:

Order Status API

Webhook callbacks

COMPLETED state confirms successful payment

Call the refund() Method
<?php

require_once "vendor/autoload.php";

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";
$clientVersion = CLIENT_VERSION;
$clientSecret = "YOUR_CLIENT_SECRET";
$env = Env::PRODUCTION;

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

try {
    $refundResponse = $client->refund($refundRequest);

    echo "Refund ID: " . $refundResponse->getRefundId() . PHP_EOL;
    echo "Refund Amount: " . $refundResponse->getAmount() . PHP_EOL;
    echo "Refund State: " . $refundResponse->getState() . PHP_EOL;

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    echo "Error initiating refund: " . $e->getMessage();
}

?>

⚠️ Invalid Refund Amount Rule

Refund amount must not exceed the original order amount

Refund amount must be greater than or equal to 100 paisa

SDK will reject invalid refund requests

Refund Initiation Response

The refund() function returns a StandardCheckoutRefundResponse object.

Property Name	Data Type	Description
refundId	String	Refund ID generated by PhonePe
state	String	Current refund state
amount	Long	Refunded amount in paisa
Check Refund Status

Refund status can be retrieved using the
getRefundStatus() function.

Refund Status Request Parameters
Parameter Name	Data Type	Mandatory	Description
refundId	String	Yes	Refund ID created during initiation
Sample Refund Status Request
<?php

require_once "vendor/autoload.php";

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";
$clientVersion = CLIENT_VERSION;
$clientSecret = "YOUR_CLIENT_SECRET";
$env = Env::PRODUCTION;

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

$merchantRefundId = "YOUR_MERCHANT_REFUND_ID";

try {
    $refundStatusCheckResponse = $client->getRefundStatus($merchantRefundId);

    echo "Refund ID: " . $refundStatusCheckResponse->getRefundId() . PHP_EOL;
    echo "State: " . $refundStatusCheckResponse->getState() . PHP_EOL;
    echo "Amount: " . $refundStatusCheckResponse->getAmount() . PHP_EOL;

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    echo "Error checking refund status: " . $e->getMessage();
}

?>

Refund Status Response

The getRefundStatus() function returns a RefundStatusCheckResponse object.

Property Name	Data Type	Description
originalMerchantOrderId	String	Original order ID
merchantRefundId	String	Refund ID
state	String	Refund state
amount	Long	Refund amount
paymentDetails	List	Refund payment attempt details
Payment Refund Details

Each entry in paymentDetails represents a refund attempt.

Property Name	Data Type	Description
transactionId	Integer	PhonePe transaction ID
paymentMode	String	Payment method used:
UPI_INTENT, UPI_COLLECT, UPI_QR, CARD, TOKEN, NET_BANKING
timestamp	Long	Epoch timestamp
amount	Integer	Amount refunded in paisa
state	String	Refund state (PENDING, COMPLETED, FAILED)
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code
splitInstruments	List	Split instrument details
Important Notes

Refunds are always processed back to the original payment method

Refund status may remain PENDING until settlement

Always verify refund completion using getRefundStatus()

Call the refund() Method
<?php

require_once "vendor/autoload.php";

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";
$clientVersion = CLIENT_VERSION;
$clientSecret = "YOUR_CLIENT_SECRET";
$env = Env::PRODUCTION;

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

try {
    $refundResponse = $client->refund($refundRequest);

    echo "Refund ID: " . $refundResponse->getRefundId() . PHP_EOL;
    echo "Refund Amount: " . $refundResponse->getAmount() . PHP_EOL;
    echo "Refund State: " . $refundResponse->getState() . PHP_EOL;

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    echo "Error initiating refund: " . $e->getMessage();
}

?>

⚠️ Invalid Refund Amount Rule

Refund amount must not exceed the original order amount

Refund amount must be greater than or equal to 100 paisa

SDK will reject invalid refund requests

Refund Initiation Response

The refund() function returns a StandardCheckoutRefundResponse object.

Property Name	Data Type	Description
refundId	String	Refund ID generated by PhonePe
state	String	Current refund state
amount	Long	Refunded amount in paisa
Check Refund Status

Refund status can be retrieved using the
getRefundStatus() function.

Refund Status Request Parameters
Parameter Name	Data Type	Mandatory	Description
refundId	String	Yes	Refund ID created during initiation
Sample Refund Status Request
<?php

require_once "vendor/autoload.php";

use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\Env;

$clientId = "YOUR_CLIENT_ID";
$clientVersion = CLIENT_VERSION;
$clientSecret = "YOUR_CLIENT_SECRET";
$env = Env::PRODUCTION;

$client = StandardCheckoutClient::getInstance(
    $clientId,
    $clientVersion,
    $clientSecret,
    $env
);

$merchantRefundId = "YOUR_MERCHANT_REFUND_ID";

try {
    $refundStatusCheckResponse = $client->getRefundStatus($merchantRefundId);

    echo "Refund ID: " . $refundStatusCheckResponse->getRefundId() . PHP_EOL;
    echo "State: " . $refundStatusCheckResponse->getState() . PHP_EOL;
    echo "Amount: " . $refundStatusCheckResponse->getAmount() . PHP_EOL;

} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    echo "Error checking refund status: " . $e->getMessage();
}

?>

Refund Status Response

The getRefundStatus() function returns a RefundStatusCheckResponse object.

Property Name	Data Type	Description
originalMerchantOrderId	String	Original order ID
merchantRefundId	String	Refund ID
state	String	Refund state
amount	Long	Refund amount
paymentDetails	List	Refund payment attempt details
Payment Refund Details

Each entry in paymentDetails represents a refund attempt.

Property Name	Data Type	Description
transactionId	Integer	PhonePe transaction ID
paymentMode	String	Payment method used:
UPI_INTENT, UPI_COLLECT, UPI_QR, CARD, TOKEN, NET_BANKING
timestamp	Long	Epoch timestamp
amount	Integer	Amount refunded in paisa
state	String	Refund state (PENDING, COMPLETED, FAILED)
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code
splitInstruments	List	Split instrument details
Important Notes

Refunds are always processed back to the original payment method

Refund status may remain PENDING until settlement

Always verify refund completion using getRefundStatus()

Authenticating Callbacks from PhonePe

PhonePe uses Basic Authentication for webhook callbacks.

Steps to Verify Callback

Extract Headers

Use getallheaders() to retrieve request headers

Read Request Body

Use file_get_contents('php://input')

Verify and Parse

Call verifyCallbackResponse()

Respond with Proper HTTP Status

200 OK → Callback processed successfully (no retry)

400 Bad Request → Verification failed

500 Internal Server Error → Server error (PhonePe will retry)

Log Errors

Always log failures for audit and debugging

⚠️ PhonePe retries callbacks if HTTP 200 is not returned.

Callback Response

The method returns a CallbackResponse object containing:

Parameter	Data Type	Description
type	CallbackType	Type of event
payload	CallbackData	Event-specific details
Callback Event Types
Callback Type	Description
CHECKOUT_ORDER_COMPLETED	Payment successfully completed
CHECKOUT_ORDER_FAILED	Payment failed
PG_REFUND_COMPLETED	Refund completed
PG_REFUND_FAILED	Refund failed
PG_REFUND_ACCEPTED	Refund acknowledged but pending
Callback Payload Details
Parameter Name	Data Type	Description
merchantId	String	Merchant ID
orderId	String	PhonePe-generated order ID
originalMerchantOrderId	String	Merchant-generated order ID
refundId	String	PhonePe-generated refund ID
merchantRefundId	String	Merchant refund ID
state	String	Order or refund state
amount	Long	Amount in paisa
expireAt	Long	Expiry timestamp (epoch)
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code
metaInfo	MetaInfo	Metadata passed during payment
paymentDetails	List<PaymentDetail>	Payment attempt details
PaymentDetail Object
Attribute	Data Type	Description
transactionId	String	PhonePe transaction ID
paymentMode	String	Payment mode (UPI, CARD, NET_BANKING, etc.)
timestamp	Long	Epoch timestamp
state	String	COMPLETED, FAILED, PENDING
errorCode	String	Error code (if failed)
detailedErrorCode	String	Detailed error code
Exception Handling

The PhonePe SDK throws PhonePeException for API and validation errors.

PhonePeException Attributes
Attribute	Type	Description
code	String	Error code
message	String	Error message
httpStatusCode	Integer	HTTP status code
data	Map<String, String>	Error metadata
Sample Exception Handling
<?php

use PhonePe\common\exceptions\PhonePeException;

try {
    // PhonePe API call
} catch (PhonePeException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "HTTP Status Code: " . $e->getHttpStatusCode() . PHP_EOL;
}

?>

Best Practices for Webhooks

Always verify callback authenticity

Never trust redirect-based payment status alone

Update order/refund status only after webhook or status API confirmation

Log all callback events

Ensure webhook URL is HTTPS and publicly accessible

## Merchant Integration Checklist

This section outlines the **mandatory integration requirements** and the **UAT sign-off process** that must be completed before moving to Production.

All checklist items below are **mandatory** and must be implemented for successful UAT closure.

---

## UAT Sign-Off Process

Once your integration is completed:

1. Share your **UAT instance** with PhonePe for **end-to-end flow verification**
2. All payment, status, refund, and webhook flows will be tested
3. Any issues identified during UAT must be:
   - Fixed
   - Reverified
   - Tested again in the **same UAT environment**
4. UAT sign-off is provided **only after all required use cases pass**
5. After sign-off:
   - Merchant must acknowledge the sign-off
   - PhonePe will issue **Production Credentials**

---

## API-Wise Mandatory Checklist

ℹ️ **Must-Do**  
All use cases listed below **must be implemented**.

---

## 1. Authorization API

### Token Lifecycle Management (Mandatory)

You must manage token expiry using the `expires_at` parameter.

### Option 1: Validate Token Before Each Request
- If token is active → reuse
- If token is expired → regenerate using Authorization API

### Option 2: Scheduled Token Renewal (Recommended)
- Set up a scheduler / cron job
- Regenerate token **3–5 minutes before expiry**

⚠️ **Avoid Unnecessary Token Calls**  
Do NOT call Authorization API for every request without checking token expiry.

---

## 2. Payment API

### Request Parameters (Mandatory)

- `merchantOrderId`
  - Must be **unique for every transaction**
- `expireAfter`
  - Defines order expiry in seconds
  - Standard Checkout:
    - Min: 300 seconds
    - Max: 3600 seconds
- `amount`
  - Must be in **Paise (₹ × 100)**
- `metaInfo.udf`
  - Use only if required
  - Remove unused fields (do not leave empty)
- `paymentFlow.redirectUrl`
  - URL where user is redirected after payment
- `paymentFlow.type`
  - Must be set to `PG_CHECKOUT`

### Response Fields Handling

- `orderId`
  - PhonePe-generated order ID
  - Must be mapped to your `merchantOrderId`
- `redirectUrl`
  - Must be used **exactly as received**
  - Do not modify or encode

---

## 3. Order Status API

### Status Evaluation Rules

- Use **root-level `state` only**
- Do NOT rely on strict deserialization

| State | Meaning |
|-----|--------|
| `COMPLETED` | Payment successful |
| `FAILED` | Payment failed |
| `PENDING` | Payment in progress |

---

### Handling `PENDING` Transactions

When the payment state is `PENDING`, choose **one** strategy:

#### Option 1: Mark as Failed
- Show **Payment Failed** page to user
- Continue backend reconciliation
- If final state becomes `COMPLETED`, initiate **Refund**
- Refund API integration is strongly recommended

#### Option 2: Mark as Pending (Recommended)
- Show **Payment Pending** page
- Reconcile transaction until final state is reached
- Fulfill order only after `COMPLETED`

---

### Reconciliation Schedule (Mandatory)

When state = `PENDING`, call Order Status API as follows:

1. First check: **20–25 seconds** after payment initiation
2. Then:
   - Every **3 seconds** for next **30 seconds**
   - Every **6 seconds** for next **60 seconds**
   - Every **10 seconds** for next **60 seconds**
   - Every **30 seconds** for next **60 seconds**
   - Every **1 minute** until:
     - Terminal state (`COMPLETED` / `FAILED`)
     - Or `expireAfter` duration is reached

---

## 4. Webhook Handling

### Mandatory Rules

- Avoid strict deserialization of webhook payload
- Use **`payload.state`** to determine status
- Ignore `type` parameter (deprecated)
- Use `event` field instead
- `expireAt` and `timestamp` are **epoch milliseconds**

---

### Webhook Validation (Mandatory)

Upon receiving a webhook:

1. Compute:
SHA256(username:password)

2. Compare with `Authorization` header

- If matched → process webhook
- If mismatched → discard webhook and fetch latest status via Order Status API

---

### Webhook Setup

- **UAT**
- Share webhook URL with PhonePe integration team
- **Production**
- Configure webhook URL via PhonePe Business Dashboard

---

## 5. Refund API

Applies only if refunds are initiated via API (not dashboard).

### Required Parameters

- `merchantRefundId`
- Must be unique for every refund
- `originalMerchantOrderId`
- Original order ID
- `amount`
- In Paise (₹ × 100)

### Refund Flow

- Refund starts in `PENDING` state
- Must track refund via:
- Webhook
- Refund Status API (Mandatory)

---

## 6. Refund Status API

### Refund State Handling

Use **root-level `state` only**.

| State | Meaning |
|-----|--------|
| `PENDING` | Refund processing |
| `CONFIRMED` | Acknowledged, not final |
| `COMPLETED` | Refund successful |
| `FAILED` | Refund failed |

---

### Handling Ongoing Refunds

- If state = `PENDING` or `CONFIRMED`:
- Call Refund Status API periodically
- Use scheduler / cron job
- Do NOT initiate another refund for same order
- If state = `COMPLETED`:
- Refund successful
- If state = `FAILED`:
- Reinitiate refund using **new `merchantRefundId`**

---

## What’s Next?

You have completed:
- Mandatory API handling
- Webhook validation
- Reconciliation strategies
- Refund management
- UAT readiness checklist

➡️ Next section covers **Payment Flow Simulation**, where you can test:
- Payment success
- Payment failure
- Pending transactions  
without performing real transactions, using predefined response templates.

---

## UAT Sandbox

The **UAT Sandbox** allows you to simulate **end-to-end payment flows** and thoroughly test your PhonePe integration.  
It uses predefined **test case templates** that map APIs to sample responses, enabling you to simulate:

- Payment **Success**
- Payment **Failure**
- Payment **Pending**

⚠️ No real transactions are performed in the UAT Sandbox.

---

## Benefits of Using the UAT Sandbox

- Continue testing even if the PhonePe UAT server is unavailable
- Simulate real-world payment outcomes reliably
- Validate complete payment lifecycle from initiation to final status
- Ensure stability before moving to Production

---

## Verifying Payment Lifecycle Using the UAT Sandbox

The UAT Sandbox supports simulation across multiple payment methods, including:
- UPI Intent
- UPI Collect
- UPI QR
- Cards
- NetBanking

This allows comprehensive testing of **success, failure, and pending** scenarios.

---

## 1. Simulate Payment Flows on Standard Checkout

To begin testing, route all payment and order status requests to the **UAT Sandbox host**.

### Update Host URL (Mandatory)

Replace the default API host with:

https://api-preprod.phonepe.com/apis/pgsandbox


This applies to:
- PG Pay API
- PG Order Status API

---

## 1.1 Install and Set Up the PhonePe Test App

The **PhonePe Test App** is required to simulate certain payment flows.

### Android
- Download the app  
- Package Name: `com.phonepe.simulator`

### iOS
- Share your **email ID** with the PhonePe Integration Team
- You will receive an invite via **Firebase**

---

### ⚠️ Allow Developer Access (iOS)

If you see **“Untrusted Enterprise Developer”**, follow these steps:

1. Go to **Settings → General → VPN & Device Management**
2. Tap on **Developer App**
3. Select **Trust [Developer]**
4. Confirm by selecting **Trust** again

---

## 2. Simulate UPI QR Payments

- Do **NOT** use the PhonePe Test App to scan QR codes
- Do **NOT** manually configure templates
- Scan the UAT QR using:
  - Production PhonePe App
  - Any other UPI app

### Flow
1. Scan the UAT QR
2. A link appears on the screen
3. Open the link in your browser
4. Select:
   - **Success**
   - **Failure**
   - **Pending**

The appropriate template is applied automatically.

---

## 3. Simulate UPI Collect Requests (VPA)

### Important Rules
- Do **NOT** configure VPA in the PhonePe Simulator App
- Do **NOT** manually configure templates

### Valid Test VPAs

| Scenario | VPA |
|--------|-----|
| Success | `success@ybl` |
| Failure | `failed@ybl` |
| Pending | `pending@ybl` |

### Behavior
- Success / Failure → Redirect within **5 seconds**
- Pending → Redirect within **60 seconds**

### Invalid VPA
- Use any random VPA not listed above to simulate invalid cases

---

## 4. Simulate Card and NetBanking Transactions

- No manual template configuration required
- You will be redirected to a page where you can choose:
  - **Success**
  - **Failure**
  - **Pending**

The selected outcome is applied automatically.

---

## Setting Up Your Test Environment

Use **Test Case Templates** for PayPage UPI Intent flows.

---

## Configure Template for PayPage UPI Intent Flow

### Steps

1. Open the **PhonePe Test App**
2. Tap on **Test Case Templates**
3. Enter your **Merchant ID**
4. Click **Get Configured Templates**
5. If a template exists:
   - It will be displayed
6. If not:
   - You will see **No Template Configured**

---

### Template Configuration Rules

- Select flow:  
  **Custom and Standard Checkout V2**
- Templates related to Standard Checkout start with:  
  **`Paypage`**

---

## Templates for PayPage UPI Intent

Use the following templates to simulate PG – Mobile Intent flows:

| Scenario | Template Name |
|--------|---------------|
| Success | Paypage Upi Intent Success |
| Failure | Paypage Upi Intent Failure |
| Pending | Paypage Upi Intent Pending |

---

## Test Card Details

Use the following test cards to simulate card payments in UAT.

### Credit Card

- Card Number: `4208 5851 9011 6667`
- Card Type: `CREDIT_CARD`
- Issuer: `VISA`
- Expiry: `06/2027`
- CVV: `508`

### Debit Card

- Card Number: `4242 4242 4242 4242`
- Card Type: `DEBIT_CARD`
- Issuer: `VISA`
- Expiry: `12/2023`
- CVV: `936`

---

### ℹ️ Simulation OTP

Use the following OTP on the bank page to complete simulation:



123456


---

## What’s Next?

You have now learned how to:
- Simulate payment success, failure, and pending flows
- Test complete end-to-end payment lifecycle
- Validate integration reliability without real transactions

➡️ Next, proceed to the **Go-Live Process**, where you will:
- Complete final checks
- Receive Production credentials
- Move your integration live

---
## Go Live Process

After completing your integration and UAT testing, share your **testing URL / App** with the **PhonePe Integration Team** for a final sanity check.

Once the integration is verified in the **UAT environment**:
- **UAT Sign-off** will be provided
- **Production credentials** will be shared by PhonePe

Follow the steps below to migrate from **UAT to Production** and go live.

---

## 1. Replace Host URLs (Mandatory)

Update all API host URLs from **UAT Sandbox** to **Production**.

| API Type | Production Host URL |
|--------|---------------------|
| Auth Token API | `https://api.phonepe.com/apis/identity-manager` |
| Payment, Order Status, Refund APIs | `https://api.phonepe.com/apis/pg` |

⚠️ Ensure no UAT or sandbox URLs remain in Production configuration.

---

## 2. Replace Client ID and Client Secret

- Remove **UAT Client ID** and **UAT Client Secret**
- Replace them with **Production Client ID** and **Production Client Secret**
- These credentials are used to generate **Production Auth Tokens**

---

## 3. Generate Production Auth Token

Call the **Auth Token API** using your **Production credentials**.

The generated Production token must be used for:
- Payment initiation
- Order status checks
- Refund initiation
- Refund status checks

⚠️ Never reuse UAT tokens in Production.

---

## SDK Configuration for Production

Ensure SDKs are correctly configured for Production before release.

---

## Android SDK (Production Configuration)

### Required Settings
- Environment: `RELEASE`
- `enableLogging`: `false`
- `appId`: Optional (`null` or empty string allowed)

### Code Reference

```kotlin
val result = PhonePeKt.init(
  context = this,
  merchantId = "MID",
  flowId = "FLOW_ID",
  phonePeEnvironment = PhonePeEnvironment.RELEASE,
  enableLogging = false,
  appId = null
)
iOS SDK (Production Configuration)
Required Settings

Environment: .production

enableLogging: false

Code Reference
let ppPayment = PPPayment(
    environment: .production,
    merchantId: "MERCHANT_ID",
    enableLogging: false
)

Hybrid SDK (Production Configuration)

For Hybrid SDK integrations:

Set environment to PRODUCTION

Use Production Merchant ID (MID)

Ensure:

enableLogging = false

Final Go-Live Checklist

Before enabling Production traffic:

✅ UAT Sign-off received

✅ Production credentials configured

✅ Production Auth Token generation verified

✅ Production host URLs updated

✅ Logging disabled in Production

✅ Webhooks configured in Production Dashboard

✅ No sandbox / test artifacts remain