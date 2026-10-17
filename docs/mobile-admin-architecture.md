# Lamset Anotha — Mobile Admin App Architecture

## Project Status
Architecture proposal only. Do not modify production code, production database, or Hostinger deployment without explicit approval.

## 1. Architecture
- Mobile framework: Flutter for Android and iOS.
- Backend: existing Laravel application.
- API: versioned JSON endpoints under /api/v1.
- Database: reuse existing store database after reviewing the schema; do not create a duplicate production database.
- Languages: Arabic RTL first, with a structure that can support English.
- Environments: separate development/staging environment; production remains untouched until approval.

## 2. Admin App Modules
1. Dashboard: sales summary, order counts, low-stock alerts, and date filters.
2. Products: product details, images, categories, variants, prices, and publication status.
3. Inventory: stock levels, adjustments, movement history, and low-stock alerts.
4. Orders: search, order details, fulfilment status, cancellation, and returns.
5. Customers: limited customer profiles and support history.
6. Finance: sales reports, refunds, and payment summaries according to permissions.
7. Staff and Roles: staff accounts, roles, and granular permissions.
8. Audit Log: sensitive actions with actor, timestamp, reason, and relevant changes.
9. Settings: only settings explicitly approved for administrative access.

## 3. Roles and Permissions
Apply least privilege. Enforce every permission on the Laravel server, not only in the app UI.

- Owner: full access, including role assignment and critical settings.
- Manager: delegated operational access; no critical owner-only actions by default.
- Catalog Manager: view, create, and update products and categories.
- Order Operator: view orders and perform permitted fulfilment actions.
- Inventory Operator: view stock and record justified adjustments.
- Finance: view authorized financial reports and handle approved refund workflows.
- Customer Support: limited customer and order access; no unrestricted refunds.
- Auditor: read-only access to explicitly authorized records and audit logs.

Suggested permission keys:
- dashboard.view
- products.view, products.create, products.update, products.archive
- inventory.view, inventory.adjust, inventory.movements.view
- orders.view, orders.fulfil, orders.cancel
- customers.view, customers.support
- refunds.view, refunds.request, refunds.approve
- reports.sales.view, reports.finance.view
- staff.view, staff.manage, roles.assign
- settings.manage, audit.view

## 4. Proposed API Endpoints
These are proposed contracts. Confirm existing Laravel models and workflows before implementation.

Authentication:
- POST /api/v1/auth/login
- POST /api/v1/auth/logout
- GET /api/v1/auth/me

Dashboard:
- GET /api/v1/admin/dashboard

Products:
- GET /api/v1/admin/products
- POST /api/v1/admin/products
- GET /api/v1/admin/products/{id}
- PATCH /api/v1/admin/products/{id}
- GET /api/v1/admin/categories

Inventory:
- GET /api/v1/admin/inventory/movements
- POST /api/v1/admin/inventory/adjustments

Orders:
- GET /api/v1/admin/orders
- GET /api/v1/admin/orders/{id}
- PATCH /api/v1/admin/orders/{id}/status

Customers and reports:
- GET /api/v1/admin/customers
- GET /api/v1/admin/reports/sales

Staff and auditing:
- GET /api/v1/admin/staff
- PUT /api/v1/admin/staff/{id}/roles
- GET /api/v1/admin/audit-log

## 5. Security Requirements
- HTTPS only.
- Evaluate Laravel Sanctum and existing authentication before selecting the mobile auth method.
- Revoke mobile tokens on logout and when access is withdrawn.
- Validate authorization on every protected endpoint.
- Validate all request data and uploaded files.
- Never trust prices, totals, discounts, or permissions supplied by the mobile client.
- Use database transactions and concurrency-safe stock/order updates.
- Protect checkout and refund operations against duplicate requests.
- Keep API keys, payment secrets, and production credentials out of source control.
- Record sensitive changes in an audit log.
- Limit exposure of customer personal data.

## 6. Testing Plan
- Authentication and token revocation.
- Role and permission enforcement, including denied requests.
- Product and variant editing.
- Stock adjustment, concurrent updates, and movement history.
- Order status transitions and cancellation.
- Checkout and payment sandbox integration.
- Refund authorization and duplicate-request handling.
- Input validation, upload validation, privacy, and audit logs.
- Android and iOS device testing.

## 7. Safe Release Process
1. Review repository files, migrations, authentication, roles, products, orders, inventory, payments, and shipping.
2. Verify differences between GitHub and the current Hostinger deployment.
3. Create a separate staging environment with test data.
4. Implement changes on a feature branch.
5. Run automated tests and manual acceptance tests.
6. Review code changes and test results with the store owner.
7. Obtain explicit approval before production deployment.
8. Back up production and prepare a rollback plan before any approved release.

## Current Limitations
The existing production schema and payment/shipping integrations must be inspected before implementation. This document does not claim that the APIs or mobile applications have been built or tested.
