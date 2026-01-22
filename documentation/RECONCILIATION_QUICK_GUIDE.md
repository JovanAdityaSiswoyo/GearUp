# Reconciliation Module - Quick Reference

## Access Points

### Main Navigation
- Admin Panel → Sidebar → Rekonsiliasi (Calculator icon)
- Route: `/admin/reconciliation`

### Module Routes
| Route | Method | Name | Action |
|-------|--------|------|--------|
| `/admin/reconciliation` | GET | `admin.reconciliation.index` | List payments with filters |
| `/admin/reconciliation/report` | GET | `admin.reconciliation.report` | View period-based report |
| `/admin/reconciliation/match-bookings` | GET | `admin.reconciliation.match-bookings` | Find unmatched bookings |
| `/admin/reconciliation/{payment}/verify` | POST | `admin.reconciliation.verify` | Update payment status |
| `/admin/reconciliation/{booking}/create-payment` | POST | `admin.reconciliation.create-payment` | Record manual payment |

## Main Features

### 1. Payment Dashboard (Index)
- **Location:** `/admin/reconciliation`
- **Features:**
  - Summary cards (total, paid, pending, failed amounts)
  - Filter by status, date range
  - Search by provider reference
  - List of all payments with details
  - Quick status update buttons
  - Direct links to Report and Match Bookings

### 2. Reconciliation Report
- **Location:** `/admin/reconciliation/report`
- **Features:**
  - Select date range for analysis
  - View summary by payment status
  - View summary by payment method
  - View summary by payment provider
  - Print functionality
  - Percentage calculations

### 3. Match Bookings Interface
- **Location:** `/admin/reconciliation/match-bookings`
- **Features:**
  - List bookings without payments
  - Quick add payment button for each booking
  - Modal form for payment details
  - Auto-filled booking code and amount
  - Set payment method and status

## Common Tasks

### Check Payment Status
1. Go to Reconciliation → Dashboard
2. View summary cards at top
3. Scroll through payment list or use filters
4. Look for status badges (color-coded)

### Update Payment Status
1. On Dashboard, find the payment
2. Click "Update" button
3. Select new status in modal
4. Confirm action
5. Page refreshes with updated status

### Generate Monthly Report
1. Go to Reconciliation → View Report
2. Set Date From and Date To
3. Click "Generate Report"
4. Review summaries by status/method/provider
5. Click "Print" button to print or save as PDF

### Record Missing Payment
1. Go to Reconciliation → Match Bookings
2. Find booking without payment
3. Click "Add Payment" button
4. Fill in payment details:
   - Amount (auto-filled with booking amount)
   - Method (select from dropdown)
   - Status (pending/paid/failed)
   - Provider (optional)
   - Reference (optional)
5. Click "Create Payment"
6. Success alert shows confirmation

## Status Types

- **Pending** 🟡 - Payment awaiting confirmation
- **Paid** 🟢 - Payment successfully received
- **Failed** 🔴 - Payment failed/rejected
- **Refunded** ⚪ - Payment refunded to customer

## Payment Methods

- Cash
- Bank Transfer
- Credit Card
- Debit Card
- E-Wallet

## Currency

- **Stored:** Cents (1000 IDR = 100,000 cents)
- **Display:** Rupiah formatted (Rp 1.000.000)
- **Currency Code:** IDR

## Data Filtering

### By Status
- Select from dropdown: Pending, Paid, Failed, Refunded
- Shows only payments with selected status

### By Date Range
- Set "Date From" and "Date To"
- Shows payments created within range

### By Search
- Search by provider reference (transaction ID)
- Partial matching supported

## Permissions

- **Access:** Admin users only
- **Role:** Requires `auth:web,admin` middleware
- **Guard:** Admin authentication guard

## Related Models & Tables

- **Payment** table - Stores payment records
- **BookProduct** table - Booking/rental records
- **User** table - User information
- **Product** table - Product information

## Key Functionality

| Feature | Location | How To |
|---------|----------|--------|
| View all payments | Dashboard | Scroll list or filter |
| Filter payments | Dashboard | Use filter form at top |
| Update status | Dashboard | Click Update button |
| View report | Report page | Select date range, click Generate |
| Print report | Report page | Click Print button |
| Find unmatched | Match Bookings | Auto-loaded list |
| Create payment | Match Bookings | Click Add Payment button |

## Database Amounts

When working with the API or database directly:
- Amounts are stored in **cents** (divide by 100 for Rupiah)
- Example: 1000000 in database = Rp 10.000 displayed
- When creating via form: amount entered in Rupiah (form multiplies by 100)

## Tips & Tricks

1. **Quick Stats:** Check summary cards for overview
2. **Month-to-Month:** Use report feature to track trends
3. **Unmatched Payments:** Run match-bookings regularly
4. **Bulk Status:** Filter first, then update individually
5. **Print Reports:** Use browser print (Ctrl+P) for paper records

## Troubleshooting

### Payment not showing
- Check if date range includes creation date
- Verify payment status matches filter
- Search by provider reference if searching

### Can't find booking
- Verify booking status is "active"
- Check if payment already exists
- Refresh page if list seems outdated

### Amount mismatch
- Verify amounts are in correct unit (Rp, not cents)
- Check decimal places in calculation
- Review booking amount vs payment amount
