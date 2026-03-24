class Rider {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final String phoneNumber;
  final String profilePictureUrl;
  final String status; // PENDING, ACTIVE, INACTIVE
  final double rating;
  final int totalDeliveries;
  final double totalEarnings;
  final String vehicleType;
  final String? vehicleLicense;

  Rider({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.phoneNumber,
    required this.profilePictureUrl,
    required this.status,
    required this.rating,
    required this.totalDeliveries,
    required this.totalEarnings,
    required this.vehicleType,
    this.vehicleLicense,
  });

  String get fullName => '$firstName $lastName';

  factory Rider.fromJson(Map<String, dynamic> json) {
    return Rider(
      id: json['id'],
      firstName: json['first_name'],
      lastName: json['last_name'],
      email: json['email'],
      phoneNumber: json['phone_number'],
      profilePictureUrl: json['profile_picture_url'] ?? '',
      status: json['status'],
      rating: double.parse(json['rating'].toString()),
      totalDeliveries: json['total_deliveries'] ?? 0,
      totalEarnings: double.parse(json['total_earnings'].toString()),
      vehicleType: json['vehicle_type'],
      vehicleLicense: json['vehicle_license'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
      'phone_number': phoneNumber,
      'profile_picture_url': profilePictureUrl,
      'status': status,
      'rating': rating,
      'total_deliveries': totalDeliveries,
      'total_earnings': totalEarnings,
      'vehicle_type': vehicleType,
      'vehicle_license': vehicleLicense,
    };
  }
}
