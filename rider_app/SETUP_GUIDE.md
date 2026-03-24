# Le Maison Rider App - Setup & Build Guide

## 📱 Project Overview

A complete Flutter-based delivery rider application for Le Maison restaurant that enables riders to:
- Accept and manage food delivery orders
- Track GPS location in real-time
- Upload delivery proofs with photos
- Monitor earnings and performance metrics
- Maintain rider profile and ratings

## 🎯 Quick Start

### Prerequisites Check
```bash
flutter doctor
# Ensure SDK, Android SDK, and iOS tools are installed
```

### Installation Steps

1. **Navigate to Rider App Directory**
   ```bash
   cd Le-Maison/rider_app
   ```

2. **Install Dependencies**
   ```bash
   flutter pub get
   ```

3. **Configure Backend URL**
   - Edit `lib/utils/constants.dart`
   - Update `apiBaseUrl` to your backend server
   ```dart
   static const String apiBaseUrl = 'http://your-server:5000/api';
   ```

4. **Run on Emulator/Device**
   ```bash
   flutter run
   ```

## 📂 Project Structure

### Core Files
```
rider_app/lib/
├── main.dart                     # App entry point with GetX setup
├── models/
│   ├── delivery_model.dart      # Delivery order data structure
│   └── rider_model.dart         # Rider profile data structure
├── screens/
│   ├── login_screen.dart        # Authentication screen
│   ├── home_screen.dart         # Main bottom navigation hub
│   ├── available_deliveries_screen.dart  # Browse pending orders
│   ├── active_deliveries_screen.dart     # In-progress deliveries
│   ├── delivery_details_screen.dart      # Order details & actions
│   ├── earnings_screen.dart     # Financial dashboard
│   └── profile_screen.dart      # Rider profile & settings
├── services/
│   ├── api_service.dart         # Dio-based API client
│   ├── location_service.dart    # Geolocator GPS tracking
│   └── storage_service.dart     # SharedPreferences wrapper
└── utils/
    └── constants.dart           # Colors, strings, defaults
```

## 🔑 Key Features Implemented

### 1. Authentication System
- Email/password login
- JWT token storage
- Automatic session persistence
- Secure logout with session clearing

**Usage:**
```dart
// Login
final response = await ApiService.login(email, password);
await StorageService.setToken(response['token']);
```

### 2. Delivery Management
- View available deliveries with filtering
- Accept/reject delivery orders
- Track active deliveries in real-time
- Mark deliveries as complete with proof upload

**States:**
- `WAITING` - Available for acceptance
- `PICKED_UP` - Order collected from restaurant  
- `ON_THE_WAY` - En route to customer
- `DELIVERED` - Successfully delivered

### 3. GPS Tracking
- Real-time location updates every 5 seconds
- Location permission handling
- Background location services
- Distance calculation utilities

**Usage:**
```dart
final location = await LocationService().getCurrentLocation();
final stream = await LocationService().getLocationStream();
```

### 4. Earnings Dashboard
- Total earnings display
- Daily earnings breakdown
- Weekly earnings chart
- Completed delivery count
- Performance metrics

### 5. Rider Profile
- Profile information display
- Rider rating and statistics
- Vehicle details
- Contact information
- Account logout functionality

## 🚀 Running the App

### On Android Emulator
```bash
flutter run
# or specify device
flutter run -d emulator-5554
```

### On Physical Android Device
```bash
# Connect device and enable USB debugging
flutter run -d <device_id>
```

### On iOS Simulator
```bash
flutter run -d iPhone
```

### On iOS Physical Device
```bash
# Open iOS project in Xcode and configure signing
open ios/Runner.xcworkspace
```

### On Web Browser
```bash
flutter run -d chrome
# or build for web
flutter build web --release
```

## 🔌 API Integration

### Authentication Endpoint
```
POST /api/riders/login
{
  "email": "rider@example.com",
  "password": "password"
}

Response:
{
  "success": true,
  "token": "jwt_token_here",
  "rider": { /* rider data */ }
}
```

### Delivery Endpoints
```
GET /api/deliveries/available
GET /api/deliveries/active
POST /api/deliveries/{id}/accept
POST /api/deliveries/{id}/start
POST /api/deliveries/{id}/complete
POST /api/deliveries/upload-proof
```

