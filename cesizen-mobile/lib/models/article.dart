class Article {
  final int id;
  final String titre; // Correspond au SQL
  final String contenu; // Correspond au SQL
  final String? imageUrl;
  final int userId;

  Article({
    required this.id,
    required this.titre,
    required this.contenu,
    this.imageUrl,
    required this.userId,
  });

  factory Article.fromJson(Map<String, dynamic> json) {
    return Article(
      id: json['id'],
      titre: json['titre'],
      contenu: json['contenu'],
      imageUrl: json['image_url'],
      userId: json['user_id'],
    );
  }
}