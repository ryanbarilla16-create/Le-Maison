# Le Maison Rider App - Developer Reference

## Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│              Flutter UI Layer (Screens)              │
│  ├─ LoginScreen                                      │
│  ├─ HomeScreen (Bottom Navigation)                   │
│  ├─ AvailableDeliveriesScreen                       │
│  ├─ ActiveDeliveriesScreen                          │
│  ├─ DeliveryDetailsScreen                           │
│  ├─ EarningsScreen                                  │
│  └─ ProfileScreen                                   │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│          Business Logic & Services Layer             │
│  ├─ ApiService (HTTP/Dio)                           │
│  ├─ LocationService (GPS/Geolocator)                │
│  └─ StorageService (Local Data)                     │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│             Data Models                              │
│  ├─ Rider Model                                     │
│  └─ Delivery Model                                  │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│          Backend API (Flask/Python)                  │
│  ├─ Authentication                                  │
│  ├─ Delivery Management                             │
│  ├─ Location Tracking                               │
│  └─ Earnings Calculation                            │
└─────────────────────────────────────────────────────┘
```

## Adding New Features

### 1. Add New Screen

**Step 1: Create Model (if needed)**
```dart
// lib/models/new_model.dart
class NewModel {
  final String id;
  final String name;
  
  NewModel({required this.id, required this.name});
  
  factory NewModel.fromJson(Map<String, dynamic> json) {
    return NewModel(
      id: json['id'],
      name: json['name'],
    );
  }
}
```

**Step 2: Create Screen**
```dart
// lib/screens/new_screen.dart
import 'package:flutter/material.dart';
import '../utils/constants.dart';

class NewScreen extends StatefulWidget {
  const NewScreen({Key? key}) : super(key: key);

  @override
  State<NewScreen> createState() => _NewScreenState();
}

class _NewScreenState extends State<NewScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('New Screen'),
      ),
      body: const Center(
        child: Text('New Screen Content'),
      ),
    );
  }
}
```

**Step 3: Add to Navigation**
```dart
// lib/screens/home_screen.dart
final List<Widget> _screens = [
  // ... existing screens
  const NewScreen(),  // Add here
];

// Update BottomNavigationBar
BottomNavigationBarItem(
  icon: const Icon(Icons.new_icon),
  label: 'New',
),
```

### 2. Add New API Endpoint

**Step 1: Add to ApiService**
```dart
// lib/services/api_service.dart
static Future<Map<String, dynamic>> getNewData() async {
  try {
    final response = await _dio.get('/new-endpoint');
    return response.data;
  } catch (e) {
    rethrow;
  }
}
```

**Step 2: Use in Screen**
```dart
Future<void> _loadData() async {
  try {
    final data = await ApiService.getNewData();
    setState(() {
      _data = data;
    });
  } catch (e) {
    Fluttertoast.showToast(msg: 'Error: $e');
  }
}
```

### 3. Add New Data Model

**Step 1: Create Model File**
```dart
// lib/models/chat_model.dart
class Chat {
  final int id;
  final String message;
  final String senderName;
  final DateTime timestamp;
  
  Chat({
    required this.id,
    required this.message,
    required this.senderName,
    required this.timestamp,
  });
  
  factory Chat.fromJson(Map<String, dynamic> json) {
    return Chat(
      id: json['id'],
      message: json['message'],
      senderName: json['sender_name'],
      timestamp: DateTime.parse(json['timestamp']),
    );
  }
}
```

**Step 2: Add API Methods**
```dart
// lib/services/api_service.dart
static Future<List<Chat>> getChats(int deliveryId) async {
  try {
    final response = await _dio.get('/chats/$deliveryId');
    return (response.data['chats'] as List)
        .map((c) => Chat.fromJson(c))
        .toList();
  } catch (e) {
    rethrow;
  }
}

