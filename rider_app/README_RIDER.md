# Le Maison Rider App

A Flutter-based mobile application for delivery riders to manage and track food delivery orders from Le Maison restaurant.

## Features

### Core Features
- **User Authentication**: Email-based login with secure token management
- **Available Deliveries**: Browse list of pending delivery orders with details
- **Active Deliveries**: Track currently assigned deliveries
- **Real-time GPS Tracking**: Live location updates while delivering
- **Delivery Status Management**: Accept, reject, start, and complete deliveries
- **Delivery Proof Upload**: Capture photos as proof of delivery
- **Earnings Dashboard**: View total and daily earnings with weekly breakdown
- **Rider Profile**: View rider information, statistics, and rating
- **Push Notifications**: Receive alerts for new orders and status updates

### Technical Features
- Cross-platform support (iOS, Android, Web)
- Offline-ready with local storage
- Real-time location streaming
- Image capture and upload
- RESTful API integration
- State management with GetX
- Material Design UI

## Prerequisites

- Flutter SDK 3.0+
- Dart 3.0+
- Android Studio or Xcode
- Git

## Installation

### 1. Clone the Repository
```bash
git clone https://github.com/ryanbarilla16-create/Le-Maison.git
cd Le-Maison/rider_app
```

### 2. Install Dependencies
```bash
flutter pub get
```

### 3. Configure API Connection
Update the backend API URL in `lib/utils/constants.dart`:
```dart
static const String apiBaseUrl = 'http://your-backend-url/api';
```

### 4. Run the App
```bash
# Android
flutter run -d emulator-5554

# iOS
flutter run -d iPhone

# Web
flutter run -d chrome
```

## Project Structure

```
rider_app/
├── lib/
│   ├── main.dart                 # App entry point
│   ├── models/
│   │   ├── delivery_model.dart   # Delivery data model
│   │   └── rider_model.dart      # Rider profile model
│   ├── screens/
│   │   ├── login_screen.dart     # Authentication screen
│   │   ├── home_screen.dart      # Main navigation screen
│   │   ├── available_deliveries_screen.dart
│   │   ├── active_deliveries_screen.dart
│   │   ├── delivery_details_screen.dart
│   │   ├── earnings_screen.dart
│   │   └── profile_screen.dart
│   ├── services/
│   │   ├── api_service.dart      # Backend API calls
│   │   ├── location_service.dart # GPS tracking
│   │   └── storage_service.dart  # Local data storage
│   └── utils/
│       └── constants.dart        # App colors, strings, defaults
├── android/                      # Android-specific files
├── ios/                          # iOS-specific files
├── web/                          # Web-specific files
├── pubspec.yaml                  # Dependencies
└── README.md                     # This file
```

## Key Dependencies

- **get** (4.6.5): State management and routing
- **dio** (5.3.0): HTTP client for API calls
- **geolocator** (11.0.0): GPS location tracking
- **google_maps_flutter** (2.5.0): Maps integration
- **image_picker** (1.0.0): Camera and gallery access
- **shared_preferences** (2.2.0): Local storage
- **intl** (0.19.0): Date and time formatting
- **fluttertoast** (8.2.0): Toast notifications

## API Integration

### Authentication
```dart
// Login
POST /riders/login
{
  "email": "rider@example.com",
  "password": "password123"
}

// Response
{
  "success": true,
  "token": "auth_token",
  "rider": { /* rider data */ }
}
```

### Deliveries
```dart
// Available deliveries
GET /deliveries/available

// Active deliveries
GET /deliveries/active

// Accept delivery
POST /deliveries/{id}/accept

// Complete delivery
POST /deliveries/{id}/complete
```

### Location
```dart
// Update rider location
POST /riders/location
{
  "latitude": 14.5994,
  "longitude": 120.9842
}
```

## Environment Variables

Create a `.env` file in the root directory:
```
API_BASE_URL=http://localhost:5000/api
GOOGLE_MAPS_API_KEY=your_google_maps_key
```

## Permissions Required

### Android
- Location (Fine & Coarse)
- Camera
- Storage (Read & Write)

### iOS
- Location (When in Use & Always)
- Camera
- Photos

Add to `android/app/src/main/AndroidManifest.xml`:
```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
```

Add to `ios/Runner/Info.plist`:
```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>We need your location to track deliveries</string>
<key>NSCameraUsageDescription</key>
<string>We need camera access to capture delivery proofs</string>
```

## Usage Workflow

### 1. Login
- Enter email and password
- Receive authentication token
- Token stored locally for session persistence

### 2. Accept a Delivery
- View available deliveries
- Tap on order to see details
- Click "Accept Delivery" button
- Order moves to Active Deliveries

### 3. Track Delivery
- Navigate to delivery location using built-in maps
- Real-time GPS location is sent to backend every 5 seconds
- Customer can track rider in real-time

### 4. Complete Delivery
- Arrive at customer location
- Click "Upload Proof" button
- Take photo of delivery/receipt
- Confirm delivery completion
- Earnings credited to account

### 5. View Earnings
- Check total earnings amount
- View daily and weekly breakdown
- Track total deliveries completed
- Monitor rider rating

## Building for Production

### Android APK
```bash
flutter build apk --release
```

### iOS App
```bash
flutter build ios --release
```

### Web App
```bash
flutter build web --release
```

## Troubleshooting

### Location Permission Denied
- Grant location permissions in device settings
- Ensure GPS is enabled
- For iOS, add privacy descriptions in Info.plist

### API Connection Error
- Verify backend server is running
- Check API_BASE_URL in constants.dart
- Ensure device can reach the server (check firewall)

### Image Upload Failed
- Check camera permissions
- Verify storage space available
- Ensure internet connection

## Testing

Run unit tests:
```bash
flutter test
```

Run integration tests:
```bash
flutter test integration_test
```

## Performance Optimization

- Lazy loading of delivery lists
- Image caching for profile pictures
- Efficient location streaming (every 10 meters or 5 seconds)
- Database indexing for quick queries

## Security Considerations

- Tokens stored securely in SharedPreferences
- API requests include Authorization header
- Password hashing on backend
- SSL/TLS for all API communications
- Input validation on all forms

## Future Enhancements

- SOS button for emergency support
- Chat with customers and support team
- Offline order acceptance
- Performance bonuses for high ratings
- Integration with payment gateways
- Multi-language support
- Dark mode support
- OCR for delivery proof validation

## Contributing

1. Create a feature branch
2. Make your changes
3. Submit a pull request
4. Code review and merge

## License

Proprietary - Le Maison Yelo Lane

## Support

For issues or questions:
- Email: support@lemaisonyelolane.com
- Phone: (555) 123-4567

## Version

**Current Version**: 1.0.0\
**Last Updated**: March 24, 2026

---

**Created for**: Le Maison Yelo Lane Restaurant\
**Developer**: Ryan Barilla\
**GitHub**: [Le-Maison](https://github.com/ryanbarilla16-create/Le-Maison)
