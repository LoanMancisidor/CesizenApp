class Emotion {
  final int id;
  final String nom;
  final int? niveau;
  final String? imageIcone;
  final int? parentId;

  Emotion({
    required this.id,
    required this.nom,
    this.niveau,
    this.imageIcone,
    this.parentId,
  });

  factory Emotion.fromJson(Map<String, dynamic> json) {
    return Emotion(
      id: json['id'],
      nom: json['nom'],
      niveau: json['niveau'],
      imageIcone: json['image_icone'],
      parentId: json['parent_id'],
    );
  }
}