### Location Endpoint
```
POST /api/riders/location
{
  "latitude": 14.5994,
  "longitude": 120.9842
}
```

## 📦 Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| get | 4.6.5 | State management & routing |
| dio | 5.3.0 | HTTP client |
| geolocator | 11.0.0 | GPS tracking |
| google_maps_flutter | 2.5.0 | Map visualization |
| image_picker | 1.0.0 | Camera/gallery access |
| shared_preferences | 2.2.0 | Local storage |
| intl | 0.19.0 | Date formatting |
| fluttertoast | 8.2.0 | Toast notifications |
| cached_network_image | 3.3.0 | Image caching |
| loading_animation_widget | 1.2.1 | Loading indicators |

## 🔐 Permissions

### Android (android/app/src/main/AndroidManifest.xml)
```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
```

### iOS (ios/Runner/Info.plist)
```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>We need your location to track deliveries in real-time</string>

<key>NSLocationAlwaysAndWhenInUseUsageDescription</key>
<string>We need background location access to update delivery status</string>

<key>NSCameraUsageDescription</key>
<string>We need camera access to capture delivery proof photos</string>

<key>NSPhotoLibraryUsageDescription</key>
<string>We need photo library access to select delivery proof images</string>
```

## 🧪 Testing

### Unit Tests
```bash
flutter test
```

### Integration Tests
```bash
flutter test integration_test
```

### Build for Testing
```bash
# Android debug APK
flutter build apk --debug

# iOS debug build
flutter build ios --debug
```

## 🏗️ Building for Release

### Android Release APK
```bash
flutter build apk --release
```

### Android App Bundle
```bash
flutter build appbundle --release
```

### iOS Release Build
```bash
flutter build ios --release
```

### Web Production Build
```bash
flutter build web --release
```

## 🔧 Troubleshooting

### Common Issues

**Location Permission Denied**
- Open device Settings → Apps → Le Maison Rider
- Grant Location permission as "Allow all the time"
- Ensure GPS is enabled

**API Connection Failed**
- Verify backend server is running
- Check API URL in constants.dart
- Test with Postman: `http://localhost:5000/api/deliveries/available`
- Check firewall and network connectivity

**Image Upload Error**
- Grant Camera and Storage permissions
- Check available disk space
- Verify file size < 5MB
- Test with a different image

**GPS Not Working**
- Ensure location services enabled
- For simulator, mock location data
- On Android emulator: Extended controls → Location

**App Crashes on Startup**
- Run `flutter clean && flutter pub get`
- Check main.dart StorageService initialization
- Verify no null pointer exceptions in services

## 📊 Performance Tips

1. **Image Optimization**
   - Compress images before upload
   - Use image caching for profile pictures

2. **API Calls**
   - Implement pagination for delivery lists
   - Cache frequently accessed data

3. **GPS Tracking**
   - Adjust location update frequency as needed
   - Use aggressive location accuracy only when needed

4. **Memory Management**
   - Dispose controllers properly
   - Release streams on screen close

## 🚢 Deployment Checklist

- [ ] Update API_BASE_URL to production server
- [ ] Generate signing keys for Android
- [ ] Configure iOS signing certificates
- [ ] Test all API endpoints
- [ ] Verify GPS tracking accuracy
- [ ] Test image upload functionality
- [ ] Check push notifications
- [ ] Performance test with multiple users
- [ ] Security audit completed
- [ ] Privacy policy reviewed
- [ ] Terms of service prepared

## 📝 Development Notes

### State Management
Uses GetX for simplicity and performance:
- No BuildContext required
- Reactive variables
- Easy routing and navigation

### APIs
All API calls through ApiService with:
- Automatic token injection
- Error handling
- Retry logic
- Request/response logging

### Storage
SharedPreferences for:
- Authentication tokens
- User preferences
- Offline data caching

## 🤝 Contributing

1. Create feature branch: `git checkout -b feature/new-feature`
2. Commit changes: `git commit -m "Add new feature"`
3. Push to branch: `git push origin feature/new-feature`
4. Submit pull request

## 📞 Support & Contact

- **Email**: support@lemaisonyelolane.com
- **Phone**: (555) 123-4567
- **Issues**: Report on GitHub

## 📄 License

Proprietary - Le Maison Yelo Lane

---

**Last Updated**: March 24, 2026  
**Version**: 1.0.0  
**Created By**: Ryan Barilla
