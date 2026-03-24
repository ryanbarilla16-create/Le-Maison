import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:intl/intl.dart';
import '../models/delivery_model.dart';
import '../services/api_service.dart';
import '../utils/constants.dart';
import 'delivery_details_screen.dart';

class ActiveDeliveriesScreen extends StatefulWidget {
  const ActiveDeliveriesScreen({Key? key}) : super(key: key);

  @override
  State<ActiveDeliveriesScreen> createState() => _ActiveDeliveriesScreenState();
}

class _ActiveDeliveriesScreenState extends State<ActiveDeliveriesScreen> {
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
      final data = await ApiService.getActiveDeliveries();
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
        title: const Text(AppStrings.activeDeliveries),
      ),
      body: RefreshIndicator(
        onRefresh: (_) async => _loadDeliveries(),
        child: FutureBuilder<List<Delivery>>(
          future: _deliveriesFuture,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            final deliveries = snapshot.data ?? [];

            if (deliveries.isEmpty) {
              return Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(
                      Icons.delivery_dining_outlined,
                      size: 48,
                      color: AppColors.textSecondary,
                    ),
                    const SizedBox(height: 16),
                    const Text('No active deliveries'),
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
                  _buildStatusBadge(delivery.status),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Icon(Icons.location_on_outlined,
                      size: 16, color: AppColors.primary),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      delivery.deliveryAddress,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 13),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                'Fee: ₱${delivery.deliveryFee.toStringAsFixed(2)}',
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

  Widget _buildStatusBadge(String status) {
    Color color;
    String label;

    switch (status) {
      case 'PICKED_UP':
        color = Colors.orange;
        label = 'Picked Up';
        break;
      case 'ON_THE_WAY':
        color = AppColors.primary;
        label = 'On the Way';
        break;
      default:
        color = AppColors.warning;
        label = status;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.2),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontWeight: FontWeight.bold,
          fontSize: 12,
        ),
      ),
    );
  }
}
