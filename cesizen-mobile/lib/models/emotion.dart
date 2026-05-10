class Emotion {
  final int id;
  final String nom;
  final String imageIcone;
  final List<Emotion> enfants; // Ajout de la liste des sous-émotions

  Emotion({required this.id, required this.nom, this.imageIcone = '', this.enfants = const []});

  factory Emotion.fromJson(Map<String, dynamic> json) {
    return Emotion(
      id: json['id'],
      nom: json['nom'],
      imageIcone: json['image_icone'] ?? '',
      enfants: json['enfants'] != null
          ? (json['enfants'] as List).map((i) => Emotion.fromJson(i)).toList()
          : [],
    );
  }
}