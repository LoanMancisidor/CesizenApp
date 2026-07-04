import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:cesizen_mobile/models/article.dart';
import 'package:cesizen_mobile/screens/login_screen.dart';

void main() {
  group('Article', () {
    test('fromJson parse les champs attendus', () {
      final article = Article.fromJson({
        'id': 1,
        'titre': 'Comprendre le stress',
        'contenu': 'Un article sur la sante mentale.',
        'image_url': 'https://example.com/image.png',
      });

      expect(article.id, 1);
      expect(article.titre, 'Comprendre le stress');
      expect(article.contenu, 'Un article sur la sante mentale.');
      expect(article.imageUrl, 'https://example.com/image.png');
    });

    test('fromJson accepte une image_url absente', () {
      final article = Article.fromJson({
        'id': 2,
        'titre': 'Sans image',
        'contenu': 'Contenu',
        'image_url': null,
      });

      expect(article.imageUrl, isNull);
    });
  });

  group('LoginScreen', () {
    testWidgets('affiche les champs email/mot de passe et le bouton de connexion',
        (WidgetTester tester) async {
      await tester.pumpWidget(const MaterialApp(home: LoginScreen()));

      expect(find.text('CESIZen'), findsOneWidget);
      expect(find.widgetWithText(TextField, 'Email'), findsOneWidget);
      expect(find.widgetWithText(TextField, 'Mot de passe'), findsOneWidget);
      expect(find.widgetWithText(ElevatedButton, 'Se connecter'), findsOneWidget);
    });

    testWidgets('permet de saisir un email', (WidgetTester tester) async {
      await tester.pumpWidget(const MaterialApp(home: LoginScreen()));

      await tester.enterText(find.widgetWithText(TextField, 'Email'), 'user@cesizen.fr');
      await tester.pump();

      expect(find.text('user@cesizen.fr'), findsOneWidget);
    });
  });
}
