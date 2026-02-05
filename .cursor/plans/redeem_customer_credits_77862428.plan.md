---
name: Redeem Customer Credits
overview: Implement customer-account credit redemption at checkout using a credit ledger and a fixed-amount apply flow.
todos:
  - id: credit-ledger-model
    content: Create credit ledger model + migration + relationships
    status: completed
  - id: credit-ledger-api
    content: Add API endpoints + validation for balance/debit
    status: completed
  - id: pay-ui-redeem
    content: Add redeem modal + apply fixed amount to totals
    status: completed
  - id: payment-flow-redeem
    content: Debit credit on Pay Now with feedback
    status: completed
---

- Add a credit ledger data model and migration to track customer credit transactions (credit/debit), linked to customers, and expose relationships on the customer model. Use a summed balance for availability. Files: [C:\xampp\htdocs\lucky\nailsalon-app\database\migrations\*create_customer_credit_ledger_table.php], [C:\xampp\htdocs\lucky\nailsalon-app\app\Models\Customer.php], new [C:\xampp\htdocs\lucky\nailsalon-app\app\Models\CustomerCreditLedger.php].
- Create API endpoints to fetch a customer’s available credit balance and to create a debit entry when a redeem is applied (validated: amount > 0, amount <= available balance). Add Form Requests and controller methods under the existing `api/salon` group. Files: [C:\xampp\htdocs\lucky\nailsalon-app\app\Http\Controllers\SalonCustomerController.php] or a new controller, new requests under [C:\xampp\htdocs\lucky\nailsalon-app\app\Http\Requests], and update [C:\xampp\htdocs\lucky\nailsalon-app\routes\web.php].
- Update the pay UI to open a Redeem modal that shows current available credit for the appointment’s customer and lets staff enter a fixed redeem amount. Apply it as a line item (like gift card) and include hidden fields for the redeem amount and ledger reference. Files: [C:\xampp\htdocs\lucky\nailsalon-app\public\js\salon-pay.js], [C:\xampp\htdocs\lucky\nailsalon-app\resources\views\salon\booking\pay.blade.php].
- On “Pay Now,” call the redeem API to create the debit ledger entry (only if a redeem amount is applied) and then proceed with the existing flow. Add user feedback if the redeem fails (e.g., insufficient balance).