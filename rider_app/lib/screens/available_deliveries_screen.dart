import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:intl/intl.dart';
import '../models/delivery_model.dart';
import '../services/api_service.dart';
import '../utils/constants.dart';
import 'delivery_details_screen.dart';

class AvailableDeliveriesScreen extends StatefulWidget {
  const AvailableDeliveriesScreen({Key? key}) : super(key: key);

  @override
  State<AvailableDeliveriesScreen> createState() =>
      _AvailableDeliveriesScreenState();
}

class _AvailableDeliveriesScreenState extends State<AvailableDeliveriesScreen> {
  late Future<List<Delivery>> _deliveriesFuture;

  @override
  void initState() {
    super.initState();
    _loadDeliveries();
  }

  void _loadDeliveries() {
    _deliveriesFuture = _fetchDeliveries();
  }

  Future<List<Delivery>> _fetchDeliveries() async {
    try {
      final data = await ApiService.getAvailableDeliveries();
      return (data as List).map((d) => Delivery.fromJson(d)).toList();
    } catch (e) {
      Fluttertoast.showToast(msg: 'Error loading deliveries: $e');
      return [];
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppStrings.availableDeliveries),
      ),
      body: RefreshIndicator(
        onRefresh: (_) async => _loadDeliveries(),
        child: FutureBuilder<List<Delivery>>(
          future: _deliveriesFuture,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            if (snapshot.hasError) {
              return Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(
                      Icons.error_outline,
                      size: 48,
                      color: AppColors.error,
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'Error loading deliveries',
                      style: Theme.of(context).textTheme.titleMedium,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadDeliveries,
                      child: const Text('Retry'),
                    ),
                  ],
                ),
              );
            }

            final deliveries = snapshot.data ?? [];

            if (deliveries.isEmpty) {
              return Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(
                      Icons.local_shipping_outlined,
                      size: 48,
                      color: AppColors.textSecondary,
                    ),
                    const SizedBox(height: 16),
                    const Text(AppStrings.noDeliveries),
                  ],
                ),
              );
            }

            return ListView.builder(
              padding: const EdgeInsets.all(12),
              itemCount: deliveries.length,
              itemBuilder: (context, index) {
                final delivery = deliveries[index];
                return _buildDeliveryCard(context, delivery);
              },
            );
          },
        ),
      ),
    );
  }

  Widget _buildDeliveryCard(BuildContext context, Delivery delivery) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 8),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => DeliveryDetailsScreen(delivery: delivery),
            ),
          ).then((_) => _loadDeliveries());
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Order #${delivery.orderId}',
                          style: Theme.of(context).textTheme.titleMedium
                              ?.copyWith(fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          delivery.customerName,
                          style: Theme.of(context).textTheme.bodyMedium,
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: AppColors.success,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      '₱${delivery.deliveryFee.toStringAsFixed(2)}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              _buildLocationRow(Icons.location_on_outlined,
                  AppStrings.pickupLocation, delivery.pickupAddress),
              const SizedBox(height: 8),
              _buildLocationRow(Icons.flag_outlined, AppStrings.deliveryLocation,
                  delivery.deliveryAddress),
              const SizedBox(height: 12),
              Text(
                'Posted: ${DateFormat('hh:mm a').format(delivery.createdAt)}',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                      color: AppColors.textSecondary,
                    ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLocationRow(
    IconData icon,
    String label,
    String address,
  ) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 16, color: AppColors.primary),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                ),
              ),
              Text(
                address,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 13),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
