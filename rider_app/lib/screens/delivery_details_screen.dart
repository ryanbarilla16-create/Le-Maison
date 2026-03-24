import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:image_picker/image_picker.dart';
import '../models/delivery_model.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';
import '../utils/constants.dart';

class DeliveryDetailsScreen extends StatefulWidget {
  final Delivery delivery;

  const DeliveryDetailsScreen({
    Key? key,
    required this.delivery,
  }) : super(key: key);

  @override
  State<DeliveryDetailsScreen> createState() => _DeliveryDetailsScreenState();
}

class _DeliveryDetailsScreenState extends State<DeliveryDetailsScreen> {
  bool _isLoading = false;
  String? _selectedImagePath;

  Future<void> _acceptDelivery() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService.acceptDelivery(widget.delivery.id);
      if (response['success']) {
        Fluttertoast.showToast(msg: AppStrings.deliveryAccepted);
        Navigator.pop(context, true);
      } else {
        Fluttertoast.showToast(msg: response['message'] ?? 'Error');
      }
    } catch (e) {
      Fluttertoast.showToast(msg: 'Error: $e');
    }
    setState(() => _isLoading = false);
  }

  Future<void> _rejectDelivery() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService.rejectDelivery(widget.delivery.id);
      if (response['success']) {
        Fluttertoast.showToast(msg: 'Delivery rejected');
        Navigator.pop(context, true);
      } else {
        Fluttertoast.showToast(msg: response['message'] ?? 'Error');
      }
    } catch (e) {
      Fluttertoast.showToast(msg: 'Error: $e');
    }
    setState(() => _isLoading = false);
  }

  Future<void> _startDelivery() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService.startDelivery(widget.delivery.id);
      if (response['success']) {
        Fluttertoast.showToast(msg: 'Delivery started');
        Navigator.pop(context, true);
      } else {
        Fluttertoast.showToast(msg: response['message'] ?? 'Error');
      }
    } catch (e) {
      Fluttertoast.showToast(msg: 'Error: $e');
    }
    setState(() => _isLoading = false);
  }

  Future<void> _uploadProof() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.camera);

    if (pickedFile != null) {
      setState(() => _selectedImagePath = pickedFile.path);
      _completeDelivery(pickedFile.path);
    }
  }

  Future<void> _completeDelivery(String imagePath) async {
    setState(() => _isLoading = true);
    try {
      final uploadResponse = await ApiService.uploadDeliveryProof(
        widget.delivery.id,
        imagePath,
      );

      if (uploadResponse['success']) {
        final completeResponse = await ApiService.completeDelivery(
          widget.delivery.id,
          proofUrl: uploadResponse['proof_url'],
        );

        if (completeResponse['success']) {
          Fluttertoast.showToast(msg: AppStrings.deliveryCompleted);
          Navigator.pop(context, true);
        } else {
          Fluttertoast.showToast(
              msg: completeResponse['message'] ?? 'Error');
        }
      } else {
        Fluttertoast.showToast(msg: uploadResponse['message'] ?? 'Error');
      }
    } catch (e) {
      Fluttertoast.showToast(msg: 'Error: $e');
    }
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Order #${widget.delivery.orderId}'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Customer Info
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Customer Information',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildInfoRow('Name', widget.delivery.customerName),
                    const SizedBox(height: 8),
                    _buildInfoRow('Phone', widget.delivery.customerPhone),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Locations
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Delivery Route',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildLocationBox(
                      Icons.location_on_outlined,
                      AppStrings.pickupLocation,
                      widget.delivery.pickupAddress,
                      Colors.green,
                    ),
                    const SizedBox(height: 8),
                    Center(
                      child: Icon(
                        Icons.arrow_downward,
                        color: AppColors.primary,
                      ),
                    ),
                    const SizedBox(height: 8),
                    _buildLocationBox(
                      Icons.flag_outlined,
                      AppStrings.deliveryLocation,
                      widget.delivery.deliveryAddress,
                      AppColors.primary,
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Delivery Fee
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Delivery Fee',
                      style: TextStyle(fontSize: 16),
                    ),
                    Text(
                      '₱${widget.delivery.deliveryFee.toStringAsFixed(2)}',
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: AppColors.success,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Special Instructions
            if (widget.delivery.specialInstructions != null)
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Special Instructions',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(widget.delivery.specialInstructions!),
                    ],
                  ),
                ),
              ),
            const SizedBox(height: 24),

            // Action Buttons
            _buildActionButtons(),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label),
        Text(
          value,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
      ],
    );
  }

  Widget _buildLocationBox(
    IconData icon,
    String label,
    String address,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        border: Border.all(color: color.withOpacity(0.3)),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                ),
                const SizedBox(height: 4),
                Text(
                  address,
                  style: const TextStyle(fontSize: 13),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButtons() {
    if (widget.delivery.status == 'WAITING') {
      return Column(
        children: [
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              onPressed: _isLoading ? null : _acceptDelivery,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.success,
              ),
              child: const Text(
                AppStrings.acceptDelivery,
                style: TextStyle(color: Colors.white),
              ),
            ),
          ),
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: OutlinedButton(
              onPressed: _isLoading ? null : _rejectDelivery,
              child: const Text(AppStrings.rejectDelivery),
            ),
          ),
        ],
      );
    } else if (widget.delivery.status == 'PICKED_UP') {
      return SizedBox(
        width: double.infinity,
        height: 50,
        child: ElevatedButton(
          onPressed: _isLoading ? null : _startDelivery,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
          ),
          child: const Text(
            AppStrings.startDelivery,
            style: TextStyle(color: Colors.white),
          ),
        ),
      );
    } else if (widget.delivery.status == 'ON_THE_WAY') {
      return SizedBox(
        width: double.infinity,
        height: 50,
        child: ElevatedButton(
          onPressed: _isLoading ? null : _uploadProof,
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.success,
          ),
          child: const Text(
            AppStrings.markDelivered,
            style: TextStyle(color: Colors.white),
          ),
        ),
      );
    }

    return const SizedBox.shrink();
  }
}
