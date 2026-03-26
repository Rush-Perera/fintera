# Fintera - Personal Finance Management System

A modern, mobile-first personal finance management application built with Laravel and Vue.js. Fintera helps users track income, expenses, manage multiple payment methods and fund accounts, and gain insights into their spending patterns.

## Features

### Core Features
- **Income & Expense Tracking**: Record daily transactions with categories and remarks
- **Payment Methods**: Create and manage multiple payment methods (cash, cards, digital wallets, etc.)
- **Fund Accounts**: Manage bank accounts and other fund sources
- **Categories**: Organize transactions with customizable income and expense categories
- **Dashboard**: Real-time view of daily/monthly income, expenses, and net balance

### Fund Account Management
- **Direct Income/Expense Recording**: Add income or expenses directly to fund accounts
- **Fund Transfers**: Transfer money between fund accounts or to payment methods
- **Balance Tracking**: Automatic balance updates with transaction history
- **Account Management**: Create, update, and manage multiple bank accounts

### Analytics & Insights
- **Daily Balance Overview**: Track income, expenses, and net changes for each day
- **Monthly Summary**: View monthly income, expenses, and trends
- **Category Breakdown**: Analyze spending by category with top 5 categories display
- **Activity Filters**: Filter transactions by date range, category, and type

### Additional Features
- **File Uploads**: Attach payslips or receipts to income transactions
- **Transaction History**: Full transaction history with ability to edit/delete
- **Responsive Design**: Mobile-first interface optimized for all devices
- **Multi-Account Support**: Manage multiple fund accounts and payment methods simultaneously

## Technology Stack

- **Backend**: Laravel (PHP framework)
- **Frontend**: Blade templates with Vite for asset bundling
- **Database**: MySQL/SQLite
- **Authentication**: Laravel built-in authentication
- **Styling**: Custom CSS with mobile-first approach

## Project Structure

```
├── app/
│   ├── Http/Controllers/        # Application controllers
│   │   ├── AuthController
│   │   ├── DashboardController
│   │   ├── TransactionController
│   │   ├── CategoryController
│   │   ├── PaymentMethodController
│   │   └── FundAccountController
│   └── Models/                  # Eloquent models
│       ├── User
│       ├── Transaction
│       ├── Category
│       ├── PaymentMethod
│       ├── FundAccount
│       └── FundTransfer
├── database/
│   ├── migrations/              # Database migrations
│   ├── factories/               # Model factories for testing
│   └── seeders/                 # Database seeders
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── dashboard/
│   │   ├── fund-accounts/
│   │   ├── payment-methods/
│   │   ├── categories/
│   │   └── layouts/
│   ├── css/                     # Stylesheets
│   └── js/                      # JavaScript files
└── routes/
    └── web.php                  # Web route definitions
```

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL or SQLite

### Installation

1. Clone the repository
   ```bash
   git clone <repository-url>
   cd fintera
   ```

2. Install dependencies
   ```bash
   composer install
   npm install
   ```

3. Create environment configuration
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env` and run migrations
   ```bash
   php artisan migrate
   ```

5. Seed the database (optional)
   ```bash
   php artisan db:seed
   ```

6. Build assets
   ```bash
   npm run build
   ```

7. Start the development server
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` in your browser.

## Usage

### Managing Fund Accounts
1. Navigate to **Fund Accounts** from the dashboard
2. Create a new fund account with bank details and initial balance
3. Record income/expenses directly to the account
4. View transaction history and balance updates

### Recording Transactions
1. Go to **Dashboard** or **Fund Accounts**
2. Click **Add Income** or **Add Expense**
3. Select payment method or fund account
4. Enter amount, date, and optional category/remarks
5. Attach receipts/payslips if needed

### Transferring Funds
1. From **Fund Accounts** page, use the transfer forms:
   - Transfer to Payment Method
   - Transfer Between Fund Accounts
2. Enter source, destination, amount, and transfer date
3. Transaction is recorded and balances updated automatically

## Database Schema

### Key Tables
- `users`: User accounts
- `transactions`: Income and expense records with payment method or fund account linkage
- `categories`: Transaction categories
- `payment_methods`: User's payment methods (cards, cash, etc.)
- `fund_accounts`: Bank accounts and fund sources
- `fund_transfers`: Transfers between fund accounts or to payment methods

## Recent Updates (March 2026)

### New Feature: Direct Fund Account Income/Expenses
- Added ability to record income and expenses directly to fund accounts
- `transactions` table now includes `fund_account_id` column
- Fund account balances automatically update with transactions
- Transaction history shows account source/destination
- Dashboard activity view displays fund account transactions

## API Routes

### Authentication
- `POST /register` - User registration
- `POST /login` - User login
- `POST /logout` - User logout

### Dashboard
- `GET /dashboard` - View dashboard with filters

### Transactions
- `POST /transactions` - Create transaction
- `DELETE /transactions/{transaction}` - Delete transaction
- `GET /transactions/{transaction}/payslip` - Download payslip

### Fund Accounts
- `GET /fund-accounts` - List fund accounts
- `POST /fund-accounts` - Create fund account
- `PUT /fund-accounts/{fundAccount}` - Update fund account
- `DELETE /fund-accounts/{fundAccount}` - Delete fund account
- `POST /fund-transfers` - Transfer to payment method
- `POST /fund-transfers/between-accounts` - Transfer between accounts

### Payment Methods
- `GET /payment-methods` - List payment methods
- `POST /payment-methods` - Create payment method
- `PUT /payment-methods/{paymentMethod}` - Update payment method
- `DELETE /payment-methods/{paymentMethod}` - Delete payment method

### Categories
- `GET /categories` - List categories
- `POST /categories` - Create category
- `PUT /categories/{category}` - Update category
- `DELETE /categories/{category}` - Delete category

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please create an issue in the repository.
