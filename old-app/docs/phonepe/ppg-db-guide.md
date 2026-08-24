PhonePe Investigation-Ready Database Schema
Design Goals

✔ Full audit trail
✔ Never overwrite gateway data
✔ Support retries & reconciliation
✔ Store raw webhook payloads
✔ Legal / finance investigation safe

1️⃣ phonepe_orders

Master order table (1 row per merchant order)

CREATE TABLE phonepe_orders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    merchant_order_id VARCHAR(64) NOT NULL UNIQUE,
    phonepe_order_id VARCHAR(64),

    amount BIGINT NOT NULL, -- paisa
    currency VARCHAR(10) DEFAULT 'INR',

    state ENUM('PENDING','COMPLETED','FAILED') NOT NULL,
    last_known_state ENUM('PENDING','COMPLETED','FAILED') NOT NULL,

    expire_at BIGINT, -- epoch millis
    redirect_url TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_state (state),
    INDEX idx_phonepe_order_id (phonepe_order_id)
);


📌 Why this table

One row per transaction

Never delete

Always keep last known state

2️⃣ phonepe_payment_attempts

Each payment attempt (UPI / card / retry)

CREATE TABLE phonepe_payment_attempts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    merchant_order_id VARCHAR(64) NOT NULL,
    transaction_id VARCHAR(64), -- PhonePe transactionId

    payment_mode VARCHAR(32), -- UPI_INTENT, CARD, NET_BANKING
    state ENUM('PENDING','COMPLETED','FAILED'),

    amount BIGINT,
    timestamp BIGINT, -- epoch millis

    error_code VARCHAR(64),
    detailed_error_code VARCHAR(128),

    raw_response JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_txn (transaction_id),
    INDEX idx_order (merchant_order_id)
);


📌 Why

PhonePe can retry

Users can attempt multiple payments

Required for RCA (Root Cause Analysis)

3️⃣ phonepe_webhook_logs

Raw webhook payload storage (VERY IMPORTANT)

CREATE TABLE phonepe_webhook_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    event_type VARCHAR(64),
    merchant_order_id VARCHAR(64),
    phonepe_order_id VARCHAR(64),
    merchant_refund_id VARCHAR(64),

    authorization_header VARCHAR(255),
    verified BOOLEAN DEFAULT FALSE,

    payload JSON NOT NULL,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    processing_status ENUM('RECEIVED','PROCESSED','FAILED') DEFAULT 'RECEIVED',
    failure_reason TEXT
);


📌 Why

Legal disputes

Bank complaints

PhonePe support investigations

Reprocessing webhooks

⚠️ Never delete webhook logs

4️⃣ phonepe_order_status_checks

Reconciliation & polling history

CREATE TABLE phonepe_order_status_checks (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    merchant_order_id VARCHAR(64),
    phonepe_order_id VARCHAR(64),

    state ENUM('PENDING','COMPLETED','FAILED'),
    amount BIGINT,

    response JSON,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


📌 Why

UAT & Production reconciliation

Mandatory per PhonePe schedule

Audit trail of status polling

5️⃣ phonepe_refunds

Refund master table

CREATE TABLE phonepe_refunds (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    merchant_refund_id VARCHAR(64) NOT NULL UNIQUE,
    merchant_order_id VARCHAR(64) NOT NULL,

    phonepe_refund_id VARCHAR(64),

    amount BIGINT NOT NULL,
    state ENUM('PENDING','CONFIRMED','COMPLETED','FAILED'),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_order (merchant_order_id)
);


📌 Why

One refund can exist per order

Retry support

Financial settlement tracking

6️⃣ phonepe_refund_status_checks

Refund reconciliation history

CREATE TABLE phonepe_refund_status_checks (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    merchant_refund_id VARCHAR(64),
    state ENUM('PENDING','CONFIRMED','COMPLETED','FAILED'),

    response JSON,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


📌 Why

Refunds are async

Mandatory retry logic

Compliance tracking

7️⃣ phonepe_callback_events

Normalized webhook events (business-friendly)

CREATE TABLE phonepe_callback_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    event ENUM(
        'CHECKOUT_ORDER_COMPLETED',
        'CHECKOUT_ORDER_FAILED',
        'PG_REFUND_COMPLETED',
        'PG_REFUND_FAILED',
        'PG_REFUND_ACCEPTED'
    ),

    merchant_order_id VARCHAR(64),
    merchant_refund_id VARCHAR(64),

    state VARCHAR(32),
    amount BIGINT,

    occurred_at BIGINT, -- epoch millis
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


📌 Why

Easy reporting

Analytics

Ops dashboards

🔐 8️⃣ phonepe_security_audit

Security & verification tracking

CREATE TABLE phonepe_security_audit (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    source ENUM('WEBHOOK','API'),
    reference_id VARCHAR(64),

    authorization_received VARCHAR(255),
    authorization_expected VARCHAR(255),

    verified BOOLEAN,
    remarks TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


📌 Why

Detect spoofed callbacks

Compliance audits

SOC / ISO readiness

📊 Recommended Relationships (Logical)
phonepe_orders
 ├── phonepe_payment_attempts
 ├── phonepe_order_status_checks
 ├── phonepe_refunds
 │    └── phonepe_refund_status_checks
 ├── phonepe_webhook_logs
 └── phonepe_callback_events

✅ What This Covers (100%)

✔ PhonePe disputes
✔ Customer complaints
✔ Bank escalation
✔ UAT verification
✔ Legal compliance
✔ Refund investigations
✔ Duplicate payment analysis

🧠 Pro Tips (From Real Production Systems)

NEVER update history tables → only insert

Always store raw JSON

Use BIGINT for amounts

Keep epoch timestamps

Index by merchant_order_id