import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import '../utils/constants.dart';

class EarningsScreen extends StatefulWidget {
  const EarningsScreen({Key? key}) : super(key: key);

  @override
  State<EarningsScreen> createState() => _EarningsScreenState();
}

class _EarningsScreenState extends State<EarningsScreen> {
  late Future<Map<String, dynamic>> _earningsFuture;

  @override
  void initState() {
    super.initState();
    _earningsFuture = ApiService.getRiderEarnings();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(AppStrings.earnings),
      ),
      body: RefreshIndicator(
        onRefresh: (_) async {
          setState(() => _earningsFuture = ApiService.getRiderEarnings());
        },
        child: FutureBuilder<Map<String, dynamic>>(
          future: _earningsFuture,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            if (snapshot.hasError) {
              return Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.error_outline,
                        size: 48, color: AppColors.error),
                    const SizedBox(height: 16),
                    const Text('Error loading earnings'),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () {
                        setState(() {
                          _earningsFuture =
                              ApiService.getRiderEarnings();
                        });
                      },
                      child: const Text('Retry'),
                    ),
                  ],
                ),
              );
            }

            final data = snapshot.data ?? {};
            final totalEarnings = double.parse(
              data['total_earnings'].toString(),
            );
            final todayEarnings =
                double.parse(data['today_earnings'].toString());
            final deliveriesCompleted = data['deliveries_completed'] ?? 0;
            final weeklyEarnings =
                List<Map<String, dynamic>>.from(data['weekly_earnings'] ?? []);

            return SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  // Total Earnings Card
                  Card(
                    color: AppColors.primary,
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        children: [
                          const Text(
                            AppStrings.totalEarnings,
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            '₱${totalEarnings.toStringAsFixed(2)}',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 32,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Stats Grid
                  GridView.count(
                    crossAxisCount: 2,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    mainAxisSpacing: 12,
                    crossAxisSpacing: 12,
                    children: [
                      _buildStatCard(
                        AppStrings.todayEarnings,
                        '₱${todayEarnings.toStringAsFixed(2)}',
                        Icons.today,
                        AppColors.success,
                      ),
                      _buildStatCard(
                        AppStrings.deliveriesCompleted,
                        '$deliveriesCompleted',
                        Icons.check_circle,
                        AppColors.secondary,
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Weekly Earnings Chart
                  if (weeklyEarnings.isNotEmpty) ...[
                    const Align(
                      alignment: Alignment.centerLeft,
                      child: Text(
                        'This Week',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: weeklyEarnings.map((item) {
                            return Padding(
                              padding: const EdgeInsets.symmetric(
                                vertical: 8,
                              ),
                              child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(item['day']),
                                  Text(
                                    '₱${double.parse(item['earnings'].toString()).toStringAsFixed(2)}',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      color: AppColors.success,
                                    ),
                                  ),
                                ],
                              ),
                            );
                          }).toList(),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildStatCard(
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 32, color: color),
            const SizedBox(height: 12),
            Text(
              label,
              style: const TextStyle(
                fontSize: 12,
                color: AppColors.textSecondary,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            Text(
              value,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
