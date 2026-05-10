import 'package:flutter/material.dart';
import '../repositories/emotion_repository.dart';
import 'package:intl/intl.dart';

class HistoryScreen extends StatefulWidget {
  final String token;
  const HistoryScreen({super.key, required this.token});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  final EmotionRepository _repository = EmotionRepository();
  late Future<List<dynamic>> _historyFuture;

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  void _loadHistory() {
    setState(() {
      _historyFuture = _repository.getHistory(widget.token);
    });
  }

  void _deleteEntry(int id) async {
    bool success = await _repository.deleteEmotion(id, widget.token);
    if (success) {
      _loadHistory();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Entrée supprimée du journal")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Mon Journal de Bord"),
        backgroundColor: Colors.teal[50],
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _historyFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(child: Text("Erreur : ${snapshot.error}"));
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.history_edu, size: 60, color: Colors.grey),
                  SizedBox(height: 10),
                  Text("Aucun historique pour le moment."),
                ],
              ),
            );
          }

          final history = snapshot.data!;

          return ListView.separated(
            padding: const EdgeInsets.all(15),
            itemCount: history.length,
            separatorBuilder: (context, index) => const Divider(),
            itemBuilder: (context, index) {
              final item = history[index];
              final String emotionNom = item['emotion']['nom'] ?? 'Inconnue';
              final DateTime date = DateTime.parse(item['created_at']);

              return ListTile(
                leading: const CircleAvatar(
                  backgroundColor: Colors.teal,
                  child: Icon(Icons.favorite, color: Colors.white, size: 20),
                ),
                title: Text(
                  emotionNom,
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                subtitle: Text(
                  DateFormat('dd/MM/yyyy HH:mm').format(date.toLocal()),
                ),
                trailing: IconButton(
                  icon: const Icon(Icons.delete_outline, color: Colors.redAccent),
                  onPressed: () => _deleteEntry(item['id']),
                ),
              );
            },
          );
        },
      ),
    );
  }
}