static Future<Map<String, dynamic>> sendMessage(
  int deliveryId,
  String message,
) async {
  try {
    final response = await _dio.post(
      '/chats/$deliveryId/send',
      data: {'message': message},
    );
    return response.data;
  } catch (e) {
    rethrow;
  }
}
```

## Common UI Components

### Loading State
```dart
if (snapshot.connectionState == ConnectionState.waiting) {
  return const Center(child: CircularProgressIndicator());
}
```

### Error State
```dart
if (snapshot.hasError) {
  return Center(
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Icon(Icons.error_outline, size: 48, color: AppColors.error),
        const SizedBox(height: 16),
        const Text('Error loading data'),
        const SizedBox(height: 16),
        ElevatedButton(
          onPressed: () => setState(() => _loadData()),
          child: const Text('Retry'),
        ),
      ],
    ),
  );
}
```

### Empty State
```dart
if (data.isEmpty) {
  return Center(
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Icon(Icons.inbox, size: 48, color: AppColors.textSecondary),
        const SizedBox(height: 16),
        const Text('No data available'),
      ],
    ),
  );
}
```

### Card Component
```dart
Card(
  margin: const EdgeInsets.symmetric(vertical: 8),
  child: Padding(
    padding: const EdgeInsets.all(16),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Card Title',
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 12),
        Text('Card content goes here'),
      ],
    ),
  ),
)
```

### Status Badge
```dart
Container(
  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
  decoration: BoxDecoration(
    color: AppColors.success.withOpacity(0.2),
    borderRadius: BorderRadius.circular(20),
  ),
  child: const Text(
    'Active',
    style: TextStyle(
      color: AppColors.success,
      fontWeight: FontWeight.bold,
      fontSize: 12,
    ),
  ),
)
```

## Debugging Tips

### Enable Debug Logging
```dart
// In api_service.dart
_dio.interceptors.add(
  LoggingInterceptor(),
);
```

### Print HTTP Requests/Responses
```dart
print('Request: ${options.path}');
print('Data: ${options.data}');
print('Response: ${response.data}');
```

### Check Local Storage
```dart
// In StorageService
print('Token: ${StorageService.getToken()}');
print('User: ${StorageService.getUserName()}');
```

### Monitor GPS
```dart
// In location_service.dart
stream.listen((position) {
  print('Lat: ${position.latitude}, Lng: ${position.longitude}');
});
```

## Best Practices

### 1. Error Handling
```dart
try {
  final data = await ApiService.getSomething();
  setState(() => _data = data);
} catch (e) {
  Fluttertoast.showToast(
    msg: 'Error: ${e.toString()}',
    toastLength: Toast.LENGTH_LONG,
  );
}
```

### 2. Resource Cleanup
```dart
@override
void dispose() {
  _controller?.dispose();
  _subscription?.cancel();
  super.dispose();
}
```

### 3. Loading States
```dart
if (_isLoading) {
  return const CircularProgressIndicator();
}
```

### 4. Form Validation
```dart
if (_emailController.text.isEmpty) {
  Fluttertoast.showToast(msg: 'Email is required');
  return;
}
```

## Performance Optimization

### List Pagination
```dart
int _page = 1;
final List<Delivery> _deliveries = [];

Future<void> _loadMore() async {
  final data = await ApiService.getDeliveries(page: _page);
  setState(() {
    _deliveries.addAll(data);
    _page++;
  });
}

ListView.builder(
  itemCount: _deliveries.length + 1,
  itemBuilder: (context, index) {
    if (index == _deliveries.length) {
      return ElevatedButton(
        onPressed: _loadMore,
        child: const Text('Load More'),
      );
    }
    return DeliveryCard(delivery: _deliveries[index]);
  },
)
```

### Image Caching
```dart
CachedNetworkImage(
  imageUrl: 'https://example.com/image.jpg',
  placeholder: (context, url) => const CircularProgressIndicator(),
  errorWidget: (context, url, error) => const Icon(Icons.error),
)
```

### Lazy Loading Widgets
```dart
LazyLoadScrollView(
  onEndOfPage: () {
    _loadMore();
  },
  child: ListView(
    children: _items,
  ),
)
```

## Testing Guidelines

### Test Model Parsing
```dart
test('Delivery model parses JSON correctly', () {
  final json = {
    'id': 1,
    'order_id': 123,
    'customer_name': 'John Doe',
    // ... other fields
  };
  final delivery = Delivery.fromJson(json);
  expect(delivery.id, 1);
  expect(delivery.customerName, 'John Doe');
});
```

### Test API Calls
```dart
test('getAvailableDeliveries returns list', () async {
  final deliveries = await ApiService.getAvailableDeliveries();
  expect(deliveries, isA<List>());
});
```

### Test Widgets
```dart
testWidgets('LoginScreen renders correctly', (WidgetTester tester) async {
  await tester.pumpWidget(const MyApp());
  expect(find.text('Login'), findsOneWidget);
  expect(find.byType(TextField), findsNWidgets(2));
});
```

## File Organization

New features should follow this structure:
```
feature_name/
├── models/
│   └── feature_model.dart
├── services/
│   └── feature_service.dart
├── screens/
│   └── feature_screen.dart
└── widgets/
    └── feature_widgets.dart
```

## Constants & Styling

Always use constants from `lib/utils/constants.dart`:
```dart
// Colors
AppColors.primary
AppColors.secondary
AppColors.accent
AppColors.success
AppColors.error

// Strings
AppStrings.appTitle
AppStrings.login
AppStrings.loading

// Defaults
AppDefaults.apiBaseUrl
AppDefaults.requestTimeout
```

## Useful Packages

- **get**: State management
- **dio**: HTTP client
- **geolocator**: GPS
- **image_picker**: Camera/gallery
- **shared_preferences**: Storage
- **intl**: Formatting
- **fluttertoast**: Toast notifications

---

**Questions?** Check the main README or API documentation!
