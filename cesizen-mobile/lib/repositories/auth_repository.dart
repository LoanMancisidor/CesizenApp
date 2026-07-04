import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

class AuthRepository {
  final String apiUrl = "http://10.0.2.2:8000/api/login";

Future<String?> login(String email, String password) async {
  try {
    final response = await http.post(
      Uri.parse("http://10.0.2.2:8000/api/login"),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'android_emulator',
      }),
    );

    debugPrint("Réponse Laravel: ${response.body}");

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['token'];
    } else {
      debugPrint("Erreur de validation ou autre: ${response.statusCode}");
      return null;
    }
  } catch (e) {
    debugPrint("Erreur de connexion: $e");
    return null;
  }
}

Future<String?> register(String name, String email, String password) async {
  try {
    final response = await http.post(
      Uri.parse("http://10.0.2.2:8000/api/register"),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'name': name,
        'email': email,
        'password': password,
        'device_name': 'android_emulator',
      }),
    );

    if (response.statusCode == 201) {
      return jsonDecode(response.body)['token'];
    }
    return null;
  } catch (e) {
    return null;
  }
}

Future<Map<String, dynamic>> updatePassword(String token, String currentPassword, String newPassword, String confirmPassword) async {
  final response = await http.put(
    Uri.parse("http://10.0.2.2:8000/api/user/password"),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'current_password': currentPassword,
      'new_password': newPassword,
      'new_password_confirmation': confirmPassword, // Laravel attend ce nom pour le 'confirmed'
    }),
  );

  return {
    'success': response.statusCode == 200,
    'message': jsonDecode(response.body)['message'] ?? 'Erreur inconnue',
  };
}

Future<bool> logout(String token) async {
  final response = await http.post(
    Uri.parse("http://10.0.2.2:8000/api/logout"),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );
  return response.statusCode == 200;
}
}