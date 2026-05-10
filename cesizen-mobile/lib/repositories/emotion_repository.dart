import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/emotion.dart';

class EmotionRepository {
  final String baseUrl = "http://10.0.2.2:8000/api";

  Future<List<Emotion>> getEmotions(String token) async {
    final response = await http.get(
      Uri.parse("$baseUrl/emotions"),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      List data = jsonDecode(response.body);
      return data.map((e) => Emotion.fromJson(e)).toList();
    }
    throw Exception("Erreur lors de la récupération des émotions");
  }

  Future<bool> sendUserEmotion(int emotionId, String token) async {
    final response = await http.post(
      Uri.parse("$baseUrl/user-emotions"),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({'emotion_id': emotionId}),
    );
    return response.statusCode == 201;
  }

  Future<List<dynamic>> getHistory(String token) async {
    final response = await http.get(
      Uri.parse("$baseUrl/user-emotions/history"),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return [];
  }

  Future<bool> deleteEmotion(int id, String token) async {
    final response = await http.delete(
      Uri.parse("$baseUrl/user-emotions/$id"),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );
    return response.statusCode == 200;
  }

  Future<List<dynamic>> getStats(String token) async {
  final response = await http.get(
    Uri.parse("$baseUrl/user-emotions/stats"),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  if (response.statusCode == 200) {
    return jsonDecode(response.body);
  }
  return [];
}
}