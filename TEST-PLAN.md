# Manual acceptance test plan — IT0049 POS Midterm

Record the observed result and take clean screenshots **without showing your administrator password**.

| ID | Test | Expected result |
|---|---|---|
| SEC-01 | Log out and open `/products` directly | Redirects to `/login` |
| SEC-02 | Try a wrong username or password | Friendly error; no login session |
| SEC-03 | Log in with seeded admin | Dashboard opens; session created |
| SEC-04 | After logging out, try `/sales/new` | Redirects back to login |
| SEC-05 | Submit a POST without CSRF token | Request blocked |
| PROD-01 | Add product with price 120.00 and stock 10 | Product appears with 10 in stock |
| PROD-02 | Edit product price and stock | Changes persist after page refresh |
| PROD-03 | Upload a valid JPG, PNG or WebP | Prepared 640×640 JPEG appears |
| PROD-04 | Upload an invalid text file or file larger than 2 MB | Validation message; no image saved |
| CUST-01 | Add customer with a valid name and email | Customer appears on listing |
| CUST-02 | Add invalid email | Validation error; old values preserved |
| CUST-03 | Edit then delete customer | Changes persist; then row removed |
| STAFF-01 | Add staff with 10+ character password | New staff can log in; password stored hashed |
| STAFF-02 | Try a duplicate username | Form validation blocks it |
| STAFF-03 | Upload 256×256 avatar from a rectangular photo | 256×256 JPEG thumbnail displays |
| STAFF-04 | Disable a different staff account | That account cannot log in again |
| SALES-01 | With a product at 10 stock, record sale of quantity 3 | New sale is recorded and stock becomes 7 |
| SALES-02 | Try selling 8 more of that product | Clear insufficient-stock message; stock remains 7; no new sale |
| SALES-03 | Make sale with customer blank | Sale succeeds and history displays Walk-in / Guest |
| SALES-04 | Check sale history | Correct product/customer/staff/qty/total/date |
| SALES-05 | View receipt | Displays calculated total and can be printed |
| SALES-06 | Remove a product referenced by a sale | Product archives; past sale stays visible |
| SALES-07 | Remove staff referenced by a sale | Staff archives; past sale stays visible |
| SALES-08 | Two simultaneous checkout attempts for last few items | At most available quantity sold; no negative stock |

## Example stock test

1. Add a product named `Midterm Test Drink`, price `₱50.00`, stock `10`.
2. Record quantity `3`. Check sales history: total **₱150.00**. Check product: remaining stock **7**.
3. Try quantity `8`. Check the red error and stock still **7**.
4. Record quantity `2` (optional guest). Check total **₱100.00** and remaining stock **5**.

## Evidence screenshots

1. Login screen and denied logged-out access.
2. Logged-in dashboard with overview cards.
3. Product list, create/edit product screen, and uploaded image.
4. Customer create/edit/list pages and validation errors.
5. Staff profile, hashed password code (no hashes or passwords copied) and avatar thumbnail.
6. Checkout page before sale and confirmation receipt after sale.
7. Stock before/after, oversell warning, sales history table.
8. Database tables in phpMyAdmin with `sales` foreign keys or migration screenshot.
9. GitHub repository showing source/migrations/README and a working hosted site.

Screenshots are supporting evidence only. The graded submission explicitly requests GitHub and hosting URLs.
