import 'package:flutter/material.dart';

class AppColors {
  static const Color primary = Color(0xFF8B4513);      // Brown
  static const Color secondary = Color(0xFFD2B48C);    // Tan
  static const Color accent = Color(0xFFCD853F);       // Peru
  static const Color success = Color(0xFF4CAF50);
  static const Color warning = Color(0xFFFFC107);
  static const Color error = Color(0xFFF44336);
  static const Color background = Color(0xFFFAF9F6);
  static const Color surface = Colors.white;
  static const Color textPrimary = Color(0xFF333333);
  static const Color textSecondary = Color(0xFF666666);
  static const Color divider = Color(0xFFEEEEEE);
}

class AppStrings {
  // Auth
  static const String appTitle = 'Le Maison Rider';
  static const String login = 'Login';
  static const String signup = 'Sign Up';
  static const String email = 'Email';
  static const String password = 'Password';
  static const String phone = 'Phone Number';
  static const String fullName = 'Full Name';

  // Delivery
  static const String availableDeliveries = 'Available Deliveries';
  static const String activeDeliveries = 'Active Deliveries';
  static const String completedDeliveries = 'Completed';
  static const String pickupLocation = 'Pickup Location';
  static const String deliveryLocation = 'Delivery Location';
  static const String acceptDelivery = 'Accept Delivery';
  static const String rejectDelivery = 'Reject Delivery';
  static const String startDelivery = 'Start Delivery';
  static const String markDelivered = 'Mark as Delivered';
  static const String uploadProof = 'Upload Proof';

  // Profile
  static const String profile = 'Profile';
  static const String earnings = 'Earnings';
  static const String totalEarnings = 'Total Earnings';
  static const String todayEarnings = 'Today\'s Earnings';
  static const String deliveriesCompleted = 'Deliveries Completed';
  static const String rating = 'Rating';
  static const String logout = 'Logout';

  // Messages
  static const String noDeliveries = 'No deliveries available';
  static const String loadingDeliveries = 'Loading deliveries...';
  static const String deliveryAccepted = 'Delivery accepted successfully';
  static const String deliveryCompleted = 'Delivery marked as completed';
  static const String locationPermission = 'Location permission required';
}

class AppDefaults {
  static const String apiBaseUrl = 'http://localhost:5000/api';
  static const Duration requestTimeout = Duration(seconds: 30);
  static const int locationUpdateInterval = 5000; // milliseconds
}
