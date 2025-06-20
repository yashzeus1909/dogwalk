# Walker Backend API Documentation

## Separate Backend URLs for Walker Management

### 1. Walker Booking List API
**URL**: `api/walker_booking_list.php`
**Method**: GET
**Purpose**: Get paginated list of bookings for a specific walker with filtering and statistics

#### Parameters:
- `walker_id` (required): Walker's unique ID
- `status` (optional): Filter by booking status (pending, confirmed, in_progress, completed, cancelled)
- `limit` (optional): Number of bookings per page (default: 50)
- `offset` (optional): Number of bookings to skip for pagination (default: 0)

#### Response:
```json
{
  "success": true,
  "bookings": [...],
  "walker_info": {
    "id": 1,
    "name": "Walker Name",
    "email": "walker@example.com",
    "image": "profile_image_url"
  },
  "stats": {
    "pending": 5,
    "confirmed": 3,
    "in_progress": 1,
    "completed": 20,
    "cancelled": 2,
    "total_earnings": 450.00
  },
  "pagination": {
    "total": 31,
    "limit": 50,
    "offset": 0,
    "has_more": false
  },
  "filters": {
    "status": "all"
  }
}
```

#### Usage Examples:
```javascript
// Get all bookings for walker ID 1
fetch('api/walker_booking_list.php?walker_id=1')

// Get only confirmed bookings
fetch('api/walker_booking_list.php?walker_id=1&status=confirmed')

// Get bookings with pagination
fetch('api/walker_booking_list.php?walker_id=1&limit=10&offset=20')
```

### 2. Walker Profile Update API
**URL**: `api/walker_profile_update.php`
**Methods**: GET, POST, PUT
**Purpose**: View and update walker profile information

#### GET Request (View Profile):
**Parameters**: `walker_id` (required)

**Response**:
```json
{
  "success": true,
  "walker": {
    "id": 1,
    "name": "Walker Name",
    "email": "walker@example.com",
    "image": "profile_image_url",
    "rating": "4.8",
    "review_count": 127,
    "distance": "1.2 miles",
    "price": 25,
    "description": "Experienced walker...",
    "availability": "Mon-Fri 9AM-5PM",
    "badges": ["Background Checked", "Insured"],
    "background_check": true,
    "insured": true,
    "certified": false
  }
}
```

#### POST/PUT Request (Update Profile):
**Body**:
```json
{
  "walker_id": 1,
  "name": "Updated Name",
  "email": "newemail@example.com",
  "image": "new_profile_image_url",
  "price": 30,
  "description": "Updated description",
  "availability": "Mon-Sun 8AM-6PM",
  "badges": ["Background Checked", "Insured", "Certified"],
  "background_check": true,
  "insured": true,
  "certified": true
}
```

**Response**:
```json
{
  "success": true,
  "message": "Walker profile updated successfully",
  "walker": { ... }
}
```

### 3. Walker Booking Actions API
**URL**: `api/walker_booking_actions.php`
**Methods**: PUT, DELETE
**Purpose**: Update booking status or delete bookings

#### PUT Request (Update Booking Status):
**Body**:
```json
{
  "booking_id": 123,
  "walker_id": 1,
  "status": "confirmed",
  "notes": "Optional status update notes"
}
```

**Valid Status Transitions**:
- `pending` → `confirmed`, `cancelled`
- `confirmed` → `in_progress`, `cancelled`, `completed`
- `in_progress` → `completed`, `cancelled`
- `completed` → (no changes allowed)
- `cancelled` → (no changes allowed)

#### DELETE Request (Delete Booking):
**Body**:
```json
{
  "booking_id": 123,
  "walker_id": 1,
  "reason": "Optional deletion reason"
}
```

**Note**: Only `pending` or `cancelled` bookings can be deleted.

### 4. Walker Authentication API
**URL**: `api/walker_auth.php`
**Method**: POST
**Purpose**: Authenticate walker by email

#### Request:
```javascript
const formData = new FormData();
formData.append('email', 'walker@example.com');
// OR
formData.append('walker_id', '1');
```

#### Response:
```json
{
  "success": true,
  "message": "Walker authenticated successfully",
  "walker": { ... }
}
```

## Error Handling

All endpoints return consistent error responses:
```json
{
  "success": false,
  "message": "Error description"
}
```

Common HTTP status codes:
- `200`: Success
- `400`: Bad Request (validation errors)
- `405`: Method Not Allowed
- `500`: Internal Server Error

## Security Features

1. **Authorization**: All booking operations verify that the booking belongs to the requesting walker
2. **Input Validation**: All inputs are sanitized and validated
3. **Status Transition Validation**: Prevents invalid status changes
4. **Deletion Restrictions**: Only certain booking statuses can be deleted
5. **Email Validation**: Proper email format validation for profile updates