# DataTable Export Formatting Guide

## Overview


The export system now includes intelligent, flexible formatting that automatically handles various data types including dates, relationships, currencies, percentages, and more. The formatting works consistently across all export formats (Excel, CSV, and PDF).

## Automatic Formatting Features

### 1. **Date & DateTime Formatting**
Automatically detects and formats date fields based on field name patterns:
- Detects fields containing: `date`, `datetime`, `_at`, `time`, `created`, `updated`, `start`, `end`, `due`, `birth`, `hire`, `termination`, `pay_period`
- Formats dates as `Y-m-d` (e.g., 2024-01-15)
- Formats datetimes as `Y-m-d H:i` (e.g., 2024-01-15 14:30)

**Example:**
```php
// Input: "2024-01-15 14:30:00"
// Output: "2024-01-15 14:30"

// Input: "2024-01-15 00:00:00"
// Output: "2024-01-15"
```

### 2. **Relationship Formatting**
Automatically extracts meaningful values from relationship objects:
- Extracts `name`, `label`, or `title` properties
- Handles arrays of relationships
- Comma-separates multiple values

**Example:**
```php
// Status relationship object
// Input: { id: 1, name: "Completed", color: "#00ff00" }
// Output: "Completed"

// Multiple assigned users
// Input: [{ id: 1, name: "John" }, { id: 2, name: "Jane" }]
// Output: "John, Jane"
```

### 3. **Currency Formatting**
Automatically detects and formats currency fields:
- Detects fields containing: `amount`, `price`, `cost`, `total`, `salary`, `rate`, `pay`, `earning`, `gross`, `net`, `tax`
- Formats as: `$1,234.56`

**Example:**
```php
// Input: 1234.56
// Output: "$1,234.56"
```

### 4. **Percentage Formatting**
Automatically detects and formats percentage fields:
- Detects fields containing: `percent`, `percentage`, `multiplier`
- Formats as: `15.50%`

**Example:**
```php
// Input: 15.5
// Output: "15.50%"
```

### 5. **Boolean Formatting**
Converts boolean values to readable text:
- `true` → "Yes"
- `false` → "No"

### 6. **HTML Stripping**
Automatically removes HTML tags and decodes entities from text fields:
- Strips all HTML tags
- Decodes HTML entities
- Trims whitespace

**Example:**
```php
// Input: "<strong>Important</strong> &amp; urgent"
// Output: "Important & urgent"
```

## Usage Examples

### Excel/CSV Export

The DataTable component automatically applies formatting when exporting:

```vue
<DataTable
  :data="tasks"
  :columns="columns"
  exportable
  export-filename="tasks-export"
  export-title="Tasks Report"
/>
```

### Column Configuration

Define columns as usual - the export system handles formatting automatically:

```javascript
const columns = [
  {
    key: 'name',
    label: 'Task Name',
  },
  {
    key: 'status.name',  // Will extract just the name
    label: 'Status',
  },
  {
    key: 'start_datetime',  // Will format as date
    label: 'Start Date',
  },
  {
    key: 'total_amount',  // Will format as currency
    label: 'Amount',
  },
  {
    key: 'completion_percentage',  // Will format as percentage
    label: 'Progress',
  },
  {
    key: 'is_active',  // Will format as Yes/No
    label: 'Active',
  },
];
```

## Detected Field Patterns

### Date Fields
```
date, datetime, _at, time, created, updated, deleted, published,
start, end, due, birth, hire, termination, pay_period
```

### Currency Fields
```
amount, price, cost, total, salary, rate, pay, earning,
gross, net, tax
```

### Percentage Fields
```
percent, percentage, rate, multiplier
```

## Custom Formatting

If you need custom formatting for specific fields, you can preprocess your data before passing it to the DataTable:

```javascript
const formattedData = computed(() => {
  return props.data.data.map(item => ({
    ...item,
    custom_field: formatCustomValue(item.custom_field),
  }));
});
```

## Export Formats

### 1. Excel (.xlsx)
- Full formatting support
- Auto-sized columns
- Bold headers
- Preserves data types

### 2. CSV (.csv)
- Plain text with formatting applied
- Comma-separated values
- Compatible with Excel and Google Sheets

### 3. PDF (.pdf)
- Professional layout
- Formatted tables
- Headers and footers
- Generation timestamp

## Best Practices

1. **Field Naming**: Use descriptive field names that match the patterns above for automatic formatting
2. **Relationship Data**: Ensure relationships include `name`, `label`, or `title` properties
3. **Exclude Actions**: The system automatically excludes `actions` and `bulk_select` columns
4. **Data Types**: Use appropriate data types (boolean, numeric, datetime) for automatic formatting

## Troubleshooting

### Date Not Formatting
- Check if field name contains date-related keywords
- Ensure value is a valid date string or Carbon instance

### Currency Not Formatting
- Check if field name contains currency-related keywords
- Ensure value is numeric

### Relationship Showing Objects
- Ensure the relationship object has `name`, `label`, or `title` property
- Check if the relationship is properly loaded in the data

## Example: Payroll Export

```javascript
const columns = [
  { key: 'user.name', label: 'Employee' },              // Relationship
  { key: 'pay_period_start', label: 'Period Start' },  // Date formatting
  { key: 'pay_period_end', label: 'Period End' },      // Date formatting
  { key: 'regular_hours', label: 'Hours' },            // Number
  { key: 'gross_pay', label: 'Gross Pay' },            // Currency formatting
  { key: 'net_pay', label: 'Net Pay' },                // Currency formatting
  { key: 'status', label: 'Status' },                   // Relationship
];

// Export will automatically format:
// - Dates: "2024-01-01" to "2024-01-31"
// - Currency: $1,234.56
// - Relationships: Just the name value
```

## Integration with Other DataTables

The export system is fully reusable across all DataTables in the application:

- Tasks
- Projects
- Clients
- Employees
- Payslips
- Time Entries
- Any custom DataTable

Simply add `exportable` prop and the formatting will work automatically!
