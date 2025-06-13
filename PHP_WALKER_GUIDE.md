# Adding Dog Walkers - PHP Project Guide

## Quick Start

### 1. Web Admin Interface
- Navigate to `admin.php` in your XAMPP setup
- Click "Add Walker" link in the main app header
- Fill out the form and submit

### 2. Direct Database Insert
Use phpMyAdmin to insert walkers directly:

```sql
INSERT INTO walkers (
    name, image, rating, review_count, distance, price, 
    description, availability, badges, background_check, 
    insured, certified
) VALUES (
    'John Smith',
    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
    45,  -- 4.5 stars (stored as integer)
    89,
    '1.2 miles',
    28,
    'Experienced dog walker with flexible schedule',
    'Mon-Fri 6am-9pm',
    '["Dog Walking", "Pet Sitting", "Verified"]',
    1,   -- Background check completed
    1,   -- Insured
    0    -- Not certified
);
```

### 3. PHP Script Method
Run the bulk insert script:
```bash
php scripts/add_sample_walkers.php
```

## File Structure

```
/your-project/
├── admin.php                 # Admin interface for adding walkers
├── api/add_walker.php        # API endpoint for form submission
├── models/Walker.php         # Walker model with create() method
├── scripts/add_sample_walkers.php  # Bulk insert script
└── index.html               # Main app (now has "Add Walker" link)
```

## Form Fields Explained

### Required Fields
- **Name**: Walker's full name
- **Price**: Hourly rate in dollars

### Optional Fields
- **Image**: Direct URL to walker's photo
- **Rating**: 0-5 stars (stored as integer: 4.8 = 48)
- **Review Count**: Number of reviews received
- **Distance**: Location proximity (e.g., "1.2 miles")
- **Description**: Walker's experience and specialties
- **Availability**: Schedule information
- **Service Badges**: JSON array of services offered
- **Certifications**: Boolean flags for credentials

## Service Badge Options

Available service badges for filtering:
- `Verified` - Basic verification completed
- `Insured` - Insurance coverage
- `Background Checked` - Background verification
- `Dog Walking` - Walking services
- `Pet Sitting` - Pet sitting services
- `Pet Boarding` - Boarding at walker's location
- `Doggy Daycare` - Daycare services
- `Grooming` - Grooming services
- `Training` - Training services

## Database Schema

```sql
CREATE TABLE walkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(500),
    rating INT DEFAULT 0,              -- Stored as integer (48 = 4.8 stars)
    review_count INT DEFAULT 0,
    distance VARCHAR(50),
    price INT NOT NULL,                -- Price per hour in dollars
    description TEXT,
    availability VARCHAR(255),
    badges JSON,                       -- Service badges as JSON array
    background_check BOOLEAN DEFAULT 0,
    insured BOOLEAN DEFAULT 0,
    certified BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## API Response Format

Successful creation:
```json
{
    "success": true,
    "message": "Walker added successfully",
    "walker_name": "John Smith",
    "walker_id": 4
}
```

Error response:
```json
{
    "success": false,
    "message": "Name and price are required fields"
}
```

## Validation Rules

- Name: Required, 2+ characters
- Price: Required, minimum $1
- Rating: 0-5 range (converted to 0-50 integer)
- Image: Must be valid URL if provided
- Badges: Automatically converted to JSON array
- Certifications: Boolean checkboxes

## Testing Your Setup

1. **Access admin page**: Go to `admin.php`
2. **Add a test walker** with required fields
3. **Check main app**: Verify walker appears in listings
4. **Test service filtering**: Select service types in search
5. **Verify database**: Check phpMyAdmin for new record

## Troubleshooting

**Form not submitting?**
- Check XAMPP Apache/MySQL are running
- Verify database connection in `config/database.php`
- Check browser console for JavaScript errors

**Walker not appearing in listings?**
- Refresh the main page (index.html)
- Check if walker was actually inserted in database
- Verify API responses in browser network tab

**Service filtering not working?**
- Ensure badges are properly formatted as JSON
- Check Walker.php search method for service matching
- Verify service dropdown values match database content

## Quick Examples

### Example Walker Data
```php
$walker = [
    'name' => 'Sarah Wilson',
    'price' => 30,
    'image' => 'https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400',
    'rating' => 47,  // 4.7 stars
    'review_count' => 156,
    'distance' => '0.8 miles',
    'description' => 'Professional pet sitter with 3+ years experience',
    'availability' => 'Weekdays 8am-6pm',
    'badges' => '["Verified", "Pet Sitting", "Insured"]',
    'background_check' => 1,
    'insured' => 1,
    'certified' => 0
];
```

### cURL Test
```bash
curl -X POST http://localhost/pawwalk/api/add_walker.php \
  -F "name=Test Walker" \
  -F "price=25" \
  -F "description=Test description" \
  -F "badges[]=Dog Walking" \
  -F "badges[]=Verified"
```