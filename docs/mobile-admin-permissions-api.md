# Lamset Anotha — Mobile Admin Permissions and API

## 1. Project Status
This document defines the proposed permissions and API for the Lamset Anotha mobile admin application.
Documentation only. Do not modify production code, production data, or Hostinger deployment without testing and explicit approval.

## 2. Proposed Architecture
- Mobile applications: Flutter for Android and iOS.
- Backend: existing Laravel store.
- API prefix: /api/v1.
- Database: reuse the existing store database only after reviewing its schema and testing in staging.
- Interface: Arabic-first, RTL-compatible, with role-based navigation.
- Environments: separate development, staging, and production.

## 3. Roles and Permissions

### Owner
- Full administrative access.
- Manage staff accounts and assign approved roles.
- Approve critical settings and authorized financial operations.
- View audit logs and reports.

### Manager
- View operational dashboard and approved reports.
- Manage permitted orders and catalog operations.
- Cannot assign owner privileges or change critical security settings by default.

### Catalog Manager
- View, create, and update products.
- Manage approved categories and product images.
- Cannot access financial data or assign roles.

### Order Operator
- View orders and permitted customer details.
- Update order fulfilment status according to approved workflow.
- Cannot approve refunds or change staff permissions by default.

### Inventory Operator
- View stock levels and stock movements.
- Record authorized stock adjustments with a reason.
- Cannot alter financial settings or approve refunds.

### Finance
- View approved sales and financial reports.
- Process refund requests only within explicitly approved limits.
- All financial actions must be audited.

### Customer Support
- Access only customer information needed for support.
- View permitted order details.
- Cannot change inventory, assign roles, or approve refunds.

### Auditor
- Read-only access to approved reports and audit events.
- Cannot modify orders, products, staff, or settings.

## 4. Permission Keys
- dashboard.view
- products.view
- products.create
- products.update
- categories.manage
- inventory.view
- inventory.adjust
- inventory.movements.view
- orders.view
- orders.fulfil
- orders.cancel
- customers.view_limited
- customers.support
- refunds.view
- refunds.request
- refunds.approve
- reports.sales.view
- reports.finance.view
- reports.inventory.view
- staff.view
- staff.manage
- roles.assign
- settings.manage
- audit.view

Every permission must be checked on the Laravel server. Hiding a button in the mobile app is not sufficient authorization.

## 5. Proposed API Endpoints
These endpoints are proposals and are not confirmed to exist yet.

### Authentication
- POST /api/v1/auth/login
- POST /api/v1/auth/logout
- GET /api/v1/auth/me

### Dashboard
- GET /api/v1/admin/dashboard

### Products and Categories
- GET /api/v1/admin/products
- POST /api/v1/admin/products
- GET /api/v1/admin/products/{product}
- PATCH /api/v1/admin/products/{product}
- GET /api/v1/admin/categories

### Inventory
- GET /api/v1/admin/inventory/movements
- POST /api/v1/admin/inventory/adjustments

### Orders and Customers
- GET /api/v1/admin/orders
- GET /api/v1/admin/orders/{order}
- PATCH /api/v1/admin/orders/{order}/status
- GET /api/v1/admin/customers

### Finance and Staff
- GET /api/v1/admin/reports/sales
- GET /api/v1/admin/reports/finance
- GET /api/v1/admin/staff
- PUT /api/v1/admin/staff/{user}/roles
- GET /api/v1/admin/audit-log

## 6. Security Requirements
- Authenticate all protected API requests.
- Enforce authorization on the server for every action.
- Reject inactive staff accounts.
- Use the authentication solution compatible with the installed Laravel version.
- Review dependencies before adding Sanctum or any new package.
- Validate every request and minimize customer personal data.
- Use database transactions for inventory, order, and refund operations.
- Prevent duplicate stock adjustments caused by request retries.
- Audit role changes, refunds, cancellations, stock adjustments, and critical settings.
- Keep credentials and API secrets out of Git.
- Never run untested migrations against the production database.

## 7. Testing Requirements
- Unauthenticated requests are rejected.
- Inactive staff cannot access protected endpoints.
- Each role can perform only authorized operations.
- Unauthorized operations return HTTP 403.
- Invalid order status transitions are rejected.
- Stock adjustments are atomic and recorded in the audit log.
- Repeated requests do not apply the same adjustment twice.
- Logout revokes the applicable token.
- Tests use isolated staging or test data, never production data.

## 8. Safe Release Process
1. Review the Laravel version, current models, migrations, and authentication.
2. Implement changes on a separate feature branch.
3. Run automated tests in an isolated environment.
4. Review the changes and create a pull request.
5. Back up production and prepare rollback steps.
6. Obtain explicit owner approval before deployment.
7. Deploy only after staging verification.

## 9. Outstanding Decisions
- Confirm the deployed production commit.
- Confirm the current database schema and inventory model.
- Approve refund limits and order cancellation rules.
- Approve which roles may access personal and financial data.
- Confirm the Laravel authentication strategy.
- Confirm staging environment and backup/rollback procedures.

## 10. Current Status
Architecture and permission proposal only. No production changes are authorized by this document.
