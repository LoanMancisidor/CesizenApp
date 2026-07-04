import 'package:flutter/material.dart';
import '../models/article.dart';
import '../repositories/article_repository.dart';
import 'article_detail_screen.dart'; // N'oublie pas d'importer l'écran de détail

class HomeScreen extends StatelessWidget {
  HomeScreen({super.key});

  final ArticleRepository _articleRepository = ArticleRepository();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('CESIZen - Actualités'),
        backgroundColor: Colors.teal[100],
      ),
      body: FutureBuilder<List<Article>>(
        future: _articleRepository.getArticles(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return const Center(child: Text("Erreur de connexion au backend"));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(child: Text("Aucun article disponible"));
          }

          return ListView.builder(
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
              final article = snapshot.data![index];
              
              // On utilise InkWell pour rendre la carte cliquable
              return InkWell(
                onTap: () {
                  // Navigation vers la page de détail en passant l'objet article
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => ArticleDetailScreen(article: article),
                    ),
                  );
                },
                child: Card(
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  color: Colors.teal[50],
                  margin: const EdgeInsets.symmetric(horizontal: 15, vertical: 8),
                  child: Padding(
                    padding: const EdgeInsets.all(12),
                    child: Row(
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: article.imageUrl != null 
                            ? Image.network(
                                article.imageUrl!, 
                                width: 80, 
                                height: 80, 
                                fit: BoxFit.cover,
                                // Gestion d'erreur si l'image ne charge pas
                                errorBuilder: (context, error, stackTrace) => Container(
                                  width: 80, 
                                  height: 80, 
                                  color: Colors.teal[100], 
                                  child: const Icon(Icons.broken_image)
                                ),
                              )
                            : Container(width: 80, height: 80, color: Colors.teal[100], child: const Icon(Icons.image)),
                        ),
                        const SizedBox(width: 15),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                article.titre, 
                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)
                              ),
                              const SizedBox(height: 5),
                              Text(
                                article.contenu, 
                                maxLines: 2, 
                                overflow: TextOverflow.ellipsis, 
                                style: TextStyle(color: Colors.grey[700])
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}