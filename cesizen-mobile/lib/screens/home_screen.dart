import 'package:flutter/material.dart';
import '../models/article.dart';
import '../repositories/article_repository.dart';

class HomeScreen extends StatelessWidget {
  final ArticleRepository _articleRepository = ArticleRepository();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('CESIZen - Actualités'),
        backgroundColor: Colors.teal[100], // Couleur Zen
      ),
      body: FutureBuilder<List<Article>>(
        future: _articleRepository.getArticles(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator()); // Chargement
          } else if (snapshot.hasError) {
            return Center(child: Text("Erreur de connexion au backend"));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text("Aucun article disponible"));
          }

          return ListView.builder(
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              final article = snapshot.data![index];
              return Card(
                margin: EdgeInsets.all(10),
                child: ListTile(
                  title: Text(article.titre, style: TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text(article.contenu, maxLines: 2, overflow: TextOverflow.ellipsis),
                  trailing: Icon(Icons.arrow_forward_ios, size: 16),
                ),
              );
            },
          );
        },
      ),
    );
  }
}