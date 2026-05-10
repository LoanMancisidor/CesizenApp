import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/article.dart';

class ArticleRepository {
  // IMPORTANT : 10.0.2.2 est l'adresse pour accéder au localhost du PC 
  // Si vrai téléphone, il faudra mettre l'adresse IP du PC (ex: 192.168.1.x).
  final String apiUrl = "http://10.0.2.2:8000/api/articles";

  Future<List<Article>> getArticles() async {
    try {
      final response = await http.get(Uri.parse(apiUrl));

      if (response.statusCode == 200) {
        List<dynamic> body = jsonDecode(response.body);
        // On transforme chaque élément du JSON en objet Article
        return body.map((item) => Article.fromJson(item)).toList();
      } else {
        throw Exception("Erreur lors de la récupération des articles");
      }
    } catch (e) {
      throw Exception("Connexion impossible au serveur Laravel : $e");
    }
  }
}