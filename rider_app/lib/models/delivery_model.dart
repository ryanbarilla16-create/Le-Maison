class Delivery {
  final int id;
  final int orderId;
  final String customerName;
  final String customerPhone;
  final String pickupAddress;
  final double pickupLat;
  final double pickupLng;
  final String deliveryAddress;
  final double deliveryLat;
  final double deliveryLng;
  final String status; // WAITING, PICKED_UP, ON_THE_WAY, DELIVERED
  final double deliveryFee;
  final String? specialInstructions;
  final DateTime createdAt;

  Delivery({
    required this.id,
    required this.orderId,
    required this.customerName,
    required this.customerPhone,
    required this.pickupAddress,
    required this.pickupLat,
    required this.pickupLng,
    required this.deliveryAddress,
    required this.deliveryLat,
    required this.deliveryLng,
    required this.status,
    required this.deliveryFee,
    this.specialInstructions,
    required this.createdAt,
  });

  factory Delivery.fromJson(Map<String, dynamic> json) {
    return Delivery(
      id: json['id'],
      orderId: json['order_id'],
      customerName: json['customer_name'],
      customerPhone: json['customer_phone'],
      pickupAddress: json['pickup_address'],
      pickupLat: double.parse(json['pickup_lat'].toString()),
      pickupLng: double.parse(json['pickup_lng'].toString()),
      deliveryAddress: json['delivery_address'],
      deliveryLat: double.parse(json['delivery_lat'].toString()),
      deliveryLng: double.parse(json['delivery_lng'].toString()),
      status: json['status'],
      deliveryFee: double.parse(json['delivery_fee'].toString()),
      specialInstructions: json['special_instructions'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_id': orderId,
      'customer_name': customerName,
      'customer_phone': customerPhone,
      'pickup_address': pickupAddress,
      'pickup_lat': pickupLat,
      'pickup_lng': pickupLng,
      'delivery_address': deliveryAddress,
      'delivery_lat': deliveryLat,
      'delivery_lng': deliveryLng,
      'status': status,
      'delivery_fee': deliveryFee,
      'special_instructions': specialInstructions,
      'created_at': createdAt.toIso8601String(),
    };
  }
}
