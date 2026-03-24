import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static late SharedPreferences _prefs;

  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Auth
  static Future<void> setToken(String token) async {
    await _prefs.setString('auth_token', token);
  }

  static String? getToken() {
    return _prefs.getString('auth_token');
  }

  static bool isLoggedIn() {
    return _prefs.getString('auth_token') != null;
  }

  static Future<void> setUserId(int userId) async {
    await _prefs.setInt('user_id', userId);
  }

  static int? getUserId() {
    return _prefs.getInt('user_id');
  }

  static Future<void> setUserData(Map<String, dynamic> userData) async {
    await _prefs.setString('user_name', userData['name'] ?? '');
    await _prefs.setString('user_email', userData['email'] ?? '');
    await _prefs.setString('user_phone', userData['phone'] ?? '');
  }

  static String? getUserName() {
    return _prefs.getString('user_name');
  }

  static String? getUserEmail() {
    return _prefs.getString('user_email');
  }

  static String? getUserPhone() {
    return _prefs.getString('user_phone');
  }

  // Preferences
  static Future<void> setLocationTracking(bool enabled) async {
    await _prefs.setBool('location_tracking', enabled);
  }

  static bool isLocationTrackingEnabled() {
    return _prefs.getBool('location_tracking') ?? true;
  }

  // Clear all
  static Future<void> logout() async {
    await _prefs.clear();
  }
}
