import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import '../repositories/emotion_repository.dart';
import '../models/emotion.dart';
import 'history_screen.dart';
import 'stats_screen.dart';

class EmotionScreen extends StatefulWidget {
  final String token;
  const EmotionScreen({super.key, required this.token});

  @override
  State<EmotionScreen> createState() => _EmotionScreenState();
}

class _EmotionScreenState extends State<EmotionScreen> {
  final EmotionRepository _repository = EmotionRepository();
  late Future<List<Emotion>> _emotionsFuture;

  @override
  void initState() {
    super.initState();
    _emotionsFuture = _repository.getEmotions(widget.token);
  }

  void _selectEmotion(Emotion emotion) {
    if (emotion.enfants.isNotEmpty) {
      showModalBottomSheet(
        context: context,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
        ),
        builder: (context) {
          return Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 40,
                  height: 4,
                  margin: const EdgeInsets.only(bottom: 20),
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
                Text(
                  "Précisez votre ressenti : ${emotion.nom}",
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 20),
                ...emotion.enfants.map((sousEmotion) => ListTile(
                  title: Text(sousEmotion.nom),
                  // ON AJOUTE L'IMAGE ICI
                  leading: sousEmotion.imageIcone.isNotEmpty
                      ? SvgPicture.network(
                          "http://10.0.2.2:8000/images/${sousEmotion.imageIcone}",
                          width: 32,
                          height: 32,
                          fit: BoxFit.contain,
                          placeholderBuilder: (_) => const Icon(Icons.sentiment_satisfied, color: Colors.teal),
                        )
                      : const Icon(Icons.subdirectory_arrow_right, color: Colors.teal),
                  trailing: const Icon(Icons.add_circle_outline, color: Colors.teal),
                  onTap: () {
                    Navigator.pop(context);
                    _confirmAndSave(sousEmotion);
                  },
                )).toList(),
                const SizedBox(height: 20),
              ],
            ),
          );
        },
      );
    } else {
      _confirmAndSave(emotion);
    }
  }

  void _confirmAndSave(Emotion emotion) async {
    try {
      bool success = await _repository.sendUserEmotion(emotion.id, widget.token);
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("Humeur '${emotion.nom}' enregistrée dans le journal"),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    } catch (e) {
      print("Erreur : $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Comment vous sentez-vous ?"),
        actions: [
          // BOUTON JOURNAL DE BORD (HISTORIQUE)
          IconButton(
            icon: const Icon(Icons.history_edu),
            tooltip: "Journal de bord",
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => HistoryScreen(token: widget.token),
                ),
              );
            },
          ),
        ],
      ),
      body: FutureBuilder<List<Emotion>>(
        future: _emotionsFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError) {
            return Center(child: Text("Erreur : ${snapshot.error}"));
          }

          final emotions = snapshot.data!;
          return GridView.builder(
            padding: const EdgeInsets.all(20),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              crossAxisSpacing: 20,
              mainAxisSpacing: 20,
            ),
            itemCount: emotions.length,
            itemBuilder: (context, index) {
              final emotion = emotions[index];
              return InkWell(
                onTap: () => _selectEmotion(emotion),
                borderRadius: BorderRadius.circular(20),
                child: Card(
                  elevation: 2,
                  color: Colors.teal[50],
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Si une image existe en base, on l'affiche, sinon on met un soleil par défaut
                      emotion.imageIcone.isNotEmpty
                          ? SvgPicture.network(
                              "http://10.0.2.2:8000/images/${emotion.imageIcone}",
                              height: 60,
                              width: 60,
                              fit: BoxFit.contain,
                              placeholderBuilder: (_) =>
                                  const Icon(Icons.sentiment_satisfied, size: 40, color: Colors.teal),
                            )
                          : const Icon(Icons.wb_sunny_outlined, size: 40, color: Colors.teal),
                      
                      const SizedBox(height: 10),
                      Text(
                        emotion.nom,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: Colors.teal,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
      // BOUTON STATISTIQUES (RAPPORT)
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => StatsScreen(token: widget.token),
            ),
          );
        },
        label: const Text("Rapport"),
        icon: const Icon(Icons.bar_chart),
        backgroundColor: Colors.teal,
      ),
    );
  }
}