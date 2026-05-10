class Article {
  final int id;
  final String titre;
  final String contenu;
  final String? imageUrl;

  Article({required this.id, required this.titre, required this.contenu, this.imageUrl});

  factory Article.fromJson(Map<String, dynamic> json) {
    return Article(
      id: json['id'],
      titre: json['titre'],
      contenu: json['contenu'],
      imageUrl: json['image_url'],
    );
  }
}