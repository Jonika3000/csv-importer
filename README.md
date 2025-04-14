# Product CSV Importer

A Symfony console application for importing product data from CSV files with validation.
Import depends on the data provider: the appropriate importer is selected via fileIsSupported(). The CsvProductImporterRegistry finds the matching class or throws an exception.
The file public/data/main_partner_stock.csv serves as an example.

## Installation

```bash
# Clone repository
git clone https://github.com/Jonika3000/csv-importer.git
cd product-importer

# Install dependencies
composer install

# Configure database (edit .env)
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0"

# Setup database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Usage

```bash
#Basic Import
php bin/console product:import /path/to/products.csv

#Test Mode (Dry Run)
php bin/console product:import /path/to/products.csv --test
```

## Business Rules

### Valid products must:

- Have all required fields (code, name, description)
- Have numeric stock ≥ 0 and price > 0
- Price must be between 5 and 1000 (unless stock ≥ 10)
- Product code must be unique

## Testing

```bash
# Run all tests
./vendor/bin/phpunit
```

## Stack

- Symfony
- MySQL
- Rector
- PhpOffice
