# How to Add Dog Walkers

This guide shows you multiple ways to add new dog walkers to your PawWalk application.

## Method 1: React App - Admin Interface (Recommended)

### Access the Admin Panel
1. Navigate to `/admin` in your React application
2. Fill out the walker information form
3. Select appropriate service badges
4. Click "Add Walker" to save

### Required Fields
- **Name**: Walker's full name
- **Price**: Hourly rate in dollars

### Optional Fields
- **Profile Image URL**: Link to walker's photo
- **Rating**: 0-5 star rating
- **Review Count**: Number of reviews
- **Distance**: Location distance (e.g., "1.2 miles")
- **Description**: Walker's experience and specialties
- **Availability**: Schedule (e.g., "Mon-Fri 9am-5pm")
- **Service Badges**: Select from available services
- **Certifications**: Background check, insurance, certification status

## Method 2: Direct API Call (React)

```javascript
// POST request to add a walker
const newWalker = {
  name: "John Smith",
  price: 28,
  image: "https://example.com/photo.jpg",
  rating: 4.7,
  reviewCount: 95,
  distance: "0.8 miles",
  description: "Professional dog walker with 3+ years experience",
  availability: "Mon-Sun 8am-6pm",
  badges: ["Verified", "Insured", "Dog Walking", "Pet Sitting"],
  backgroundCheck: true,
  insured: true,
  certified: true
};

fetch('/api/walkers', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(newWalker)
});
```

## Method 3: PHP Project - Database Insert

### Using XAMPP/phpMyAdmin
1. Open phpMyAdmin in your browser
2. Select the `pawwalk` database
3. Go to the `walkers` table
4. Click "Insert" tab
5. Fill in the walker data

### SQL Insert Statement
```sql
INSERT INTO walkers (
  name, image, rating, review_count, distance, price, 
  description, availability, badges, background_check, 
  insured, certified
) VALUES (
  'Jane Doe',
  'https://example.com/jane.jpg',
  47,  -- Rating as integer (47 = 4.7 stars)
  82,
  '1.5 miles',
  30,
  'Experienced pet sitter specializing in large breeds',
  'Weekdays 7am-7pm',
  '["Verified", "Pet Sitting", "Background Checked"]',
  1,   -- 1 = true, 0 = false
  1,
  1
);
```

### PHP Script Method
Create a PHP file to add walkers programmatically:

```php
<?php
include_once 'config/database.php';
include_once 'models/Walker.php';

$database = new Database();
$db = $database->getConnection();
$walker = new Walker($db);

$walker->name = "Alex Johnson";
$walker->image = "https://example.com/alex.jpg";
$walker->rating = 46; // 4.6 stars
$walker->review_count = 124;
$walker->distance = "2.1 miles";
$walker->price = 25;
$walker->description = "Part-time walker available evenings and weekends";
$walker->availability = "Evenings and weekends";
$walker->badges = '["Verified", "Dog Walking"]';
$walker->background_check = false;
$walker->insured = true;
$walker->certified = true;

if($walker->create()) {
    echo "Walker added successfully!";
} else {
    echo "Failed to add walker.";
}
?>
```

## Service Badge Options

When adding walkers, you can assign these service badges for filtering:

- **Verified**: Basic verification completed
- **Insured**: Walker has insurance coverage
- **Background Checked**: Background verification completed
- **Dog Walking**: Offers dog walking services
- **Pet Sitting**: Provides pet sitting services
- **Pet Boarding**: Offers boarding at walker's location
- **Doggy Daycare**: Provides daycare services
- **Grooming**: Offers grooming services
- **Training**: Provides training services

## Image Guidelines

For profile photos, use:
- Square aspect ratio (400x400px recommended)
- Professional headshot or photo with dogs
- High quality images
- Hosted on reliable image services like Unsplash

### Example Image URLs:
```
https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face
https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face
```

## Testing Your New Walker

After adding a walker:

1. **Check the home page** - New walker should appear in the list
2. **Test service filtering** - Select services to verify filtering works
3. **Try booking** - Test the booking flow with the new walker
4. **Verify data** - Ensure all information displays correctly

## Bulk Adding Walkers

For adding multiple walkers at once, run the database update script:

```bash
# In your XAMPP environment
php scripts/update_walker_services.php
```

This script will populate your database with sample walkers that have proper service badges for testing the filtering functionality.

## Troubleshooting

**Walker not appearing?**
- Check that required fields (name, price) are filled
- Verify database connection is working
- Refresh the page or clear browser cache

**Service filtering not working?**
- Ensure service badges are properly formatted as JSON array
- Check that badge names match the filtering options exactly
- Verify the search functionality is using correct service names

**Images not loading?**
- Use direct image URLs, not redirects
- Ensure images are publicly accessible
- Try using Unsplash URLs for testing