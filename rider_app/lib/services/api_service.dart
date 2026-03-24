import 'package:dio/dio.dart';
import '../utils/constants.dart';
import 'storage_service.dart';

class ApiService {
  static final Dio _dio = Dio(
    BaseOptions(
      baseUrl: AppDefaults.apiBaseUrl,
      connectTimeout: AppDefaults.requestTimeout,
      receiveTimeout: AppDefaults.requestTimeout,
      contentType: 'application/json',
    ),
  )..interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) {
        final token = StorageService.getToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          // Token expired - logout
          StorageService.logout();
        }
        return handler.next(error);
      },
    ),
  );

  // Auth
  static Future<Map<String, dynamic>> login(
    String email,
    String password,
  ) async {
    try {
      final response = await _dio.post(
        '/riders/login',
        data: {'email': email, 'password': password},
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> signup(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/riders/signup', data: data);
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // Deliveries
  static Future<List<dynamic>> getAvailableDeliveries() async {
    try {
      final response = await _dio.get('/deliveries/available');
      return response.data['deliveries'] ?? [];
    } catch (e) {
      rethrow;
    }
  }

  static Future<List<dynamic>> getActiveDeliveries() async {
    try {
      final response = await _dio.get('/deliveries/active');
      return response.data['deliveries'] ?? [];
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> getDeliveryDetails(int deliveryId) async {
    try {
      final response = await _dio.get('/deliveries/$deliveryId');
      return response.data['delivery'] ?? {};
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> acceptDelivery(int deliveryId) async {
    try {
      final response = await _dio.post('/deliveries/$deliveryId/accept');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> rejectDelivery(int deliveryId) async {
    try {
      final response = await _dio.post('/deliveries/$deliveryId/reject');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> startDelivery(int deliveryId) async {
    try {
      final response = await _dio.post('/deliveries/$deliveryId/start');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> completeDelivery(
    int deliveryId, {
    String? proofUrl,
  }) async {
    try {
      final response = await _dio.post(
        '/deliveries/$deliveryId/complete',
        data: {'proof_url': proofUrl},
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // Location
  static Future<Map<String, dynamic>> updateLocation(
    double latitude,
    double longitude,
  ) async {
    try {
      final response = await _dio.post(
        '/riders/location',
        data: {'latitude': latitude, 'longitude': longitude},
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // Profile & Earnings
  static Future<Map<String, dynamic>> getRiderProfile() async {
    try {
      final response = await _dio.get('/riders/profile');
      return response.data['rider'] ?? {};
    } catch (e) {
      rethrow;
    }
  }

  static Future<Map<String, dynamic>> getRiderEarnings() async {
    try {
      final response = await _dio.get('/riders/earnings');
      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  // Upload proof
  static Future<Map<String, dynamic>> uploadDeliveryProof(
    int deliveryId,
    String imagePath,
  ) async {
    try {
      FormData formData = FormData.fromMap({
        'delivery_id': deliveryId,
        'proof_file': await MultipartFile.fromFile(
          imagePath,
          filename: 'delivery_proof_$deliveryId.jpg',
        ),
      });

      final response = await _dio.post(
        '/deliveries/upload-proof',
        data: formData,
      );
      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
