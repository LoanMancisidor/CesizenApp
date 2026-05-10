import 'package:flutter/material.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/emotion_screen.dart';
import 'screens/profile_screen.dart';

void main() {
  runApp(const CesizenApp());
}

class CesizenApp extends StatelessWidget {
  const CesizenApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'CESIZen',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.teal),
        useMaterial3: true,
      ),
      // Si le token est nul, on saura que c'est un visiteur.
      home: const MainNavigation(token: null), 
    );
  }
}

class MainNavigation extends StatefulWidget {
  final String? token;
  const MainNavigation({super.key, this.token});

  @override
  State<MainNavigation> createState() => _MainNavigationState();
}

class _MainNavigationState extends State<MainNavigation> {
  int _selectedIndex = 0;

  void _onItemTapped(int index) {
    // Si l'utilisateur clique sur un onglet qui nécessite d'être connecté
    // (Index 1 = État, Index 2 = Profil)
    if (index > 0 && widget.token == null) {
      Navigator.push(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    } else {
      setState(() => _selectedIndex = index);
    }
  }

  @override
  Widget build(BuildContext context) {
    // On définit les pages de manière dynamique pour correspondre aux onglets
    final List<Widget> pages = [
      HomeScreen(),
      // On met des placeholders si non connecté pour éviter les erreurs d'index
      widget.token != null ? EmotionScreen(token: widget.token!) : const SizedBox(),
    ];
    
    // On ajoute la page profil seulement si le token existe
    if (widget.token != null) {
      pages.add(ProfileScreen(token: widget.token!));
    }

    return Scaffold(
      body: pages[_selectedIndex < pages.length ? _selectedIndex : 0], // Sécurité anti-crash
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: [
          const BottomNavigationBarItem(icon: Icon(Icons.article_outlined), label: 'Actualités'),
          const BottomNavigationBarItem(icon: Icon(Icons.favorite_border), label: 'Mon État'),
          if (widget.token != null)
            const BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
        currentIndex: _selectedIndex < (widget.token != null ? 3 : 2) ? _selectedIndex : 0,
        selectedItemColor: Colors.teal,
        onTap: _onItemTapped,
      ),
    );
  }
}