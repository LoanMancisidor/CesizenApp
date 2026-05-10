import 'package:flutter/material.dart';
import '../repositories/emotion_repository.dart';

class StatsScreen extends StatelessWidget {
  final String token;
  final EmotionRepository _repository = EmotionRepository();

  StatsScreen({super.key, required this.token});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Mon Rapport Bien-être")),
      body: FutureBuilder<List<dynamic>>(
        future: _repository.getStats(token),
        builder: (context, snapshot) {
          if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
          if (snapshot.data!.isEmpty) return const Center(child: Text("Pas assez de données pour le rapport."));

          // Calcul du total pour les pourcentages
          int totalGlobal = snapshot.data!.fold(0, (sum, item) => sum + (item['total'] as int));

          return ListView.builder(
            padding: const EdgeInsets.all(20),
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              final item = snapshot.data![index];
              final double pourcentage = (item['total'] / totalGlobal);

              return Padding(
                padding: const EdgeInsets.symmetric(vertical: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(item['nom'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                        Text("${(pourcentage * 100).toStringAsFixed(1)}%"),
                      ],
                    ),
                    const SizedBox(height: 8),
                    LinearProgressIndicator(
                      value: pourcentage,
                      backgroundColor: Colors.teal[50],
                      color: Colors.teal,
                      minHeight: 10,
                      borderRadius: BorderRadius.circular(5),
                    ),
                  ],
                ),
              );
            },
          );
        },
      ),
    );
  }
}